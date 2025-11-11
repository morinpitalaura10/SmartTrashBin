// include.js (versi robust & paralel)
document.addEventListener('DOMContentLoaded', async () => {
  const placeholders = Array.from(document.querySelectorAll('[data-include]'));

  await Promise.all(
    placeholders.map(async (el) => {
      const url = el.getAttribute('data-include');
      try {
        const res = await fetch(url, { cache: 'no-cache' });
        if (!res.ok) throw new Error(`${res.status} ${res.statusText}`);
        const html = await res.text();
        el.innerHTML = html;
      } catch (err) {
        console.error('Gagal load:', url, err);
        el.innerHTML = `<!-- Error loading ${url} -->`;
      }
    })
  );

  // Kasih tahu bahwa semua komponen sudah masuk
  document.dispatchEvent(new CustomEvent('componentsLoaded'));
});
