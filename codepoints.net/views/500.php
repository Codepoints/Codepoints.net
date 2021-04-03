<?php include 'partials/header.php'; ?>
<main class="main main--500">
  <h1><?=q($title)?></h1>
  <p><?=q($page_description)?></p>
  <p><?=sprintf(_q('If this problem persists, please tell us on %sTwitter%s or via %se-mail%s.'),
      '<a href="https://twitter.com/CodepointsNet">',
      '</a>',
      '<a href="https://manuel-strehl.de/contact">',
      '</a>',
  )?></p>
</main>
<?php include 'partials/footer.php'; ?>
