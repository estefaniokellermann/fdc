(() => {
    const searchForm = document.querySelector('#search-form');
    const results = document.querySelector('#results');
    const searchMessage = document.querySelector('#search-message');
    const budget = document.querySelector('#budget');
    const budgetCount = document.querySelector('#budget-count');
    const toastRegion = document.querySelector('#toast-region');
    const budgetPanel = document.querySelector('.budget-panel');

    const escapeHtml = (value) => String(value)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');

    const showMessage = (message, type = '') => {
        searchMessage.textContent = message;
        searchMessage.className = type;
    };

    const showToast = (message, type = '') => {
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        toast.textContent = message;
        toastRegion.appendChild(toast);
        window.setTimeout(() => toast.remove(), 4000);
    };

    const categoryClass = (category) => ({
        'Escudo': 'category-shield',
        'Estádio': 'category-stadium',
        'Lendárias': 'category-legendary',
    }[category] || 'category-common');

    const categoryLabel = (category) => category || 'Comum';

    const renderBudget = (payload) => {
        const current = payload.orcamento || payload;
        const items = Array.isArray(current.itens) ? current.itens : Object.values(current.itens || {});
        budgetCount.textContent = current.quantidade_total ?? 0;
        if (items.length === 0) {
            budget.innerHTML = '<div class="empty-budget"><p>Seu orçamento está vazio.</p><strong>Total: R$ 0,00</strong></div>';
            return;
        }
        budget.innerHTML = items.map((item) => `<div class="budget-item"><strong>${escapeHtml(item.codigo)}</strong><span class="category-badge ${categoryClass(item.categoria)}">${escapeHtml(categoryLabel(item.categoria))}</span><div class="budget-meta">${escapeHtml(item.nome)} · ${item.quantidade} × R$ ${escapeHtml(item.preco_unitario)}</div><button class="action-button" data-remove="${item.figurinha_id}">Remover unidade</button></div>`).join('')
            + `<div class="budget-summary"><span>Total acumulado</span><strong class="budget-total">R$ ${escapeHtml(current.valor_total)}</strong></div>`;
        budget.querySelectorAll('[data-remove]').forEach((button) => button.addEventListener('click', () => removeItem(button.dataset.remove, button)));
    };

    const loadBudget = async () => {
        const response = await fetch('/orcamento');
        renderBudget(await response.json());
    };

    const setButtonLoading = (button, loading) => {
        if (!button) return;
        button.disabled = loading;
        button.classList.toggle('is-loading', loading);
        if (loading) { button.dataset.originalText = button.textContent; button.textContent = 'Adicionando...'; }
        else { button.textContent = button.dataset.originalText || 'Adicionar ao orçamento'; }
    };

    const addItem = async (id, button) => {
        setButtonLoading(button, true);
        try {
            const response = await fetch('/orcamento/adicionar', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ figurinha_id: Number(id) }) });
            const payload = await response.json();
            if (response.ok) { renderBudget(payload); showToast('Figurinha adicionada ao orçamento.', 'success'); button.textContent = 'Adicionado'; window.setTimeout(() => setButtonLoading(button, false), 1500); }
            else { showToast(payload.mensagem || 'Não foi possível adicionar.', 'error'); setButtonLoading(button, false); }
        } catch (error) {
            showToast('Não foi possível atualizar o orçamento.', 'error');
            setButtonLoading(button, false);
        }
    };

    const removeItem = async (id, button) => {
        setButtonLoading(button, true);
        try {
            const response = await fetch('/orcamento/remover', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ figurinha_id: Number(id) }) });
            const payload = await response.json();
            if (response.ok) { renderBudget(payload); showToast('Unidade removida do orçamento.', 'success'); }
            else showToast(payload.mensagem || 'Não foi possível remover.', 'error');
        } catch (error) {
            showToast('Não foi possível atualizar o orçamento.', 'error');
        }
        setButtonLoading(button, false);
    };

    searchForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        results.innerHTML = '';
        showMessage('Buscando...');
        const params = new URLSearchParams({ codigo: searchForm.codigo.value, ano: searchForm.ano.value });
        try {
            const response = await fetch(`/busca?${params}`);
            const payload = await response.json();
            if (!response.ok) { showMessage(payload.mensagem || 'Busca inválida.', 'error'); return; }
            if (!payload.resultados.length) { showMessage('Nenhuma figurinha encontrada.'); results.innerHTML = '<div class="empty-state"><div class="empty-mark">⌁</div><h3>Nenhum resultado por aqui</h3><p>Tente um código popular para continuar.</p><div class="suggestions"><button type="button" data-suggestion="BRA10">BRA10</button><button type="button" data-suggestion="ARG01">ARG01</button></div></div>'; bindSuggestions(); return; }
            showMessage(`${payload.resultados.length} resultado(s) encontrado(s).`, 'success');
            results.innerHTML = `<table><thead><tr><th>Código</th><th>Item</th><th>Edição</th><th>Categoria</th><th>Preço</th><th></th></tr></thead><tbody>${payload.resultados.map((item) => `<tr><td>${escapeHtml(item.codigo)}</td><td class="result-name">${escapeHtml(item.nome)}<br>${escapeHtml(item.selecao)}</td><td>${escapeHtml(item.edicao_album)}</td><td><span class="category-badge ${categoryClass(item.categoria)}">${escapeHtml(categoryLabel(item.categoria))}</span></td><td>R$ ${escapeHtml(item.preco_unitario)}</td><td><button class="action-button" data-add="${item.id}">Adicionar ao orçamento</button></td></tr>`).join('')}</tbody></table>`;
            results.querySelectorAll('[data-add]').forEach((button) => button.addEventListener('click', () => addItem(button.dataset.add, button)));
        } catch (error) {
            showToast('Não foi possível realizar a busca.', 'error');
        }
    });

    const bindSuggestions = () => document.querySelectorAll('[data-suggestion]').forEach((button) => button.addEventListener('click', () => { searchForm.codigo.value = button.dataset.suggestion; searchForm.requestSubmit(); }));
    bindSuggestions();
    budgetPanel.addEventListener('click', (event) => { if (window.innerWidth < 900 && !event.target.closest('button')) budgetPanel.classList.toggle('is-expanded'); });

    loadBudget().catch(() => { budget.textContent = 'Não foi possível carregar o orçamento.'; });
})();
