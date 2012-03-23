<?php
$query = $result->getQuery();
$cQuery = count($query);
$fQuery = $result->getCount();
$title = $cQuery > 0? 'Result' : 'Search';
include "header.php";
include "nav.php";
?>
<div class="payload search">
  <h1><?php e($title)?></h1>
  <?php if ($fQuery > 0):?>
  <p>There are <strong><?php e($fQuery)?></strong> codepoints<?php if ($cQuery > 0):?>
    where
      <?php foreach ($query as $i => $q):
        $sep = ', ';
        if (in_array($cQuery, array(1, $i+1))) {
            $sep = '.';
        } elseif ($i === $cQuery - 2) {
            $sep = ' and ';
        }
        $tmp = array();
        foreach ((array)$q[2] as $q2) {
            $tmp[] = ($q2? $info->getLabel($q[0], $q2) : 'empty');
        }
        printf('<span class="where"><em>%s</em> is <strong>%s</strong></span>%s',
            $info->getCategory($q[0]), join('</strong> or <strong>', $tmp), $sep);
      endforeach ?>
    <?php elseif (isset($range)):?>
      in the range <strong><?php e($range)?></strong>.
    <?php else:?>.
    <?php endif?>
  </p>
  <?php echo $pagination?>
  <ol class="block-data">
    <?php foreach ($result->get() as $cp => $na):
      echo '<li value="' . $cp . '">'; cp($na); echo '</li>';
    endforeach ?>
  </ol>
  <?php echo $pagination?>
  <?php else:?>
    <p>Please enter your search specification.</p>
  <?php endif?>
  <?php include "searchform.php"?>
</div>
<?php include "footer.php"?>
