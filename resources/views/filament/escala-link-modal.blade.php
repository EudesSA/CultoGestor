<div class="space-y-4 py-2">
    <p class="text-sm text-gray-600">
        Envie este link para <strong>{{ $nome }}</strong> ({{ $funcao }}) confirmar ou recusar a presença:
    </p>

    <div class="flex items-center gap-2">
        <input id="escala-url" type="text" readonly value="{{ $url }}"
            class="flex-1 rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-700 focus:outline-none select-all">
        <button onclick="copiarLink()"
            class="rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 font-medium transition-colors whitespace-nowrap">
            Copiar
        </button>
    </div>

    <p id="copiado-msg" class="text-xs text-green-600 font-medium hidden">Link copiado!</p>

    <a href="{{ $url }}" target="_blank"
        class="inline-flex items-center gap-1.5 text-sm text-blue-600 hover:text-blue-800 underline">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/>
        </svg>
        Abrir página de confirmação
    </a>
</div>

<script>
function copiarLink() {
    const input = document.getElementById('escala-url');
    input.select();
    navigator.clipboard.writeText(input.value).then(() => {
        document.getElementById('copiado-msg').classList.remove('hidden');
        setTimeout(() => document.getElementById('copiado-msg').classList.add('hidden'), 2500);
    });
}
</script>
