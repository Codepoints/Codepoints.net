<?php if ($cQuery > 0):?>
  where
  <?php foreach ($query as $i => $q):
    $sep = ', ';
    if (in_array($cQuery, array(1, $i+1))) {
        $sep = '.';
    } elseif ($i === $cQuery - 2) {
        $sep = $q[3] === 'AND'? ' and ' : ' or ';
    }
    $tmp = array();
    foreach ((array)$q[2] as $q2) {
        $tmp[] = ($q2? $info->getLabel($q[0], trim($q2, '%')) : 'empty');
    }
    switch ($q[1]) {
        case 'LIKE':
        case 'like':
            $ct = 'contains';
            break;
        case '!=':
            $ct = 'is not';
            break;
        default:
            $ct = 'is';
    }
    $tmp2 = $tmp[0];
    for ($i = 1, $j = count($tmp); $i < $j; $i++) {
        if ($i < $j -1) {
            $tmp2 .= '</strong>, <strong>'.$tmp[$i];
        } else {
            $tmp2 .= '</strong> or <strong>'.$tmp[$i];
        }
    }
    printf('<span class="where"><em>%s</em> %s <strong>%s</strong></span>%s',
        $info->getCategory($q[0]), $ct, $tmp2, $sep);
  endforeach ?>
<?php elseif (isset($range)):?>
  in the range <strong><?php e($range)?></strong>.
<?php else:?>.
<?php endif?>
