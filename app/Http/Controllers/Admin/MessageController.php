<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        $query = ContactMessage::query();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%")
                  ->orWhere('subject', 'like', "%{$s}%")
                  ->orWhere('message', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $perPage = $request->input('per_page', 10);
        $messages = $query->latest()->paginate($perPage)->withQueryString();

        $stats = [
            'total' => ContactMessage::count(),
            'unread' => ContactMessage::where('status', 'Unread')->count(),
            'read' => ContactMessage::where('status', 'Read')->count(),
            'replied' => ContactMessage::where('status', 'Replied')->count(),
        ];

        return view('admin.messages.index', compact('messages', 'stats'));
    }

    public function toggleStatus($id)
    {
        $msg = ContactMessage::findOrFail($id);
        $msg->status = ($msg->status === 'Read' || $msg->status === 'Replied') ? 'Unread' : 'Read';
        $msg->save();

        return back()->with('success', 'Message marked as ' . $msg->status);
    }

    public function markAllAsRead()
    {
        ContactMessage::where('status', 'Unread')->update(['status' => 'Read']);
        return back()->with('success', 'All inquiries marked as read.');
    }

    public function destroy($id)
    {
        $msg = ContactMessage::findOrFail($id);
        $msg->delete();

        return redirect()->route('admin.messages.index')->with('success', 'Message deleted successfully!');
    }
}
