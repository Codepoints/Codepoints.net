<table class="props">
  <thead>
    <tr>
      <th><?php _e('Property')?></th>
      <th><?php _e('Value')?></th>
    </tr>
  </thead>
  <tbody>
    <?php $bools = $info->getBooleanCategories();
    uksort($props, function($a, $b) {
        $n = strcasecmp($a, $b);
        if ($n === 0) {
            return 0;
        }
        $r = array('age', 'na', 'na1', 'blk', 'gc', 'sc', 'bc', 'ccc',
            'dt', 'dm', 'Lower', 'slc', 'lc', 'Upper', 'suc', 'uc',
            'stc', 'tc', 'cf');
        $r2 = array();
        for ($i = 0, $c = count($r); $i < $c; $i++) {
            if ($a === $r[$i]) {
                if (in_array($b, $r2)) {
                    return 1;
                } else {
                    return -1;
                }
            } elseif ($b === $r[$i]) {
                if (in_array($a, $r2)) {
                    return -1;
                } else {
                    return 1;
                }
            } elseif ($a[0] === 'k' && $b[0] === 'k') {
                if ($a[1] === 'I' && $b[1] !== 'I') {
                    return -1;
                } elseif ($a[1] !== 'I' && $b[1] === 'I') {
                    return 1;
                } else {
                    return strcasecmp($a, $b);
                }
            } else {
                $r2[] = $r[$i];
            }
        }
        return strcasecmp($a, $b);
    });
    foreach ($props as $k => $v):
        if (! in_array($k, array('cp', 'image', 'abstract')) && ! ($k[0] === 'k' && ! $v)):?>
      <tr class="p_<?php e($k)?>">
        <th class="gl" data-term="<?php e($k)?>"><?php e($info->getCategory($k))?> <small>(<?php e($k)?>)</small></th>
        <td>
        <?php if ($v === '' || $v === Null):?>
          <span class="x">—</span>
        <?php elseif (in_array($k, $bools)):?>
          <span class="<?php if ($v):?>y">✔<?php else:?>n">✘<?php endif?></span>
        <?php elseif (is_array($v) || $v instanceof Codepoint):?>
          <?php cp($v, '', 'min') ?>
        <?php elseif ($k === 'scx'):
        foreach(explode(' ', $v) as $sc):?>
            <a href="<?php e($router->getUrl('search?'.$k.'='.$v))?>"><?php e($info->getLabel('sc', $sc))?></a>
        <?php endforeach;
        elseif (in_array($k, array('kCompatibilityVariant', 'kDefinition',
            'kSemanticVariant', 'kSimplifiedVariant',
            'kSpecializedSemanticVariant', 'kTraditionalVariant', 'kZVariant'))):
          echo preg_replace_callback('/U\+([0-9A-F]{4,6})/', function($m) use ($codepoint) {
            if (hexdec($m[1]) === $codepoint->getId()) {
                return _cp($codepoint, '', 'min');
            }
            $router = Router::getRouter();
            $db = $router->getSetting('db');
            return _cp(Codepoint::getCP(hexdec($m[1]), $db), '', 'min');
          }, $v);
        else:
          $s($k);
        endif?>
        </td>
      </tr>
    <?php endif; endforeach?>
  </tbody>
</table>
