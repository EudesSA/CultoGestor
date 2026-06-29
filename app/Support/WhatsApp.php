<?php

namespace App\Support;

/**
 * Gera links wa.me que abrem o WhatsApp Web ou Desktop com a mensagem
 * pré-preenchida — sem API, sem credenciais. O operador apenas clica em
 * "Enviar". Funciona tanto no navegador quanto no app instalado.
 */
class WhatsApp
{
    /** Normaliza o telefone (só dígitos) e garante o DDI 55 (Brasil) por padrão. */
    public static function numero(?string $telefone): ?string
    {
        if (! $telefone) {
            return null;
        }

        $num = preg_replace('/\D/', '', $telefone);
        if (! $num) {
            return null;
        }

        // Sem DDI e com tamanho de número nacional → assume Brasil (55).
        if (! str_starts_with($num, '55') && strlen($num) <= 11) {
            $num = '55' . $num;
        }

        return $num;
    }

    /**
     * Link de envio. Sem número, abre o WhatsApp para escolher o contato.
     */
    public static function link(?string $telefone, string $mensagem): string
    {
        $num   = static::numero($telefone);
        $texto = rawurlencode($mensagem);

        return $num
            ? "https://wa.me/{$num}?text={$texto}"
            : "https://wa.me/?text={$texto}";
    }
}
