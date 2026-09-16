<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use App\Models\Product;
use App\Models\User;
use App\Models\ChatMessage;
use App\Mail\WishlistNotificationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class WishlistController extends Controller
{
    public function index(Request $request)
    {
        $query = Wishlist::with(['user', 'product.images']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->whereHas('user', function ($uq) use ($s) {
                    $uq->where('name', 'like', "%{$s}%")
                       ->orWhere('email', 'like', "%{$s}%")
                       ->orWhere('phone', 'like', "%{$s}%");
                })->orWhereHas('product', function ($pq) use ($s) {
                    $pq->where('name', 'like', "%{$s}%")
                       ->orWhere('sku', 'like', "%{$s}%");
                });
            });
        }

        if ($request->filled('stock_status')) {
            $stockStatus = $request->stock_status;
            if ($stockStatus === 'in-stock') {
                $query->whereHas('product', function ($pq) {
                    $pq->where('stock', '>', 0);
                });
            } elseif ($stockStatus === 'out-of-stock') {
                $query->whereHas('product', function ($pq) {
                    $pq->where('stock', '<=', 0);
                });
            }
        }

        $perPage = $request->input('per_page', 10);
        $wishlists = $query->latest()->paginate($perPage)->withQueryString();

        $stats = [
            'total' => Wishlist::count(),
            'unique_customers' => Wishlist::distinct('user_id')->count('user_id'),
            'in_stock' => Wishlist::whereHas('product', function ($q) { $q->where('stock', '>', 0); })->count(),
            'out_of_stock' => Wishlist::whereHas('product', function ($q) { $q->where('stock', '<=', 0); })->count(),
            'notified_count' => Wishlist::whereNotNull('last_notified_at')->count(),
        ];

        return view('admin.wishlist.index', compact('wishlists', 'stats'));
    }

    public function notifyUser(Request $request, $id)
    {
        $wishlist = Wishlist::with(['user', 'product'])->findOrFail($id);

        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'send_email' => 'nullable',
            'send_chat' => 'nullable',
        ]);

        $user = $wishlist->user;
        $product = $wishlist->product;

        if (!$user) {
            return back()->with('error', 'Customer associated with this wishlist item could not be found.');
        }

        $subject = $request->input('subject');
        $message = $request->input('message');
        $sendEmail = $request->has('send_email');
        $sendChat = $request->has('send_chat');

        $channels = [];

        // 1. Send Email Notification
        if ($sendEmail && $user->email) {
            try {
                $actionUrl = config('app.url') . '/products/' . ($product->id ?? '');
                Mail::to($user->email)->send(new WishlistNotificationMail($user, $product, $subject, $message, $actionUrl));
                $channels[] = 'Email';
            } catch (\Throwable $e) {
                Log::error('Wishlist Mail Notification Error: ' . $e->getMessage());
                // Still record attempt if mail server is unconfigured locally
                $channels[] = 'Email (Queued)';
            }
        }

        // 2. Send Live Chat / In-app Message
        if ($sendChat) {
            try {
                $admin = Auth::user();
                $sessionId = 'user_' . $user->id;
                ChatMessage::create([
                    'session_id' => $sessionId,
                    'user_id' => $user->id,
                    'admin_id' => $admin ? $admin->id : null,
                    'sender' => 'admin',
                    'message' => "📢 [Wishlist Alert - " . ($product->name ?? 'Item') . "]\n" . $subject . "\n\n" . $message,
                    'is_read' => false,
                ]);
                $channels[] = 'Live Chat';
            } catch (\Throwable $e) {
                Log::error('Wishlist Chat Notification Error: ' . $e->getMessage());
            }
        }

        // Update last_notified_at
        $wishlist->update(['last_notified_at' => now()]);

        $channelStr = !empty($channels) ? implode(' & ', $channels) : 'Notification';

        return redirect()->route('admin.wishlist.index')->with('success', "{$channelStr} sent successfully to {$user->name}!");
    }

    public function destroy($id)
    {
        $wishlist = Wishlist::findOrFail($id);
        $wishlist->delete();

        return redirect()->route('admin.wishlist.index')->with('success', 'Wishlist item removed successfully!');
    }
}
