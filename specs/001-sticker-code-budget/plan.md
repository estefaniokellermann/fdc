# Implementation Plan: Busca por Código e Orçamento Automático

**Branch**: `001-sticker-code-budget` | **Date**: 2026-08-18 | **Spec**: [spec.md](spec.md)

**Input**: Feature specification from `specs/001-sticker-code-budget/spec.md`

**Note**: This template is filled in by the `/speckit-plan` command; its definition describes the execution workflow.

## Summary

Entregar uma busca pública por código de figurinha, com filtro opcional por ano, e um orçamento temporário por sessão com cálculo server-side, limite de cinco unidades por código e preços por categoria. A implementação será PHP 8.2+ nativo, organizada em camadas pequenas, com PDO parametrizado para MySQL 5.7, endpoints JSON e JavaScript `fetch` sem dependências de vendor.

## Technical Context

<!--
  ACTION REQUIRED: Replace the content in this section with the technical details
  for the project. The structure here is presented in advisory capacity to guide
  the iteration process.
-->

**Language/Version**: PHP 8.2+ com `declare(strict_types=1);`

**Primary Dependencies**: Nenhuma dependência nova; PDO, sessão, JSON e escaping nativos

**Storage**: MySQL 5.7.23-23 para catálogo; `$_SESSION['orcamento']` para orçamento temporário

**Testing**: Scripts manuais pré-definidos e validação na interface; sem PHPUnit ou runner automatizado

**Target Platform**: Servidor web com PHP 8.2+ e navegador HTML5/ES6+

**Project Type**: Módulo web MVC leve em PHP

**Performance Goals**: Pelo menos 95% das buscas exatas em até 2 segundos em condições normais; atualização assíncrona do orçamento após cada ação

**Constraints**: MySQL 5.7 sem CTE/window functions/collations MySQL 8; SQL somente parametrizado; limite 5 por código; sem login, checkout ou persistência do carrinho

**Scale/Scope**: Uma página pública, catálogo de figurinhas, busca e painel de orçamento temporário; sem administração de catálogo nesta fase

## Constitution Check

*GATE: Must pass before Phase 0 research. Re-check after Phase 1 design.*

- **I. Clarity Over Cleverness**: PASS. Nomes e responsabilidades são explícitos; sem abstrações especulativas.
- **II. Single Responsibility**: PASS. Controllers, Services, Repositories, Models e Views têm fronteiras separadas.
- **III. Minimal Design**: PASS. PHP nativo, PDO e APIs de sessão eliminam frameworks e dependências novas.
- **IV. Explicit Contracts and Safe Errors**: PASS. Contratos JSON definem entradas, saídas, status e falhas; validação ocorre nos controllers/services.
- **V. No Automated Test Suite**: PASS. O plano substitui PHPUnit por `tests/manual_test_orcamento.php` e cenários manuais, conforme a diretriz mais recente do pedido.
- **Gate result**: PASS. Nenhuma violação requer justificativa ou emenda constitucional.

## Project Structure

### Documentation (this feature)

```text
specs/001-sticker-code-budget/
├── plan.md              # This file (/speckit-plan command output)
├── research.md          # Phase 0 output (/speckit-plan command)
├── data-model.md        # Phase 1 output (/speckit-plan command)
├── quickstart.md        # Phase 1 output (/speckit-plan command)
├── contracts/           # Phase 1 output (/speckit-plan command)
└── tasks.md             # Phase 2 output (/speckit-tasks command - NOT created by /speckit-plan)
```

### Source Code (repository root)

```text
config/
└── database.php
database/
└── migrations/
  └── 001_create_figurinhas_table.sql
public/
├── index.php
├── css/app.css
└── js/app.js
src/
├── Controllers/
│   ├── BuscaController.php
│   └── OrcamentoController.php
├── Models/
│   └── Figurinha.php
├── Repositories/
│   └── FigurinhaRepository.php
├── Services/
│   └── OrcamentoService.php
└── Views/
  └── home.php
tests/
└── manual_test_orcamento.php
```

