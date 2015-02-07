<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <sitemap>
    <loc>https://codepoints.net/sitemap/base.xml</loc>
  </sitemap>
  <?php foreach($blocks as $name):?>
    <sitemap>
      <loc>https://codepoints.net/sitemap/<?php e(u($name))?>.xml</loc>
    </sitemap>
  <?php endforeach?>
</sitemapindex>
