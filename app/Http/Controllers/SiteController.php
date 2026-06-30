<?php

/**
 * CultoGestor — Controller do site público institucional.
 *
 * Serve as páginas públicas (Home, Funcionalidades, Como funciona, Sobre, FAQ,
 * Contato) e o sitemap. Totalmente isolado do painel Filament (/admin): não
 * expõe nenhuma rota, dado ou model do painel administrativo.
 *
 * @author  Eudes S. Aguiar — ProezaTech — www.proezatech.com
 * @link     https://www.proezatech.com
 */

namespace App\Http\Controllers;

use App\Http\Requests\ContatoRequest;
use App\Mail\ContatoRecebido;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class SiteController extends Controller
{
    public function index(): View
    {
        return view('site.home');
    }

    public function funcionalidades(): View
    {
        return view('site.funcionalidades');
    }

    public function comoFunciona(): View
    {
        return view('site.como-funciona');
    }

    public function sobre(): View
    {
        return view('site.sobre');
    }

    public function faq(): View
    {
        return view('site.faq');
    }

    public function contato(): View
    {
        return view('site.contato');
    }

    /**
     * Processa o formulário de contato e dispara o e-mail para a equipe.
     */
    public function enviarContato(ContatoRequest $request): RedirectResponse
    {
        $dados = $request->validated();

        // Destinatário configurável; cai no remetente padrão do app se não definido.
        $destino = config('mail.contato_to') ?: config('mail.from.address');

        Mail::to($destino)->send(new ContatoRecebido(
            nomeRemetente: $dados['nome'],
            emailRemetente: $dados['email'],
            mensagem: $dados['mensagem'],
        ));

        return back()->with('contato_ok', true);
    }

    /**
     * Sitemap simples com as páginas públicas.
     */
    public function sitemap(): Response
    {
        $rotas = [
            'site.home'           => '1.0',
            'site.funcionalidades' => '0.8',
            'site.como-funciona'  => '0.8',
            'site.sobre'          => '0.6',
            'site.faq'            => '0.6',
            'site.contato'        => '0.5',
        ];

        $hoje = now()->toDateString();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

        foreach ($rotas as $nome => $prioridade) {
            $xml .= '  <url>' . PHP_EOL;
            $xml .= '    <loc>' . e(route($nome)) . '</loc>' . PHP_EOL;
            $xml .= '    <lastmod>' . $hoje . '</lastmod>' . PHP_EOL;
            $xml .= '    <changefreq>monthly</changefreq>' . PHP_EOL;
            $xml .= '    <priority>' . $prioridade . '</priority>' . PHP_EOL;
            $xml .= '  </url>' . PHP_EOL;
        }

        $xml .= '</urlset>';

        return response($xml, 200, ['Content-Type' => 'application/xml']);
    }
}
