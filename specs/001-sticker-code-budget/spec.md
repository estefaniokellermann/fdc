# Feature Specification: Busca por Código e Orçamento Automático de Figurinhas

**Feature Branch**: `001-sticker-code-budget`

**Created**: 2026-08-18

**Status**: Draft

**Input**: User description: "Criar a especificação funcional e técnica para o módulo inicial de Busca por Código e Orçamento Automático de Figurinhas do portal Figurinhas das Copas."

## User Scenarios & Testing *(mandatory)*

### User Story 1 - Encontrar figurinha por código (Priority: P1)

Como colecionador iniciante ou experiente, quero buscar uma figurinha pelo código oficial e pelo ano da Copa para identificar rapidamente o item e seu preço.

**Why this priority**: A busca é a entrada principal do módulo e entrega valor mesmo sem montar um orçamento.

**Independent Test**: Com uma base contendo códigos de diferentes edições, informar um código exato, parcial ou formatado com espaços e selecionar um ano; a listagem deve mostrar somente resultados compatíveis e seus detalhes.

**Acceptance Scenarios**:

1. **Given** que existem itens dos álbuns de 2018 e 2022, **When** o visitante informa `bra 10` e seleciona 2022, **Then** o sistema normaliza a entrada e apresenta o item correspondente de 2022, incluindo nome, seleção, edição, categoria e preço unitário.
2. **Given** que existem vários itens cujo código começa com `FWC`, **When** o visitante informa `fwc`, **Then** o sistema apresenta os resultados compatíveis sem diferenciar maiúsculas, minúsculas ou espaços.
3. **Given** que nenhum item corresponde ao código e ano informados, **When** o visitante executa a busca, **Then** o sistema informa que não encontrou resultados e preserva os critérios usados.
4. **Given** que o campo de código está vazio ou contém somente espaços, **When** o visitante tenta buscar, **Then** o sistema solicita um código válido sem executar uma busca ampla não solicitada.

### User Story 2 - Montar orçamento temporário (Priority: P1)

Como colecionador, quero adicionar itens encontrados a uma seleção temporária para saber quanto custará o conjunto antes de decidir pela compra.

**Why this priority**: O orçamento transforma a identificação do item em uma ação útil e é o segundo objetivo central do módulo.

**Independent Test**: Encontrar itens de categorias e preços distintos, adicioná-los ao orçamento e conferir a quantidade de unidades e a soma exibida após cada ação.

**Acceptance Scenarios**:

1. **Given** que um item foi encontrado, **When** o visitante seleciona "Adicionar ao Orçamento", **Then** o item aparece no painel do orçamento com código, descrição, categoria, preço unitário e quantidade 1.
2. **Given** que o orçamento contém itens com preços diferentes, **When** o visitante adiciona outro item, **Then** o painel atualiza a quantidade total e o valor total como a soma dos preços unitários de todas as unidades.
3. **Given** que o visitante adiciona novamente um código já presente, **When** a quantidade ainda é menor que 5, **Then** a quantidade daquele código aumenta em uma unidade e o total é recalculado.
4. **Given** que um código já possui 5 unidades no orçamento, **When** o visitante tenta adicioná-lo novamente, **Then** o sistema rejeita a ação, mantém a quantidade em 5 e informa o limite aplicável.
5. **Given** que o visitante recarrega a página durante a mesma sessão, **When** o painel é exibido, **Then** o orçamento temporário continua disponível e com os mesmos valores.

### User Story 3 - Distinguir categorias e controlar entradas (Priority: P2)

Como responsável pelo portal, quero que cada item use o preço de sua categoria e que entradas inválidas não alterem o orçamento.

**Why this priority**: Preços por categoria são necessários para representar corretamente o catálogo, enquanto a validação evita totais incorretos.

**Independent Test**: Consultar itens classificados como Escudo, Estádio e Lendárias, confirmar preços distintos quando cadastrados, e tentar adicionar um item inexistente ou uma resposta inválida.

