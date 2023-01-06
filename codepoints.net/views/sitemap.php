<?php
/**
 * @var bool $is_index
 */

if ($is_index):
  /**
   * @var string[] $data
   */
?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach($data as $item):?>
  <sitemap>
    <loc>https://codepoints.net/<?=q($item)?></loc>
  </sitemap>
<?php endforeach?>
</sitemapindex>
<?php else:
  /**
   * @var list<Array{loc: string, priority: string, changefreq?: string}> $data
   */
?>
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
