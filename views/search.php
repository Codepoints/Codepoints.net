<?php
$title = 'Result';
include "header.php";
$query = $result->getQuery();
$cQuery = count($query);
?>
<div class="payload search">
  <h1><?php e($title);?></h1>
  <p>Codepoints where
    <?php foreach ($query as $i => $q):
        $sep = ', ';
        if (in_array($cQuery, array(1, $i+1))) {
            $sep = '.';
        } elseif ($i === $cQuery - 2) {
            $sep = ' and ';
        }
        printf('<span class="where"><em>%s</em> is <strong>%s</strong></span>%s',
            $info->getCategory($q[0]), ($q[2]? $info->getLabel($q[0], $q[2]) : 'empty'),
            $sep);
    endforeach ?>
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
