---

description: "Task list for sticker code search and automatic budget"
---

# Tasks: Busca por Código e Orçamento Automático de Figurinhas

**Input**: Design documents from `specs/001-sticker-code-budget/`

**Prerequisites**: [plan.md](plan.md), [spec.md](spec.md), [research.md](research.md), [data-model.md](data-model.md), [contracts/http-json.md](contracts/http-json.md), [quickstart.md](quickstart.md)

**Testing**: Validação manual por CLI, navegador e viewport móvel. Não criar PHPUnit, runners ou dependências automatizadas.

**Organization**: Tasks are grouped by user story. Existing implementation tasks are marked `[X]`; new UX/UI tasks remain pending.

## Phase 1: Setup (Shared Infrastructure)

**Purpose**: Estrutura mínima PHP nativa e assets da aplicação.

- [X] T001 Criar os diretórios `config/`, `database/migrations/`, `public/css/`, `public/js/`, `src/Controllers/`, `src/Models/`, `src/Repositories/`, `src/Services/`, `src/Views/` e `tests/` conforme `plan.md`.
- [X] T002 [P] Criar o ponto de entrada HTTP em `public/index.php` com `declare(strict_types=1);` e carregamento explícito.
- [X] T003 [P] Criar a configuração PDO em `config/database.php` sem credenciais versionadas e sem dependências em `vendor/`.
- [X] T004 [P] Criar o esqueleto visual em `src/Views/home.php`, `public/css/app.css` e `public/js/app.js`.

## Phase 2: Foundational (Blocking Prerequisites)

**Purpose**: Banco, modelo, sessão e contratos HTTP compartilhados.

**Checkpoint**: A migração aplica em MySQL 5.7 e a aplicação responde JSON sem expor detalhes internos.

- [X] T005 Criar `database/migrations/001_create_figurinhas_table.sql` com charset/collation corretos, preço não negativo e índices em `codigo` e `edicao_album`.
- [X] T006 Configurar PDO em `config/database.php` com exceções, prepared statements nativos e compatibilidade MySQL 5.7.23-23.
- [X] T007 [P] Criar `src/Models/Figurinha.php` com propriedades tipadas e hidratação explícita.
- [X] T008 [P] Definir o roteamento para `/`, `/busca`, `/orcamento`, `/orcamento/adicionar` e `/orcamento/remover` em `public/index.php`.
- [X] T009 [P] Definir respostas JSON seguras e códigos HTTP em `public/index.php`.
- [X] T010 Documentar fixtures de 2018/2022 em `specs/001-sticker-code-budget/quickstart.md`.

## Phase 3: User Story 1 - Encontrar figurinha por código (Priority: P1) 🎯 MVP

**Goal**: Buscar códigos exatos/parciais, filtrar por ano e exibir detalhes e estados de busca.

**Independent Test**: Buscar `bra 10` com 2022, `fwc` sem ano, código vazio, ano inválido e código inexistente; confirmar resultados, erros e empty state.

### Implementation for User Story 1

- [X] T011 [P] [US1] Implementar normalização e validação de código/ano em `src/Services/OrcamentoService.php` ou helper compartilhado.
- [X] T012 [P] [US1] Implementar `buscarPorCodigo(string $codigo, ?int $ano)` em `src/Repositories/FigurinhaRepository.php` com SQL parametrizado e compatível com MySQL 5.7.
- [X] T013 [US1] Implementar `buscarPorId(int $id)` em `src/Repositories/FigurinhaRepository.php`.
- [X] T014 [US1] Implementar `BuscaController` em `src/Controllers/BuscaController.php` conforme o contrato `GET /busca`.
- [X] T015 [US1] Integrar a rota `/busca` em `public/index.php` com respostas 400, lista vazia e erro 500 seguro.
- [X] T016 [US1] Renderizar formulário e tabela de resultados em `src/Views/home.php` com conteúdo escapado.
- [X] T017 [US1] Implementar busca assíncrona em `public/js/app.js` com `fetch` e estados básicos.
- [X] T018 [US1] Verificar manualmente os cenários de busca em `tests/manual_test_orcamento.php`.
- [X] T019 [US1] Adicionar badges de categoria para Comum, Escudo, Estádio e Lendárias em `src/Views/home.php`, com destaque dourado para Lendárias.
- [X] T020 [US1] Criar estilos mobile-first, tema escuro esportivo, acentos `#059669`/`#10b981`, códigos monospace e moeda brasileira em `public/css/app.css`.
- [X] T021 [US1] Implementar empty state ilustrativo com sugestões acionáveis `BRA10` e `ARG01` em `src/Views/home.php` e `public/js/app.js`.
- [X] T022 [US1] Adicionar transições CSS nativas para cards, tabela, entrada de resultados e estados de busca em `public/css/app.css`.

