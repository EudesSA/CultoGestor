<?php

namespace App\Support;

use App\Models\Culto;
use Illuminate\Support\Carbon;

/**
 * Gera links "Adicionar ao Google Agenda" (calendar render) — cada membro
 * abre o link e salva o evento no SEU PRÓPRIO Google Calendar, sem precisar
 * de service account nem credenciais (Especificação Mestra, Módulo 3b).
 */
class GoogleCalendar
{
    public static function linkCulto(Culto $culto, ?string $detalhes = null): string
    {
        $titulo = ($culto->tipo?->nome ?? 'Culto') . ($culto->tema ? ' — ' . $culto->tema : '');

        $data   = $culto->data instanceof Carbon ? $culto->data->copy() : Carbon::parse($culto->data);
        $inicio = $data->copy()->setTimeFromTimeString($culto->hora_inicio ?: '09:00');
        $fim    = $culto->hora_fim
            ? $data->copy()->setTimeFromTimeString($culto->hora_fim)
            : $inicio->copy()->addHour();

        // Formato local (sem Z) → o horário aparece como está, na agenda do membro.
        $dates = $inicio->format('Ymd\THis') . '/' . $fim->format('Ymd\THis');

        $corpo = trim(($detalhes ? $detalhes . "\n" : '') . 'Via CultoGestor');

        return 'https://calendar.google.com/calendar/render?action=TEMPLATE'
            . '&text=' . rawurlencode($titulo)
            . '&dates=' . $dates
            . '&details=' . rawurlencode($corpo)
            . '&location=' . rawurlencode($culto->local ?: '');
    }

    /** Variante para um ensaio (data/hora + local). */
    public static function linkEnsaio(\App\Models\Ensaio $ensaio): string
    {
        $titulo = 'Ensaio' . ($ensaio->culto?->tipo?->nome ? ' — ' . $ensaio->culto->tipo->nome : '');

        $inicio = $ensaio->data_hora instanceof Carbon ? $ensaio->data_hora->copy() : Carbon::parse($ensaio->data_hora);
        $fim    = $inicio->copy()->addHours(2);

        $dates = $inicio->format('Ymd\THis') . '/' . $fim->format('Ymd\THis');

        return 'https://calendar.google.com/calendar/render?action=TEMPLATE'
            . '&text=' . rawurlencode($titulo)
            . '&dates=' . $dates
            . '&details=' . rawurlencode('Ensaio via CultoGestor')
            . '&location=' . rawurlencode($ensaio->local ?: '');
    }
}
