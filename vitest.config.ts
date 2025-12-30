import { defineConfig } from 'vitest/config'
import { dirname, resolve } from 'path'
import { fileURLToPath } from 'url'

const __filename = fileURLToPath(import.meta.url)
const __dirname = dirname(__filename)

export default defineConfig({
  test: {
    environment: 'node',

    // Include test files
    include: ['__tests__/**/*.test.ts'],

    // Module resolution
    resolve: {
      alias: {
        '@': resolve(__dirname, 'src/ts')
      }
    },

    // Coverage configuration
    coverage: {
      include: ['src/ts/**/*.ts'],
      exclude: [
        'src/ts/**/interfaces/*.ts',
        'src/ts/**/types/*.ts',
        'src/ts/**/constants/*.ts',
        'src/ts/**/index.ts',
        'src/ts/global.d.ts',
        'node_modules/',
        '**/*.d.ts'
      ]
    }
  }
})
