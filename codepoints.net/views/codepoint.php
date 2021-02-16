<?php include 'partials/header.php'; ?>
<main class="main main--codepoint">
  <figure>
    <?=cpimg($codepoint, 250)?>
  </figure>
  <h1><?=q($codepoint)?> <?=q($codepoint->name)?></h1>
<?php if ($codepoint->gc === 'Xx'): ?>
  <p><?=_q('This codepoint doesn’t exist.')?>
  If it would, it’d be located in the
  Nirvana of Undefined Behaviour beyond the 17<sup>th</sup> plane, a land <a href="http://www.unicode.org/mail-arch/unicode-ml/y2003-m10/0234.html">no member of the Unicode mailing list has ever seen</a>.
  </p>
<?php endif ?>
<?php if ($block): ?>
  Block: <?=bl($block)?><br>
<?php endif ?>
<?php if ($plane): ?>
  Plane: <?=pl($plane)?><br>
<?php else: ?>
<?php endif ?>
<?php if ($prev): ?>
  Prev: <?=cp($prev)?><br>
<?php endif ?>
<?php if ($next): ?>
  Next: <?=cp($next)?><br>
<?php endif ?>
</main>
<?php include 'partials/footer.php'; ?>
