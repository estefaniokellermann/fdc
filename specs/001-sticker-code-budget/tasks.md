---

description: "Task list for sticker code search and automatic budget"
---

# Tasks: Busca por Codigo e Orcamento Automatico de Figurinhas

**Input**: Design documents from /specs/001-sticker-code-budget/

**Prerequisites**: plan.md, spec.md, research.md, data-model.md, contracts/http-json.md, quickstart.md

**Tests**: Validacao manual por CLI e navegador. Nao criar PHPUnit, runners ou dependencias de teste automatizado.

**Organization**: Tasks are grouped by user story for independent implementation and validation.

## Phase 1: Setup (Project Initialization)

**Purpose**: Garantir estrutura minima da aplicacao web PHP nativa.

- [X] T001 Validar e ajustar estrutura de diretorios em config/, database/migrations/, public/css/, public/js/, src/Controllers/, src/Models/, src/Repositories/, src/Services/, src/Views/ e tests/
- [X] T002 [P] Configurar entrada HTTP e bootstrap de aplicacao em public/index.php
- [X] T003 [P] Configurar conexao PDO com parametros de ambiente em config/database.php
- [X] T004 [P] Validar shell inicial de interface e assets em src/Views/home.php, public/css/app.css e public/js/app.js

## Phase 2: Foundational (Blocking Prerequisites)

**Purpose**: Definir base de dados, modelo, contratos HTTP e infraestrutura compartilhada para todas as historias.

- [X] T005 Criar/validar schema de catalogo em database/migrations/001_create_figurinhas_table.sql
- [X] T006 [P] Implementar modelo tipado de figurinha em src/Models/Figurinha.php
- [X] T007 [P] Implementar repository de leitura do catalogo em src/Repositories/FigurinhaRepository.php
- [X] T008 [P] Configurar roteamento HTTP para /, /busca, /orcamento, /orcamento/adicionar e /orcamento/remover em public/index.php
- [X] T009 [P] Padronizar resposta JSON segura e tratamento de falhas em public/index.php
- [X] T010 Sincronizar contratos de endpoint e mensagens com contracts/http-json.md
- [X] T011 Preparar fixtures e passos de verificacao manual em specs/001-sticker-code-budget/quickstart.md

## Phase 3: User Story 1 - Encontrar figurinha por codigo (Priority: P1)

**Goal**: Permitir busca por codigo exato/parcial com normalizacao e filtro opcional por ano.

**Independent Test**: Buscar bra 10 com 2022, buscar fwc sem ano, buscar com codigo vazio, buscar com ano invalido e buscar codigo inexistente; confirmar respostas corretas e estado vazio amigavel.

- [X] T012 [P] [US1] Implementar normalizacao de codigo e validacao de ano em src/Controllers/BuscaController.php
- [X] T013 [P] [US1] Implementar busca por codigo exato/parcial com filtro opcional por ano em src/Repositories/FigurinhaRepository.php
- [X] T014 [US1] Implementar endpoint GET /busca e mapeamento de erros 400/500 em src/Controllers/BuscaController.php
- [X] T015 [US1] Integrar fluxo de busca e retorno JSON no roteador em public/index.php
- [X] T016 [US1] Renderizar formulario de busca e regiao de resultados em src/Views/home.php
- [X] T017 [US1] Implementar requisicao assincrona de busca e renderizacao de tabela em public/js/app.js
- [X] T018 [US1] Implementar estado vazio com sugestoes BRA10 e ARG01 em src/Views/home.php e public/js/app.js
- [X] T019 [US1] Aplicar badges visuais de categoria para Comum, Escudo, Estadio e Lendarias em public/css/app.css
- [X] T020 [US1] Aplicar microinteracoes de carregamento e feedback visual de busca em public/css/app.css e public/js/app.js
- [X] T021 [US1] Executar validacao manual dos cenarios de busca em tests/manual_test_orcamento.php

## Phase 4: User Story 2 - Montar orcamento temporario (Priority: P1)

**Goal**: Permitir adicionar/remover itens no orcamento de sessao com recalculo de quantidade e total sem recarregar a pagina.

**Independent Test**: Adicionar itens de categorias distintas, repetir o mesmo codigo ate o limite, recarregar pagina, remover itens ate vazio e confirmar atualizacao instantanea do painel.

- [X] T022 [P] [US2] Implementar inicializacao e leitura de estado de orcamento em sessao em src/Services/OrcamentoService.php
- [X] T023 [US2] Implementar adicao de figurinha com validacao server-side em src/Services/OrcamentoService.php
- [X] T024 [US2] Implementar regra de limite de 5 unidades por codigo sem mutar estado em falha em src/Services/OrcamentoService.php
- [X] T025 [US2] Implementar remocao de unidade e recalculo de totais em src/Services/OrcamentoService.php
- [X] T026 [US2] Implementar endpoints GET /orcamento e POST /orcamento/adicionar em src/Controllers/OrcamentoController.php
- [X] T027 [US2] Implementar endpoint POST /orcamento/remover e mapeamento de status 400/404/409/422/500 em src/Controllers/OrcamentoController.php
- [X] T028 [US2] Integrar rotas de orcamento e sessao no roteador em public/index.php
- [X] T029 [US2] Renderizar painel de orcamento e contador de itens em src/Views/home.php
- [X] T030 [US2] Implementar acoes de adicionar/remover sem reload e reconciliacao do painel em public/js/app.js
- [X] T031 [US2] Implementar loading no botao de adicao e confirmacao temporaria de sucesso em public/js/app.js
- [X] T032 [US2] Implementar toasts flutuantes para sucesso, remocao e rejeicoes de negocio em public/js/app.js e public/css/app.css
- [X] T033 [US2] Implementar barra fixa de orcamento no rodape em viewport movel em public/css/app.css
- [X] T034 [US2] Executar validacao manual de sessao, limite, soma e orcamento vazio em tests/manual_test_orcamento.php

