// Mobile sidebar toggle
document.addEventListener('DOMContentLoaded', () => {
  const side = document.getElementById('sidebar');
  const btn = document.getElementById('menuBtn');
  if (btn && side) {
    btn.addEventListener('click', (e) => { e.stopPropagation(); side.classList.toggle('open'); });
    document.addEventListener('click', (e) => { if (!side.contains(e.target)) side.classList.remove('open'); });
  }

  // Dismissible alerts
  document.querySelectorAll('[data-dismiss]').forEach((b) =>
    b.addEventListener('click', () => b.closest('.alert').remove()));

  // Live image preview for file inputs
  document.querySelectorAll('input[type=file][data-preview]').forEach((input) => {
    input.addEventListener('change', () => {
      const target = document.getElementById(input.dataset.preview);
      const file = input.files && input.files[0];
      if (!target || !file) return;
      target.innerHTML = '';
      const img = document.createElement('img');
      img.src = URL.createObjectURL(file);
      target.appendChild(img);
    });
  });

  // Confirm destructive actions
  document.querySelectorAll('form[data-confirm]').forEach((f) =>
    f.addEventListener('submit', (e) => { if (!confirm(f.dataset.confirm)) e.preventDefault(); }));

  // Record-sale live summary
  const form = document.getElementById('saleForm');
  if (form) {
    const product = form.querySelector('#product_id');
    const qty = form.querySelector('#quantity');
    const peso = (n) => '₱' + n.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    const $ = (id) => document.getElementById(id);
    const placeholderHtml = $('sumImg').innerHTML;

    const update = () => {
      const opt = product.options[product.selectedIndex];
      const price = parseFloat(opt?.dataset.price || '0');
      const stock = parseInt(opt?.dataset.stock || '0', 10);
      const q = Math.max(parseInt(qty.value || '0', 10) || 0, 0);
      $('sumName').textContent = opt && opt.value ? opt.dataset.name : '—';
      $('sumPrice').textContent = opt && opt.value ? peso(price) : '—';
      $('sumStock').textContent = opt && opt.value ? stock : '—';
      $('sumQty').textContent = q || '—';
      $('sumTotal').textContent = peso(price * q);
      const warn = $('stockWarn');
      const over = opt && opt.value && q > stock;
      warn.hidden = !over;
      if (over) warn.textContent = stock > 0 ? `Only ${stock} in stock — this sale will be rejected.` : 'This product is out of stock.';
      const img = opt?.dataset.image;
      const holder = $('sumImg');
      holder.innerHTML = img ? `<img src="${img}" alt="" style="width:100%;height:100%;object-fit:cover;border-radius:12px">` : placeholderHtml;
    };
    product.addEventListener('change', update);
    qty.addEventListener('input', update);
    update();
  }
});