**Acceptance Scenarios**:

1. **Given** que itens de Escudo, Estádio e Lendárias têm preços cadastrados, **When** são exibidos nos resultados, **Then** cada item mostra o preço unitário associado à sua própria categoria.
2. **Given** que o item solicitado não existe ou não está disponível para o ano indicado, **When** o visitante tenta adicioná-lo, **Then** o sistema rejeita a operação e não insere dados arbitrários no orçamento.
3. **Given** que a consulta ou a sessão temporária falha, **When** o visitante executa a ação, **Then** recebe uma mensagem clara e o orçamento já existente não é silenciosamente substituído.

### Edge Cases

- Códigos com espaços no início, no fim ou entre letras e números devem ser comparados após normalização.
- A busca deve tratar entradas em maiúsculas e minúsculas como equivalentes.
- Um código parcial que corresponda a muitos itens deve retornar uma lista navegável, sem misturar edições quando o ano for informado.
- Um ano não suportado ou em formato inválido deve gerar validação clara e não uma busca ambígua.
- Preço ausente, negativo ou inválido no catálogo não pode ser usado para formar um orçamento; o item deve ser sinalizado como indisponível.
- A quantidade máxima de 5 unidades se aplica individualmente a cada código, enquanto códigos diferentes podem coexistir no mesmo orçamento.
- O orçamento vazio deve exibir quantidade zero e total igual a R$ 0,00.
- O formato monetário deve ser consistente em resultados e total, usando duas casas decimais e a convenção brasileira.
- O encerramento ou expiração da sessão deve descartar o orçamento temporário, sem criar persistência permanente.

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: O sistema MUST disponibilizar na página inicial uma busca pública por código oficial de figurinha.
- **FR-002**: O sistema MUST aceitar código exato ou parcial e comparar a entrada sem diferenciar maiúsculas, minúsculas ou espaços.
- **FR-003**: O sistema MUST permitir informar o ano da Copa como critério de busca opcional e restringir os resultados ao ano selecionado quando informado.
- **FR-004**: O sistema MUST exibir, para cada resultado, o nome do jogador ou item, a seleção, a edição do álbum, a categoria ou tipo e o preço unitário.
- **FR-005**: O sistema MUST informar claramente quando a entrada for inválida, quando não houver resultados ou quando ocorrer uma falha operacional.
- **FR-006**: O sistema MUST permitir adicionar um resultado válido ao orçamento temporário do visitante.
- **FR-007**: O orçamento MUST manter seus itens na sessão temporária do visitante e MUST NOT exigir autenticação ou persistência em banco nesta fase.
- **FR-008**: O painel de orçamento MUST exibir a quantidade total de unidades, os itens selecionados, a quantidade por código e o valor total acumulado.
- **FR-009**: O sistema MUST calcular o valor total como a soma do preço unitário de cada unidade selecionada.
- **FR-010**: O sistema MUST aplicar o preço unitário cadastrado para a categoria do item, incluindo categorias com valores distintos como Escudo, Estádio e Lendárias.
- **FR-011**: O sistema MUST limitar cada código a no máximo 5 unidades por orçamento e informar a rejeição quando o limite for atingido.
- **FR-012**: O sistema MUST preventivamente validar código, ano, identificação do item, categoria, preço e quantidade antes de alterar o orçamento.
- **FR-013**: O sistema MUST manter o orçamento existente intacto quando uma operação de adição falhar.
- **FR-014**: O módulo MUST NOT include login, checkout, gateway de pagamento, frete ou persistência do carrinho em banco nesta fase.

### Technical Constraints

