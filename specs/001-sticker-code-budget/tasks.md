---

description: "Task list for sticker code search and automatic budget"
---

# Tasks: Busca por Código e Orçamento Automático de Figurinhas

**Input**: Design documents from `specs/001-sticker-code-budget/`

**Prerequisites**: [plan.md](plan.md), [spec.md](spec.md), [research.md](research.md), [data-model.md](data-model.md), [contracts/http-json.md](contracts/http-json.md), [quickstart.md](quickstart.md)

**Testing**: Validação manual por script CLI, navegador e checklist do quickstart. Não criar PHPUnit, runners ou dependências de teste automatizado.

**Organization**: Tasks are grouped by user story. Each story has an independent test and a checkpoint.

## Phase 1: Setup (Shared Infrastructure)

**Purpose**: Criar a estrutura mínima do módulo sem frameworks ou dependências novas.

- [X] T001 Criar os diretórios `config/`, `database/migrations/`, `public/css/`, `public/js/`, `src/Controllers/`, `src/Models/`, `src/Repositories/`, `src/Services/`, `src/Views/` e `tests/` conforme [plan.md](plan.md).
- [X] T002 [P] Criar o ponto de entrada HTTP mínimo em `public/index.php` com `declare(strict_types=1);` e carregamento explícito dos arquivos necessários.
- [X] T003 [P] Criar a configuração de ambiente documentada em `config/database.php` sem credenciais versionadas e sem adicionar dependências em `vendor/`.
- [X] T004 [P] Criar o esqueleto visual inicial em `src/Views/home.php`, `public/css/app.css` e `public/js/app.js` para permitir o smoke test da página.

## Phase 2: Foundational (Blocking Prerequisites)

**Purpose**: Preparar banco, contratos internos e convenções de resposta antes de qualquer história.

**Checkpoint**: A migração pode ser aplicada em MySQL 5.7 e a aplicação consegue iniciar uma sessão e responder JSON sem expor detalhes internos.

- [X] T005 Criar `database/migrations/001_create_figurinhas_table.sql` com tabela `figurinhas`, charset `utf8mb4`, collation `utf8mb4_unicode_ci`, preço decimal não negativo e índices em `codigo` e `edicao_album`.
- [X] T006 Configurar a fábrica PDO em `config/database.php` com `PDO::ERRMODE_EXCEPTION`, prepared statements nativos e conexão compatível com MySQL 5.7.23-23.
- [X] T007 [P] Criar o DTO tipado `src/Models/Figurinha.php` com hidratação explícita de `id`, `codigo`, `nome`, `selecao`, `edicao_album`, `ano_copa`, `categoria` e `preco_unitario`.
- [X] T008 [P] Definir em `public/index.php` o roteamento mínimo para `/`, `/busca`, `/orcamento`, `/orcamento/adicionar` e `/orcamento/remover`, incluindo `session_start()` antes do uso da sessão.
- [X] T009 [P] Definir helpers de resposta JSON e mensagens seguras em `public/index.php`, com `Content-Type` UTF-8 e códigos HTTP do contrato.
- [X] T010 Documentar em `specs/001-sticker-code-budget/quickstart.md` as fixtures manuais de 2018 e 2022, incluindo `BRA10`, `ARG01`, `FWC15` e categorias Escudo, Estádio e Lendárias.

## Phase 3: User Story 1 - Encontrar figurinha por código (Priority: P1) 🎯 MVP

**Goal**: Permitir buscas exatas e parciais, normalizadas e filtradas opcionalmente por ano, exibindo todos os detalhes necessários do catálogo.

**Independent Test**: Aplicar a migração e fixtures, iniciar `php -S localhost:8080 -t public`, buscar `bra 10` com ano 2022, buscar `fwc` sem ano e confirmar erro para código vazio/ano inválido.

### Implementation for User Story 1

- [X] T011 [P] [US1] Implementar normalização e validação de código/ano em `src/Services/OrcamentoService.php` ou helper compartilhado, removendo espaços e convertendo o código para maiúsculas sem aceitar busca vazia.
- [X] T012 [P] [US1] Implementar `buscarPorCodigo(string $codigo, ?int $ano)` em `src/Repositories/FigurinhaRepository.php` usando apenas SQL parametrizado, `LIKE` para parcial, igualdade para exata e filtro opcional por `ano_copa`.
- [X] T013 [US1] Implementar `buscarPorId(int $id)` em `src/Repositories/FigurinhaRepository.php` para recuperar novamente o item no servidor antes de qualquer adição ao orçamento.
- [X] T014 [US1] Implementar `BuscaController` em `src/Controllers/BuscaController.php` para validar GET, chamar repository e retornar o contrato `GET /busca` definido em `contracts/http-json.md`.
- [X] T015 [US1] Integrar a rota `/busca` de `public/index.php` ao `BuscaController`, tratando entradas inválidas como 400, ausência de resultados como lista vazia e falhas operacionais como 500 seguro.
- [X] T016 [US1] Renderizar os campos de código/ano e a tabela de resultados em `src/Views/home.php`, mostrando nome, seleção, edição, categoria e preço unitário escapados com `htmlspecialchars`.
- [X] T017 [US1] Implementar a busca assíncrona em `public/js/app.js` com `fetch`, estados de carregamento/erro/sem resultado e preservação dos critérios informados.
- [X] T018 [US1] Criar `tests/manual_test_orcamento.php` e verificar nele os cenários de busca, registrando PASS/FAIL para normalização, filtro por ano, correspondência parcial e entradas inválidas.

