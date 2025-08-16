
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

    function validateForm() {
      const selects = document.querySelectorAll('select[name="product_id[]"]');
      const quantities = document.querySelectorAll('input[name="quantity[]"]');
      for (let i = 0; i < selects.length; i++) {
        if (selects[i].value === '' || quantities[i].value <= 0) {
          alert("Please select a valid product and quantity.");
          return false;
        }
      }
      return true;
    }

    // Initialize first row
    document.addEventListener('DOMContentLoaded', () => {
      const firstRow = document.querySelector('.product-row');
      bindEvents(firstRow);
      calculateTotal();
    });
