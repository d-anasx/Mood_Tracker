<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function create(Request $request)
    {
        $users          = User::where('status', 'active')->orderBy('name')->get();
        $selectedUserId = $request->get('user_id');

        return view('admin.notifications.create', compact('users', 'selectedUserId'));
    }

    public function send(Request $request)
    {
        $request->validate([
            'user_id'     => 'nullable|exists:users,id',
            'send_to_all' => 'boolean',
            'title'       => 'required|string|max:100',
            'message'     => 'required|string|max:1000',
        ]);

        if ($request->send_to_all) {
            $users = User::where('status', 'active')->get();

            foreach ($users as $user) {
                Notification::create([
                    'user_id'    => $user->id,
                    'title'      => $request->title,
                    'message'    => $request->message,
                    'is_read'    => false,
                    'created_at' => now(),
                ]);
            }

            $message = "Notification sent to {$users->count()} users.";
        } else {
            $user = User::findOrFail($request->user_id);

            Notification::create([
                'user_id'    => $user->id,
                'title'      => $request->title,
                'message'    => $request->message,
                'is_read'    => false,
                'created_at' => now(),
            ]);

            $message = "Notification sent to {$user->name}.";
        }

        return redirect()->route('admin.users.index')->with('success', $message);
    }

    public function markAsRead($id)
    {
        $notification = Notification::where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if ($notification) {
            $notification->update(['is_read' => true]);
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 404);
    }
}
