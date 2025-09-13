<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('frontend/assets/img/favicon.png') }}">
    <!-- Admin-specific styles -->
    <link rel="stylesheet" href="{{ asset('backend/assets/vendor/css/pages/page-auth.css') }}" />
    <link rel="stylesheet" href="{{ asset('backend/assets/vendor/fonts/boxicons.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/assets/vendor/css/core.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/assets/vendor/css/theme-default.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/assets/css/demo.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

</head>

<body class="font-sans antialiased">

<div class="container-xxl container-p-y py-5">
    <div class="misc-wrapper text-center">
        <h2 class="mb-2 mx-2">Too Many Requests 😖</h2>
        <p class="mb-4 mx-2">
            You have sent too many requests in a short period of time. Please wait and try again later.
        </p>

        <!-- Back button using history -->
        <button class="btn btn-primary" onclick="goBack()">
            Go Back
        </button>

        <div class="mt-3">
            <img
                src="{{ asset('backend/assets/img/illustrations/page-misc-error-light.png') }}"
                alt="page-misc-error-light"
                width="500"
                class="img-fluid"
            />
        </div>
    </div>
</div>

<script>
    function goBack() {
        if (window.history.length > 1) {
            window.history.back();
        } else {
            window.location.href = '/';
        }
    }
</script>



    <!-- Admin-specific scripts -->
    <script src="{{ asset('backend/assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('backend/assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('backend/assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('backend/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('backend/assets/vendor/js/menu.js') }}"></script>
    <script src="{{ asset('backend/assets/js/main.js') }}"></script>
</body>

</html>
