<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\MessageThread;
use App\Notifications\MessageReplyNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class MessageController extends Controller
{
    /**
     * Display a listing of all message threads.
     *
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        $query = MessageThread::with(['user', 'lastMessage'])
            ->orderBy('last_message_at', 'desc');

        // Handle search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($request->filled('status') && in_array($request->status, ['active', 'closed', 'unread'])) {
            if ($request->status === 'unread') {
                $query->whereHas('messages', function($q) {
                    $q->where('user_id', '!=', null)
                      ->where('is_read', false);
                });
            } else {
                $query->where('status', $request->status);
            }
        }

        $threads = $query->paginate(15);

        return Inertia::render('Dashboard/Messages/Index', [
            'threads' => $threads,
            'filters' => $request->only(['search', 'status'])
        ]);
    }

    /**
     * Display the message thread.
     *
     * @param  \App\Models\MessageThread  $thread
     * @return \Inertia\Response
     */
    public function show(MessageThread $thread)
    {
        $thread->load(['user', 'messages' => function($query) {
            $query->with(['user', 'admin'])->orderBy('created_at', 'asc');
        }]);

        // Mark all unread messages as read
        $thread->messages()
            ->where('user_id', '!=', null)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return Inertia::render('Dashboard/Messages/Show', [
            'thread' => $thread
        ]);
    }

    /**
     * Reply to a message thread.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\MessageThread  $thread
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reply(Request $request, MessageThread $thread)
    {
        $validated = $request->validate([
            'message'       => 'required|string',
            'attachment'    => 'nullable|file|max:10240', // 10MB max
        ]);

        // Handle attachment if present
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('message_attachments', 'public');
        }

        // Create reply
        Message::create([
            'thread_id'     => $thread->id,
            'admin_id'      => Auth::id(),
            'content'       => $validated['message'],
            'attachment'    => $attachmentPath,
        ]);

        // Update thread last message time
        $thread->update([
            'last_message_at'   => now(),
            'status'            => 'active' // Ensure it's marked as active
        ]);

        // Notify user about reply
        if ($thread->user) {
            $thread->user->notify(new MessageReplyNotification($thread));
        }

        return redirect()->back()->with('success', 'Your reply has been sent.');
    }

    /**
     * Close a message thread.
     *
     * @param  \App\Models\MessageThread  $thread
     * @return \Illuminate\Http\RedirectResponse
     */
    public function close(MessageThread $thread)
    {
        $thread->update(['status' => 'closed']);

        return redirect()->back()->with('success', 'Thread has been closed.');
    }

    /**
     * Reopen a message thread.
     *
     * @param  \App\Models\MessageThread  $thread
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reopen(MessageThread $thread)
    {
        $thread->update(['status' => 'active']);

        return redirect()->back()->with('success', 'Thread has been reopened.');
    }
}