**Structure Decision**: Aplicação web PHP nativa com `public/` como entrada HTTP, dependências de infraestrutura em `config/`, catálogo em `database/`, lógica em `src/` e verificação manual em `tests/`. O navegador acessa somente controllers/rotas públicas; nenhum SQL ou manipulação direta de sessão fica na View.

## Implementation Checklist

### Fase 1: Infraestrutura de dados e model

- [ ] Criar `database/migrations/001_create_figurinhas_table.sql` com `utf8mb4_unicode_ci`, colunas do modelo e índices em `codigo` e `edicao_album`.
- [ ] Criar `config/database.php` com fábrica PDO, modo de erro por exceção, prepared statements nativos e configuração externa das credenciais.
- [ ] Criar `src/Models/Figurinha.php` com propriedades tipadas, hidratação explícita e representação monetária segura.
- [ ] Implementar `src/Repositories/FigurinhaRepository.php` com `buscarPorCodigo(string $codigo, ?int $ano)` e `buscarPorId(int $id)`, sem `WITH`, window functions ou interpolação SQL.

### Fase 2: Regras de negócio e validação manual

- [ ] Criar `src/Services/OrcamentoService.php` para iniciar/ler/adicionar/remover itens em `$_SESSION['orcamento']`.
- [ ] Normalizar códigos removendo todos os espaços e convertendo para maiúsculas antes de consultar.
- [ ] Validar item novamente no servidor antes de adicionar e copiar preço do catálogo, ignorando valores enviados pelo cliente.
- [ ] Impor RN03: máximo de 5 unidades por código, com falha sem mutar o estado anterior.
- [ ] Recalcular quantidade e total a cada mutação, inclusive para categorias com preços diferentes.
- [ ] Criar `tests/manual_test_orcamento.php` com PASS/FAIL para normalização, soma, limite, falha sem mutação e orçamento vazio.

### Fase 3: HTTP e controllers

- [ ] Criar `public/index.php` e roteamento mínimo para home, busca e orçamento.
- [ ] Criar `BuscaController` para validar GET, chamar repository e retornar o contrato de busca JSON.
- [ ] Criar `OrcamentoController` para GET/POST de leitura, adição e remoção, com status 400/404/409/422/500 definidos no contrato.
- [ ] Configurar sessão antes dos controllers e respostas JSON com `json_encode` e charset correto.
- [ ] Garantir que erros não revelem exceções, SQL, credenciais ou caminhos internos.

### Fase 4: Interface

- [ ] Criar `src/Views/home.php` com formulário de código/ano, tabela de resultados e painel de orçamento.
- [ ] Escapar todo conteúdo de catálogo com `htmlspecialchars` no HTML.
- [ ] Criar `public/js/app.js` com `fetch` para busca, adição, remoção e atualização do painel sem recarga completa.
- [ ] Criar `public/css/app.css` somente com estilos necessários; usar HTML/CSS puro ou CDN sem adicionar pacote ao projeto.
- [ ] Exibir estados vazio, carregando, sem resultado, sucesso e limite atingido.

### Fase 5: Verificação manual

- [ ] Aplicar a migração em MySQL 5.7.23-23 e inserir fixtures de 2018/2022 nas categorias Escudo, Estádio e Lendárias.
- [ ] Executar o script manual por CLI e corrigir qualquer FAIL.
- [ ] Executar os oito cenários do [quickstart.md](quickstart.md) no navegador.
- [ ] Confirmar busca normalizada, filtro por ano, soma, persistência na sessão, limite 5, XSS escapado e erros seguros.
- [ ] Medir 10 buscas exatas e confirmar o critério de 95% em até 2 segundos.

## Complexity Tracking

Nenhuma violação constitucional identificada. A separação em camadas foi solicitada explicitamente e permanece mínima: um repository, um service e dois controllers para o fluxo desta fase.
