<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

jimport( 'joomla.application.component.view' );
require_once (JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_fsf'.DS.'settings.php');
jimport('joomla.html.pane');


class FsfsViewFsfs extends JViewLegacy
{
 
    function display($tpl = null)
	{
		JToolBarHelper::title( JText::_( 'FREESTYLE_FAQS' ), 'fsf.png' );
		FSFAdminHelper::DoSubToolbar();
	
		parent::display($tpl);
	}
	
	function Item($title, $link, $icon, $help)
	{
?>
		<div class="fsf_main_item fsj_tip" title="<?php echo JText::_($help); ?>">	
			<div class="fsf_main_icon">
				<a href="<?php echo FSFRoute::x($link); ?>">
					<img src="<?php echo JURI::root( true ); ?>/administrator/components/com_fsf/assets/images/<?php echo $icon;?>-48x48.png" width="48" height="48">
				</a>
			</div>
			<div class="fsf_main_text">
				<a href="<?php echo FSFRoute::x($link); ?>">
					<?php echo JText::_($title); ?>
				</a>
			</div>
		</div>	
<?php
	}
}


