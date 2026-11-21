<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Sign in') · {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
<div class="auth">
    <section class="auth-art">
        <div class="brand"><span class="brand-mark"><x-icon name="store"/></span> {{ config('app.name') }}</div>
        <div>
            <h2>Sell faster. Track every peso.</h2>
            <p>A complete point-of-sale for your store: products, customers, staff and every transaction in one place.</p>
            <ul>
                <li><x-icon name="zap"/> Record a sale in seconds, stock updates automatically</li>
                <li><x-icon name="shield"/> Overselling is blocked — stock can never go negative</li>
                <li><x-icon name="receipt"/> Full sales history with staff and customer details</li>
            </ul>
        </div>
    </section>

    <section class="auth-form">
        <div class="auth-box">
            @yield('content')
        </div>
    </section>
</div>
</body>
</html>
