/**
 * Project Configuration for `@arts/elementor-extension`
 * Library for creating Elementor extensions, widgets, skins, tabs, and more in declarative way
 */
export default {
  // Basic project information
  name: 'Arts Query Control for Elementor',
  entry: './src/js/index.js',
  author: 'Artem Semkin',
  license: 'MIT',
  description: 'Query Control for Elementor',
  homepage: 'https://artemsemkin.com',
  donateUrl: 'https://buymeacoffee.com/artemsemkin',

  // Path configuration
  paths: {
    root: './',
    src: './src',
    dist: './dist',
    php: './src/php',
    styles: './src/styles',
    ts: './src/ts',
    js: './src/js',
    wordpress: {
      plugin: './src/wordpress-plugin',
      languages: './src/php/languages'
    },
    library: {
      base: 'libraries',
      name: 'arts-query-control-for-elementor',
      assets: 'src/php/libraries/arts-query-control-for-elementor'
    }
  },

  // Development configuration
  dev: {
    root: './src/ts/www',
    server: {
      port: 8080,
      host: 'localhost'
    }
  },

  // Live reloading server configuration
  liveReload: {
    enabled: true,
    port: 3000,
    host: 'localhost',
    https: {
      key: '/Users/art/.localhost-ssl/_wildcard.dev.local+6-key.pem',
      cert: '/Users/art/.localhost-ssl/_wildcard.dev.local+6.pem'
    },
    injectChanges: true,
    reloadDebounce: 200,
    reloadThrottle: 1000,
    notify: {
      styles: {
        top: 'auto',
        bottom: '0',
        right: '0',
        left: 'auto',
        padding: '5px',
        borderRadius: '5px 0 0 0',
        fontSize: '12px'
      }
    },
    ghostMode: {
      clicks: false,
      forms: false,
      scroll: false
    },
    open: false,
    snippet: false
  },

  // WordPress sync configuration
  wordpress: {
    enabled: true,
    source: './src/php',
    extensions: ['.js', '.css', '.php', '.jsx', '.ts', '.tsx'],
    targets: [], // Targets will be added by the build system based on environment
    debug: false
  },

  // WordPress plugin development configuration
  wordpressPlugin: {
    enabled: false,
    source: './src/wordpress-plugin',
    extensions: ['.php', '.js', '.css', '.jsx', '.ts', '.tsx', '.json', '.txt', '.md'],
    target: null, // Set in the environment-specific config
    debug: false,
    vendor: {
      source: './vendor',
      target: 'vendor',
      extensions: ['.php', '.js', '.css', '.json', '.txt', '.md'],
      delete: true,
      watch: true
    },
    packageName: 'arts-query-control-for-elementor',
    zipOutputName: 'arts-query-control-for-elementor.zip',
    packageExclude: [
      'node_modules',
      '.git',
      '.DS_Store',
      '**/.DS_Store',
      '.*',
      '**/.*',
      '*.log',
      '*.map',
      '*.zip',
      'package.json',
      'package-lock.json',
      'pnpm-lock.yaml',
      'yarn.lock',
      'README.md',
      'LICENSE',
      '.gitignore',
      '.editorconfig',
      '.eslintrc',
      '.prettierrc',
      'tsconfig.json',
      'vite.config.js',
      'vitest.config.js',
      'cypress.config.js',
      '__tests__',
      '__e2e__',
      'coverage',
      'dist'
    ],
    sourceFiles: {
      php: './src/php',
      vendor: './vendor',
      dist: {
        files: ['index.umd.js', 'index.css']
      },
      composer: ['composer.json', 'composer.lock']
    }
  },

  // Build configuration
  build: {
    formats: ['cjs', 'iife'],
    target: 'es2018',
    sourcemap: false,
    createDistFolder: false, // Option to disable dist folder creation
    externals: {},
    globals: {},
    cleanOutputDir: true,
    umd: {
      name: 'ArtsQueryControlForElementor',
      exports: 'named',
      globals: {}
    },
    // Output filenames by format
    output: {
      cjs: 'index.cjs',
      iife: 'index.iife.js'
    }
  },

  // Sass configuration
  sass: {
    enabled: true,
    entry: './src/styles/index.sass',
    output: './dist/index.css',
    options: {
      sourceMap: false,
      outputStyle: 'compressed',
      includePaths: ['node_modules']
    },
    // Direct library output path for compiled CSS
    libraryOutput: './src/php/libraries/arts-query-control-for-elementor/index.css'
  },

  // Watch options
  watch: {
    ignored: ['**/node_modules/**', '**/dist/**', '**/.*', '**/.*/**']
  },

  // Internationalization options
  i18n: {
    enabled: true,
    src: 'src/php/**/*.php',
    dest: 'src/php/languages/arts-query-control-for-elementor.pot',
    domain: 'arts-query-control-for-elementor',
    package: 'Arts Query Control for Elementor',
    bugReport: 'https://artemsemkin.com',
    lastTranslator: 'Artem Semkin',
    team: 'Artem Semkin',
    relativeTo: './'
  }
}
