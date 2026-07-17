<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PricingPlan;
use App\Models\Subscription;
use App\Models\SupportTicket;
use App\Models\TicketReply;
use Illuminate\Http\Request;

class CustomerDashboardController extends Controller
{
    public function index()
    {
        $u = auth()->user();

        return view('dashboard.index', [
            'orders' => $u->orders()->count(),
            'invoices' => $u->invoices()->count(),
            'payments' => $u->payments()->count(),
            'tickets' => $u->supportTickets()->count(),
            'subscriptions' => $u->subscriptions()->count(),
        ]);
    }

    public function orders(Request $request)
    {
        $perPage = in_array((int) $request->input('per_page', 10), [10, 25, 50], true)
            ? (int) $request->input('per_page', 10)
            : 10;
        $sort = in_array($request->input('sort'), ['created_at', 'order_number', 'total', 'status', 'payment_status'], true)
            ? $request->input('sort')
            : 'created_at';
        $direction = $request->input('direction') === 'asc' ? 'asc' : 'desc';

        $orders = auth()->user()->orders()
            ->when($request->search, fn ($q, $s) => $q->where('order_number', 'like', "%{$s}%"))
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->payment_status, fn ($q, $s) => $q->where('payment_status', $s))
            ->orderBy($sort, $direction)
            ->paginate($perPage)
            ->withQueryString();

