<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>@yield('title','Admin')</title><link href="{{ asset_cdn('bootstrap_css', 'vendor/bootstrap/bootstrap.min.css') }}" rel="stylesheet"><link rel="stylesheet" href="{{ asset_cdn('fontawesome', 'vendor/fontawesome/css/all.min.css') }}"><link href="{{ asset('css/app.css') }}" rel="stylesheet"></head><body><aside class="app-sidebar no-print"><div class="fw-bold fs-5 text-white mb-3"><i class="fa-solid fa-shield-halved me-1"></i>Admin Panel</div><div class="nav-stack">@php
$links = [
    'dashboard' => 'Dashboard',
    'support-tickets' => 'Support Tickets',
    'orders' => 'Orders',
    'payments' => 'Payments',
    'invoices' => 'Invoices',
    'subscriptions' => 'Subscriptions',
    'products' => 'Products',
    'categories' => 'Categories',
    'pricing-plans' => 'Pricing Plans',
    'contact-messages' => 'Contact Messages',
];
if (auth()->user()?->isAdmin()) {
    $links = [
        'dashboard' => 'Dashboard',
        'users' => 'Users',
        'teams' => 'Teams',
        'team-members' => 'Team Members',
        'categories' => 'Categories',
        'products' => 'Products',
        'pricing-plans' => 'Pricing Plans',
        'orders' => 'Orders',
        'payments' => 'Payments',
        'invoices' => 'Invoices',
        'subscriptions' => 'Subscriptions',
        'support-tickets' => 'Support Tickets',
        'contact-messages' => 'Contact Messages',
        'activity-logs' => 'Activity Logs',
        'settings' => 'Settings',
    ];
}
@endphp
@php($icons = ['dashboard'=>'gauge-high','users'=>'users','teams'=>'people-group','team-members'=>'user-group','categories'=>'tags','products'=>'box-open','pricing-plans'=>'layer-group','orders'=>'cart-shopping','payments'=>'credit-card','invoices'=>'file-invoice-dollar','subscriptions'=>'arrows-rotate','support-tickets'=>'headset','contact-messages'=>'envelope','activity-logs'=>'clock-rotate-left','settings'=>'gear'])
@foreach($links as $key=>$label)<a class="{{ request()->routeIs('admin.'.$key.'*') || ($key==='dashboard' && request()->routeIs('admin.dashboard')) ? 'active' : '' }}" href="{{ $key==='dashboard' ? route('admin.dashboard') : route('admin.'.$key.'.index') }}"><i class="fa-solid fa-{{ $icons[$key] ?? 'circle' }}"></i>{{ $label }}</a>@endforeach</div></aside><main class="app-main d-flex flex-column"><header class="app-topbar no-print"><h5 class="m-0 fw-bold">@yield('page_title','Admin') <span class="badge bg-light text-dark border ms-2">{{ ucfirst(auth()->user()->role) }}</span></h5><form action="{{ route('logout') }}" method="POST">@csrf<button class="btn btn-sm btn-outline-secondary"><i class="fa-solid fa-right-from-bracket me-1"></i>Logout</button></form></header><section class="content flex-grow-1">@include('partials.flash')@yield('content')</section><footer class="site-footer border-top bg-white px-4 py-3 small text-muted"><i class="fa-solid fa-layer-group me-1"></i>{{ setting('site_name','SaaS Store') }} Admin</footer></main><div class="modal fade" id="deleteModal"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title"><i class="fa-solid fa-triangle-exclamation text-danger me-2"></i>Confirm Delete</h5><button class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body">This action cannot be undone.</div><div class="modal-footer"><button class="btn btn-light" data-bs-dismiss="modal">Cancel</button><form id="deleteForm" method="POST">@csrf @method('DELETE')<button class="btn btn-danger"><i class="fa-solid fa-trash me-1"></i>Delete</button></form></div></div></div></div><script src="{{ asset_cdn('bootstrap_js', 'vendor/bootstrap/bootstrap.bundle.min.js') }}"></script><script>document.getElementById('deleteModal')?.addEventListener('show.bs.modal',e=>document.getElementById('deleteForm').action=e.relatedTarget.dataset.url)</script></body></html>
