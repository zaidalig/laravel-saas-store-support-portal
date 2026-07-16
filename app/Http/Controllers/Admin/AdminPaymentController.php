<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Support\ActivityLogger;
use Illuminate\Http\Request;

class AdminPaymentController extends Controller
{
    public function index() { return view('admin.payments.index', ['payments'=>Payment::with('order','user')->latest()->paginate(10), 'orders'=>Order::latest()->get()]); }
    public function store(Request $request)
    {
        $data = $request->validate(['order_id'=>'required|exists:orders,id','amount'=>'required|numeric|min:0.01','payment_method'=>'required|in:cash,bank_transfer,card,other','status'=>'required|in:pending,completed,failed,refunded','paid_at'=>'nullable|date','notes'=>'nullable']);
        $order = Order::findOrFail($data['order_id']);
        $payment = Payment::create($data + ['user_id'=>$order->user_id, 'payment_number'=>next_number('PAY')]);
        $paid = $order->payments()->where('status','completed')->sum('amount') + ($payment->status === 'completed' ? 0 : 0);
        $order->update(['payment_status' => $paid >= $order->total ? 'paid' : ($paid > 0 ? 'partial' : 'unpaid')]);
        ActivityLogger::log('Created','Payment',"Recorded payment {$payment->payment_number}");
        return back()->with('success','Payment saved.');
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
    public function destroy(Payment $payment) { $payment->delete(); return back()->with('success','Payment deleted.'); }
}
