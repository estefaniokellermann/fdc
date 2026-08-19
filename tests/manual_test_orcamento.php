<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/Models/Figurinha.php';
require_once __DIR__ . '/../src/Repositories/FigurinhaRepository.php';
require_once __DIR__ . '/../src/Services/OrcamentoService.php';

final class InMemoryFigurinhaRepository extends FigurinhaRepository
{
    /** @var array<int, Figurinha> */
    private array $catalog;

    public function __construct()
    {
        $this->catalog = [
            1 => new Figurinha(1, 'BRA10', 'Escudo Brasil', 'Brasil', 'Copa 2022', 2022, 'Escudo', '1.50'),
            2 => new Figurinha(2, 'ARG01', 'Lendária Argentina', 'Argentina', 'Copa 2022', 2022, 'Lendárias', '3.25'),
            3 => new Figurinha(3, 'FWC15', 'Estádio', '', 'Copa 2018', 2018, 'Estádio', '2.00'),
        ];
    }

    public function buscarPorId(int $id): ?Figurinha
    {
        return $this->catalog[$id] ?? null;
    }
}

$_SESSION = [];
$service = new OrcamentoService(new InMemoryFigurinhaRepository());
$failures = 0;

$check = static function (bool $condition, string $message) use (&$failures): void {
    echo ($condition ? 'PASS' : 'FAIL') . " - {$message}\n";
    if (!$condition) {
        $failures++;
    }
};

$check(OrcamentoService::normalizarCodigo(' bra 10 ') === 'BRA10', 'normalização remove espaços e converte para maiúsculas');
$service->adicionar(1);
$service->adicionar(2);
$service->adicionar(3);
$summary = $service->resumo();
$check($summary['quantidade_total'] === 3, 'quantidade total soma todas as unidades');
$check($summary['valor_total'] === '6.75', 'total soma preços de categorias diferentes');
for ($index = 0; $index < 4; $index++) {
    $service->adicionar(1);
}
$check($service->resumo()['itens'][0]['quantidade'] === 5, 'limite de cinco unidades é aplicado');
try {
    $service->adicionar(1);
    $check(false, 'sexta unidade deve falhar');
} catch (OrcamentoException $exception) {
    $check($exception->status === 409 && $service->resumo()['itens'][0]['quantidade'] === 5, 'sexta unidade falha sem alterar o orçamento');
}
$service->remover(1);
$check($service->resumo()['itens'][0]['quantidade'] === 4, 'remoção decrementa a quantidade');

exit($failures === 0 ? 0 : 1);
