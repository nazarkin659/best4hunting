<?php
/**
 * sh404SEF - SEO extension for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier 2014
 * @package     sh404SEF
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     4.4.0.1725
 * @date		2014-04-09
 */

/**
 * Input:
 * 
 * $displayData['tracking_code']
 * $displayData['custom_vars'][] = {name:string, value: string}
 * $displayData['custom_url']
 */
// Security check to ensure this file is being included by a parent file.
if (!defined('_JEXEC')) die('Direct Access to this location is not allowed.');

?>

<!-- Google Analytics Classic snippet -->
<script type='text/javascript'>
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', '<?php echo $displayData['tracking_code']; ?>']);
  <?php 
    if(!empty($displayData['custom_vars'])) :
      foreach($displayData['custom_vars'] as $index => $customVar) :
      	echo "\n_gaq.push(['_setCustomVar', " . $index . ", '" . htmlentities($customVar->name, ENT_QUOTES, 'UTF-8') . "', '"
      		. htmlentities($customVar->value, ENT_QUOTES, 'UTF-8') . "', 3]);";
	  endforeach;
	  echo "\n";
    endif;
  ?>

  _gaq.push(['_trackPageview'<?php echo empty($displayData['custom_url']) ? '' : ",'" . $displayData['custom_url'] . "'"; ?>]);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
</script>
