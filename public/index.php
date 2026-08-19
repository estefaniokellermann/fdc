<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/Models/Figurinha.php';
require_once __DIR__ . '/../src/Repositories/FigurinhaRepository.php';
require_once __DIR__ . '/../src/Services/OrcamentoService.php';
require_once __DIR__ . '/../src/Controllers/BuscaController.php';
require_once __DIR__ . '/../src/Controllers/OrcamentoController.php';

session_start();

function sendJson(array $payload, int $status = 200): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION);
    exit;
}

function requestPath(): string
{
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    return is_string($path) && $path !== '' ? $path : '/';
}

try {
    $path = requestPath();
    $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

    if ($path === '/' && $method === 'GET') {
        require __DIR__ . '/../src/Views/home.php';
        exit;
    }

    $database = createDatabaseConnection();
    $repository = new FigurinhaRepository($database);
    $budgetService = new OrcamentoService($repository);

    if ($path === '/busca' && $method === 'GET') {
        (new BuscaController($repository))->search($_GET);
    }

    if ($path === '/orcamento' && $method === 'GET') {
        (new OrcamentoController($budgetService))->show();
    }

    if (($path === '/orcamento/adicionar' || $path === '/orcamento/remover') && $method === 'POST') {
        $body = json_decode((string) file_get_contents('php://input'), true);
        if (!is_array($body)) {
            sendJson(['sucesso' => false, 'mensagem' => 'JSON inválido.'], 400);
        }

        $controller = new OrcamentoController($budgetService);
        if ($path === '/orcamento/adicionar') {
            $controller->add($body);
        }
        $controller->remove($body);
    }

    sendJson(['sucesso' => false, 'mensagem' => 'Rota não encontrada.'], 404);
} catch (OrcamentoException $exception) {
    sendJson(['sucesso' => false, 'mensagem' => $exception->getMessage()], $exception->status);
} catch (Throwable $exception) {
    error_log($exception->getMessage());
    sendJson(['sucesso' => false, 'mensagem' => 'Não foi possível concluir a operação.'], 500);
}
