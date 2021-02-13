<nav class="pagination">
  <ol>
    <?php if ($page > 1): ?>
      <li value="<?=$page - 1?>" class="prev"><a href="<?=q(sprintf($url, $page - 1))?>">previous</a></li>
    <?php else: ?>
      <li value="0" class="prev disabled"><span>previous</span>
    <?php endif ?>
    <?php $ellipsis = false ?>
    <?php for ($i = 1; $i <= $pages; $i++): ?>
      <?php if ($i === $page): ?>
        <li class="current" value="<?=$i?>"><span><?=$i?></span></li>
        <?php $ellipsis = false ?>
      <?php elseif ($i === 1 || $i === $pages || abs($page - $i) < $pages_shown): ?>
        <li value="<?=$i?>"><a href="<?=q(sprintf($url, $i))?>"><?=$i?></a></li>
        <?php $ellipsis = false ?>
      <?php elseif (! $ellipsis): ?>
        <li value="0" class="ellipsis">…</li>
        <?php $ellipsis = true ?>
      <?php endif ?>
    <?php endfor ?>
    <?php if ($page < $pages): ?>
      <li value="<?=$page + 1?>" class="next"><a href="<?=q(sprintf($url, $page + 1))?>">next</a></li>
    <?php else: ?>
      <li value="0" class="next disabled"><span>next</span>
    <?php endif ?>
  </ol>
</nav>
