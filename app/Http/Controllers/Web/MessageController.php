<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\MessageThread;
use App\Models\User;
use App\Notifications\NewMessageNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class MessageController extends Controller
{
    /**
     * Display a listing of the customer's messages.
     *
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        $query = MessageThread::where('user_id', Auth::id())
            ->with(['lastMessage'])
            ->orderBy('last_message_at', 'desc');

        // Filter by status if provided
        if ($request->filled('status') && in_array($request->status, ['active', 'closed'])) {
            $query->where('status', $request->status);
        }

        $threads = $query->paginate(10);

        return Inertia::render('Web/Messages/Index', [
            'threads' => $threads,
            'filters' => $request->only(['status'])
        ]);
    }

    /**
     * Store a new message from the contact form.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|max:255',
            'subject'       => 'required|string|max:255',
            'message'       => 'required|string',
            'attachment'    => 'nullable|file|max:10240', // 10MB max
        ]);

        try {
            $user = Auth::user();

            // If not logged in, find or create user by email
            if (!$user) {
                $user = User::firstOrCreate(
                    ['email' => $validated['email']],
                    [
                        'name'      => $validated['name'],
                        'password'  => bcrypt(uniqid()), // Random password
                    ]
                );

                // Assign customer role if this is a new user
                if ($user->wasRecentlyCreated) {
                    $user->assignRole(\App\Enums\RolesEnum::CUSTOMER->value);
                }
            }

            // Handle attachment if present
            $attachmentPath = null;
            if ($request->hasFile('attachment')) {
                $attachmentPath = $request->file('attachment')->store('message_attachments', 'public');
            }

            // Create thread
            $thread = MessageThread::create([
                'user_id'           => $user->id,
                'subject'           => $validated['subject'],
                'status'            => 'active',
                'last_message_at'   => now(),
            ]);

            // Create message
            Message::create([
                'thread_id'     => $thread->id,
                'user_id'       => $user->id,
                'content'       => $validated['message'],
                'attachment'    => $attachmentPath,
            ]);

            // Notify admins about new message
            $admins = User::role([\App\Enums\RolesEnum::SUPER_ADMIN->value, \App\Enums\RolesEnum::ADMIN->value])
                ->get();

            foreach ($admins as $admin) {
                $admin->notify(new NewMessageNotification($thread));
            }

            return redirect()->back()->with('success', 'Your message has been sent successfully! We will respond as soon as possible.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to send message. Please try again later.');
        }
    }

    /**
     * Display the message thread.
     *
     * @param  \App\Models\MessageThread  $thread
     * @return \Inertia\Response
     */
    public function show(MessageThread $thread)
    {
        // Ensure user can only view their own threads
        if ($thread->user_id !== Auth::id()) {
            abort(403);
        }

        $thread->load(['messages' => function($query) {
            $query->with(['user', 'admin'])->orderBy('created_at', 'asc');
        }]);

        // Mark all unread messages as read
        $thread->messages()
            ->where('admin_id', '!=', null)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return Inertia::render('Web/Messages/Show', [
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
        // Ensure user can only reply to their own threads
        if ($thread->user_id !== Auth::id()) {
            abort(403);
        }

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
            'user_id'       => Auth::id(),
            'content'       => $validated['message'],
            'attachment'    => $attachmentPath,
        ]);

        // Update thread last message time
        $thread->update([
            'last_message_at'   => now(),
            'status'            => 'active' // Reopen if it was closed
        ]);

        // Notify admins about new reply
        $admins = User::role([\App\Enums\RolesEnum::SUPER_ADMIN->value, \App\Enums\RolesEnum::ADMIN->value])
            ->get();

        foreach ($admins as $admin) {
            $admin->notify(new NewMessageNotification($thread, true));
        }

        return redirect()->back()->with('success', 'Your reply has been sent.');
    }
}
