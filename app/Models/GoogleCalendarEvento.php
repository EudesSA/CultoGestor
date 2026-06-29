<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoogleCalendarEvento extends Model
{
    protected $fillable = [
        'culto_id', 'google_event_id', 'calendar_id', 'html_link', 'sincronizado_em',
    ];

    protected function casts(): array
    {
        return ['sincronizado_em' => 'datetime'];
    }

    public function culto(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Culto::class);
    }
}
