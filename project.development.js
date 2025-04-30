/**
 * Development-specific configuration overrides for `@arts/query-control-for-elementor`
 * @param {Object} baseConfig - The base configuration object
 * @returns {Object} - Modified configuration for development
 */
export default function (baseConfig) {
  // Create a deep copy to avoid modifying the original
  const config = JSON.parse(JSON.stringify(baseConfig))

  // Set environment
  config.currentEnvironment = 'development'

  // Development-specific settings
  config.build.sourcemap = true
  config.build.minify = false
  config.build.createDistFolder = false

  // Configure Sass for development
  config.sass.options.sourceMap = true
  config.sass.options.outputStyle = 'expanded'

  // Configure live reload for development
  config.liveReload.logLevel = 'debug'
  config.liveReload.reloadOnRestart = true

  config.wordpress.targets = [
    '/Users/art/Projects/Trigger/DEV/src/wp/plugin/vendor/arts/query-control-for-elementor/src/php'
  ]

  // Configure WordPress plugin target
  config.wordpressPlugin.target = null
  config.wordpressPlugin.debug = true

  // Enable debug logging
  config.wordpress.debug = true

  return config
}
