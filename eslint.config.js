import eslint from '@eslint/js'
import prettier from 'eslint-config-prettier'
import globals from 'globals'
import tseslint from 'typescript-eslint'

// Common globals for Elementor environment
const elementorGlobals = {
  $e: 'readonly',
  elementor: 'readonly',
  elementorCommon: 'readonly',
  jQuery: 'readonly',
  Backbone: 'readonly',
  _: 'readonly'
}

export default [
  eslint.configs.recommended,
  ...tseslint.configs.recommended,
  prettier,
  {
    ignores: [
      'node_modules/',
      'dist/',
      'coverage/',
      '__e2e__/',
      '__tests__/',
      'vendor/',
      'src/js/www',
      'src/php'
    ]
  },
  // Configuration for build/config files
  {
    files: [
      '*.config.js',
      'config/**/*.js',
      'vite.config.js',
      '__builder__/**/*.js',
      '__build__/**/*.js'
    ],
    languageOptions: {
      ecmaVersion: 2022,
      sourceType: 'module',
      globals: {
        ...globals.node, // Node.js environment globals
        ...elementorGlobals // Elementor-specific globals
      }
    },
    rules: {
      'no-console': 'off'
    }
  },
  // Configuration for source files
  {
    files: ['src/ts/**/*.ts'],
    languageOptions: {
      ecmaVersion: 2022,
      sourceType: 'module',
      globals: {
        ...globals.browser, // Browser environment globals
        ...globals.es2021, // ES2021 globals
        ...elementorGlobals // Elementor-specific globals
      }
    },
    rules: {
      'no-console': 'warn',
      'no-unused-vars': 'off',
      '@typescript-eslint/no-unused-vars': [
        'warn',
        {
          argsIgnorePattern: '^_',
          varsIgnorePattern: '^_',
          caughtErrorsIgnorePattern: '^_'
        }
      ],
      'no-var': 'error',
      'prefer-const': 'error',
      'prefer-arrow-callback': 'error',
      'object-shorthand': 'error',
      'no-undef': 'off',
      'no-redeclare': 'off',
      '@typescript-eslint/no-redeclare': 'error',
      'no-shadow': 'off',
      '@typescript-eslint/no-shadow': [
        'error',
        {
          builtinGlobals: true,
          hoist: 'all',
          allow: ['_', 'name']
        }
      ],
      'no-use-before-define': 'off',
      '@typescript-eslint/no-use-before-define': [
        'error',
        {
          functions: false,
          classes: true,
          variables: true
        }
      ]
    }
  }
]
