<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Support\ActivityLogger;
use Illuminate\Http\Request;

class AdminPaymentController extends Controller
{
    public function index(Request $request)
    {
        $perPage = in_array((int) $request->input('per_page', 10), [10, 25, 50, 100], true)
            ? (int) $request->input('per_page', 10)
            : 10;
        $sort = in_array($request->input('sort'), ['created_at', 'payment_number', 'amount', 'status', 'payment_method'], true)
            ? $request->input('sort')
            : 'created_at';
        $direction = $request->input('direction') === 'asc' ? 'asc' : 'desc';

        $payments = Payment::with('order', 'user')
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

        return view('admin.payments.index', [
            'payments' => $payments,
            'orders' => Order::latest()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,bank_transfer,card,other',
            'status' => 'required|in:pending,completed,failed,refunded',
            'paid_at' => 'nullable|date',
            'notes' => 'nullable',
        ]);
        $order = Order::findOrFail($data['order_id']);
        $payment = Payment::create($data + ['user_id' => $order->user_id, 'payment_number' => next_number('PAY')]);
        $paid = $order->payments()->where('status', 'completed')->sum('amount') + ($payment->status === 'completed' ? 0 : 0);
        $order->update(['payment_status' => $paid >= $order->total ? 'paid' : ($paid > 0 ? 'partial' : 'unpaid')]);
        ActivityLogger::log('Created', 'Payment', "Recorded payment {$payment->payment_number}");

        return back()->with('success', 'Payment saved.');
    }

    public function update(Request $request, Payment $payment)
    {
        $payment->update($request->validate([
            'status' => 'required|in:pending,completed,failed,refunded',
            'notes' => 'nullable|string',
        ]));

        $paid = $payment->order->payments()->where('status', 'completed')->sum('amount');
        $payment->order->update([
            'payment_status' => $paid >= $payment->order->total ? 'paid' : ($paid > 0 ? 'partial' : 'unpaid'),
        ]);

        return back()->with('success', 'Payment status updated.');
    }

    public function destroy(Payment $payment)
    {
        $payment->delete();

        return back()->with('success', 'Payment deleted.');
    }
}
