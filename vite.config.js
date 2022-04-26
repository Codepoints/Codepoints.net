import { defineConfig } from 'vite';
import { generateAssetFileName } from 'vite';

export default defineConfig({
  base: '/static/',
  publicDir: 'src/public',
  build: {
    //manifest: true,
    outDir: 'codepoints.net/static/',
    rollupOptions: {
      input: ['src/js/main.js', 'src/css/main.css'],
      output: {
        chunkFileNames: '[name]-[hash][extname]',
        entryFileNames: '[name].js',
        assetFileNames: (assetInfo) => {
          if (assetInfo.name === 'main.css') {
            return '[name][extname]';
          }
          return 'assets/[name]-[hash][extname]';
        },
      },
    }
  }
})
