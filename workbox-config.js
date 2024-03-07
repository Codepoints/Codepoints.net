const env = process.env.NODE_ENV || 'production';

const global_revision = 'Feicheez5phe2am3';


module.exports = {
  globDirectory: "codepoints.net/",
  globPatterns: [
    "**/*.{webmanifest,css,woff2,js,html,svg,png,jpg,webp}"
  ],
  globIgnores: [
    "vendor/**",
    "views/**",
    "image/**",
  ],
  swDest: "codepoints.net/sw.js",
  sourcemap: env !== 'production',
  additionalManifestEntries: [
    {
      url: '/offline.html',
      revision: global_revision,
    },
    {
      url: '/',
      revision: global_revision,
    },
    {
      url: '/planes',
      revision: global_revision,
    },
  ],
  navigateFallback: '/offline.html',
  skipWaiting: true,
  clientsClaim: true,
};
