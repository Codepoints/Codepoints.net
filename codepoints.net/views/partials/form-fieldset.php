<?php
/**
 * @var \Codepoints\Unicode\PropertyInfo $info
 * @var string $cat
 * @var array<string> $all_block_names
 * @var array<array> $query
 */
?>
<cp-search-property>
    <span slot="desc"><?= q(array_get($info->properties, $cat, $cat)) ?></span>
    <?php
    if ($cat === 'blk') {
        $values = $all_block_names;
    } else {
        $values = $info->getLegends($cat);
    }
    $query_values = [];
    if (array_key_exists($cat, $query)) {
        $query_values = $query[$cat];
    }
    foreach($values as $value => $label): ?>
      <p>
        <label>
          <input type="checkbox"
                 name="<?= q($cat)?>[]"
                 value="<?= q($value)?>"
                 <?php if (in_array($value, $query_values)):?> checked<?php endif?>>
          <?= q(is_array($label)? end($label) : $label) ?>
        </label>
      </p>
    <?php endforeach?>
</cp-search-property>
