const cssFiles = ["assets/embed-CC83iNc-.css","assets/main-spGjUE7H.css"];
const scriptPath = "assets/embed-Cet4KfnD.js";

for (const href of cssFiles) {
  const fullHref = new URL(href, import.meta.url).href;
  const exists = Array.from(document.querySelectorAll('link[rel="stylesheet"]')).some((link) => link.href === fullHref);

  if (!exists) {
    const link = document.createElement('link');
    link.rel = 'stylesheet';
    link.href = fullHref;
    document.head.appendChild(link);
  }
}

await import(new URL(scriptPath, import.meta.url).href);
