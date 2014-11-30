
<?php
/**
 *
 * Customfilters entry point
 *
 * @package		customfilters
 * @author		Sakis Terz
 * @link		http://breakdesigns.net
 * @copyright	Copyright (c) 2010 - 2011 breakdesigns.net. All rights reserved.
 * @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *				customfilters is free software. This version may have been modified
 *				pursuant to the GNU General Public License, and as distributed
 *				it includes or is derivative of works licensed under the GNU
 *				General Public License or other free or open source software
 *				licenses.
 * @version $Id: customfilters.php 1 2011-10-21 18:36:00Z sakis $
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_customfilters')) {
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

// Add stylesheets and Scripts
$document = JFactory::getDocument();
$document->addStyleSheet(JURI::root().'administrator/components/com_customfilters/assets/css/display.css');
JHtml::_('behavior.framework');
JHtml::_('behavior.modal');
// Include dependencies
jimport('joomla.application.component.controller');

$controller = JControllerLegacy::getInstance('customfilters');
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();


?>
<div id="cf_info">
<a target="_blank" href="http://breakdesigns.net/extensions/custom-filters">Custom Filters</a> 
<div id="cf_versioninfo"></div>
<div id="cfupdate_toolbar">	
<a class="cf_update_btn modal"
	href="http://breakdesigns.net/index.php?option=com_content&view=article&id=151&tmpl=component" rel="{handler:'iframe',size: {x: 700, y: 600}}"
	target="_blank"><?php echo JText::_('COM_CUSTOMFILTERS_VIEW_CHANGELOG'); ?> </a>

<a class="cf_update_btn"
	href="http://breakdesigns.net/downloads"
	target="_blank"><?php echo JText::_('COM_CUSTOMFILTERS_GET_LATEST_VERSION'); ?> </a>				
<div style="clear: both"></div>
</div>
<div id="cf_copyright_footer">Copyright &copy; <a target="_blank" href="http://breakdesigns.net">breakdesigns.net</a></div>
</div>  