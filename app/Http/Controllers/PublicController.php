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
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'integer', 'exists:categories,id'],
            'billing_type' => ['nullable', 'string', 'max:50'],
            'sort' => ['nullable', 'in:newest,name_asc,name_desc,price_asc,price_desc'],
            'per_page' => ['nullable', 'integer', 'in:9,18,36'],
        ]);

        $query = Product::with('category')
            ->where('status', 'active')
            ->when($validated['search'] ?? null, function ($query, $search) {
                $query->where(function ($nested) use ($search) {
                    $nested->where('name', 'like', "%{$search}%")
                        ->orWhere('short_description', 'like', "%{$search}%");
                });
            })
            ->when($validated['category'] ?? null, fn ($query, $category) => $query->where('category_id', $category))
            ->when($validated['billing_type'] ?? null, fn ($query, $type) => $query->where('billing_type', $type));

        match ($validated['sort'] ?? 'newest') {
            'name_asc' => $query->orderBy('name'),
            'name_desc' => $query->orderByDesc('name'),
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            default => $query->latest(),
        };

        $products = $query->paginate($validated['per_page'] ?? 9)->withQueryString();
        $categories = \App\Models\Category::where('status', 'active')->orderBy('name')->get();
        $billingTypes = Product::where('status', 'active')->whereNotNull('billing_type')
            ->distinct()->orderBy('billing_type')->pluck('billing_type');

        return view('public.products', compact('products', 'categories', 'billingTypes'));
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