// Builds the editor's control views and their styles into the PHP tree, where Managers/Compatibility.php enqueues them.
import { build } from 'esbuild'
import * as sass from 'sass'
import { mkdir, writeFile } from 'node:fs/promises'
import { dirname, resolve } from 'node:path'
import { fileURLToPath } from 'node:url'

const root = resolve(dirname(fileURLToPath(import.meta.url)), '..')
const target = resolve(root, 'src/php/libraries/arts-query-control-for-elementor')

await mkdir(target, { recursive: true })
await build({
  // The entry is imported, not built as one: its exports are for TypeScript consumers, and an entry's
  // exports are never tree-shaken, so the helpers no control uses would ship in the editor.
  stdin: { contents: "'use strict'\nimport './src/ts/index.ts'", resolveDir: root },
  outfile: resolve(target, 'index.umd.js'),
  bundle: true,
  minify: true,
  // A release build has never logged: the two warnings in the sources are for development.
  drop: ['console'],
  format: 'iife',
  target: 'es2022',
  logLevel: 'warning'
})

// No byte order mark: the file has shipped without one.
const { css } = sass.compile(resolve(root, 'src/styles/index.sass'), { style: 'compressed', charset: false })

await writeFile(resolve(target, 'index.css'), css)
