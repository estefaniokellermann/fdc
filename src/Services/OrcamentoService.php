<?php

declare(strict_types=1);

final class OrcamentoException extends RuntimeException
{
    public function __construct(string $message, public readonly int $status)
    {
        parent::__construct($message);
    }
}

final class OrcamentoService
{
    private const SESSION_KEY = 'orcamento';
    private const MAX_UNITS_PER_CODE = 5;

    public function __construct(private readonly FigurinhaRepository $repository)
    {
        if (!isset($_SESSION[self::SESSION_KEY]) || !is_array($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = ['itens' => []];
        }
    }

    public static function normalizarCodigo(string $codigo): string
    {
        return strtoupper((string) preg_replace('/\s+/', '', trim($codigo)));
    }

    /** @return array{itens: list<array<string, int|string>>, quantidade_total: int, valor_total: string} */
    public function resumo(): array
    {
        $items = $this->items();
        $quantity = 0;
        $totalCents = 0;
        $result = [];

        foreach ($items as $item) {
            $quantity += $item['quantidade'];
            $totalCents += self::toCents((string) $item['preco_unitario']) * $item['quantidade'];
            $result[] = $item;
        }

        return [
            'itens' => $result,
            'quantidade_total' => $quantity,
            'valor_total' => self::fromCents($totalCents),
        ];
    }

    /** @return array{itens: list<array<string, int|string>>, quantidade_total: int, valor_total: string} */
    public function adicionar(int $figurinhaId): array
    {
        if ($figurinhaId <= 0) {
            throw new OrcamentoException('Identificador da figurinha inválido.', 400);
        }

        $figurinha = $this->repository->buscarPorId($figurinhaId);
        if ($figurinha === null) {
            throw new OrcamentoException('Figurinha não encontrada.', 404);
        }
        if ($figurinha->categoria === '' || !preg_match('/^\d+(\.\d{1,2})?$/', $figurinha->precoUnitario)) {
            throw new OrcamentoException('Figurinha sem preço válido para orçamento.', 422);
        }

        $items = $this->items();
        $codeQuantity = 0;
        foreach ($items as $item) {
            if ($item['codigo'] === $figurinha->codigo) {
                $codeQuantity += $item['quantidade'];
            }
        }
        if ($codeQuantity >= self::MAX_UNITS_PER_CODE) {
            throw new OrcamentoException('O limite de 5 unidades por código foi atingido.', 409);
        }

        $key = (string) $figurinha->id;
        if (isset($items[$key])) {
            $items[$key]['quantidade']++;
        } else {
            $items[$key] = [
                'figurinha_id' => $figurinha->id,
                'codigo' => $figurinha->codigo,
                'nome' => $figurinha->nome,
                'categoria' => $figurinha->categoria,
                'preco_unitario' => $figurinha->precoUnitario,
                'quantidade' => 1,
            ];
        }

        $_SESSION[self::SESSION_KEY] = ['itens' => $items];
        return $this->resumo();
    }

    /** @return array{itens: list<array<string, int|string>>, quantidade_total: int, valor_total: string} */
    public function remover(int $figurinhaId): array
    {
        $key = (string) $figurinhaId;
        $items = $this->items();
        if (!isset($items[$key])) {
            throw new OrcamentoException('Item não está no orçamento.', 404);
        }

        if ($items[$key]['quantidade'] <= 1) {
            unset($items[$key]);
        } else {
            $items[$key]['quantidade']--;
        }

        $_SESSION[self::SESSION_KEY] = ['itens' => $items];
        return $this->resumo();
    }

    /** @return array<string, array<string, int|string>> */
    private function items(): array
    {
        $items = $_SESSION[self::SESSION_KEY]['itens'] ?? [];
        return is_array($items) ? $items : [];
    }

    private static function toCents(string $value): int
    {
        [$whole, $fraction] = array_pad(explode('.', $value, 2), 2, '0');
        return ((int) $whole * 100) + (int) str_pad(substr($fraction, 0, 2), 2, '0');
    }

    private static function fromCents(int $cents): string
    {
        return number_format($cents / 100, 2, '.', '');
    }
}
