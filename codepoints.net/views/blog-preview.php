<h2><?php _e('Latest from the blog:')?> <a href="<?php  echo q($post['url-with-slug'])?>"><?php  echo q($post['regular-title'])?></a></h2>
<p><?php  echo q(substr($post['regular-body'], 0, 255))?>
   <a href="<?php  echo q($post['url-with-slug'])?>"><?php _e('...read on')?></a>.</p>
