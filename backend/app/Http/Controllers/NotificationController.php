<?php

namespace App\Http\Controllers;

use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(DatabaseNotification $notification)
    {
        if ($notification->unread()) {
            $notification->markAsRead();
        }
        $model = (new $notification->data['referensi_type'])->find($notification->data['referensi_id']);
        return redirect($model->routeNotification());
    }
}
