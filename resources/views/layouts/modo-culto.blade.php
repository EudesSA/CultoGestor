<!DOCTYPE html>
<html lang="pt-BR" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modo Culto — {{ $culto->tipo?->nome ?? 'Culto' }} {{ $culto->data?->format('d/m/Y') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        body { overflow: hidden; }
        .scrollbar-thin::-webkit-scrollbar { width: 4px; }
        .scrollbar-thin::-webkit-scrollbar-track { background: transparent; }
        .scrollbar-thin::-webkit-scrollbar-thumb { background: #374151; border-radius: 2px; }
    </style>
</head>
<body class="h-full bg-gray-950 text-white antialiased">
    {{ $slot }}
    @livewireScripts
</body>
</html>
