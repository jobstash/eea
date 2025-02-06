// View your website at your own local server
// for example http://vite-php-setup.test

// http://localhost:3000 is serving Vite on development
// but accessing it directly will be empty

import { defineConfig } from 'vite'
import liveReload from 'vite-plugin-live-reload'
import { resolve } from 'path'

// find theme dir name
function getThemeDir() {
  return '/wp-content/themes/wp-starter'
}

// https://vitejs.dev/config
export default defineConfig({
  plugins: [liveReload(__dirname + '/**/*.[php,twig]')],
  // config
  root: '',
  base:
    process.env.ENVIRONMENT === 'local'
      ? `${getThemeDir()}/`
      : `${getThemeDir()}/dist/`,

  // base: process.env.ENVIRONMENT === 'local' ? `/` : `/dist/`,

  build: {
    // output dir for production build
    outDir: resolve(__dirname, './dist'),
    emptyOutDir: true,

    // emit manifest so PHP can find the hashed files
    manifest: true,

    // esbuild target
    target: 'es2018',

    // our entry
    rollupOptions: {
      input: {
        main: resolve(__dirname + '/main.js'),
      },

      /*
      output: {
          entryFileNames: `[name].js`,
          chunkFileNames: `[name].js`,
          assetFileNames: `[name].[ext]`
      }*/
    },

    // minifying switch
    minify: true,
    write: true,
  },

  server: {
    // required to load scripts from custom host
    cors: true,

    // proxy: {
    // 	'/wp-content/themes/wp-starter/': 'http://wordpress:8080', // Proxy requests to the WordPress backend (adjust the path accordingly)
    // },

    // we need a strict port to match on PHP side
    // change freely, but update in your functions.php to match the same port
    strictPort: true,
    port: process.env.NODE_PORT,
    host: true,

    // serve over http
    https: false,

    // serve over httpS
    // to generate localhost certificate follow the link:
    // https://github.com/FiloSottile/mkcert - Windows, MacOS and Linux supported - Browsers Chrome, Chromium and Firefox (FF MacOS and Linux only)
    // installation example on Windows 10:
    // > choco install mkcert (this will install mkcert)
    // > mkcert -install (global one time install)
    // > mkcert localhost (in project folder files localhost-key.pem & localhost.pem will be created)

    hmr: {
      // clientPort: 3000,
      host: process.env.NODE_HOST,
    },
  },
})
