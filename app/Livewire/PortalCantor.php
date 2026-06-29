<?php

namespace App\Livewire;

use App\Models\Cantor;
use App\Models\Culto;
use App\Models\Escala;
use App\Models\Musica;
use App\Models\MusicaArquivo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;

class PortalCantor extends Component
{
    use WithFileUploads;

    // --- Estado geral ---
    public Cantor $cantor;
    public string $aba = 'perfil';
    public ?string $flash = null;
    public string $flashTipo = 'sucesso';

    // --- Aba: Perfil ---
    public string $pNome = '';
    public string $pEmail = '';
    public string $pTelefone = '';
    public string $pVoz = '';

    // --- Aba: Músicas — formulário nova música ---
    public bool $showFormMusica = false;
    public ?int $mCultoId = null;
    public string $mNome = '';
    public string $mArtista = '';
    public string $mTom = '';
    public string $mLinkYoutube = '';
    public string $mObservacoes = '';
    // Arquivo opcional anexado já na criação da música
    public $mArquivo = null;
    public string $mArquivoTipo = 'mp3';

    // --- Upload de arquivo por música ---
    public ?int $uploadMusicaId = null;
    public string $uploadTipo = 'mp3';
    public $uploadArquivo = null;

    // ---------------------------------------------------

    public function mount(string $token): void
    {
        $this->cantor = Cantor::where('token_portal', $token)->firstOrFail();
        $this->preencherPerfil();
    }

    private function preencherPerfil(): void
    {
        $this->pNome      = $this->cantor->nome ?? '';
        $this->pEmail     = $this->cantor->email ?? '';
        $this->pTelefone  = $this->cantor->telefone ?? '';
        $this->pVoz       = $this->cantor->voz ?? '';
    }

    // --- Aba: Perfil ---

    public function salvarPerfil(): void
    {
        $this->validate([
            'pNome'     => 'required|string|max:150',
            'pEmail'    => 'nullable|email|max:150',
            'pTelefone' => 'nullable|string|max:30',
            'pVoz'      => 'nullable|in:soprano,contralto,tenor,baritono,baixo,outro',
        ]);

        $this->cantor->update([
            'nome'     => $this->pNome,
            'email'    => $this->pEmail ?: null,
            'telefone' => $this->pTelefone ?: null,
            'voz'      => $this->pVoz ?: null,
        ]);

        $this->flash    = 'Perfil atualizado com sucesso!';
        $this->flashTipo = 'sucesso';
    }

    // --- Aba: Escalas ---

    public function confirmarEscala(int $id): void
    {
        $escala = $this->buscarEscalaDoCantor($id);
        if (! $escala) return;

        $escala->update(['status' => 'confirmado', 'confirmado_em' => now()]);
        $this->flash    = 'Presença confirmada!';
        $this->flashTipo = 'sucesso';
    }

    public function recusarEscala(int $id): void
    {
        $escala = $this->buscarEscalaDoCantor($id);
        if (! $escala) return;

        $escala->update(['status' => 'recusado', 'confirmado_em' => null]);
        $this->flash    = 'Presença marcada como recusada.';
        $this->flashTipo = 'info';
    }

    private function buscarEscalaDoCantor(int $id): ?Escala
    {
        if (! $this->cantor->user_id) return null;
        return Escala::where('id', $id)->where('user_id', $this->cantor->user_id)->first();
    }

    // --- Aba: Músicas ---

    public function abrirFormMusica(): void
    {
        $this->showFormMusica = true;
        $this->resetMusicaForm();
    }

    public function fecharFormMusica(): void
    {
        $this->showFormMusica = false;
        $this->resetMusicaForm();
    }

    private function resetMusicaForm(): void
    {
        $this->mCultoId      = null;
        $this->mNome         = '';
        $this->mArtista      = '';
        $this->mTom          = '';
        $this->mLinkYoutube  = '';
        $this->mObservacoes  = '';
        $this->mArquivo      = null;
        $this->mArquivoTipo  = 'mp3';
    }

    public function salvarMusica(): void
    {
        $this->validate([
            'mCultoId'     => 'nullable|exists:cultos,id',
            'mNome'        => 'required|string|max:200',
            'mArtista'     => 'nullable|string|max:200',
            'mTom'         => 'nullable|string|max:10',
            'mLinkYoutube' => 'nullable|url|max:500',
            'mObservacoes' => 'nullable|string|max:1000',
            'mArquivoTipo' => 'required|in:mp3,playback,letra,cifra,partitura,outro',
            'mArquivo'     => 'nullable|file|max:102400|mimes:mp3,wav,ogg,pdf,doc,docx',
        ]);

        $musica = Musica::create([
            'cantor_id'    => $this->cantor->id,
            'culto_id'     => $this->mCultoId ?: null,
            'nome'         => $this->mNome,
            'artista'      => $this->mArtista ?: null,
            'tom'          => $this->mTom ?: null,
            'link_youtube' => $this->mLinkYoutube ?: null,
            'status'       => 'pendente',
        ]);

        // Anexa o arquivo enviado junto, se houver.
        if ($this->mArquivo) {
            $this->salvarArquivoMusica($musica, $this->mArquivo, $this->mArquivoTipo);
        }

        $this->showFormMusica = false;
        $this->resetMusicaForm();
        $this->flash    = 'Música adicionada! O diretor será notificado.';
        $this->flashTipo = 'sucesso';
    }