**Checkpoint**: US1 funciona sem orçamento: uma pessoa localiza itens por código e ano, entende seus detalhes e recebe erros seguros.

## Phase 4: User Story 2 - Montar orçamento temporário (Priority: P1)

**Goal**: Adicionar resultados válidos à sessão, recalcular quantidade/total e impedir mais de cinco unidades por código.

**Independent Test**: A partir de itens encontrados, adicionar itens de preços diferentes, recarregar a página, atingir cinco unidades do mesmo código e confirmar que a sexta tentativa não altera o orçamento.

### Implementation for User Story 2

- [X] T019 [P] [US2] Criar a estrutura inicial de `$_SESSION['orcamento']` em `src/Services/OrcamentoService.php`, com itens agrupados por identificador e total sempre calculado, nunca recebido do cliente.
- [X] T020 [US2] Implementar `adicionar(int $figurinhaId)` em `src/Services/OrcamentoService.php`, validando existência, preço e quantidade antes de mutar a sessão e copiando os dados do catálogo.
- [X] T021 [US2] Implementar o limite de 5 unidades por código e erro de negócio em `src/Services/OrcamentoService.php`, preservando o estado anterior quando o limite for excedido.
- [X] T022 [US2] Implementar remoção de unidade e recálculo de quantidade/valor em `src/Services/OrcamentoService.php`, eliminando o item ao chegar a zero.
- [X] T023 [US2] Implementar `OrcamentoController` em `src/Controllers/OrcamentoController.php` para `GET /orcamento`, `POST /orcamento/adicionar` e `POST /orcamento/remover` conforme `contracts/http-json.md`.
- [X] T024 [US2] Integrar as rotas de orçamento em `public/index.php`, validando JSON de entrada, método HTTP, identificador inteiro e status 400/404/409/422/500.
- [X] T025 [US2] Renderizar o painel de orçamento em `src/Views/home.php` com itens, quantidade por código, quantidade total, valor total e estado vazio em `R$ 0,00`.
- [X] T026 [US2] Implementar em `public/js/app.js` os botões de adicionar/remover e atualização assíncrona do painel após sucesso ou erro, incluindo mensagem do limite de cinco.
- [X] T027 [US2] Criar `tests/manual_test_orcamento.php` com verificações de soma por unidade, múltiplos preços, limite cinco, remoção, orçamento vazio e falha sem mutação.

**Checkpoint**: US2 funciona com dados de busca já existentes e entrega um orçamento temporário completo, sem login ou persistência no banco.

## Phase 5: User Story 3 - Distinguir categorias e controlar entradas (Priority: P2)

**Goal**: Garantir que categorias usem seus próprios preços e que itens inválidos, preços inválidos e falhas de sessão não corrompam o orçamento.

**Independent Test**: Consultar Escudo, Estádio e Lendárias com preços diferentes, adicionar cada uma, tentar um identificador inexistente/preço inválido e verificar que o estado anterior permanece.

### Implementation for User Story 3

- [X] T028 [P] [US3] Reforçar validação de categoria e `preco_unitario` em `src/Services/OrcamentoService.php`, rejeitando nulo, negativo ou não numérico com erro 422.
- [X] T029 [P] [US3] Ajustar a hidratação e a formatação monetária em `src/Models/Figurinha.php` para preservar duas casas decimais e evitar arredondamentos intermediários indevidos.
- [X] T030 [US3] Garantir em `src/Repositories/FigurinhaRepository.php` que consultas não usem `WITH`, `ROW_NUMBER`, window functions ou `utf8mb4_0900_ai_ci`, e que o filtro por edição/ano permaneça compatível com MySQL 5.7.
- [X] T031 [US3] Exibir categoria e preço específicos por resultado em `src/Views/home.php` e garantir que textos do catálogo sejam escapados antes da renderização.
- [X] T032 [US3] Tratar em `src/Controllers/OrcamentoController.php` falhas de item inexistente, sessão indisponível e exceções operacionais sem substituir silenciosamente o orçamento existente.
- [X] T033 [US3] Atualizar `public/js/app.js` para diferenciar erros de validação, item indisponível, limite atingido e falha operacional sem exibir detalhes internos.
- [X] T034 [US3] Executar no roteiro `tests/manual_test_orcamento.php` os casos de categorias com preços distintos, preço ausente/negativo, item inexistente, sessão com orçamento prévio e resposta segura.

