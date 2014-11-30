<?php defined('_JEXEC') or die('Restricted access');

# VM OG Meta Tag - System Plugin
# Version	: 1.1
# Copyright (C) 2013 VirtuePlanet Services LLP. All Rights Reserved.
# License	: GNU General Public License version 2. http://www.gnu.org/licenses/gpl-2.0.html
# Author	: VirtuePlanet Services LLP
# Email		: info@virtueplanet.com
# Websites: www.virtueplanet.com

jimport('joomla.plugin.plugin');
jimport('joomla.application.component.model');

class plgSystemVmogmetatag extends JPlugin {
	function plgSystemVmogmetatag(&$subject, $config)	{
		parent::__construct($subject, $config);	
		$this->_plugin = JPluginHelper::getPlugin( 'system', 'vmogmetatag' );
	}
	
function onAfterRender() { 
 $element_url = '/administrator/'; $current_url = $_SERVER['REQUEST_URI'];
 global $mainframe;
 $app = JFactory::getApplication();
 if(!$app->isAdmin()) { 
	require('helper.php');
	VmConfig::loadConfig(); 	
 	$buffer = JResponse::getBody(); 
  $document = &JFactory::getDocument();
  $view =JRequest::getVar('view');
	$option = JRequest::getVar('option');
  $vmogmetatag_title_instance = $document->getTitle();
  $vmogmetatag_base = $document->getBase();
  $vmogmetatag_desc_instance = $document->getDescription();
  $vmogmetatag_name_instance = $app->getCfg('sitename');
	
  $vmogmetatag_type_instance = $this->params->get( 'vmogmetatag_type' );
  $vmogmetatag_image_instance = $this->params->get( 'vmogmetatag_image' );
  $vmogmetatag_latitude_instance = $this->params->get( 'vmogmetatag_latitude' );
  $vmogmetatag_longitude_instance = $this->params->get( 'vmogmetatag_longitude' );
  $vmogmetatag_street_address_instance = $this->params->get( 'vmogmetatag_street_address' );
  $vmogmetatag_locality_instance = $this->params->get( 'vmogmetatag_locality' );
  $vmogmetatag_region_instance = $this->params->get( 'vmogmetatag_region' );
  $vmogmetatag_postal_code_instance = $this->params->get( 'vmogmetatag_postal_code' );
  $vmogmetatag_country_instance = $this->params->get( 'vmogmetatag_country' );
  $vmogmetatag_email_instance = $this->params->get( 'vmogmetatag_email' );
  $vmogmetatag_phone_instance = $this->params->get( 'vmogmetatag_phone' );
  $vmogmetatag_fax_instance = $this->params->get( 'vmogmetatag_fax' );
  $vmogmetatag_admins_instance = $this->params->get( 'vmogmetatag_admins' );
  $use_admin = $this->params->get( 'use_admin' );

  $articlesModel = JModel::getInstance('ContentModelArticle'); 
  $articleId = JRequest::getInt('id', 0); 

  $vendorId = JRequest::getInt('vendorid', 1);
	$categoryModel = VmModel::getModel('category');
	$categoryId = JRequest::getInt('virtuemart_category_id', 0);	
	
  $product_model = VmModel::getModel('product');
  $virtuemart_product_idArray = JRequest::getInt('virtuemart_product_id', 0);
  $vendorId = JRequest::getInt('vendorid', 1);
  $product_model = VmModel::getModel('product');
  $virtuemart_product_idArray = JRequest::getInt('virtuemart_product_id', 0);
  if (is_array($virtuemart_product_idArray)) {
   $virtuemart_product_id = $virtuemart_product_idArray[0];
  } else {
   $virtuemart_product_id = $virtuemart_product_idArray;
  }		
	
  $vmogmetatag_title = "\r\n" .'  <meta property="og:title" content="'.$vmogmetatag_title_instance.'"/>'. "\r\n";
  $vmogmetatag_type =  '  <meta property="og:type" content="'.$vmogmetatag_type_instance.'"/>'. "\r\n";
  $vmogmetatag_url =   '  <meta property="og:url" content="'.$vmogmetatag_base.'"/>'. "\r\n";
  $vmogmetatag_image =   '  <meta property="og:image" content="'.JURI::base().'images/'.$vmogmetatag_image_instance.'" />'. "\r\n";	
  $vmogmetatag_image_thumb = '  <meta property="og:image" content="'.$articleId.'" />'. "\r\n";
        
  if ($view == "article") {
   $article = $articlesModel->getItem($articleId); 
   $imagesCode = $article->images; $thumbCode = str_replace("\/", "/", $imagesCode); $thumb = explode('"', $thumbCode); $thumb_img = $thumb[03];              
   if (!empty($thumb_img)) {
    $vmogmetatag_image_thumb = '  <meta property="og:image" content="'.JURI::base().$thumb_img.'" />'. "\r\n";
   } 
   else {
    $vmogmetatag_image_thumb = '  <meta property="og:image" content="'.JURI::base().''.$vmogmetatag_image_instance.'" />'. "\r\n";
   }
  }
	
  else if ($option == "com_virtuemart" and $view == "category") {
   $category = $categoryModel->getCategory($categoryId);
   $categoryModel->addImages($category);	              
	 $categoryImage = str_replace(' ', '%20', $category->images[0]->file_url);
   $vmogmetatag_image_thumb = '  <meta property="og:image" content="'.JURI::base().''.$categoryImage.'" />'. "\r\n";
  }		
			
  else if ($option == "com_virtuemart" and $view == "productdetails") {
   $product = $product_model->getProduct($virtuemart_product_id); 
   $product_model->addImages($product);	              
	 $productImage = str_replace(' ', '%20', $product->images[0]->file_url);
   $vmogmetatag_image_thumb = '  <meta property="og:image" content="'.JURI::base().''.$productImage.'" />'. "\r\n";
  }	
			
  else {
   $vmogmetatag_image_thumb = '  <meta property="og:image" content="'.JURI::base().''.$vmogmetatag_image_instance.'" />'. "\r\n";
  };

  $vmogmetatag_desc =   '  <meta property="og:description" content="'.$vmogmetatag_desc_instance.'" />'. "\r\n";
  $vmogmetatag_name =   '  <meta property="og:site_name" content="'.$vmogmetatag_name_instance.'" />'. "\r\n";
  if (!empty($vmogmetatag_latitude_instance)) {
   $vmogmetatag_latitude = '  <meta property="og:latitude" content="'.$vmogmetatag_latitude_instance.'"/>'. "\r\n";
  } 
  else {
   $vmogmetatag_latitude = '';
  };
  if (!empty($vmogmetatag_longitude_instance)) {
   $vmogmetatag_longitude = '  <meta property="og:longitude" content="'.$vmogmetatag_longitude_instance.'"/>'. "\r\n";
  } 
  else {
   $vmogmetatag_longitude = '';
  };
  if (!empty($vmogmetatag_street_address_instance)) {
   $vmogmetatag_street_address = '  <meta property="og:street-address" content="'.$vmogmetatag_street_address_instance.'"/>'. "\r\n";
  } 
  else {
   $vmogmetatag_street_address = '';
  };
  if (!empty($vmogmetatag_locality_instance)) {
   $vmogmetatag_locality = '  <meta property="og:locality" content="'.$vmogmetatag_locality_instance.'"/>'. "\r\n";
  } 
  else {
   $vmogmetatag_locality = '';
  };       
  if (!empty($vmogmetatag_region_instance)) {
   $vmogmetatag_region = '  <meta property="og:region" content="'.$vmogmetatag_region_instance.'"/>'. "\r\n";
  } 
  else {
   $vmogmetatag_region = '';
  };
  if (!empty($vmogmetatag_postal_code_instance)) {
   $vmogmetatag_postal_code = '  <meta property="og:postal-code" content="'.$vmogmetatag_postal_code_instance.'"/>'. "\r\n";
  } 
  else {
   $vmogmetatag_postal_code = '';
  }; 
  if (!empty($vmogmetatag_country_instance)) {
   $vmogmetatag_country = '  <meta property="og:country-name" content="'.$vmogmetatag_country_instance.'"/>'. "\r\n";
  } 
  else {
   $vmogmetatag_country = '';
  };
  if (!empty($vmogmetatag_email_instance)) {
   $vmogmetatag_email = '  <meta property="og:email" content="'.$vmogmetatag_email_instance.'"/>'. "\r\n";
  } 
  else {
   $vmogmetatag_email = '';
  };
  if (!empty($vmogmetatag_phone_instance)) {
   $vmogmetatag_phone = '  <meta property="og:phone_number" content="'.$vmogmetatag_phone_instance.'"/>'. "\r\n";
  } 
  else {
   $vmogmetatag_phone = '';
  };       
  if (!empty($vmogmetatag_fax_instance)) {
   $vmogmetatag_fax = '  <meta property="og:fax_number" content="'.$vmogmetatag_fax_instance.'"/>'. "\r\n";
  } 
  else {
   $vmogmetatag_fax = '';
  };
        
  $vmogmetatag_admins =   '  <meta property="fb:admins" content="'.$vmogmetatag_admins_instance.'"/>';
	
  $vmogmetatag_all = $vmogmetatag_title.$vmogmetatag_type.$vmogmetatag_url.$vmogmetatag_image_thumb.$vmogmetatag_desc.$vmogmetatag_name.$vmogmetatag_latitude.$vmogmetatag_longitude.$vmogmetatag_street_address.$vmogmetatag_locality.$vmogmetatag_region.$vmogmetatag_postal_code.$vmogmetatag_country.$vmogmetatag_email.$vmogmetatag_phone.$vmogmetatag_fax;
	
  if ($use_admin == '1') {
   $vmogmetatag_all = $vmogmetatag_all.$vmogmetatag_admins;
  }
  $buffer = str_replace ('<html xmlns="http://www.w3.org/1999/xhtml"', '<html xmlns="http://www.w3.org/1999/xhtml" xmlns:og="http://ogp.me/ns#" xmlns:fb="http://www.facebook.com/2008/fbml" ', $buffer);
  $buffer = str_replace ('</title>', '</title>'.$vmogmetatag_all, $buffer);
			
  JResponse::setBody($buffer);
  return true;
}}}