<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Product;
use App\Support\ActivityLogger;

class OrderPlacementController extends Controller
{
    public function create(?Product $product = null)
    {
        return view('public.order', [
            'product' => $product,
            'products' => Product::where('status', 'active')->orderBy('name')->get(),
        ]);
    }

    public function store(OrderRequest $request)
    {
        $product = Product::findOrFail($request->product_id);
        $quantity = (int) $request->quantity;
        $subtotal = $product->price * $quantity;
        $tax = round($subtotal * ((float) setting('tax_percentage', 8) / 100), 2);

        $order = Order::create([
            'user_id' => auth()->id(),
            'order_number' => next_number(setting('order_prefix', 'ORD')),
            'customer_name' => $request->customer_name,
            'customer_email' => $request->customer_email,
            'customer_phone' => $request->customer_phone,
            'subtotal' => $subtotal,
            'discount' => 0,
            'tax' => $tax,
            'total' => $subtotal + $tax,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'notes' => $request->notes,
        ]);

        $order->items()->create([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => $quantity,
            'unit_price' => $product->price,
            'total' => $subtotal,
        ]);

        Invoice::create([
            'order_id' => $order->id,
            'user_id' => auth()->id(),
            'invoice_number' => next_number(setting('invoice_prefix', 'INV')),
            'issue_date' => now()->toDateString(),
            'due_date' => now()->addDays(14)->toDateString(),
            'subtotal' => $order->subtotal,
            'discount' => $order->discount,
            'tax' => $order->tax,
            'total' => $order->total,
            'status' => 'unpaid',
        ]);

        ActivityLogger::log('Created', 'Order', "Customer placed order {$order->order_number}");
        return redirect()->route('dashboard.orders.show', $order)->with('success', 'Order placed successfully.');
    }
}
