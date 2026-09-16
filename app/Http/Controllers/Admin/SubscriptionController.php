<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $query = Subscription::query();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where('email', 'like', "%{$s}%");
        }

        $perPage = $request->input('per_page', 20);
        $subscriptions = $query->latest()->paginate($perPage)->withQueryString();

        $stats = [
            'total' => Subscription::count(),
            'today' => Subscription::whereDate('created_at', today())->count(),
            'this_week' => Subscription::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month' => Subscription::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
        ];

        return view('admin.subscriptions.index', compact('subscriptions', 'stats'));
    }

    public function destroy($id)
    {
        $subscription = Subscription::findOrFail($id);
        $subscription->delete();

        return redirect()->route('admin.subscriptions.index')->with('success', 'Subscriber email removed successfully!');
    }
}
