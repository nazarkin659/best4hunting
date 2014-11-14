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
 * $displayData['fbLayout']
 * $displayData['url']
 * $displayData['enableFbSend']
 * $displayData['fbAction']
 * $displayData['fbWidth']
 * $displayData['fbShowFaces']
 * $displayData['fbColorscheme']
 * $displayData['enableFbShare']
 */
// Security check to ensure this file is being included by a parent file.
if (!defined('_JEXEC')) die('Direct Access to this location is not allowed.');

?>
      <!-- HTML5 Facebook like button -->
      <div class="fb-like" data-href="<?php echo $displayData['url']; ?>" data-send="<?php echo $displayData['enableFbSend'] ? 'true' : 'false'; ?>"
      data-action="<?php echo $displayData['fbAction']; ?>" data-width="<?php echo $displayData['fbWidth']; ?>"
      data-layout="<?php echo $displayData['fbLayout']; ?>" data-show-faces="<?php echo $displayData['fbShowFaces']; ?>"
      data-colorscheme="<?php echo $displayData['fbColorscheme']; ?>"
	  <?php echo $displayData['enableFbShare'] ? '' : ' data-share="true"'; ?> >
	  </div>
      <!-- End HTML5 Facebook like button -->				
