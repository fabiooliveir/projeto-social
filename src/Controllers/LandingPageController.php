<?php

declare(strict_types=1);

namespace App\Controllers;

use Jenssegers\Blade\Blade;

final class LandingPageController
{
    public function __construct(
        private readonly Blade $blade,
    ) {
    }

    public function index(): void
    {
        $cotas = $this->cotasDisponiveis();

        header('Content-Type: text/html; charset=utf-8');

        echo $this->blade->render('landing.index', [
            'cotas' => $cotas,
        ]);
    }

    public function enviarContato(): void
    {
        $dados = $_POST;

        $erros = $this->validarContato($dados);

        if ($erros !== []) {
            http_response_code(422);

            if ($this->isAjax()) {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['erros' => $erros]);
                return;
            }

            $_SESSION['erro_contato'] = $erros;
            $_SESSION['dados_contato'] = $dados;
            header('Location: /#contato');
            exit;
        }

        http_response_code(200);

        if ($this->isAjax()) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['sucesso' => true, 'mensagem' => 'Mensagem enviada com sucesso! Entraremos em contato em breve.']);
            return;
        }

        $_SESSION['sucesso_contato'] = 'Mensagem enviada com sucesso! Entraremos em contato em breve.';
        header('Location: /#contato');
        exit;
    }

    /**
     * @return array<string, string>
     */
    private function cotasDisponiveis(): array
    {
        return [
            [
                'id'    => 'salas-pedagogicas',
                'icone' => '🪑',
                'nome'  => 'Cota Salas Pedagógicas',
                'desc'  => 'Mobiliário infantil anatômico e brinquedoteca pedagógica para as salas de Educação Infantil.',
                'valor' => 250,
                'meta'  => 40,
                'arrecadado' => 0,
                'cor'   => 'blue',
            ],
            [
                'id'    => 'auditorio-acustico',
                'icone' => '🎭',
                'nome'  => 'Cota Auditório Acústico',
                'desc'  => 'Painéis acústicos e forro isolante para o Auditório Multiuso com capacidade de 120 pessoas.',
                'valor' => 500,
                'meta'  => 30,
                'arrecadado' => 0,
                'cor'   => 'purple',
            ],
            [
                'id'    => 'iluminacao-eletrica',
                'icone' => '💡',
                'nome'  => 'Cota Iluminação & Elétrica',
                'desc'  => 'Refletores LED, infraestrutura cenica e sistema de iluminação profissional para eventos.',
                'valor' => 350,
                'meta'  => 25,
                'arrecadado' => 0,
                'cor'   => 'amber',
            ],
            [
                'id'    => 'parque-infantil',
                'icone' => '🧩',
                'nome'  => 'Cota Parque Infantil Adaptado',
                'desc'  => 'Piso emborrachado e brinquedos inclusivos para o parque infantil ao ar livre.',
                'valor' => 150,
                'meta'  => 50,
                'arrecadado' => 0,
                'cor'   => 'green',
            ],
            [
                'id'    => 'contraturno-digital',
                'icone' => '💻',
                'nome'  => 'Cota Contraturno Digital',
                'desc'  => 'Computadores e kits de iniciação a robótica para as oficinas de contraturno (6 a 14 anos).',
                'valor' => 600,
                'meta'  => 20,
                'arrecadado' => 0,
                'cor'   => 'rose',
            ],
        ];
    }

    /**
     * @param array<string, mixed> $dados
     * @return array<string, string>
     */
    private function validarContato(array $dados): array
    {
        $erros = [];

        $nome = trim((string) ($dados['nome'] ?? ''));
        if ($nome === '') {
            $erros['nome'] = 'O campo nome é obrigatório.';
        }

        $contato = trim((string) ($dados['contato'] ?? ''));
        if ($contato === '') {
            $erros['contato'] = 'O campo e-mail ou telefone é obrigatório.';
        }

        $tipoApoio = trim((string) ($dados['tipo_apoio'] ?? ''));
        if ($tipoApoio === '') {
            $erros['tipo_apoio'] = 'Selecione o tipo de apoio.';
        }

        return $erros;
    }

    private function isAjax(): bool
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
}
