<div class="space-y-4 text-center py-2">
    {{-- QR Code --}}
    <div class="flex justify-center">
        {!! SimpleSoftwareIO\QrCode\Facades\QrCode::size(200)->style('round')->color(79, 70, 229)->generate($url) !!}
    </div>

    {{-- URL --}}
    <div class="bg-gray-50 rounded-lg px-4 py-3 text-left">
        <p class="text-xs text-gray-500 font-medium mb-1">Link do Portal</p>
        <p class="text-sm text-gray-800 break-all font-mono">{{ $url }}</p>
    </div>

    {{-- Instrução --}}
    <p class="text-xs text-gray-400">
        Envie este link ou QR Code para o(a) cantor(a) acessar o portal de upload de arquivos.
    </p>
</div>
