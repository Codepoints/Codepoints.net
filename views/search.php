<?php
$title = 'Result';
include "header.php";
$query = $result->getQuery();
?>
<div class="payload search">
  <h1><?php e($title);?></h1>
  <p>Codepoints where
    <?php foreach ($query as $i => $q):
    $sep = ', ';
    if (in_array(count($query), array(1, $i+1))) {
        $sep = '';
    } elseif ($i === count($query) - 2) {
        $sep = ' and ';
    }
    echo '<span class="where"><em>' . $info->getCategory($q[0]) .
        '</em> is <strong>' . ($q[2]? $info->getLabel($q[0], $q[2]) : 'empty') .
        '</strong></span>' . $sep;
    endforeach ?>.
  </p>
  <?php echo $pagination?>
  <ol class="block-data">
    <?php foreach ($result->get() as $cp => $na):
      echo '<li value="' . $cp . '">'; cp($na); echo '</li>';
    endforeach ?>
  </ol>
  <?php echo $pagination?>
</div>
<?php include "footer.php"?>
