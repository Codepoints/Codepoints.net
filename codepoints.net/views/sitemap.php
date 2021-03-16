<?php
/**
 * @var bool $is_index
 * @var Array $data
 */

if ($is_index): ?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach($data as $item):?>
  <sitemap>
    <loc>https://codepoints.net/<?=q($item)?></loc>
  </sitemap>
<?php endforeach?>
</sitemapindex>
<?php else: ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach($data as $item):?>
    <url>
      <loc>https://codepoints.net/<?=q($item['loc'])?></loc>
<?php if (array_key_exists('priority', $item)): ?>
      <priority><?=q($item['priority'])?></priority>
<?php endif ?>
      <changefreq><?=array_get($item, 'changefreq', 'yearly')?></changefreq>
    </url>
<?php endforeach?>
</urlset>
<?php endif ?>
