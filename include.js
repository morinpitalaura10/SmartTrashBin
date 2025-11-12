// include.js — robust: parallel fetch, run <script> in fragments, support nested includes
document.addEventListener('DOMContentLoaded', async () => {
  // Prevent double-fire
  if (window.__includesLoaded) return;
  window.__includesLoaded = true;

  // Load all [data-include] under a given root; return when done (supports nesting)
  async function processIncludes(root = document) {
    const nodes = Array.from(root.querySelectorAll('[data-include]'));
    if (nodes.length === 0) return;

    // fetch all in parallel
    const results = await Promise.allSettled(
      nodes.map(async (el) => {
        const url = el.getAttribute('data-include');
        try {
          const res = await fetch(url, { cache: 'no-cache' });
          if (!res.ok) throw new Error(`${res.status} ${res.statusText}`);
          const html = await res.text();

          // inject HTML
          el.innerHTML = html;
          el.removeAttribute('data-include');

          // ---- execute <script> inside the injected HTML (keep order) ----
          const scripts = Array.from(el.querySelectorAll('script'));
          for (const old of scripts) {
            const s = document.createElement('script');
            // copy attributes
            for (const { name, value } of Array.from(old.attributes)) {
              s.setAttribute(name, value);
            }
            // inline script content
            if (!old.src) s.textContent = old.textContent;
            // replace in DOM to trigger execution
            old.parentNode.replaceChild(s, old);
            // If remote script without "async", wait till it loads to preserve order
            if (s.src && !s.async) {
              await new Promise((resolve, reject) => {
                s.onload = resolve;
                s.onerror = reject;
              });
            }
          }
        } catch (err) {
          console.error('Gagal load:', url, err);
          el.innerHTML = `<!-- Error loading ${url} -->`;
          el.removeAttribute('data-include');
        }
      })
    );

    // Optional: log any rejected fetch
    results.forEach(r => { if (r.status === 'rejected') console.warn(r.reason); });

    // After all current includes resolved, check if newly-injected HTML contains more includes
    // and process them (recursively) until no more remain.
    const hasMore = root.querySelector('[data-include]');
    if (hasMore) {
      await processIncludes(root);
    }
  }

  await processIncludes(document);

  // Fire a single event when *all* includes (incl. nested) have finished + scripts executed
  document.dispatchEvent(new CustomEvent('componentsLoaded'));
});
