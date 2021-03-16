<?php
use Codepoints\Translator;

/**
 * @var Array $env
 */
?>
<?php if (array_key_exists('current_url', $env)): ?>
  <?php foreach (Translator::SUPPORTED_LANGUAGES as $other_lang): ?>
    <?php /* Google says, that it wants a link element to the same language, too:
           * https://developers.google.com/search/docs/advanced/crawling/localized-versions */ ?>
    <link rel="alternate" hreflang="<?=q($other_lang)?>" href="https://codepoints.net/<?=$env['current_url'].(strpos($env['current_url'], '?') !== false? '&' : '?').'lang='.q($other_lang)?>">
  <?php endforeach ?>
<?php endif ?>