        return view('dashboard.orders.index', compact('orders'));
    }

    public function order(Order $order)
    {
        $this->own($order);

        return view('dashboard.orders.show', ['order' => $order->load('items', 'invoice', 'payments')]);
    }

    public function invoices(Request $request)
    {
        $perPage = in_array((int) $request->input('per_page', 10), [10, 25, 50], true)
            ? (int) $request->input('per_page', 10)
            : 10;
        $sort = in_array($request->input('sort'), ['created_at', 'invoice_number', 'total', 'status', 'issue_date'], true)
            ? $request->input('sort')
            : 'created_at';
        $direction = $request->input('direction') === 'asc' ? 'asc' : 'desc';

        $invoices = auth()->user()->invoices()->with('order')
            ->when($request->search, function ($q, $s) {
                $q->where(function ($nested) use ($s) {
                    $nested->where('invoice_number', 'like', "%{$s}%")
                        ->orWhereHas('order', fn ($order) => $order->where('order_number', 'like', "%{$s}%"));
                });
            })
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->orderBy($sort, $direction)
            ->paginate($perPage)
            ->withQueryString();

        return view('dashboard.invoices.index', compact('invoices'));
    }

    public function payments(Request $request)
    {
        $perPage = in_array((int) $request->input('per_page', 10), [10, 25, 50], true)
            ? (int) $request->input('per_page', 10)
            : 10;
        $sort = in_array($request->input('sort'), ['created_at', 'payment_number', 'amount', 'status', 'payment_method'], true)
            ? $request->input('sort')
            : 'created_at';
        $direction = $request->input('direction') === 'asc' ? 'asc' : 'desc';

        $payments = auth()->user()->payments()->with('order')
            ->when($request->search, function ($q, $s) {
                $q->where(function ($nested) use ($s) {
                    $nested->where('payment_number', 'like', "%{$s}%")
                        ->orWhereHas('order', fn ($order) => $order->where('order_number', 'like', "%{$s}%"));
                });
            })
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->payment_method, fn ($q, $s) => $q->where('payment_method', $s))
            ->orderBy($sort, $direction)
            ->paginate($perPage)
            ->withQueryString();

        return view('dashboard.payments.index', compact('payments'));
    }

    public function tickets(Request $request)
    {
        $perPage = in_array((int) $request->input('per_page', 10), [10, 25, 50], true)
            ? (int) $request->input('per_page', 10)
            : 10;
        $sort = in_array($request->input('sort'), ['created_at', 'ticket_number', 'subject', 'priority', 'status'], true)
            ? $request->input('sort')
            : 'created_at';
        $direction = $request->input('direction') === 'asc' ? 'asc' : 'desc';

        $tickets = auth()->user()->supportTickets()
            ->when($request->search, function ($q, $s) {
                $q->where(function ($nested) use ($s) {
                    $nested->where('ticket_number', 'like', "%{$s}%")->orWhere('subject', 'like', "%{$s}%");
                });
            })
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->priority, fn ($q, $s) => $q->where('priority', $s))
            ->orderBy($sort, $direction)
            ->paginate($perPage)
            ->withQueryString();

        return view('dashboard.tickets.index', compact('tickets'));
    }

    public function ticket(SupportTicket $ticket)
    {
        $this->own($ticket);

        return view('dashboard.tickets.show', [
            'ticket' => $ticket->load([
                'replies' => fn ($q) => $q->where('is_internal_note', false),
                'replies.user',
                'order',
            ]),
        ]);
    }

    public function createTicket()
    {
        return view('dashboard.tickets.create', ['orders' => auth()->user()->orders()->latest()->get()]);
    }

    public function storeTicket(Request $request)
    {
        $data = $request->validate([
            'order_id' => 'nullable|exists:orders,id',
            'subject' => 'required',
            'message' => 'required',
            'priority' => 'required|in:low,medium,high,urgent',
        ]);
        SupportTicket::create($data + [
            'user_id' => auth()->id(),
            'ticket_number' => next_number(setting('ticket_prefix', 'TCK')),
            'status' => 'open',
        ]);

        return redirect()->route('dashboard.tickets')->with('success', 'Ticket created.');
    }

    public function reply(Request $request, SupportTicket $ticket)
    {
        $this->own($ticket);
        TicketReply::create([
            'ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'message' => $request->validate(['message' => 'required|string'])['message'],
            'is_internal_note' => false,
        ]);

        return back()->with('success', 'Reply sent.');
    }

    public function profile()
    {
        return view('dashboard.profile', ['user' => auth()->user()]);
    }

    public function updateProfile(ProfileRequest $request)
    {
        $data = $request->validated();
        if (blank($data['password'])) {
            unset($data['password']);
        }
        $request->user()->update($data);

        return back()->with('success', 'Profile updated.');
    }

    public function subscriptions(Request $request)
    {
        $perPage = in_array((int) $request->input('per_page', 10), [10, 25, 50], true)
            ? (int) $request->input('per_page', 10)
            : 10;
        $sort = in_array($request->input('sort'), ['created_at', 'subscription_number', 'status', 'starts_at', 'ends_at'], true)
            ? $request->input('sort')
            : 'created_at';
        $direction = $request->input('direction') === 'asc' ? 'asc' : 'desc';

        $subscriptions = auth()->user()->subscriptions()->with('pricingPlan')
            ->when($request->search, function ($q, $s) {
                $q->where(function ($nested) use ($s) {
                    $nested->where('subscription_number', 'like', "%{$s}%")
                        ->orWhereHas('pricingPlan', fn ($plan) => $plan->where('name', 'like', "%{$s}%"));
                });
            })
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->orderBy($sort, $direction)
            ->paginate($perPage)
            ->withQueryString();

        return view('dashboard.subscriptions.index', compact('subscriptions'));
    }

    public function subscribe(PricingPlan $pricingPlan)
    {
        abort_unless($pricingPlan->status === 'active', 404);

        $user = auth()->user();
        $hasActive = $user->subscriptions()->where('status', 'active')->where('ends_at', '>', now())->exists();

        if ($hasActive) {
            return back()->with('error', 'You already have an active subscription.');
        }

        Subscription::create([
            'user_id' => $user->id,
            'pricing_plan_id' => $pricingPlan->id,
            'subscription_number' => next_number(setting('subscription_prefix', 'SUB')),
            'status' => 'active',
            'starts_at' => now(),
            'ends_at' => now()->addDays($pricingPlan->duration_days),
        ]);

        return redirect()->route('dashboard.subscriptions')->with('success', "Subscribed to {$pricingPlan->name}.");
    }

    public function cancelSubscription(Subscription $subscription)
    {
        $this->own($subscription);
        abort_unless($subscription->status === 'active', 422);

        $subscription->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        return back()->with('success', 'Subscription cancelled.');
    }

    private function own($model): void
    {
        abort_unless((int) $model->user_id === auth()->id(), 403);
    }
}
