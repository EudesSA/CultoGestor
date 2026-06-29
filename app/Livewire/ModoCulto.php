<?php

namespace App\Livewire;

use App\Events\ItemCultoAtivado;
use App\Models\Anuncio;
use App\Models\Culto;
use App\Models\CultoLiturgia;
use App\Models\Hino;
use App\Models\Informativo;
use App\Models\Musica;
use App\Models\ProvaiVede;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class ModoCulto extends Component
{
    public int $cultoId;
    public int $liturgiaAtivaId = 0;

    /** Anúncio exibido no overlay "Exibir Agora" (0 = nenhum). */
    public int $anuncioAtivoId = 0;

    /** Busca rápida no Banco de Hinos (por número). */
    public string $buscaHino = '';

    public function mount(int $culto): void
    {
        $c = Culto::with(['liturgias' => fn ($q) => $q->orderBy('ordem')])->findOrFail($culto);

        $this->cultoId = $c->id;
        $this->liturgiaAtivaId = $c->liturgias->first()?->id ?? 0;
    }

    // --- Navegação ---

    public function ativarItem(int $liturgiaId): void
    {
        $liturgia = $this->liturgias->firstWhere('id', $liturgiaId);
        if (! $liturgia) return;

        $this->liturgiaAtivaId = $liturgiaId;

        broadcast(new ItemCultoAtivado(
            cultoId: $this->cultoId,
            liturgiaId: $liturgiaId,
            ordem: $liturgia->ordem,
        ));
    }

    public function avancar(): void
    {
        $liturgias = $this->liturgias;
        $idx       = $liturgias->search(fn ($l) => $l->id === $this->liturgiaAtivaId);

        if ($idx !== false && isset($liturgias[$idx + 1])) {
            $this->ativarItem($liturgias[$idx + 1]->id);
        }
    }

    public function voltar(): void
    {
        $liturgias = $this->liturgias;
        $idx       = $liturgias->search(fn ($l) => $l->id === $this->liturgiaAtivaId);

        if ($idx !== false && $idx > 0) {
            $this->ativarItem($liturgias[$idx - 1]->id);
        }
    }

    public function marcarConcluido(): void
    {
        if (! $this->liturgiaAtivaId) return;

        CultoLiturgia::where('id', $this->liturgiaAtivaId)->update([
            'concluido'    => true,
            'concluido_em' => now(),
        ]);

        // Limpa cache do computed e avança
        unset($this->liturgias);
        $this->avancar();
    }

    // --- "Exibir Agora": anúncio fullscreen a qualquer momento (M5) ---

    public function exibirAnuncio(int $anuncioId): void
    {
        $this->anuncioAtivoId = $anuncioId;
    }

    public function fecharAnuncio(): void
    {
        $this->anuncioAtivoId = 0;
    }

    // --- Banco de Hinos: busca por número + tom mais tocado (Fase 9) ---

    #[Computed]
    public function hinoEncontrado(): ?Hino
    {
        $num = trim($this->buscaHino);
        if ($num === '' || ! ctype_digit($num)) {
            return null;
        }

        return Hino::where('numero', (int) $num)->first();
    }

    public function registrarHinoExecucao(): void
    {
        $hino = $this->hinoEncontrado;
        if ($hino) {
            $hino->registrarExecucao(null, $this->cultoId);
            unset($this->hinoEncontrado);
        }
    }

    // --- Echo: sincroniza outras telas ---

    #[On('echo:culto.{cultoId},ItemCultoAtivado')]
    public function onItemAtivado(array $payload): void
    {
        $novoId = $payload['liturgia_id'] ?? 0;
        if ($novoId !== $this->liturgiaAtivaId) {
            $this->liturgiaAtivaId = $novoId;
        }
    }

    // --- Computed ---

    #[Computed]
    public function culto(): Culto
    {
        return Culto::with('tipo')->findOrFail($this->cultoId);
    }

    #[Computed]
    public function liturgias(): Collection
    {
        return CultoLiturgia::where('culto_id', $this->cultoId)
            ->with('referencia')
            ->orderBy('ordem')
            ->get()
            ->each(function ($liturgia) {
                if ($liturgia->referencia instanceof Musica) {
                    $liturgia->referencia->load('cantor');
                }
            });
    }

    #[Computed]
    public function liturgiaAtiva(): ?CultoLiturgia
    {
        return $this->liturgias->firstWhere('id', $this->liturgiaAtivaId);
    }

    #[Computed]
    public function anunciosDisponiveis(): Collection
    {
        return Anuncio::query()
            ->ativos()
            ->orderByDesc('sempre_disponivel')
            ->orderBy('ordem')
            ->get();
    }

    #[Computed]
    public function anuncioAtivo(): ?Anuncio
    {
        return $this->anuncioAtivoId
            ? Anuncio::find($this->anuncioAtivoId)
            : null;
    }

    #[Computed]
    public function youtubeId(): ?string
    {
        $ref = $this->liturgiaAtiva?->referencia;
        if (! $ref) return null;

        $link = match (true) {
            $ref instanceof Musica      => $ref->link_youtube,
            $ref instanceof ProvaiVede  => $ref->link_youtube,
            $ref instanceof Informativo => $ref->link_youtube,
            default                     => null,
        };

        if (! $link) return null;

        preg_match('/(?:v=|youtu\.be\/)([A-Za-z0-9_-]{11})/', $link, $m);
        return $m[1] ?? null;
    }

    // --- Render ---

    public function render()
    {
        return view('livewire.modo-culto')
            ->layout('layouts.modo-culto', ['culto' => $this->culto]);
    }
}
