<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\Request;

class AdminSubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $perPage = in_array((int) $request->input('per_page', 10), [10, 25, 50, 100], true)
            ? (int) $request->input('per_page', 10)
            : 10;
        $sort = in_array($request->input('sort'), ['created_at', 'subscription_number', 'status', 'starts_at', 'ends_at'], true)
            ? $request->input('sort')
            : 'created_at';
        $direction = $request->input('direction') === 'asc' ? 'asc' : 'desc';

        $subscriptions = Subscription::with('user', 'pricingPlan')
            ->when($request->search, function ($q, $s) {
                $q->where(function ($nested) use ($s) {
                    $nested->where('subscription_number', 'like', "%{$s}%")
                        ->orWhereHas('user', fn ($user) => $user->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%"))
                        ->orWhereHas('pricingPlan', fn ($plan) => $plan->where('name', 'like', "%{$s}%"));
                });
            })
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->orderBy($sort, $direction)
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.subscriptions.index', compact('subscriptions'));
    }
}