- **TC-001**: A implementação deve usar PHP 8.2 ou superior com `declare(strict_types=1);`.
- **TC-002**: A organização deve seguir MVC leve ou camadas equivalentes, separando Controllers, Services e Repositories.
- **TC-003**: O armazenamento deve ser compatível com MySQL 5.7.23-23, usando charset `utf8mb4`, collation `utf8mb4_unicode_ci` e consultas parametrizadas via PDO.
- **TC-004**: Consultas não podem usar CTEs (`WITH`), funções de janela como `ROW_NUMBER` ou collations exclusivas do MySQL 8, incluindo `utf8mb4_0900_ai_ci`.
- **TC-005**: A interface deve usar HTML5, Tailwind CSS ou Bootstrap 5 e JavaScript ES6+ assíncrono via `fetch`, mantendo a busca e atualização do orçamento responsivas.
- **TC-006**: A camada de Services deve ser coberta por PHPUnit conforme solicitado para esta funcionalidade. A constituição atual do projeto proíbe testes automatizados; o planejamento deve obter aprovação para emendar essa regra ou registrar formalmente a exceção antes de adicionar os testes.
- **TC-007**: Entradas externas devem ser normalizadas e validadas na fronteira, e erros devem retornar mensagens acionáveis sem expor detalhes internos.

### Key Entities *(include if feature involves data)*

- **Figurinha**: Item catalogado por código oficial, ano/edição do álbum, nome, seleção, categoria e preço unitário.
- **Categoria de Figurinha**: Classificação comercial do item, como Escudo, Estádio ou Lendárias, associada à regra de preço aplicável.
- **Orçamento Temporário**: Seleção do visitante durante a sessão, composta por códigos, quantidades, preços unitários e valor total calculado.
- **Edição do Álbum**: Copa e ano que identificam a coleção à qual uma figurinha pertence.

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: Pelo menos 95% das buscas válidas por código exato retornam o item correto em até 2 segundos em condições normais de uso.
- **SC-002**: Pelo menos 90% das buscas realizadas com variações de espaços e maiúsculas/minúsculas retornam o mesmo conjunto de resultados da entrada normalizada.
- **SC-003**: Um colecionador consegue localizar um item e adicioná-lo ao orçamento em até 30 segundos, sem login.
- **SC-004**: Em 100% das tentativas de ultrapassar 5 unidades do mesmo código, o orçamento permanece limitado a 5 unidades e o visitante recebe uma explicação clara.
- **SC-005**: Em 100% dos orçamentos válidos, o total exibido corresponde à soma dos preços unitários de todas as unidades selecionadas, incluindo categorias com preços diferentes.
- **SC-006**: Pelo menos 90% dos usuários de teste conseguem interpretar a quantidade total e o valor acumulado sem assistência adicional.
- **SC-007**: Nenhuma funcionalidade desta fase exige login, checkout, frete ou gravação permanente do carrinho no banco de dados.

## Assumptions

- O catálogo de figurinhas e seus preços por categoria estarão disponíveis e mantidos por um processo administrativo ou carga de dados fora desta fase.
- O ano da Copa é um filtro opcional; quando omitido, resultados de edições compatíveis com o código podem ser apresentados e devem identificar claramente seu ano.
- A busca parcial retorna resultados ordenados de forma previsível, priorizando correspondência exata antes de correspondências parciais.
- O orçamento representa intenção de compra e não reserva estoque, garantia de disponibilidade ou oferta vinculante.
- A sessão PHP do visitante é suficiente para manter o orçamento durante sua validade; não há sincronização entre dispositivos.
- Valores monetários são armazenados e calculados com precisão adequada para moeda, sem arredondamentos intermediários indevidos.
- O suporte a PHPUnit depende de uma decisão de governança sobre a atual proibição de testes automatizados na constituição.

## Out of Scope

- Autenticação, login, contas de colecionador e sincronização entre dispositivos.
- Persistência do orçamento ou carrinho no banco de dados.
- Checkout, gateway de pagamento, reserva de estoque, emissão de pedido e cálculo de frete.
- Cadastro, edição ou administração do catálogo de figurinhas.
