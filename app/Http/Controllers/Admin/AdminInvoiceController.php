<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Order;
use Illuminate\Http\Request;

class AdminInvoiceController extends Controller
{
    public function index() { return view('admin.invoices.index', ['invoices'=>Invoice::with('order','user')->latest()->paginate(10), 'orders'=>Order::doesntHave('invoice')->get()]); }
    public function show(Invoice $invoice) { return view('admin.invoices.show', ['invoice'=>$invoice->load('order.items','user')]); }
    public function print(Invoice $invoice) { return view('admin.invoices.print', ['invoice'=>$invoice->load('order.items','user')]); }
    public function store(Request $request)
    {
        $order = Order::findOrFail($request->validate(['order_id'=>'required|exists:orders,id'])['order_id']);
        Invoice::firstOrCreate(['order_id'=>$order->id], ['user_id'=>$order->user_id,'invoice_number'=>next_number(setting('invoice_prefix','INV')),'issue_date'=>now(),'due_date'=>now()->addDays(14),'subtotal'=>$order->subtotal,'discount'=>$order->discount,'tax'=>$order->tax,'total'=>$order->total,'status'=>'unpaid']);
        return back()->with('success','Invoice generated.');
    }
    public function update(Request $request, Invoice $invoice) { $invoice->update($request->validate(['status'=>'required|in:unpaid,partial,paid,cancelled'])); return back()->with('success','Invoice updated.'); }
}