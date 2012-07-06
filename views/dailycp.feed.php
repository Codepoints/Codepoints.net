<feed xmlns="http://www.w3.org/2005/Atom">

  <title>Codepoint of the Day</title>
  <link href="http://codepoints.net<?php e($router->getUrl('codepoint_of_the_day'))?>"/>
  <link rel="self" href="http://codepoints.net<?php e($router->getUrl('codepoint_of_the_day.xml'))?>"/>
  <updated><?php e($cps[0]['date'])?></updated>

  <author>
    <name>Manuel Strehl</name>
  </author>
  <id>http://codepoints.net<?php e($router->getUrl('codepoint_of_the_day'))?></id>

  <?php foreach ($cps as $cp): $codepoint = Codepoint::getCP($cp['cp'], $router->getSetting('db'));?>
    <entry>
      <title><?php f('U+%04X', $cp['cp'])?> <?php e($codepoint->getName())?></title>
      <link href="http://codepoints.net<?php e($router->getUrl('codepoint_of_the_day?date='.$cp['date']))?>"/>
      <id>http://codepoints.net<?php e($router->getUrl('codepoint_of_the_day?date='.$cp['date']))?></id>
      <updated><?php e($cp['date'])?></updated>
      <summary><?php e($cp['comment'])?></summary>
    </entry>
  <?php endforeach?>

</feed>
