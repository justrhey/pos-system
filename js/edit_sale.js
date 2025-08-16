
function addProductRow() {
  const list = document.getElementById("product-list");
  const firstRow = list.querySelector(".product-row");
  const newRow = firstRow.cloneNode(true);
  const select = newRow.querySelector("select");
  const input = newRow.querySelector("input");
  select.selectedIndex = 0;
  input.value = '';
  list.appendChild(newRow);
  bindEvents(newRow);
}

function removeRow(btn) {
  const list = document.getElementById("product-list");
  if (list.children.length > 1) {
    btn.parentElement.remove();
    calculateTotal();
  }
}

function bindEvents(row) {
  const select = row.querySelector('select');
  const input = row.querySelector('input');
  select.addEventListener('change', calculateTotal);
  input.addEventListener('input', calculateTotal);
}

function calculateTotal() {
  let total = 0;
  const rows = document.querySelectorAll('#product-list .product-row');
  rows.forEach(row => {
    const select = row.querySelector('select');
    const quantity = row.querySelector('input');
    const price = parseFloat(select.selectedOptions[0]?.dataset.price || 0);
    const qty = parseInt(quantity.value) || 0;
    total += price * qty;
  });
  document.getElementById('total-price-add').innerText = total.toFixed(2);
}

document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('#product-list .product-row').forEach(bindEvents);
  calculateTotal();
});
