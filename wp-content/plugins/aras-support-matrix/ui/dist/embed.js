const cssFiles = ["assets/embed-CRF3QBRb.css","assets/main-C4nAUGT6.css"];
const scriptPath = "assets/embed-DQyFP6bl.js";

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
