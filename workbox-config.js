const env = process.env.NODE_ENV || 'production';

const global_revision = 'Feicheez5phe2am2';


module.exports = {
  globDirectory: "codepoints.net/",
  globPatterns: [
    "**/*.{webmanifest,css,woff2,js,html}"
  ],
  globIgnores: [
    "**/vendor/**",
    "**/views/**",
  ],
  swDest: "codepoints.net/sw.js",
  sourcemap: env !== 'production',
  additionalManifestEntries: [
    {
      url: '/offline.html',
      revision: global_revision,
    },
  ],
  //navigateFallback: '/offline',
  skipWaiting: true,
  clientsClaim: true,
};
