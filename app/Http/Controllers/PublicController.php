<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Models\ContactMessage;
use App\Models\Order;
use App\Models\PricingPlan;
use App\Models\Product;
use App\Models\SupportTicket;
use App\Support\ActivityLogger;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function home()
    {
        return view('public.home', [
            'products' => Product::where('status', 'active')->latest()->take(6)->get(),
            'plans' => PricingPlan::where('status', 'active')->orderBy('display_order')->get(),
        ]);
    }

    public function products(Request $request)
    {
        $products = Product::with('category')->where('status', 'active')
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->latest()->paginate(9)->withQueryString();
        return view('public.products', compact('products'));
    }

    public function productDetail(string $slug)
    {
        $product = Product::with('category')->where('slug', $slug)->where('status', 'active')->firstOrFail();
        return view('public.product-detail', compact('product'));
    }

    public function pricing()
    {
        $plans = PricingPlan::where('status', 'active')->orderBy('display_order')->get();
        return view('public.pricing', compact('plans'));
    }

    public function contact() { return view('public.contact'); }
    public function support() { return view('public.support'); }
    public function trackOrder() { return view('public.track-order'); }

    public function submitContact(ContactRequest $request)
    {
        ContactMessage::create($request->validated() + ['status' => 'new']);
        ActivityLogger::log('Created', 'ContactMessage', 'New contact form submission.');
        return back()->with('success', 'Thanks, your message has been received.');
    }

    public function trackOrderResult(Request $request)
    {
        $request->validate(['order_number' => 'required|string', 'customer_email' => 'required|email']);
        $order = Order::with('items')->where('order_number', $request->order_number)->where('customer_email', $request->customer_email)->first();
        return view('public.track-order', compact('order'));
    }
}