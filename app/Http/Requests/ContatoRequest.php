<?php

/**
 * CultoGestor — Validação do formulário de contato do site público.
 *
 * @author  Eudes S. Aguiar — ProezaTech — www.proezatech.com
 * @link     https://www.proezatech.com
 */

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContatoRequest extends FormRequest
{
    /**
     * Rota pública — qualquer visitante pode enviar.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'nome'     => ['required', 'string', 'min:2', 'max:120'],
            'email'    => ['required', 'email', 'max:180'],
            'mensagem' => ['required', 'string', 'min:10', 'max:3000'],
            // Honeypot: precisa existir e estar vazio. Bots costumam preencher.
            'website'  => ['nullable', 'prohibited'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'nome'     => 'nome',
            'email'    => 'e-mail',
            'mensagem' => 'mensagem',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'required'         => 'O campo :attribute é obrigatório.',
            'email.email'      => 'Informe um e-mail válido.',
            'mensagem.min'     => 'A mensagem precisa ter ao menos :min caracteres.',
            'website.prohibited' => 'Falha na verificação anti-spam.',
        ];
    }
}
