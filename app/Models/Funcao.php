<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Funcao extends Model
{
    protected $table = 'funcoes';

    protected $fillable = ['nome', 'role_vinculado', 'icone', 'cor', 'ativo', 'ordem'];

    protected function casts(): array
    {
        return ['ativo' => 'boolean'];
    }

    public function escalas(): HasMany
    {
        return $this->hasMany(Escala::class);
    }

    /** Usuários que exercem esta função (habilidades). */
    public function usuarios(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'funcao_user');
    }

    /**
     * Opções de membro [id => nome] para o select de escala desta função.
     * Prioridade:
     *   1) usuários marcados com esta função (habilidades);
     *   2) usuários com o papel vinculado (role_vinculado), se houver;
     *   3) todos (fallback, para não travar a escala antes de cadastrar).
     */
    public function opcoesUsuarios(): array
    {
        $porFuncao = $this->usuarios()->orderBy('name')->pluck('name', 'users.id')->toArray();
        if ($porFuncao) {
            return $porFuncao;
        }

        if ($this->role_vinculado) {
            $porRole = User::role($this->role_vinculado)->orderBy('name')->pluck('name', 'id')->toArray();
            if ($porRole) {
                return $porRole;
            }
        }

        return User::orderBy('name')->pluck('name', 'id')->toArray();
    }

    /** Texto de ajuda indicando a fonte do filtro. */
    public function helperUsuarios(): ?string
    {
        $qtd = $this->usuarios()->count();
        if ($qtd > 0) {
            return "Exibindo {$qtd} membro(s) que exerce(m) {$this->nome}.";
        }

        if ($this->role_vinculado && User::role($this->role_vinculado)->exists()) {
            return "Por papel: {$this->role_vinculado}.";
        }

        return "Nenhum membro cadastrado para {$this->nome} — exibindo todos. Defina em Usuários → Funções que exerce.";
    }
}