**Checkpoint**: Todas as histórias estão testáveis de forma independente após a fundação; o cálculo usa o preço da própria categoria e entradas inválidas não mutam a sessão.

## Phase 6: Polish & Cross-Cutting Concerns

**Purpose**: Fechar segurança, documentação, desempenho e aceitação manual do módulo completo.

- [X] T035 [P] Revisar `public/index.php`, `src/Controllers/BuscaController.php`, `src/Controllers/OrcamentoController.php` e `src/Views/home.php` para confirmar `declare(strict_types=1);`, validação na fronteira, `htmlspecialchars` e `json_encode` sem dados não escapados.
- [X] T036 [P] Revisar `config/database.php` e `src/Repositories/FigurinhaRepository.php` para confirmar PDO parametrizado, charset/collation corretos e ausência de recursos MySQL 8.
- [X] T037 [P] Atualizar `specs/001-sticker-code-budget/quickstart.md` com qualquer ajuste necessário nos comandos, fixtures ou mensagens observadas durante a validação.
- [X] T038 Executar todos os cenários do [quickstart.md](quickstart.md) no navegador após disponibilizar MySQL 5.7 e registrar o resultado manual do módulo completo.
- [X] T039 Executar 10 buscas exatas no ambiente local após disponibilizar MySQL 5.7 e confirmar que pelo menos 95% terminam em até 2 segundos, registrando a observação no relatório da entrega.
- [X] T040 Confirmar por `git status` que não foram adicionados PHPUnit, Composer, frameworks pesados ou arquivos de credenciais em `vendor/`/repositório.

## Dependencies & Execution Order

### Phase Dependencies

- **Setup (Phase 1)**: Sem dependências; T002, T003 e T004 podem ser executadas em paralelo após T001.
- **Foundational (Phase 2)**: Depende de T001; T005, T007, T008 e T009 podem avançar em paralelo, mas as histórias aguardam T005-T010.
- **User Stories**: US1 e US2 são P1, mas US2 depende de T013 e da infraestrutura de US1 para selecionar um item; US3 depende de US2 para validar o estado e os cálculos do orçamento.
- **Polish (Phase 6)**: Depende dos checkpoints das três histórias.

### User Story Dependencies

- **US1 (P1)**: Começa após Phase 2; é o MVP de busca e não depende de outra história.
- **US2 (P1)**: Começa após Phase 2 e requer o repository/model de US1; sua validação principal pode ser executada independentemente com fixtures conhecidas.
- **US3 (P2)**: Começa após US2, pois amplia as validações e confirma a integridade do orçamento em falhas.

### Parallel Opportunities

- **Setup**: T002, T003 e T004 após T001.
- **Fundação**: T005, T007, T008 e T009 em arquivos diferentes; T010 após a migração T005.
- **US1**: T011/T012 em paralelo; T016/T017 podem ocorrer em paralelo após o contrato de busca T014.
- **US2**: T019 e T023 podem iniciar em paralelo após T013; T025 e T026 podem ocorrer em paralelo após o contrato do controller.
- **US3**: T028, T029 e T030 em paralelo; T031 e T033 podem ocorrer em paralelo após suas bases.
- **Polish**: T035, T036 e T037 em paralelo; T038-T040 após todas as revisões.

## Parallel Example: User Story 1

```text
Task: "T011 [P] [US1] Implementar normalização e validação em src/Services/OrcamentoService.php"
Task: "T012 [P] [US1] Implementar busca PDO em src/Repositories/FigurinhaRepository.php"

Depois de T014:
Task: "T016 [US1] Renderizar resultados em src/Views/home.php"
Task: "T017 [US1] Implementar fetch em public/js/app.js"
```

## Parallel Example: User Story 2

```text
Task: "T019 [P] [US2] Criar estado da sessão em src/Services/OrcamentoService.php"
Task: "T023 [US2] Implementar endpoints em src/Controllers/OrcamentoController.php"

Depois de T023:
Task: "T025 [US2] Renderizar painel em src/Views/home.php"
Task: "T026 [US2] Atualizar painel com fetch em public/js/app.js"
```

## Implementation Strategy

### MVP First (User Story 1 Only)

1. Completar Setup e Foundational.
2. Implementar US1 para busca exata, parcial, normalizada e filtrada por ano.
3. Executar o teste independente de US1 no navegador.
4. Parar para validar o MVP de descoberta de figurinhas antes de iniciar o orçamento.

### Incremental Delivery

1. Entregar US1 como busca pública funcional.
2. Adicionar US2 para orçamento temporário, limite cinco e cálculo dinâmico.
3. Adicionar US3 para categorias, preços inválidos e falhas sem mutação.
4. Executar Polish e a validação completa do quickstart.

## Notes

- Toda tarefa segue o formato `- [ ] T### [P?] [US#?] descrição com caminho`.
- `[P]` aparece somente quando a tarefa pode trabalhar em arquivo separado sem dependência incompleta.
- O script manual substitui testes automatizados e deve ser executado por CLI ou navegador, conforme a constituição vigente.
