<?php use Codepoints\Translator; ?>
<?php if (array_key_exists('current_url', $env)): ?>
  <?php foreach (Translator::SUPPORTED_LANGUAGES as $other_lang): ?>
    <?php if ($other_lang === $lang) { continue; } ?>
    <link rel="alternate" hreflang="<?=q($other_lang)?>" href="https://codepoints.net/<?=$env['current_url'].(strpos($env['current_url'], '?') !== false? '&' : '?').'lang='.q($other_lang)?>">
  <?php endforeach ?>
<?php endif ?>
