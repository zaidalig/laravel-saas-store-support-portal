<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>@yield('title','Dashboard')</title>
    <link href="{{ asset_cdn('bootstrap_css', 'vendor/bootstrap/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset_cdn('fontawesome', 'vendor/fontawesome/css/all.min.css') }}">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
<aside class="app-sidebar">
    <div class="fw-bold fs-5 text-white mb-3"><i class="fa-solid fa-user me-1"></i>Customer</div>
    <div class="nav-stack">
        @php($links=[''=>['Dashboard','gauge-high'],'/subscriptions'=>['My Subscriptions','arrows-rotate'],'/orders'=>['My Orders','cart-shopping'],'/invoices'=>['My Invoices','file-invoice-dollar'],'/payments'=>['My Payments','credit-card'],'/tickets'=>['Support Tickets','headset'],'/profile'=>['Profile','user-pen']])
        @foreach($links as $path=>$item)
            <a class="{{ request()->is('dashboard'.($path ?: '')) ? 'active' : '' }}" href="{{ url('/dashboard'.$path) }}"><i class="fa-solid fa-{{ $item[1] }}"></i>{{ $item[0] }}</a>
        @endforeach
    </div>
</aside>
<main class="app-main d-flex flex-column">
    <header class="app-topbar">
        <h5 class="m-0 fw-bold">@yield('page_title','Dashboard')</h5>
        <form action="{{ route('logout') }}" method="POST">@csrf<button class="btn btn-sm btn-outline-secondary"><i class="fa-solid fa-right-from-bracket me-1"></i>Logout</button></form>
    </header>
    <section class="content flex-grow-1">@include('partials.flash')@yield('content')</section>
    <footer class="site-footer border-top bg-white px-4 py-3 small text-muted"><i class="fa-solid fa-layer-group me-1"></i>{{ setting('site_name','SaaS Store') }} Customer Portal</footer>
</main>
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa-solid fa-triangle-exclamation text-danger me-2"></i>Cancel Subscription</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">Are you sure you want to cancel this subscription? This cannot be undone.</div>
            <div class="modal-footer">
                <button class="btn btn-light" data-bs-dismiss="modal">Keep</button>
                <form id="cancelForm" method="POST">@csrf<button class="btn btn-danger"><i class="fa-solid fa-ban me-1"></i>Cancel Subscription</button></form>
            </div>
        </div>
    </div>
</div>
<script src="{{ asset_cdn('bootstrap_js', 'vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
<script>document.getElementById('cancelModal')?.addEventListener('show.bs.modal',e=>document.getElementById('cancelForm').action=e.relatedTarget.dataset.url)</script>
</body>
</html>