## Phase 5: User Story 3 - Distinguir categorias e controlar entradas (Priority: P2)

**Goal**: Garantir integridade de preco por categoria e rejeicao segura de entradas invalidas sem corromper o estado atual.

**Independent Test**: Testar itens Comum, Escudo, Estadio e Lendarias com precos diferentes; forcar item inexistente/preco invalido e confirmar que o orcamento anterior permanece intacto.

- [X] T035 [P] [US3] Validar categoria e preco unitario antes de qualquer mutacao em src/Services/OrcamentoService.php
- [X] T036 [P] [US3] Garantir serializacao monetaria consistente com duas casas em src/Models/Figurinha.php e src/Services/OrcamentoService.php
- [X] T037 [US3] Reforcar busca de figurinha existente no servidor antes de adicionar em src/Repositories/FigurinhaRepository.php e src/Services/OrcamentoService.php
- [X] T038 [US3] Implementar respostas acionaveis para indisponibilidade e validacao invalida em src/Controllers/OrcamentoController.php
- [X] T039 [US3] Ajustar feedback de erro no cliente sem sobrescrever painel valido em public/js/app.js
- [X] T040 [US3] Ajustar hierarquia visual de categoria e destaque de Lendarias em public/css/app.css
- [X] T041 [US3] Executar validacao manual de categoria, preco invalido e falha sem mutacao em tests/manual_test_orcamento.php

## Phase 6: Polish & Cross-Cutting Concerns

**Purpose**: Consolidar seguranca, responsividade, consistencia de UX e readiness de entrega.

- [X] T042 [P] Revisar sanitizacao de saida na interface em src/Views/home.php
- [X] T043 [P] Revisar seguranca de entrada e erros sem vazamento interno em public/index.php, src/Controllers/BuscaController.php e src/Controllers/OrcamentoController.php
- [X] T044 [P] Revisar compatibilidade MySQL 5.7 e SQL parametrizado em src/Repositories/FigurinhaRepository.php
- [X] T045 [P] Revisar tema mobile-first e transicoes CSS em public/css/app.css
- [X] T046 [P] Revisar fluxo assicrono sem recarga completa em public/js/app.js
- [X] T047 Atualizar roteiro de verificacao final com resultados em specs/001-sticker-code-budget/quickstart.md
- [X] T048 Executar validacao final de cenarios funcionais e visuais em tests/manual_test_orcamento.php e specs/001-sticker-code-budget/quickstart.md

## Dependencies & Execution Order

### Phase Dependencies

- Phase 1 deve concluir antes da Phase 2.
- Phase 2 bloqueia inicio de todas as historias.
- Phase 3 (US1) habilita descoberta de itens para as acoes da US2.
- Phase 4 (US2) habilita validacoes de integridade de estado da US3.
- Phase 5 antecede o fechamento da Phase 6.

### User Story Dependencies

- US1: Independente apos Foundational e define o MVP funcional.
- US2: Depende de US1 para fluxo completo de descoberta + adicao ao orcamento.
- US3: Depende de US2 para validar integridade em mutacoes reais.

## Parallel Execution Opportunities

- Setup: T002, T003 e T004 em paralelo apos T001.
- Foundational: T006, T007, T008 e T009 em paralelo apos T005.
- US1: T012 e T013 em paralelo; T019 e T020 em paralelo apos renderizacao base.
- US2: T022 e T029 em paralelo; T031, T032 e T033 em paralelo apos endpoints prontos.
- US3: T035 e T036 em paralelo; T039 e T040 em paralelo.
- Polish: T042 a T046 em paralelo.

## Parallel Example: US1

- T012 [P] [US1] Implementar normalizacao/validacao de entrada em src/Controllers/BuscaController.php
- T013 [P] [US1] Implementar query parametrizada de busca em src/Repositories/FigurinhaRepository.php

## Parallel Example: US2

- T031 [P] [US2] Implementar loading/confirmacao do botao de adicao em public/js/app.js
- T033 [P] [US2] Implementar barra fixa movel do orcamento em public/css/app.css

## Implementation Strategy

### MVP First (US1)

1. Concluir Phase 1 e Phase 2.
2. Entregar integralmente a Phase 3 (US1).
3. Validar os cenarios independentes da US1 antes de avancar.

### Incremental Delivery

1. Entregar US2 para completar fluxo de orcamento em sessao.
2. Entregar US3 para reforcar integridade e semantica de categorias.
3. Executar Phase 6 para consolidar qualidade, seguranca e usabilidade.

### Notes

- Todas as tarefas seguem o formato obrigatorio de checklist com ID e caminho de arquivo.
- Labels [US#] aparecem apenas nas fases de historia.
- Tarefas [P] indicam possibilidade real de execucao em paralelo por baixo acoplamento.

