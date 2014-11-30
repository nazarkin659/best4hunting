<?php
/**
 * @component AwoCoupon for Virtuemart
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @license : GNU/GPL
 * @Website : http://awodev.com
 **/
 
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view' );

class AwoCouponViewAbout extends JView {

	function display( $tpl = null ) {
		// Load tooltips
		JHTML::_('behavior.tooltip', '.hasTip');

		//add css to document
		$document	= & JFactory::getDocument();
		$document->addStyleSheet(com_awocoupon_ASSETS.'/css/style.css');

		//create the toolbar
		JToolBarHelper::title( JText::_( 'COM_AWOCOUPON_AT_ABOUT' ), 'awocoupon' );

		//Retreive version from install file
		$parser =& JFactory::getXMLParser('Simple');
		$parser->loadFile( JPATH_ADMINISTRATOR.'/components/com_awocoupon/awocoupon.xml' );
		$doc		=& $parser->document;
		
		$element	=& $doc->getElementByPath( 'version' );
		$version	= $element->data();
		
		$this->assign( 'version'	, $version );
		
		parent::display( $tpl );
	}
}