const cssFiles = ["assets/embed-CRF3QBRb.css","assets/main-Dq9sMwYA.css"];
const scriptPath = "assets/embed-CDc_qTrS.js";

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