**Checkpoint**: US1 entrega busca funcional, badges, empty state, tema visual e feedback de carregamento/erro sem depender do orçamento.

## Phase 4: User Story 2 - Montar orçamento temporário (Priority: P1)

**Goal**: Adicionar/remover itens em sessão, calcular total, respeitar cinco unidades e atualizar a interface sem reload.

**Independent Test**: Adicionar categorias com preços diferentes, repetir um código seis vezes, recarregar, remover até vazio e observar painel, loading e toasts.

### Implementation for User Story 2

- [X] T023 [P] [US2] Criar a estrutura de `$_SESSION['orcamento']` em `src/Services/OrcamentoService.php`.
- [X] T024 [US2] Implementar `adicionar(int $figurinhaId)` em `src/Services/OrcamentoService.php` validando catálogo e preço server-side.
- [X] T025 [US2] Impor limite de 5 unidades por código em `src/Services/OrcamentoService.php` sem mutar estado em falha.
- [X] T026 [US2] Implementar remoção e recálculo em `src/Services/OrcamentoService.php`.
- [X] T027 [US2] Implementar `OrcamentoController` em `src/Controllers/OrcamentoController.php` conforme os contratos HTTP.
- [X] T028 [US2] Integrar rotas de orçamento em `public/index.php` com validação JSON e status 400/404/409/422/500.
- [X] T029 [US2] Renderizar painel básico em `src/Views/home.php` com itens, quantidades, total e estado vazio.
- [X] T030 [US2] Implementar adição/remoção assíncrona em `public/js/app.js` sem recarga completa.
- [X] T031 [US2] Validar manualmente soma, múltiplos preços, limite, remoção, vazio e falha sem mutação em `tests/manual_test_orcamento.php`.
- [X] T032 [US2] Atualizar o painel e contadores em `public/js/app.js` instantaneamente após cada resposta válida, sem `window.location.reload` ou navegação.
- [X] T033 [US2] Implementar spinner/loading e estado temporário de confirmação por até 1,5 segundo nos botões de adição em `public/js/app.js`.
- [X] T034 [US2] Criar sistema de toasts flutuantes para sucesso, remoção, limite 409, indisponibilidade e falha 500 em `public/js/app.js` e `public/css/app.css`.
- [X] T035 [US2] Adicionar transições de entrada/remoção e abertura do painel de orçamento em `public/css/app.css`.
- [X] T036 [US2] Consolidar o painel em barra fixa no rodapé para smartphones, com espaço de segurança no conteúdo, em `src/Views/home.php` e `public/css/app.css`.

**Checkpoint**: US2 entrega orçamento dinâmico, zero reload, loading, confirmação, toasts e barra móvel sem quebrar sessão ou cálculo.

## Phase 5: User Story 3 - Distinguir categorias e controlar entradas (Priority: P2)

**Goal**: Garantir preços por categoria, entradas inválidas rejeitadas e feedback visual coerente.

**Independent Test**: Consultar Comum/Escudo/Estádio/Lendárias, adicionar preços distintos e forçar item inexistente/preço inválido; confirmar que o orçamento permanece íntegro.

### Implementation for User Story 3

- [X] T037 [P] [US3] Validar categoria e `preco_unitario` em `src/Services/OrcamentoService.php`, rejeitando valores inválidos com 422.
- [X] T038 [P] [US3] Ajustar moeda e hidratação em `src/Models/Figurinha.php` para duas casas sem arredondamentos indevidos.
- [X] T039 [US3] Confirmar compatibilidade MySQL 5.7 em `src/Repositories/FigurinhaRepository.php` sem CTE/window functions/collations MySQL 8.
- [X] T040 [US3] Exibir categoria e preço escapados em `src/Views/home.php`.
- [X] T041 [US3] Tratar falhas de item/sessão sem substituir o orçamento em `src/Controllers/OrcamentoController.php`.
- [X] T042 [US3] Diferenciar erros de validação, indisponibilidade, limite e falha operacional em `public/js/app.js`.
- [X] T043 [US3] Executar casos de categoria, preço inválido, item inexistente e resposta segura em `tests/manual_test_orcamento.php`.
- [X] T044 [US3] Aplicar classes semânticas de categoria e hierarquia visual para Comum, Escudo, Estádio e Lendárias em `public/css/app.css`.
- [X] T045 [US3] Confirmar que toasts e estados de erro preservam o total anterior e não ocultam o painel em `public/js/app.js`.

