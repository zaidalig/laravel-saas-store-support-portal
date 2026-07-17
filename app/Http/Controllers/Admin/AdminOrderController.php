<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Support\ActivityLogger;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    public function index(Request $request)
    {
        $perPage = in_array((int) $request->input('per_page', 10), [10, 25, 50, 100], true)
            ? (int) $request->input('per_page', 10)
            : 10;
        $sort = in_array($request->input('sort'), ['created_at', 'order_number', 'customer_name', 'total', 'status'], true)
            ? $request->input('sort')
            : 'created_at';
        $direction = $request->input('direction') === 'asc' ? 'asc' : 'desc';

        $orders = Order::with('user')->when($request->search, fn($q,$s)=>$q->where(function ($nested) use ($s) {
                $nested->where('order_number','like',"%{$s}%")
                    ->orWhere('customer_email','like',"%{$s}%")
                    ->orWhere('customer_name','like',"%{$s}%");
            }))
            ->when($request->status, fn($q,$s)=>$q->where('status',$s))
            ->when($request->payment_status, fn($q,$s)=>$q->where('payment_status',$s))
            ->orderBy($sort, $direction)->paginate($perPage)->withQueryString();
        return view('admin.orders.index', compact('orders'));
    }

    public function create() { return view('admin.orders.form', ['order'=>null, 'products'=>Product::where('status','active')->get()]); }

    public function store(Request $request)
    {
        $data = $request->validate(['customer_name'=>'required','customer_email'=>'required|email','customer_phone'=>'nullable','product_id'=>'required|exists:products,id','quantity'=>'required|integer|min:1','status'=>'required','payment_status'=>'required','notes'=>'nullable']);
        $product = Product::findOrFail($data['product_id']);
        $subtotal = $product->price * $data['quantity'];
        $tax = round($subtotal * ((float) setting('tax_percentage', 8) / 100), 2);
        $order = Order::create($data + ['order_number'=>next_number(setting('order_prefix','ORD')), 'subtotal'=>$subtotal, 'discount'=>0, 'tax'=>$tax, 'total'=>$subtotal+$tax]);
        $order->items()->create(['product_id'=>$product->id,'product_name'=>$product->name,'quantity'=>$data['quantity'],'unit_price'=>$product->price,'total'=>$subtotal]);
        ActivityLogger::log('Created','Order',"Created order {$order->order_number}");
        return redirect()->route('admin.orders.show',$order)->with('success','Order created.');
    }

    public function show(Order $order) { return view('admin.orders.show', ['order'=>$order->load('items','payments','invoice','user')]); }

    public function edit(Order $order) { return view('admin.orders.form', ['order'=>$order, 'products'=>Product::where('status','active')->get()]); }

    public function update(Request $request, Order $order)
    {
        $data = $request->validate(['customer_name'=>'required','customer_email'=>'required|email','customer_phone'=>'nullable','status'=>'required|in:pending,confirmed,processing,completed,cancelled','payment_status'=>'required|in:unpaid,partial,paid,refunded','notes'=>'nullable']);
        $order->update($data);
        ActivityLogger::log('Updated','Order',"Updated order {$order->order_number}");
        return redirect()->route('admin.orders.show',$order)->with('success','Order updated.');
    }

    public function destroy(Order $order) { $order->delete(); return redirect()->route('admin.orders.index')->with('success','Order deleted.'); }
}