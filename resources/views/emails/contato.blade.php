{{--
    CultoGestor — Corpo do e-mail de contato recebido pelo site.

    @author Eudes S. Aguiar — ProezaTech — www.proezatech.com
    @link   https://www.proezatech.com
--}}
<x-mail::message>
# Novo contato pelo site

Você recebeu uma nova mensagem pelo formulário de contato do CultoGestor.

**Nome:** {{ $nomeRemetente }}
**E-mail:** {{ $emailRemetente }}

<x-mail::panel>
{{ $mensagem }}
</x-mail::panel>

Para responder, basta usar o botão "Responder" do seu e-mail — ele já vai para o remetente.

<x-mail::subcopy>
Mensagem enviada automaticamente pelo site do CultoGestor.
</x-mail::subcopy>
</x-mail::message>
