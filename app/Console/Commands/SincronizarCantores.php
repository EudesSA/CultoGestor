<?php

namespace App\Console\Commands;

use App\Models\Cantor;
use App\Models\User;
use Illuminate\Console\Command;

class SincronizarCantores extends Command
{
    protected $signature   = 'cantores:sincronizar';
    protected $description = 'Cria registros em cantores para usuários com papel Cantor que ainda não os têm.';

    public function handle(): int
    {
        $usuarios = User::role('Cantor')->get();

        if ($usuarios->isEmpty()) {
            $this->warn('Nenhum usuário com papel "Cantor" encontrado.');
            return self::SUCCESS;
        }

        $criados    = 0;
        $existentes = 0;

        foreach ($usuarios as $user) {
            $existia = Cantor::where('user_id', $user->id)->exists();

            Cantor::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nome'  => $user->name,
                    'email' => $user->email,
                    'ativo' => true,
                ]
            );

            $existia ? $existentes++ : $criados++;
        }

        $this->info("Concluído: {$criados} criado(s), {$existentes} já existia(m).");
        return self::SUCCESS;
    }
}
