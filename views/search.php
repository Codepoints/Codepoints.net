<?php
$title = 'Result';
$page = isset($_GET['page'])? intval($_GET['page']) : 1;
$result->page = $page - 1;
$result->search();
$pagination = new Pagination($result->getCount());
$pagination->setPage($page);
include "header.php";
?>
<div class="payload search">
  <h1><?php e($title);?></h1>
  <?php echo $pagination?>
  <ol class="block-data">
    <?php foreach ($result->get() as $cp => $na):
      echo '<li value="' . $cp . '">'; cp($na); echo '</li>';
    endforeach ?>
  </ol>
  <?php echo $pagination?>
</div>
<?php include "footer.php"?>
