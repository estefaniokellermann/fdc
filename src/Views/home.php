<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Figurinhas das Copas</title>
    <link rel="stylesheet" href="/css/app.css">
</head>
<body>
    <main class="page-shell">
        <section class="search-panel">
            <h1>Busca de figurinhas</h1>
            <form id="search-form">
                <label for="codigo">Código</label>
                <input id="codigo" name="codigo" autocomplete="off" placeholder="Ex.: BRA10" required>
                <label for="ano">Ano da Copa</label>
                <input id="ano" name="ano" inputmode="numeric" placeholder="Ex.: 2022">
                <button type="submit">Buscar</button>
            </form>
            <p id="search-message" role="status"></p>
            <div id="results" aria-live="polite"></div>
        </section>
        <aside class="budget-panel">
            <h2>Orçamento</h2>
            <div id="budget" aria-live="polite">Carregando...</div>
        </aside>
    </main>
    <script src="/js/app.js" defer></script>
</body>
</html>
