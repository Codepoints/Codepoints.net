<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <url>
    <loc>http://codepoints.net<?php e($router->getUrl($block))?></loc>
    <changefreq>yearly</changefreq>
    <priority>0.7</priority>
  </url>
  <?php foreach($block->get() as $cp):?>
    <url>
      <loc>http://codepoints.net<?php e($router->getUrl($cp))?></loc>
      <changefreq>yearly</changefreq>
    </url>
  <?php endforeach?>
</urlset>
