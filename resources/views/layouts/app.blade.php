<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>e-Retribusi | Puskesmas</title>
    <link rel="icon" href="{{ asset('img/budgeting.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white text-gray-800 antialiased">

    <main>
        @yield('content')
    </main>

</body>
</html>