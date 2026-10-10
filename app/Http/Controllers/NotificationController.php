<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Mark a specific notification as read.
     */
    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->find($id);
        
        if ($notification) {
            $notification->markAsRead();
        }
        
        // Coba redirect ke halaman terkait jika ada (opsional)
        if (isset($notification->data['url'])) {
            return redirect($notification->data['url']);
        }
        
        if (isset($notification->data['implementasi_id'])) {
            return redirect()->route('implementasi.show', $notification->data['implementasi_id']);
        }
        
        return back()->with('success', __('messages.notif_read'));
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        return back()->with('success', __('messages.notif_read_all'));
    }
}
