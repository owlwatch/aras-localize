import { readFileSync, writeFileSync } from 'node:fs'
import path from 'node:path'

const root = process.cwd()
const manifestPath = path.join(root, 'dist', '.vite', 'manifest.json')
const outputPath = path.join(root, 'dist', 'embed.js')

const manifest = JSON.parse(readFileSync(manifestPath, 'utf8'))
const embedEntry = manifest['src/embed.ts']

if (!embedEntry?.file) {
  throw new Error('Could not find src/embed.ts in Vite manifest.')
}

const visited = new Set()

function collectCss(entryName) {
  if (visited.has(entryName)) {
    return []
  }

  visited.add(entryName)

  const entry = manifest[entryName]

  if (!entry) {
    return []
  }

  const ownCss = Array.isArray(entry.css) ? entry.css : []
  const importedCss = Array.isArray(entry.imports)
    ? entry.imports.flatMap((importName) => collectCss(importName))
    : []

  return [...ownCss, ...importedCss]
}

const cssFiles = Array.from(new Set(collectCss('src/embed.ts')))

const source = `const cssFiles = ${JSON.stringify(cssFiles)};
const scriptPath = ${JSON.stringify(embedEntry.file)};

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
`

writeFileSync(outputPath, source)
