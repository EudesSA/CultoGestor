<div class="space-y-4 py-2">
    <div class="flex justify-center">
        {!! SimpleSoftwareIO\QrCode\Facades\QrCode::size(180)->style('round')->color(67, 56, 202)->generate($url) !!}
    </div>

    <div class="bg-indigo-50 rounded-lg px-4 py-3">
        <p class="text-xs text-indigo-500 font-medium mb-1">Link de confirmação do ensaio</p>
        <p class="text-xs text-indigo-900 break-all font-mono leading-relaxed">{{ $url }}</p>
    </div>

    <p class="text-xs text-gray-400 text-center">
        O membro abre este link para confirmar ou recusar presença no ensaio.
    </p>
</div>
