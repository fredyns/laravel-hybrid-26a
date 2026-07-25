<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page', 15);

        $notifications = auth()->user()->notifications()->latest()->paginate($perPage)->appends($request->query());
        $unreadCount = auth()->user()->unreadNotifications()->count();

        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    private function variantColor(string $variant): string
    {
        return match ($variant) {
            'success' => 'green',
            'warning' => 'amber',
            'danger' => 'red',
            'info' => 'blue',
            default => 'zinc',
        };
    }
}
