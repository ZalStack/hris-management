<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'HRIS Management') }}</title>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        window.location.href = "{{ route('login') }}";
    </script>
</head>
<body>
    <div class="flex justify-center items-center min-h-screen">
        <div class="text-center">
            <h1 class="text-2xl font-bold mb-4">HRIS Management System</h1>
            <p class="text-gray-600">Redirecting to login page...</p>
        </div>
    </div>
</body>
</html>