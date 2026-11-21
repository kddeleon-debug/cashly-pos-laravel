<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') · {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
<div class="shell">
    <aside class="sidebar" id="sidebar">
        <a class="brand" href="{{ route('dashboard') }}" style="text-decoration:none">
            <span class="brand-mark"><x-icon name="store"/></span> <span class="t">{{ config('app.name') }}</span>
        </a>

        <nav class="nav">
            <div class="nav-label">Overview</div>
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}"><x-icon name="dashboard"/> <span class="t">Dashboard</span></a>

            <div class="nav-label">Sales</div>
            <a href="{{ route('sales.create') }}" class="{{ request()->routeIs('sales.create') ? 'active' : '' }}"><x-icon name="cart"/> <span class="t">Record Sale</span></a>
            <a href="{{ route('sales.index') }}" class="{{ request()->routeIs('sales.index') ? 'active' : '' }}"><x-icon name="receipt"/> <span class="t">Sales History</span></a>

            <div class="nav-label">Manage</div>
            <a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.*') ? 'active' : '' }}"><x-icon name="box"/> <span class="t">Products</span></a>
            <a href="{{ route('customers.index') }}" class="{{ request()->routeIs('customers.*') ? 'active' : '' }}"><x-icon name="users"/> <span class="t">Customers</span></a>
            <a href="{{ route('staff.index') }}" class="{{ request()->routeIs('staff.*') ? 'active' : '' }}"><x-icon name="badge"/> <span class="t">Staff</span></a>
        </nav>

    </aside>

    <div class="main">
        <header class="topbar">
            <button class="menu-btn" id="menuBtn" aria-label="Open menu"><x-icon name="menu"/></button>
            <div class="spacer"></div>
            <a class="me" href="{{ route('profile.edit') }}" title="My profile" style="color:inherit;text-decoration:none">
                @include('partials.avatar', ['user' => auth()->user(), 'size' => 'md'])
                <div><strong>{{ auth()->user()->full_name }}</strong><small>&#64;{{ auth()->user()->username }}</small></div>
            </a>
            <form method="POST" action="{{ route('logout') }}" style="margin:0">
                @csrf
                <button class="btn ghost sm" type="submit"><x-icon name="logout"/> Sign out</button>
            </form>
        </header>

        <main class="content">
            @include('partials.alerts')
            @yield('content')
        </main>
    </div>
</div>
<script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
