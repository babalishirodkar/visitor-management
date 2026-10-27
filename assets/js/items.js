/* Add / remove item rows in material forms */
document.addEventListener('click', e => {
  const add = e.target.closest('[data-add-item]');
  if (add) {
    const tb = document.querySelector('#' + add.dataset.addItem + ' tbody');
    if (!tb?.rows.length) return;
    const row = tb.rows[0].cloneNode(true);
    row.querySelectorAll('[id]').forEach(el => el.removeAttribute('id'));
    row.querySelectorAll('input').forEach(i => { i.value = i.type === 'number' ? 1 : ''; });
    row.querySelectorAll('select').forEach(select => { select.selectedIndex = 0; });
    row.querySelectorAll('[aria-invalid]').forEach(el => el.removeAttribute('aria-invalid'));
    tb.appendChild(row);
    window.enhanceControls?.(row);
    row.querySelector('input')?.focus();
  }
  const rm = e.target.closest('[data-remove-item]');
  if (rm) {
    const tb = rm.closest('tbody');
    if (tb.rows.length > 1) rm.closest('tr').remove(); else toast('A pass needs at least one item');
  }
});
