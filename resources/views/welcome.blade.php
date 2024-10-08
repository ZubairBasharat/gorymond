<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name='asset_url' content='{{asset("/")}}' /> 
    <title>GoRaymond Coach Portal</title>

    @vite(['resources/scss/app.scss', 'resources/css/app.css'])
</head>

<body>
    <div id="app"></div>

    @vite('resources/js/app.js')
</body>

</html>