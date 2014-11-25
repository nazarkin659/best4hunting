<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

// Include the syndicate functions only once
if (!defined("DS")) define('DS', DIRECTORY_SEPARATOR);
if (file_exists(JPATH_SITE.DS.'components'.DS.'com_fsf'.DS.'helper'.DS.'helper.php'))
{
	require_once( JPATH_SITE.DS.'components'.DS.'com_fsf'.DS.'helper'.DS.'j3helper.php' );
	require_once( JPATH_SITE.DS.'components'.DS.'com_fsf'.DS.'helper'.DS.'helper.php' );
	JHTML::_('behavior.framework', true);

	global $posdata, $date_format;

	$css = FSFRoute::x( "index.php?option=com_fsf&view=css&layout=default" );
	$document = JFactory::getDocument();
	$document->addStyleSheet($css); 
	$css = "modules/mod_fsf_faqs/tmpl/mod_fsf_faqs.css";
	$document->addStyleSheet($css); 
	FSF_Helper::IncludeJQuery();
	$document->addScript( JURI::base().'components/com_fsf/assets/js/jquery.autoscroll.js' );
	$document->addScript(JURI::base().'components/com_fsf/assets/js/accordion.js'); 
	
	$catid = $params->get('catid');
	$dispcount = $params->get('dispcount');
	$maxheight = $params->get('maxheight');
	$mode = $params->get('listtype');
	
	if ($mode == "accordion")
		$maxheight = 0;
	
	$db = JFactory::getDBO();

	$qry = "SELECT * FROM #__fsf_faq_faq";

	$where = array();
	$where[] = "published = 1";

	// for cats
	if ($catid > 0)
	{
		$where[] = "faq_cat_id = " .  FSFJ3Helper::getEscaped($db, $catid);
	} else if ($catid == -5)
	{
		$where[] = "featured = 1";
	}

	if (count($where) > 0)
	{
		$qry .= " WHERE " . implode(" AND ",$where);	
	}

	$order = "ordering";
	$qry .= " ORDER BY $order ";

	if ($dispcount > 0)
		$qry .= " LIMIT $dispcount";

	$db->setQuery($qry);

	$data = $db->loadObjectList();

	$posdata = array();

	if ($mode == "popup")
	{
		$document = JFactory::getDocument();
		$document->addStyleSheet(JURI::root().'components/com_fsf/assets/css/popup.css'); 
		$document->addScript(JURI::root().'components/com_fsf/assets/js/popup.js'); 	
	}

	require( JModuleHelper::getLayoutPath( 'mod_fsf_faqs' ) );
}