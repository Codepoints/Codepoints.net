<script><?php
?>var _paq=[<?php
?>['setSiteId',4],<?php
?>['setTrackerUrl','http://stats.codepoints.net/piwik.php'],<?php
if (isset($trackerVars) && $trackerVars):
    foreach($trackervars as $i => $var):
        ?>['setCustomVariable',<?php echo $i+1?>,"<?php
        _e($var[0]);
        ?>","<?php
_e($var[1]);
if (count($var) > 2):
        ?>","<?php
    _e($var[2]);
endif;
        ?>"],<?php
    endforeach;
endif;
?>['trackPageView'],<?php
?>['enableLinkTracking'],<?php
?>];<?php
?></script>
