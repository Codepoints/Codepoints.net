<?php
http_response_code(404);
$title = __('Hi 👋 to this non-WordPress site!');
$page_description = __('Thanks for passing by!');
include 'partials/header.php'; ?>
<main class="main main--wp-login">
  <h1><?=q($title)?></h1>
  <p><?=q($page_description ?? '')?> This is not a WordPress site, so there is
  nothing to see here.</p>
  <p>We are just a small non-commercial Unicode explainer site and we’d love
  to hear from you, if you want to help or found something peculiar. Please
  take a look at the <a href="<?=url('about')?>">about page</a> to learn how
  to get in touch!</p>
</main>
<?php include 'partials/footer.php'; ?>
