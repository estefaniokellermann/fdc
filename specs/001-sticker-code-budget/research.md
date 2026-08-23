# Research: Busca por Código e Orçamento Automático

## Decisão: PHP nativo e zero dependency

- **Decision**: Usar PHP 8.2+ com recursos nativos, PDO, `$_SESSION`, `json_encode` e `htmlspecialchars`, sem frameworks ou pacotes novos em `vendor/`.
- **Rationale**: Atende à diretriz do pedido, preserva o projeto pequeno e satisfaz os princípios de Minimal Design e Single Responsibility.
- **Alternatives considered**: Framework MVC completo, ORM e bibliotecas de frontend. Foram rejeitados por adicionarem dependências e complexidade sem necessidade para o módulo inicial.

## Decisão: Camadas MVC leves

- **Decision**: Separar `Controllers`, `Services`, `Repositories`, `Models` e `Views`, com controllers responsáveis por HTTP, services por regras e repositories por SQL.
- **Rationale**: Mantém contratos explícitos, facilita validação manual por camada e evita que a sessão ou SQL se espalhem pela interface.
- **Alternatives considered**: Um único script PHP com consulta e HTML. Foi rejeitado por misturar responsabilidades e dificultar a verificação de erros.

## Decisão: Busca compatível com MySQL 5.7

- **Decision**: Normalizar o código removendo espaços e convertendo para maiúsculas antes da consulta; usar `LIKE` parametrizado para busca parcial e igualdade para correspondência exata, sem CTEs ou window functions.
- **Rationale**: Implementa `bra 10` = `BRA10`, mantém compatibilidade com MySQL 5.7 e permite priorizar exatos sem recursos recentes.
- **Alternatives considered**: Normalização apenas no banco ou recursos de ordenação do MySQL 8. Foram rejeitados por dependerem de colunas/recursos não garantidos no ambiente.

## Decisão: Orçamento na sessão

- **Decision**: Armazenar no máximo código, item, preço unitário e quantidade em `$_SESSION['orcamento']`; recalcular totais no service após cada mutação.
- **Rationale**: O orçamento não é persistente, não exige login e deve sobreviver a recarregamentos dentro da sessão sem duplicar a fonte de verdade.
- **Alternatives considered**: Persistir carrinho no banco ou confiar no total enviado pelo navegador. Ambos violam o escopo ou permitem valores manipulados pelo cliente.

## Decisão: Validação manual

- **Decision**: Usar `tests/manual_test_orcamento.php` e cenários documentados no quickstart, executáveis por CLI ou navegador, sem PHPUnit.
- **Rationale**: A diretriz do usuário e a constituição proíbem suíte automatizada; scripts manuais verificam cálculo, limite, normalização e mensagens sem nova dependência.
- **Alternatives considered**: PHPUnit e runners automatizados. Rejeitados por conflito direto com a constituição vigente.

## Decisão: Contratos JSON pequenos

- **Decision**: Expor busca e ações de orçamento como endpoints HTTP JSON com respostas de sucesso/erro e status apropriados.
- **Rationale**: Permite `fetch` assíncrono sem introduzir framework e mantém entradas, saídas e falhas explícitas na fronteira.
- **Alternatives considered**: Requisições HTML completas para cada ação. Foram rejeitadas por piorarem a atualização dinâmica exigida pelo módulo.

## Decisão: UI mobile-first sem dependências

- **Decision**: Implementar o tema e as micro-interações em CSS nativo, com breakpoints responsivos, badges por categoria, toasts e uma barra de orçamento fixa no rodapé em telas pequenas.
- **Rationale**: Atende FR-015 a FR-019 e TC-008 a TC-010 sem introduzir framework visual, CDN ou pacote de terceiros; mantém o controle do layout e da acessibilidade dentro do projeto.
- **Alternatives considered**: Tailwind/Bootstrap via CDN e bibliotecas de toast/spinner. Foram rejeitados por aumentarem dependências de runtime e não serem necessários para os estados definidos.

## Decisão: Feedback assíncrono idempotente

- **Decision**: Desabilitar o botão durante cada requisição, atualizar o painel apenas após resposta válida, mostrar confirmação por até 1,5 segundo e usar toasts para eventos rápidos.
- **Rationale**: Evita cliques duplicados, comunica latência e garante zero page reload sem confiar no estado calculado pelo navegador.
- **Alternatives considered**: Atualização otimista permanente do total. Foi rejeitada porque poderia exibir preço/quantidade divergentes em falhas de rede ou limite de negócio.
