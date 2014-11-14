<?php
/**
 * @component AwoCoupon for Virtuemart
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @license : GNU/GPL
 * @Website : http://awodev.com
 **/
 
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');

class AwoCouponViewDashboard extends JView {
	/**
	 * Creates the Entrypage
	 *
	 * @since 1.0
	 */
	function display( $tpl = null ) {
		global $def_lists;
		
		//Load pane behavior
		jimport('joomla.html.pane');

		//initialise variables
		$document	= & JFactory::getDocument();
		$pane   	= & JPane::getInstance('sliders');
		$update 	= 0;

		//build toolbar
		JToolBarHelper::title( 'AwoCoupon Virtuemart', 'awocoupon' );

		//add css and submenu to document
		$document->addStyleSheet(com_awocoupon_ASSETS.'/css/style.css');
		
		
		//Get data from the model
		$genstats 	= & $this->get( 'Generalstats' );
		$lastentered	= & $this->get( 'LastEntered' );
		

		$this->assignRef('genstats'		, $genstats);		
		$this->assignRef('lastentered'	, $lastentered);
		$this->assignRef('pane'			, $pane);
		$this->assignRef('update'		, $update);
		$this->assignRef('check'		, $check);
		$this->assignRef('def_lists'	, $def_lists);

		parent::display($tpl);

	}
	
	/**
	 * Creates the buttons view
	 **/
	function addIcon( $image , $view, $text )
	{
		$lang		=& JFactory::getLanguage();
		$link		= 'index.php?option='.AWOCOUPON_OPTION.'&view=' . $view;
?>
		<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
			<div class="icon">
				<a href="<?php echo $link; ?>">
					<?php echo JHTML::_('image', com_awocoupon_ASSETS.'/images/'.$image.'.png' , NULL, NULL, $text ); ?>
					<span><?php echo $text; ?></span></a>
			</div>
		</div>
<?php
	}	

}
?>