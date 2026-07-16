<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\ContactMessage;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Product;
use App\Models\SupportTicket;
use App\Models\User;

class AdminDashboardController extends Controller
{
    public function __invoke()
    {
        return view('admin.dashboard', [
            'totalUsers' => User::count(),
            'totalCustomers' => User::where('role', 'customer')->count(),
            'totalProducts' => Product::count(),
            'totalOrders' => Order::count(),
            'pendingOrders' => Order::where('status', 'pending')->count(),
            'paidInvoices' => Invoice::where('status', 'paid')->count(),
            'openTickets' => SupportTicket::whereIn('status', ['open', 'in_progress'])->count(),
            'contactMessages' => ContactMessage::where('status', 'new')->count(),
            'logs' => ActivityLog::with('user')->latest('created_at')->take(10)->get(),
        ]);
    }
}