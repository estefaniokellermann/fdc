<?php

declare(strict_types=1);

final class Figurinha
{
    public function __construct(
        public readonly int $id,
        public readonly string $codigo,
        public readonly string $nome,
        public readonly string $selecao,
        public readonly string $edicaoAlbum,
        public readonly int $anoCopa,
        public readonly string $categoria,
        public readonly string $precoUnitario
    ) {
    }

    /** @param array<string, mixed> $row */
    public static function fromRow(array $row): self
    {
        $price = trim((string) $row['preco_unitario']);
        if (preg_match('/^\d+(?:\.\d+)?$/', $price) !== 1) {
            $price = '0';
        }
        [$whole, $fraction] = array_pad(explode('.', $price, 2), 2, '0');

        return new self(
            (int) $row['id'],
            (string) $row['codigo'],
            (string) $row['nome'],
            (string) $row['selecao'],
            (string) $row['edicao_album'],
            (int) $row['ano_copa'],
            (string) $row['categoria'],
            $whole . '.' . str_pad(substr($fraction, 0, 2), 2, '0')
        );
    }

    /** @return array<string, int|string> */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'codigo' => $this->codigo,
            'nome' => $this->nome,
            'selecao' => $this->selecao,
            'edicao_album' => $this->edicaoAlbum,
            'ano_copa' => $this->anoCopa,
            'categoria' => $this->categoria,
            'preco_unitario' => $this->precoUnitario,
        ];
    }
}
