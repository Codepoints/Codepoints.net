<?php
$title = __('You are off-line');
$page_description = __('You are currently off-line. Pages, that you visited recently, should still be available, though.');
include 'partials/header.php'; ?>
<main class="main main--offline">
  <h1><?=q($title)?></h1>
  <p><?=q($page_description ?? '')?></p>
  <p><?=_q('The home page, the plane overview and the detail pages for the seventeen planes should also be available.')?></p>
</main>
<?php include 'partials/footer.php'; ?>
