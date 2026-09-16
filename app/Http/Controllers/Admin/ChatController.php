<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\BlockedChat;
use App\Models\ApprovedChat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    private function parseGuestDetails($messageText)
    {
        $details = ['name' => null, 'email' => null, 'phone' => null];
        if (!$messageText) return $details;

        if (preg_match('/Full Name:\s*([^\n\r•]+)/i', $messageText, $m)) {
            $details['name'] = trim($m[1]);
        }
        if (preg_match('/Email:\s*([^\n\r•]+)/i', $messageText, $m)) {
            $details['email'] = trim($m[1]);
        }
        if (preg_match('/Phone(?:\s*Number)?:\s*([^\n\r•]+)/i', $messageText, $m)) {
            $details['phone'] = trim($m[1]);
        }
        return $details;
    }

    public function index(Request $request)
    {
        // Group chats by session_id
        $rawSessions = ChatMessage::with('user')
            ->select('session_id', 'user_id')
            ->selectRaw('MAX(created_at) as last_message_at, COUNT(*) as total_messages')
            ->groupBy('session_id', 'user_id')
            ->orderByDesc('last_message_at')
            ->get();

        $activeSessionId = $request->get('session_id', $rawSessions->first()->session_id ?? null);
        
        $messages = [];
        if ($activeSessionId) {
            $messages = ChatMessage::where('session_id', $activeSessionId)->oldest()->get();
            ChatMessage::where('session_id', $activeSessionId)->where('sender', 'user')->update(['is_read' => true]);
        }

        $unreadMap = ChatMessage::where('sender', 'user')
            ->where('is_read', false)
            ->groupBy('session_id')
            ->selectRaw('session_id, COUNT(*) as unread_count')
            ->pluck('unread_count', 'session_id')
            ->toArray();

        $lastMessages = ChatMessage::orderBy('created_at', 'desc')
            ->get()
            ->unique('session_id')
            ->keyBy('session_id');

        $firstMessages = ChatMessage::where('sender', 'user')
            ->orderBy('created_at', 'asc')
            ->get()
            ->unique('session_id')
            ->keyBy('session_id');

        $blockedMap = BlockedChat::all();
        $blockedSessionIds = $blockedMap->pluck('session_id')->filter()->toArray();
        $blockedUserIds = $blockedMap->pluck('user_id')->filter()->toArray();

        $approvedMap = ApprovedChat::where('status', 'approved')->get();
        $approvedSessionIds = $approvedMap->pluck('session_id')->filter()->toArray();
        $approvedUserIds = $approvedMap->pluck('user_id')->filter()->toArray();

        $sessions = $rawSessions->map(function ($sess) use ($unreadMap, $lastMessages, $firstMessages, $blockedSessionIds, $blockedUserIds, $approvedSessionIds, $approvedUserIds) {
            $firstMsg = $firstMessages[$sess->session_id]->message ?? '';
            $guestParsed = $this->parseGuestDetails($firstMsg);

            $sessName = $sess->user ? $sess->user->name : ($guestParsed['name'] ?: ($sess->session_id ? 'Guest (' . substr($sess->session_id, 0, 8) . '...)' : 'Website Visitor'));
            $sessEmail = $sess->user ? $sess->user->email : ($guestParsed['email'] ?: 'Guest visitor');

            $sess->unread_count = $unreadMap[$sess->session_id] ?? 0;
            $sess->last_message = isset($lastMessages[$sess->session_id]) ? $lastMessages[$sess->session_id]->message : '';
            $sess->is_blocked = (in_array($sess->session_id, $blockedSessionIds) || ($sess->user_id && in_array($sess->user_id, $blockedUserIds)));
            $sess->is_approved = (in_array($sess->session_id, $approvedSessionIds) || ($sess->user_id && in_array($sess->user_id, $approvedUserIds)));
            $sess->display_name = $sessName;
            $sess->display_email = $sessEmail;
            $sess->phone = $guestParsed['phone'] ?? null;
            return $sess;
        });

        $activeIsBlocked = false;
        $activeIsApproved = false;
        if ($activeSessionId) {
            $activeSess = $sessions->firstWhere('session_id', $activeSessionId);
            $activeUserId = $activeSess ? $activeSess->user_id : null;
            $activeIsBlocked = BlockedChat::isBlocked($activeSessionId, $activeUserId);
            $activeIsApproved = ApprovedChat::isApproved($activeSessionId, $activeUserId);
        }

        return view('admin.chats.index', compact('sessions', 'activeSessionId', 'messages', 'activeIsBlocked', 'activeIsApproved'));
    }

    public function fetchMessages(Request $request, $sessionId)
    {
        $messages = ChatMessage::where('session_id', $sessionId)->oldest()->get();
        ChatMessage::where('session_id', $sessionId)->where('sender', 'user')->update(['is_read' => true]);

        $formatted = $messages->map(function ($msg) {
            $isAdmin = ($msg->sender === 'admin' || $msg->sender_type === 'admin');
            return [
                'id' => $msg->id,
                'session_id' => $msg->session_id,
                'sender' => $msg->sender ?? 'user',
                'is_admin' => $isAdmin,
                'message' => $msg->message,
                'time' => $msg->created_at ? $msg->created_at->format('h:i A') : '',
            ];
        });

        $unreadMap = ChatMessage::where('sender', 'user')
            ->where('is_read', false)
            ->groupBy('session_id')
            ->selectRaw('session_id, COUNT(*) as unread_count')
            ->pluck('unread_count', 'session_id')
            ->toArray();

        $lastMessages = ChatMessage::orderBy('created_at', 'desc')
            ->get()
            ->unique('session_id')
            ->keyBy('session_id');

        $firstMessages = ChatMessage::where('sender', 'user')
            ->orderBy('created_at', 'asc')
            ->get()
            ->unique('session_id')
            ->keyBy('session_id');

        $blockedMap = BlockedChat::all();
        $blockedSessionIds = $blockedMap->pluck('session_id')->filter()->toArray();
        $blockedUserIds = $blockedMap->pluck('user_id')->filter()->toArray();

        $approvedMap = ApprovedChat::where('status', 'approved')->get();
        $approvedSessionIds = $approvedMap->pluck('session_id')->filter()->toArray();
        $approvedUserIds = $approvedMap->pluck('user_id')->filter()->toArray();

        $sessions = ChatMessage::with('user')
            ->select('session_id', 'user_id')
            ->selectRaw('MAX(created_at) as last_message_at, COUNT(*) as total_messages')
            ->groupBy('session_id', 'user_id')
            ->orderByDesc('last_message_at')
            ->get()
            ->map(function ($sess) use ($unreadMap, $lastMessages, $firstMessages, $blockedSessionIds, $blockedUserIds, $approvedSessionIds, $approvedUserIds) {
                $firstMsg = $firstMessages[$sess->session_id]->message ?? '';
                $guestParsed = $this->parseGuestDetails($firstMsg);

                $sessName = $sess->user ? $sess->user->name : ($guestParsed['name'] ?: ($sess->session_id ? 'Guest (' . substr($sess->session_id, 0, 8) . '...)' : 'Website Visitor'));
                $sessEmail = $sess->user ? $sess->user->email : ($guestParsed['email'] ?: 'Guest visitor');

                return [
                    'session_id' => $sess->session_id,
                    'user_id' => $sess->user_id,
                    'name' => $sessName,
                    'email' => $sessEmail,
                    'phone' => $guestParsed['phone'] ?? null,
                    'last_time' => $sess->last_message_at ? \Carbon\Carbon::parse($sess->last_message_at)->format('H:i') : '',
                    'unread_count' => $unreadMap[$sess->session_id] ?? 0,
                    'last_message' => isset($lastMessages[$sess->session_id]) ? $lastMessages[$sess->session_id]->message : '',
                    'is_blocked' => (in_array($sess->session_id, $blockedSessionIds) || ($sess->user_id && in_array($sess->user_id, $blockedUserIds))),
                    'is_approved' => (in_array($sess->session_id, $approvedSessionIds) || ($sess->user_id && in_array($sess->user_id, $approvedUserIds))),
                ];
            });

        $totalUnreadAll = ChatMessage::where('sender', 'user')->where('is_read', false)->count();

        $activeUserId = ChatMessage::where('session_id', $sessionId)->whereNotNull('user_id')->value('user_id');
        $isBlocked = BlockedChat::isBlocked($sessionId, $activeUserId);
        $isApproved = ApprovedChat::isApproved($sessionId, $activeUserId);

        return response()->json([
            'success' => true,
            'messages' => $formatted,
            'sessions' => $sessions,
            'total_unread' => $totalUnreadAll,
            'is_blocked' => $isBlocked,
            'is_approved' => $isApproved,
        ]);
    }

    public function reply(Request $request)
    {
        $data = $request->validate([
            'session_id' => 'required|string',
            'message' => 'required|string',
        ]);

        $admin = Auth::user();
        $userId = ChatMessage::where('session_id', $data['session_id'])->whereNotNull('user_id')->value('user_id');

        if (BlockedChat::isBlocked($data['session_id'], $userId)) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot send message. This customer is currently blocked.'
            ], 403);
        }

        // Auto-approve session on reply if not yet approved
        if (!ApprovedChat::isApproved($data['session_id'], $userId)) {
            ApprovedChat::create([
                'session_id' => $data['session_id'],
                'user_id' => $userId,
                'approved_by' => $admin ? $admin->id : null,
                'status' => 'approved',
            ]);
        }

        $msg = ChatMessage::create([
            'session_id' => $data['session_id'],
            'user_id' => $userId,
            'admin_id' => $admin ? $admin->id : null,
            'sender' => 'admin',
            'message' => $data['message'],
            'is_read' => true,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Reply sent successfully!',
                'data' => [
                    'id' => $msg->id,
                    'session_id' => $msg->session_id,
                    'sender' => 'admin',
                    'is_admin' => true,
                    'message' => $msg->message,
                    'time' => $msg->created_at ? $msg->created_at->format('h:i A') : '',
                ]
            ]);
        }

        return redirect()->route('admin.chats.index', ['session_id' => $data['session_id']])->with('success', 'Reply sent!');
    }

    public function toggleApprove(Request $request)
    {
        $sessionId = $request->input('session_id');
        $userId = $request->input('user_id');

        if (!$sessionId && !$userId) {
            return response()->json(['success' => false, 'message' => 'Session ID or User ID is required.'], 400);
        }

        $isApproved = ApprovedChat::isApproved($sessionId, $userId);

        if ($isApproved) {
            ApprovedChat::where(function ($q) use ($sessionId, $userId) {
                if ($sessionId) $q->orWhere('session_id', $sessionId);
                if ($userId) $q->orWhere('user_id', $userId);
            })->delete();

            $status = false;
            $msgText = 'Chat approval revoked.';
        } else {
            ApprovedChat::create([
                'session_id' => $sessionId,
                'user_id' => $userId,
                'approved_by' => Auth::id(),
                'status' => 'approved',
            ]);

            // Post a system / welcome notice
            ChatMessage::create([
                'session_id' => $sessionId,
                'user_id' => $userId,
                'admin_id' => Auth::id(),
                'sender' => 'admin',
                'message' => 'Hello! An agent has accepted your chat request. How can we help you today?',
                'is_read' => false,
            ]);

            $status = true;
            $msgText = 'Chat request approved successfully!';
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_approved' => $status,
                'message' => $msgText,
            ]);
        }

        return redirect()->back()->with('success', $msgText);
    }

    public function toggleBlock(Request $request)
    {
        $sessionId = $request->input('session_id');
        $userId = $request->input('user_id');

        if (!$sessionId && !$userId) {
            return response()->json(['success' => false, 'message' => 'Session ID or User ID is required.'], 400);
        }

        $isBlocked = BlockedChat::isBlocked($sessionId, $userId);

        if ($isBlocked) {
            BlockedChat::where(function ($q) use ($sessionId, $userId) {
                if ($sessionId) $q->orWhere('session_id', $sessionId);
                if ($userId) $q->orWhere('user_id', $userId);
            })->delete();

            $status = false;
            $msgText = 'Customer unblocked successfully!';
        } else {
            BlockedChat::create([
                'session_id' => $sessionId,
                'user_id' => $userId,
                'blocked_by' => Auth::id(),
                'reason' => 'Blocked by support admin',
            ]);

            $status = true;
            $msgText = 'Customer blocked from live chat.';
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_blocked' => $status,
                'message' => $msgText,
            ]);
        }

        return redirect()->back()->with('success', $msgText);
    }
}
