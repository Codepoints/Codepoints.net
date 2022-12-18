import { defineConfig } from 'vite';
import minifyLitTemplates from 'rollup-plugin-minify-html-literals';
import postcssCustomMedia from 'postcss-custom-media';
import postcssPresetEnv from 'postcss-preset-env';
import { customMedia } from './src/js/media_queries.ts';


export default defineConfig({
  base: '/static/',
  publicDir: 'src/public',
  plugins: [
    minifyLitTemplates(),
  ],
  server: {
    host: true,
  },
  build: {
    manifest: true,
    outDir: 'codepoints.net/static/',
    rollupOptions: {
      input: ['src/js/main.js', 'src/css/main.css'],
      output: {
        chunkFileNames: '[name]-[hash][extname]',
        entryFileNames: '[name]-[hash].js',
        assetFileNames: 'assets/[name]-[hash][extname]',
      },
    }
  },
  css: {
    postcss: {
      plugins: [
        postcssCustomMedia({
          importFrom: [
            { customMedia },
          ],
        }),
        postcssPresetEnv(),
      ],
    },
  },
})
