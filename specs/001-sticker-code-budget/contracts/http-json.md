# HTTP Contracts: Busca e Orçamento

Os endpoints retornam `Content-Type: application/json; charset=utf-8`. O cliente envia entradas em UTF-8 e não envia preço ou total como autoridade.

## `GET /busca`

**Query**:

- `codigo` (obrigatório): texto exato ou parcial; espaços são normalizados.
- `ano` (opcional): ano numérico da Copa.

**Sucesso `200`**:

```json
{
  "sucesso": true,
  "resultados": [
    {
      "id": 10,
      "codigo": "BRA10",
      "nome": "Nome do item",
      "selecao": "Brasil",
      "edicao_album": "Copa 2022",
      "ano_copa": 2022,
      "categoria": "Escudo",
      "preco_unitario": "3.50"
    }
  ]
}
```

**Erros**:

- `400`: código vazio ou ano inválido.
- `200` com `resultados: []`: busca válida sem correspondências.
- `500`: falha operacional sem detalhes internos.

## `POST /orcamento/adicionar`

**Body JSON**:

```json
{ "figurinha_id": 10 }
```

**Sucesso `200`**:

```json
{
  "sucesso": true,
  "mensagem": "Figurinha adicionada ao orçamento.",
  "orcamento": {
    "itens": [],
    "quantidade_total": 1,
    "valor_total": "3.50"
  }
}
```

**Erros**:

- `400`: JSON ausente ou identificador inválido.
- `404`: figurinha não encontrada ou indisponível.
- `409`: limite de 5 unidades para o código atingido.
- `422`: preço/categoria inválidos para orçamento.
- `500`: falha operacional; orçamento anterior deve permanecer intacto.

## `POST /orcamento/remover`

**Body JSON**: `{ "figurinha_id": 10 }`.

**Sucesso `200`**: `{ "sucesso": true, "orcamento": { ... } }` com estado recalculado. Remover a última unidade elimina o item.

## `GET /orcamento`

**Sucesso `200`**: retorna o orçamento atual da sessão, com `quantidade_total: 0` e `valor_total: "0.00"` quando vazio.

## Regras de segurança e apresentação

- Controllers validam método, entrada e formato antes de chamar services.
- Views escapam texto com `htmlspecialchars`; JSON é serializado apenas por `json_encode`.
- Mensagens expostas são estáveis e acionáveis; exceções internas são registradas pelo mecanismo disponível sem serem retornadas ao cliente.

## Contrato de estados da interface

- Enquanto `GET /busca` aguarda resposta, a área de resultados exibe estado de carregamento e preserva os critérios informados.
- Para `resultados: []`, a interface exibe empty state com sugestões acionáveis, sem tratar a ausência como erro técnico.
- Durante `POST /orcamento/adicionar`, o botão correspondente fica em loading e não aceita cliques duplicados; no sucesso, mostra confirmação temporária por até 1,5 segundo.
- Respostas `409`, `404`, `422` e `500` são exibidas como toast flutuante com mensagem acionável; o total anterior permanece visível quando a mutação falha.
- Em viewport móvel, o resumo de orçamento permanece em barra fixa no rodapé e não pode cobrir o formulário, resultados ou controles de remoção.
