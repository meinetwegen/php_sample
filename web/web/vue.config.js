const { defineConfig } = require('@vue/cli-service')

module.exports = defineConfig({
  transpileDependencies: true,
  chainWebpack: (config) => {
    config.plugin('define').tap((args) => {
      args[0].__VUE_I18N_FULL_INSTALL__ = JSON.stringify(true)
      args[0].__VUE_I18N_LEGACY_API__ = JSON.stringify(false)
      args[0].__INTLIFY_PROD_DEVTOOLS__ = JSON.stringify(false)
      return args
    })
  },
  devServer: {
    host: '0.0.0.0',
    port: 8080,
    allowedHosts: 'all',
    // The SPA talks to the API through the same origin, so no CORS setup is needed.
    proxy: {
      '/api': {
        target: process.env.VUE_APP_PROXY_TARGET || 'http://localhost:8000',
        changeOrigin: true,
      },
    },
  },
})
