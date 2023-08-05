<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title inertia>{{ config('app.name', 'Laravel') }}</title>

    <?php echo app('Tightenco\Ziggy\BladeRouteGenerator')->generate(); ?>
    @vite('resources/js/app.js')
    @inertiaHead
</head>
<body class="">
@inertia
</body>
</html>
