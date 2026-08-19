# Data Model: Busca por Código e Orçamento Automático

## Figurinha

Representa uma figurinha catalogada.

| Campo | Tipo lógico | Obrigatório | Regras |
|---|---|---:|---|
| `id` | inteiro | sim | Identificador positivo e único. |
| `codigo` | texto | sim | Código oficial normalizado; índice para busca. |
| `nome` | texto | sim | Nome do jogador ou item. |
| `selecao` | texto | sim | Seleção associada; pode ser vazio apenas para itens sem seleção aplicável. |
| `edicao_album` | texto | sim | Edição legível, por exemplo Copa 2022; índice para filtro. |
| `ano_copa` | inteiro | sim | Ano válido da edição. |
| `categoria` | texto | sim | Categoria comercial, como Escudo, Estádio ou Lendárias. |
| `preco_unitario` | decimal monetário | sim | Maior ou igual a zero; não pode ser nulo para adicionar ao orçamento. |

**Índices**: índice em `codigo`; índice em `edicao_album`; índice combinado opcional em `ano_copa, codigo` somente se a validação manual mostrar necessidade.

## Orçamento Temporário

Estado associado à sessão PHP do visitante.

| Campo | Tipo lógico | Regras |
|---|---|---|
| `itens` | mapa por `id` ou código + edição | Cada chave representa um item válido encontrado no catálogo. |
| `itens[*].figurinha_id` | inteiro | Deve corresponder a uma figurinha retornada pelo repository. |
| `itens[*].codigo` | texto | Preservado para exibição e limite por código. |
| `itens[*].preco_unitario` | decimal monetário | Copiado do catálogo após validação, nunca confiado ao cliente. |
| `itens[*].quantidade` | inteiro | Entre 1 e 5 por código dentro do orçamento. |
| `quantidade_total` | inteiro calculado | Soma das quantidades; não é fonte persistida separada. |
| `valor_total` | decimal calculado | Soma de `preco_unitario * quantidade`; recalculado no service. |

## Relacionamentos

- Uma `Figurinha` pertence a uma `Edição do Álbum` e a uma `Categoria de Figurinha` por seus atributos de edição, ano e categoria.
- Um `Orçamento Temporário` contém zero ou mais `Figurinhas` agrupadas por código/edição.
- O catálogo é persistente; o orçamento é apenas de sessão e não cria relacionamento permanente.

## Regras e transições

1. Busca: entrada recebida -> normalizar código e validar ano -> consultar catálogo -> retornar zero ou mais figurinha(s).
2. Adição: resultado válido -> buscar novamente por identificador no servidor -> validar preço/quantidade -> inserir ou incrementar -> recalcular totais.
3. Limite: quantidade 1..4 pode avançar uma unidade; quantidade 5 permanece em 5 e gera erro de negócio.
4. Falha: qualquer erro de validação ou persistência deixa o estado anterior do orçamento intacto.
5. Sessão expirada: o orçamento desaparece; nenhum dado é gravado no banco.
