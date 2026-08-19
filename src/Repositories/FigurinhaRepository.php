<?php

declare(strict_types=1);

class FigurinhaRepository
{
    public function __construct(private readonly PDO $database)
    {
    }

    /** @return list<Figurinha> */
    public function buscarPorCodigo(string $codigo, ?int $ano = null): array
    {
        $conditions = ["REPLACE(codigo, ' ', '') LIKE :pattern"];
        $parameters = [':pattern' => '%' . $codigo . '%'];

        if ($ano !== null) {
            $conditions[] = 'ano_copa = :ano';
            $parameters[':ano'] = $ano;
        }

        $query = 'SELECT id, codigo, nome, selecao, edicao_album, ano_copa, categoria, preco_unitario '
            . 'FROM figurinhas WHERE ' . implode(' AND ', $conditions)
            . ' ORDER BY CASE WHEN REPLACE(codigo, \' \', \'\') = :exact_code THEN 0 ELSE 1 END, codigo, id';
        $parameters[':exact_code'] = $codigo;

        $statement = $this->database->prepare($query);
        $statement->execute($parameters);

        return array_map(static fn (array $row): Figurinha => Figurinha::fromRow($row), $statement->fetchAll());
    }

    public function buscarPorId(int $id): ?Figurinha
    {
        $statement = $this->database->prepare(
            'SELECT id, codigo, nome, selecao, edicao_album, ano_copa, categoria, preco_unitario '
            . 'FROM figurinhas WHERE id = :id'
        );
        $statement->execute([':id' => $id]);
        $row = $statement->fetch();

        return is_array($row) ? Figurinha::fromRow($row) : null;
    }
}
