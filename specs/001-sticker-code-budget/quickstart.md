# Quickstart: Validação Manual

## Pré-requisitos

- PHP 8.2+ com extensão PDO MySQL habilitada.
- MySQL 5.7.23-23 acessível.
- Banco configurado conforme `config/database.php` e migração aplicada.

## Preparar banco

1. Criar um banco UTF-8.
2. Executar `database/migrations/001_create_figurinhas_table.sql`.
3. Inserir manualmente itens de teste de 2018 e 2022, incluindo categorias Escudo, Estádio e Lendárias, preços diferentes e códigos `BRA10`, `ARG01` e `FWC15`.

## Executar aplicação

Na raiz do projeto:

```powershell
php -S localhost:8080 -t public
```

Abrir `http://localhost:8080/`.

## Cenários obrigatórios

1. Buscar `bra 10` com ano 2022: deve retornar `BRA10` da Copa 2022.
2. Buscar `fwc` sem ano: deve listar correspondências parciais e mostrar a edição de cada uma.
3. Buscar código vazio e ano inválido: deve exibir erro sem consulta ampla.
4. Adicionar um item de cada categoria: quantidade e total devem somar os preços unitários corretos.
5. Adicionar o mesmo código seis vezes: as cinco primeiras devem funcionar; a sexta deve retornar erro e manter quantidade 5.
6. Recarregar a página: o orçamento deve permanecer na sessão.
7. Remover unidades até esvaziar: o painel deve mostrar quantidade zero e `R$ 0,00`.
8. Simular item inexistente/preço inválido: a ação deve falhar sem modificar o orçamento anterior.

## Script manual

Executar:

```powershell
php tests/manual_test_orcamento.php
```

O script deve imprimir resultados PASS/FAIL para normalização, soma por categoria, limite 5, falha sem mutação e orçamento vazio. Ele não deve exigir PHPUnit, Composer ou outro runner.

## Critérios de aceite

- Verificar os cenários de `spec.md` para as histórias P1 e P2.
- Cronometrar 10 buscas exatas e confirmar que pelo menos 95% terminam em até 2 segundos em condições normais.
- Confirmar visualmente que textos vindos do catálogo aparecem escapados e que respostas JSON não expõem exceções internas.
