<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\MessageThread;
use App\Notifications\MessageReplyNotification;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\AllowedInclude;

class MessageController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of all message threads.
     *
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', MessageThread::class);

        $threads = QueryBuilder::for(MessageThread::class)
            ->allowedIncludes(['user', 'lastMessage'])
            ->allowedFilters([
                AllowedFilter::callback('search', function ($query, $value) {
                    $query->where(function ($q) use ($value) {
                        $q->where('subject', 'like', "%{$value}%")
                          ->orWhereHas('user', function ($userQuery) use ($value) {
                              $userQuery->where('name', 'like', "%{$value}%")
                                        ->orWhere('email', 'like', "%{$value}%");
                          });
                    });
                }),
                AllowedFilter::callback('status', function ($query, $value) {
                    if ($value === 'unread') {
                        $query->whereHas('messages', function ($q) {
                            $q->where('user_id', '!=', null)
                              ->where('is_read', false);
                        });
                    } else {
                        $query->where('status', $value);
                    }
                }),
            ])
            ->allowedSorts([
                'created_at',
                'updated_at',
                'last_message_at',
                'status',
            ])
            ->defaultSort('-last_message_at')
            ->with(['user', 'lastMessage'])
            ->paginate(15)
            ->appends($request->query());

        return Inertia::render('Dashboard/Messages/Index', [
            'threads'   => $threads,
            'filters' => [
                'search' => $request->input('filter.search'),
                'status' => $request->input('filter.status'),
            ]
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
        $this->authorize('view', $thread);

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
        $this->authorize('reply', $thread);

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
        $this->authorize('close', $thread);

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
        $this->authorize('reopen', $thread);

        $thread->update(['status' => 'active']);

        return redirect()->back()->with('success', 'Thread has been reopened.');
    }

    /**
     * Get count of unread messages for admin.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function unreadCount()
    {
        $this->authorize('viewAny', MessageThread::class);

        $count = \App\Models\Message::whereHas('thread', function($query) {
                $query->where('status', 'active');
            })
            ->where('user_id', '!=', null)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'count' => $count
        ]);
    }
}
