<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\AdminUpdateNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class AdminNotificationController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        if (!$user || !$user->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'message' => ['required', 'string', 'max:1000'],
        ]);

        User::query()
            ->whereKeyNot($user->getKey())
            ->select(['id', 'name', 'email'])
            ->chunkById(500, function ($targets) use ($validated, $user) {
                Notification::send($targets, new AdminUpdateNotification([
                    'title' => $validated['title'],
                    'message' => $validated['message'],
                    'created_by' => (int) $user->getKey(),
                ]));
            });

        return back()->with('status', 'notification-sent');
    }

    public function markRead(Request $request, string $notificationId): RedirectResponse
    {
        $notification = $request->user()
            ->notifications()
            ->whereKey($notificationId)
            ->firstOrFail();

        if ($notification->read_at === null) {
            $notification->forceFill(['read_at' => now()])->save();
        }

        return back();
    }

    public function markAllRead(Request $request): RedirectResponse
    {
        $request->user()->unreadNotifications()->update(['read_at' => now()]);

        return back();
    }
}
