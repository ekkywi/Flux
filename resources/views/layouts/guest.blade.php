<!DOCTYPE html>
<html class="h-full bg-white" lang="id">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>@yield("title") - Flux</title>
    <link href="https://rsms.me/" rel="preconnect">
    <link href="https://rsms.me/inter/inter.css" rel="stylesheet">
    @vite(["resources/css/app.css", "resources/js/app.js"])
    @stack("styles")
    <style>
        :root {
            font-family: 'Inter var', sans-serif;
        }
    </style>
</head>

<body class="h-full antialiased selection:bg-indigo-500/30">
    @yield("content")
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @stack("scripts")
</body>

</html>
