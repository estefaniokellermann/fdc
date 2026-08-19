<?php

declare(strict_types=1);

final class BuscaController
{
    public function __construct(private readonly FigurinhaRepository $repository)
    {
    }

    /** @param array<string, mixed> $query */
    public function search(array $query): never
    {
        $codigo = OrcamentoService::normalizarCodigo((string) ($query['codigo'] ?? ''));
        if ($codigo === '') {
            sendJson(['sucesso' => false, 'mensagem' => 'Informe um código válido.'], 400);
        }

        $anoValue = $query['ano'] ?? null;
        $ano = null;
        if ($anoValue !== null && $anoValue !== '') {
            if (!is_scalar($anoValue) || !preg_match('/^\d{4}$/', (string) $anoValue)) {
                sendJson(['sucesso' => false, 'mensagem' => 'Informe um ano válido.'], 400);
            }
            $ano = (int) $anoValue;
        }

        $results = array_map(
            static fn (Figurinha $figurinha): array => $figurinha->toArray(),
            $this->repository->buscarPorCodigo($codigo, $ano)
        );
        sendJson(['sucesso' => true, 'resultados' => $results]);
    }
}