    public function excluirMusica(int $id): void
    {
        $musica = Musica::where('id', $id)->where('cantor_id', $this->cantor->id)->first();
        if (! $musica) return;
        if ($musica->status === 'aprovado') {
            $this->flash    = 'Músicas aprovadas não podem ser removidas.';
            $this->flashTipo = 'erro';
            return;
        }
        $musica->arquivos->each(fn ($a) => Storage::disk('cultos')->delete($a->path_local ?? ''));
        $musica->delete();
        $this->flash    = 'Música removida.';
        $this->flashTipo = 'info';
    }

    // --- Upload de arquivos ---

    public function abrirUpload(int $musicaId): void
    {
        $this->uploadMusicaId = $musicaId;
        $this->uploadTipo     = 'mp3';
        $this->uploadArquivo  = null;
    }

    public function fecharUpload(): void
    {
        $this->uploadMusicaId = null;
        $this->uploadArquivo  = null;
    }

    public function enviarArquivo(): void
    {
        $this->validate([
            'uploadTipo'    => 'required|in:mp3,playback,letra,cifra,partitura,outro',
            'uploadArquivo' => 'required|file|max:102400|mimes:mp3,wav,ogg,pdf,doc,docx',
        ]);

        $musica = Musica::where('id', $this->uploadMusicaId)
            ->where('cantor_id', $this->cantor->id)
            ->firstOrFail();

        $this->salvarArquivoMusica($musica, $this->uploadArquivo, $this->uploadTipo);

        $this->uploadArquivo  = null;
        $this->uploadMusicaId = null;
        $this->flash    = 'Arquivo enviado com sucesso!';
        $this->flashTipo = 'sucesso';
    }

    /**
     * Grava o arquivo enviado na árvore do Drive e registra em musica_arquivos.
     * Marca a música como "enviado" se ainda estiver pendente.
     */
    private function salvarArquivoMusica(Musica $musica, $file, string $tipo): void
    {
        $diretorio   = $musica->diretorioArquivo($tipo);
        $ext         = $file->getClientOriginalExtension() ?: ($file->guessExtension() ?: 'dat');
        $slug        = Str::slug($musica->nome ?: 'musica');
        $nomeArquivo = "{$slug}-{$tipo}.{$ext}";
        $path        = $file->storeAs($diretorio, $nomeArquivo, 'cultos');

        MusicaArquivo::create([
            'musica_id'     => $musica->id,
            'tipo'          => $tipo,
            'nome_original' => $file->getClientOriginalName(),
            'path_local'    => $path,
            'tamanho_bytes' => $file->getSize(),
            'mime_type'     => $file->getMimeType() ?? 'application/octet-stream',
        ]);

        if ($musica->status === 'pendente') {
            $musica->update(['status' => 'enviado', 'enviado_em' => now()]);
        }
    }

    public function excluirArquivo(int $id): void
    {
        $arquivo = MusicaArquivo::whereHas('musica', fn ($q) => $q->where('cantor_id', $this->cantor->id))
            ->findOrFail($id);

        if ($arquivo->path_local) {
            Storage::disk('cultos')->delete($arquivo->path_local);
        }
        $arquivo->delete();
        $this->flash    = 'Arquivo removido.';
        $this->flashTipo = 'info';
    }

    // --- Helpers ---

    // --- Computed ---

    #[Computed]
    public function escalas()
    {
        if (! $this->cantor->user_id) return collect();
        return Escala::with(['culto.tipo', 'funcao'])
            ->where('user_id', $this->cantor->user_id)
            ->whereHas('culto', fn ($q) => $q->where('data', '>=', now()->subDays(7)))
            ->orderByDesc(
                \App\Models\Culto::select('data')
                    ->whereColumn('cultos.id', 'escalas.culto_id')
            )
            ->get();
    }

    #[Computed]
    public function musicas()
    {
        return Musica::with(['culto.tipo', 'arquivos'])
            ->where('cantor_id', $this->cantor->id)
            ->orderByDesc('created_at')
            ->get();
    }

    #[Computed]
    public function cultosDisponiveis()
    {
        return Culto::with('tipo')
            ->where('data', '>=', now()->toDateString())
            ->orderBy('data')
            ->limit(20)
            ->get();
    }

    public function render()
    {
        return view('livewire.portal-cantor', [
            'cantorNome' => $this->cantor->nome,
            'cantorVoz'  => $this->cantor->voz
                ? ucfirst($this->cantor->voz)
                : null,
        ])->layout('layouts.cantor', [
            'title'      => 'Portal do Cantor',
            'cantorNome' => $this->cantor->nome,
            'cantorVoz'  => $this->cantor->voz ? ucfirst($this->cantor->voz) : null,
            'pwaToken'   => $this->cantor->token_portal,
        ]);
    }
}
