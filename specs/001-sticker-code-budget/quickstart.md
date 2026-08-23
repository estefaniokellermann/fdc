# Quickstart: Validação Manual

## Pré-requisitos

- PHP 8.2+ com extensão PDO MySQL habilitada.
- MySQL 5.7.23-23 acessível.
- Variáveis de ambiente `FDC_DB_HOST`, `FDC_DB_PORT`, `FDC_DB_NAME`, `FDC_DB_USER` e `FDC_DB_PASSWORD` definidas para conexão.
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
9. Confirmar badges visuais distintos para Comum, Escudo, Estádio e Lendárias, incluindo destaque dourado para Lendárias.
10. Adicionar e remover um item: confirmar zero reload, spinner imediato, confirmação temporária e toast correspondente.
11. Buscar um código inexistente: confirmar empty state com sugestões `BRA10` e `ARG01`.
12. Abrir a página em smartphone: confirmar barra fixa de orçamento no rodapé, sem sobreposição de conteúdo.

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
- Confirmar responsividade, contraste, transições CSS, badges, loading, toasts e empty states em desktop e smartphone.

## Resultado da validação remota (2026-08-18)

- MySQL remoto confirmado em `5.7.23-23`, com tabela `figurinhas` em `utf8mb4_unicode_ci`.
- Os oito cenários funcionais foram executados via HTTP com sessão persistente: busca normalizada, busca parcial, validações, categorias, limite de 5, recarga, remoção até vazio e item inexistente.
- Total das fixtures de teste por categoria: `6.75`; orçamento vazio após remoção: `0.00`.
- Dez buscas exatas: `100%` abaixo de 2 segundos; média de aproximadamente `954 ms` e máximo de aproximadamente `1.03 s`.

## Resultado da validação visual (2026-08-19)

- Home carregada localmente em viewport desktop e smartphone, com título, formulário, sugestões e orçamento acessíveis.
- Em viewport móvel (`362px`), o painel de orçamento usa `position: fixed` no rodapé; em viewport ampla (`1152px`), retorna ao fluxo normal em grade de duas colunas.
- CSS e JavaScript foram carregados sem erro; `node --check public/js/app.js` e lint PHP passaram.
- Badges de categoria, empty state, tema escuro, transições, loading/toasts e atualização do painel sem navegação foram verificados no navegador.
