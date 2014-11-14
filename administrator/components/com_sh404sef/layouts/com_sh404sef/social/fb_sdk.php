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
 * $displayData['languageTag']
 * 
 */
// Security check to ensure this file is being included by a parent file.
if (!defined('_JEXEC')) die('Direct Access to this location is not allowed.');

?>

<!-- Facebook SDK -->
<div id='fb-root'></div>
<script type='text/javascript'>

      // Load the SDK Asynchronously
      (function(d){
      var js, id = 'facebook-jssdk'; if (d.getElementById(id)) {return;}
      js = d.createElement('script'); js.id = id; js.async = true;
      js.src = '//connect.facebook.net/<?php echo $displayData['languageTag']; ?>/all.js';
      d.getElementsByTagName('head')[0].appendChild(js);
    }(document));

</script>
<!-- End Facebook SDK -->
