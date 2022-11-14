<?php
use Codepoints\Translator;

/**
 * @var Array $env
 */
?>
<?php if (array_key_exists('current_url', $env)):
    $url = preg_replace('/(\\?lang=[^&]*$|(?<=\\?)lang=[^&]*&|&lang=[^&]*)/', '', $env['current_url']);
    foreach (Translator::SUPPORTED_LANGUAGES as $other_lang):
        /* Google says, that it wants a link element to the same language, too:
         * https://developers.google.com/search/docs/advanced/crawling/localized-versions */
        ?><link rel="alternate" hreflang="<?=q($other_lang)?>" title="<?=q(Translator::getLanguageName($other_lang))?>" href="<?=get_origin()?><?=$url.(strpos($url, '?') !== false? '&' : '?').'lang='.q($other_lang)?>">
  <?php endforeach;
endif ?>
