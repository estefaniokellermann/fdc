<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#07131a">
    <title>Figurinhas das Copas | Busca e orçamento</title>
    <link rel="stylesheet" href="/css/app.css">
</head>
<body>
    <div class="site-shell">
        <header class="site-header">
            <p class="eyebrow">Figurinhas das Copas</p>
            <h1>Encontre a figurinha. Monte seu jogo.</h1>
            <p class="intro">Busque pelo código oficial e veja seu orçamento se formar em tempo real.</p>
        </header>
        <main class="page-shell">
            <section class="search-panel" aria-labelledby="search-title">
                <div class="section-heading">
                    <div>
                        <p class="eyebrow">Catálogo</p>
                        <h2 id="search-title">Buscar figurinha</h2>
                    </div>
                    <span class="live-indicator"><span></span> sessão ativa</span>
                </div>
            <form id="search-form">
                <div class="form-field form-field-code">
                    <label for="codigo">Código oficial</label>
                    <input id="codigo" name="codigo" autocomplete="off" placeholder="Ex.: BRA10" required>
                </div>
                <div class="form-field form-field-year">
                    <label for="ano">Ano <span>(opcional)</span></label>
                    <input id="ano" name="ano" inputmode="numeric" placeholder="2022">
                </div>
                <button class="primary-button" type="submit"><span class="button-icon">⌕</span> Buscar</button>
            </form>
            <p id="search-message" role="status"></p>
            <div id="results" aria-live="polite">
                <div class="empty-state empty-state-initial">
                    <div class="empty-mark">✦</div>
                    <h3>Qual figurinha você procura?</h3>
                    <p>Digite um código para começar sua seleção.</p>
                    <div class="suggestions"><button type="button" data-suggestion="BRA10">BRA10</button><button type="button" data-suggestion="ARG01">ARG01</button></div>
                </div>
            </div>
            </section>
            <aside class="budget-panel" aria-labelledby="budget-title">
                <div class="section-heading">
                    <div>
                        <p class="eyebrow">Sua seleção</p>
                        <h2 id="budget-title">Orçamento</h2>
                    </div>
                    <span id="budget-count" class="count-badge">0</span>
                </div>
                <div id="budget" aria-live="polite">Carregando...</div>
            </aside>
        </main>
        <div id="toast-region" class="toast-region" aria-live="polite" aria-atomic="true"></div>
        <footer class="site-footer">Catálogo para colecionadores <span>•</span> valores em reais</footer>
    </div>
    <script src="/js/app.js" defer></script>
</body>
</html>
