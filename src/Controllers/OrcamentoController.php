<?php

declare(strict_types=1);

final class OrcamentoController
{
    public function __construct(private readonly OrcamentoService $service)
    {
    }

    public function show(): never
    {
        sendJson(['sucesso' => true, 'orcamento' => $this->service->resumo()]);
    }

    /** @param array<string, mixed> $body */
    public function add(array $body): never
    {
        $id = $this->idFromBody($body);
        $summary = $this->service->adicionar($id);
        sendJson(['sucesso' => true, 'mensagem' => 'Figurinha adicionada ao orçamento.', 'orcamento' => $summary]);
    }

    /** @param array<string, mixed> $body */
    public function remove(array $body): never
    {
        $id = $this->idFromBody($body);
        $summary = $this->service->remover($id);
        sendJson(['sucesso' => true, 'mensagem' => 'Figurinha removida do orçamento.', 'orcamento' => $summary]);
    }

    /** @param array<string, mixed> $body */
    private function idFromBody(array $body): int
    {
        $id = $body['figurinha_id'] ?? null;
        if (!is_int($id) && !(is_string($id) && ctype_digit($id))) {
            throw new OrcamentoException('Identificador da figurinha inválido.', 400);
        }
        $normalizedId = (int) $id;
        if ($normalizedId <= 0) {
            throw new OrcamentoException('Identificador da figurinha inválido.', 400);
        }
        return $normalizedId;
    }
}
