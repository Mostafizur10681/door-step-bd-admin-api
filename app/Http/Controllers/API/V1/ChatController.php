<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\BlockedChat;
use App\Models\ApprovedChat;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    /**
     * Get chat history for a customer/guest
     */
    public function getMessages(Request $request): JsonResponse
    {
        $sessionId = $request->query('session_id');
        $userId = Auth::guard('sanctum')->id();

        $query = ChatMessage::query();

        if ($userId) {
            $query->where(function ($q) use ($userId, $sessionId) {
                $q->where('user_id', $userId);
                if ($sessionId) {
                    $q->orWhere('session_id', $sessionId);
                }
            });
        } else {
            if (!$sessionId) {
                return response()->json([
                    'success' => false,
                    'message' => 'session_id is required for guests'
                ], 400);
            }
            $query->where('session_id', $sessionId);
        }

        $messages = $query->orderBy('created_at', 'asc')->get();
        $isBlocked = BlockedChat::isBlocked($sessionId, $userId);
        $isApproved = ApprovedChat::isApproved($sessionId, $userId);

        return response()->json([
            'success' => true,
            'is_blocked' => $isBlocked,
            'is_approved' => $isApproved,
            'data' => $messages
        ]);
    }

    /**
     * Store a new chat message from customer/guest
     */
    public function sendMessage(Request $request): JsonResponse
    {
        $request->validate([
            'message' => 'required|string',
            'session_id' => 'nullable|string',
            'is_guest' => 'nullable|boolean',
        ]);

        $sessionId = $request->input('session_id');
        $isGuest = $request->boolean('is_guest') || str_starts_with($request->input('message'), 'Customer requested live agent support:');
        $userId = $isGuest ? null : Auth::guard('sanctum')->id();

        if (!$userId && !$sessionId) {
            return response()->json([
                'success' => false,
                'message' => 'session_id or user authentication is required'
            ], 400);
        }

        if (BlockedChat::isBlocked($sessionId, $userId)) {
            return response()->json([
                'success' => false,
                'message' => 'You have been blocked from using live chat support by an administrator.'
            ], 403);
        }

        $isInitialConnectRequest = str_starts_with($request->input('message'), 'Customer requested live agent support:');

        // If not initial connect request, require admin approval
        if (!$isInitialConnectRequest) {
            $isApproved = ApprovedChat::isApproved($sessionId, $userId);
            if (!$isApproved) {
                return response()->json([
                    'success' => false,
                    'is_approved' => false,
                    'message' => 'Please wait for an agent to approve your connection request before sending messages.'
                ], 403);
            }
        }

        $chatMessage = ChatMessage::create([
            'session_id' => $sessionId,
            'user_id' => $userId,
            'sender' => 'user',
            'message' => $request->input('message'),
            'is_read' => false,
        ]);

        $isApprovedNow = ApprovedChat::isApproved($sessionId, $userId);

        return response()->json([
            'success' => true,
            'is_approved' => $isApprovedNow,
            'data' => $chatMessage
        ], 201);
    }

    /**
     * Admin: Get all active chats/conversations
     */
    public function adminGetChats(Request $request): JsonResponse
    {
        $messages = ChatMessage::with(['user'])
            ->orderBy('created_at', 'desc')
            ->get();

        $chats = [];
        $seen = [];

        foreach ($messages as $msg) {
            $key = $msg->user_id ? 'user_' . $msg->user_id : 'sess_' . $msg->session_id;
            if (!in_array($key, $seen)) {
                $seen[] = $key;
                $chats[] = $msg;
            }
        }

        return response()->json([
            'success' => true,
            'data' => $chats
        ]);
    }

    public function adminGetChatMessages(Request $request, $identifier): JsonResponse
    {
        $query = ChatMessage::query();

        if (is_numeric($identifier)) {
            $userId = (int) $identifier;
            $sessionIds = ChatMessage::where('user_id', $userId)->whereNotNull('session_id')->pluck('session_id')->unique()->toArray();
            
            $query->where(function($q) use ($userId, $sessionIds) {
                $q->where('user_id', $userId);
                if (!empty($sessionIds)) {
                    $q->orWhereIn('session_id', $sessionIds);
                }
            });
        } else {
            $sessionId = $identifier;
            $associatedUserId = ChatMessage::where('session_id', $sessionId)->whereNotNull('user_id')->value('user_id');
            
            $query->where(function($q) use ($sessionId, $associatedUserId) {
                $q->where('session_id', $sessionId);
                if ($associatedUserId) {
                    $q->orWhere('user_id', $associatedUserId);
                }
            });
        }

        $messages = $query->orderBy('created_at', 'asc')->get();

        // Mark retrieved user messages as read
        $userMessageIds = $messages->where('sender', 'user')->pluck('id')->toArray();
        if (!empty($userMessageIds)) {
            ChatMessage::whereIn('id', $userMessageIds)->update(['is_read' => true]);
        }

        return response()->json([
            'success' => true,
            'data' => $messages
        ]);
    }

    /**
     * Admin: Reply to a chat session
     */
    public function adminSendReply(Request $request): JsonResponse
    {
        $request->validate([
            'message' => 'required|string',
            'session_id' => 'nullable|string',
            'user_id' => 'nullable|integer',
        ]);

        $adminId = Auth::guard('sanctum')->id() ?: (request()->user() ? request()->user()->id : null);
        $sessionId = $request->input('session_id');
        $userId = $request->input('user_id');

        // Autofill missing links
        if ($userId && !$sessionId) {
            $sessionId = ChatMessage::where('user_id', $userId)->whereNotNull('session_id')->orderBy('created_at', 'desc')->value('session_id');
        }
        if ($sessionId && !$userId) {
            $userId = ChatMessage::where('session_id', $sessionId)->whereNotNull('user_id')->value('user_id');
        }

        if (!$sessionId && !$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Either session_id or user_id must be provided'
            ], 400);
        }

        // When admin replies, automatically mark chat as approved
        if (!ApprovedChat::isApproved($sessionId, $userId)) {
            ApprovedChat::create([
                'session_id' => $sessionId,
                'user_id' => $userId,
                'approved_by' => $adminId,
                'status' => 'approved',
            ]);
        }

        $chatMessage = ChatMessage::create([
            'session_id' => $sessionId,
            'user_id' => $userId,
            'admin_id' => $adminId,
            'sender' => 'admin',
            'message' => $request->input('message'),
            'is_read' => false,
        ]);

        return response()->json([
            'success' => true,
            'data' => $chatMessage
        ], 201);
    }
}