**Checkpoint**: US3 mantém integridade do orçamento e comunica visualmente categorias e falhas de negócio.

## Phase 6: Polish & Cross-Cutting Concerns

**Purpose**: Fechar publicação, segurança, responsividade e validação final.

- [X] T046 [P] Revisar controllers/views em `public/index.php`, `src/Controllers/BuscaController.php`, `src/Controllers/OrcamentoController.php` e `src/Views/home.php`.
- [X] T047 [P] Revisar PDO/SQL em `config/database.php` e `src/Repositories/FigurinhaRepository.php`.
- [X] T048 [P] Atualizar `specs/001-sticker-code-budget/quickstart.md` com validações executadas.
- [X] T049 Executar cenários funcionais do `specs/001-sticker-code-budget/quickstart.md` no navegador.
- [X] T050 Medir dez buscas exatas e confirmar o critério de 95% em `specs/001-sticker-code-budget/quickstart.md`.
- [X] T051 Confirmar ausência de PHPUnit, Composer, frameworks pesados e credenciais versionadas com `git status`.
- [X] T052 [P] Criar `public/.htaccess` com rewrite para `index.php` e preservar arquivos estáticos.
- [X] T053 [P] Validar `public/css/app.css` e `public/js/app.js` em desktop e smartphone, incluindo barra fixa, contraste, badges, toasts, loading, empty state e transições.
- [X] T054 [P] Validar zero page reload em `public/js/app.js` e confirmar que nenhuma ação usa navegação ou recarga completa.
- [X] T055 Atualizar `specs/001-sticker-code-budget/quickstart.md` com resultados dos cenários visuais e responsivos.
- [X] T056 Executar todos os cenários funcionais e visuais do quickstart após as novas alterações em `public/` e `src/Views/`.

## Dependencies & Execution Order

### Phase Dependencies

- Setup e Foundational já concluídos e bloqueiam todas as histórias.
- US1 continua sendo o MVP e deve receber T019-T022 antes da validação visual do fluxo de busca.
- US2 depende do catálogo/repository de US1 e adiciona T032-T036 para a experiência dinâmica do orçamento.
- US3 depende do orçamento de US2 e adiciona T044-T045 para consistência visual e integridade de erro.
- Polish T052-T056 depende dos novos checkpoints das três histórias.

### User Story Dependencies

- **US1 (P1)**: independente após a fundação; MVP de busca e descoberta.
- **US2 (P1)**: depende do item encontrado por US1, mas pode ser exercitada com fixtures conhecidas.
- **US3 (P2)**: depende de US2 para validar preços, categorias e preservação do estado.

### Parallel Opportunities

- US1: T019 e T020 podem ocorrer em paralelo; T021 e T022 podem iniciar após a estrutura visual.
- US2: T032 e T033 podem ocorrer em paralelo em `public/js/app.js` somente com coordenação; T034-T036 devem seguir a integração do painel.
- US3: T044 e T045 podem ocorrer em paralelo em arquivos diferentes.
- Polish: T052, T053 e T054 podem ocorrer em paralelo; T055-T056 dependem das revisões.

## Parallel Example: User Story 1

```text
Task: "T019 [US1] Adicionar badges de categoria em src/Views/home.php"
Task: "T020 [US1] Criar tema e estilos em public/css/app.css"
```

## Parallel Example: User Story 2

```text
Task: "T032 [US2] Atualizar painel sem reload em public/js/app.js"
Task: "T034 [US2] Criar toasts em public/js/app.js e public/css/app.css"
Task: "T036 [US2] Criar barra fixa móvel em src/Views/home.php e public/css/app.css"
```

## Implementation Strategy

### MVP First

1. Usar a implementação já concluída da fundação e US1.
2. Completar T019-T022 para tornar a busca visualmente completa.
3. Validar US1 independentemente em desktop e smartphone.

### Incremental Delivery

1. Completar T032-T036 e entregar o orçamento com zero reload e feedback completo.
2. Completar T044-T045 para categorias e falhas consistentes.
3. Completar T052-T056 e executar a validação final do quickstart.

## Notes

- Toda tarefa segue `- [ ]` ou `- [X]`, ID sequencial, marcador `[P]` opcional, label `[US#]` apenas em fases de história e caminho explícito.
- Tarefas já executadas permanecem `[X]`; as tarefas novas da spec visual começam em T019.
- A constituição vigente proíbe testes automatizados; a validação continua manual.
