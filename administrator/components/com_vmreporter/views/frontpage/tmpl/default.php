<?php
/**
 * @version     1.0.0
 * @package     com_vmreporter
 * @copyright   Copyright (C) 2013 VirtuePlanet Services LLP. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      VirtuePlanet Services LLP <info@virtueplanet.com> - http://www.virtueplanet.com
 */
// no direct access
defined('_JEXEC') or die;

// Import CSS
$document = JFactory::getDocument();
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_vmreporter/assets/css/chosen.css');
$document->addStyleSheet('components/com_vmreporter/assets/css/vmreporter.css');
$document->addScript('components/com_vmreporter/assets/js/jquery.min.js');
$document->addScript('components/com_vmreporter/assets/js/chosen.jquery.min.js');
$document->addScript('components/com_vmreporter/assets/js/component.js');
?>
<div class="adminform">
	<div class="cpanel-left">
		<div class="cpanel">
			<div class="icon-wrapper">
				<div class="icon">
					<a href="<?php echo JRoute::_('index.php?option=com_vmreporter&view=byproducts') ?>"><img src="<?php echo JURI::root().'administrator/components/com_vmreporter/assets/images/product.png'; ?>" alt="<?php echo JText::_('COM_VMREPORTER_TITLE_BYPRODUCTS') ?>"><span><?php echo JText::_('COM_VMREPORTER_TITLE_BYPRODUCTS') ?></span></a>				
				</div>			
			</div>
			<div class="icon-wrapper">
				<div class="icon">
					<a href="<?php echo JRoute::_('index.php?option=com_vmreporter&view=bycategories') ?>"><img src="<?php echo JURI::root().'administrator/components/com_vmreporter/assets/images/product-categories.png'; ?>" alt="<?php echo JText::_('COM_VMREPORTER_TITLE_BYCATEGORIES') ?>"><span><?php echo JText::_('COM_VMREPORTER_TITLE_BYCATEGORIES') ?></span></a>				
				</div>			
			</div>				
			<div class="icon-wrapper">
				<div class="icon">
					<a href="<?php echo JRoute::_('index.php?option=com_vmreporter&view=bymanufacturers') ?>"><img src="<?php echo JURI::root().'administrator/components/com_vmreporter/assets/images/manufacturer.png'; ?>" alt="<?php echo JText::_('COM_VMREPORTER_TITLE_BYMANUFACTURERS') ?>"><span><?php echo JText::_('COM_VMREPORTER_TITLE_BYMANUFACTURERS') ?></span></a>				
				</div>			
			</div>
			<div class="icon-wrapper">
				<div class="icon">
					<a href="<?php echo JRoute::_('index.php?option=com_vmreporter&view=bycustomers') ?>"><img src="<?php echo JURI::root().'administrator/components/com_vmreporter/assets/images/customer.png'; ?>" alt="<?php echo JText::_('COM_VMREPORTER_TITLE_BYCUSTOMERS') ?>"><span><?php echo JText::_('COM_VMREPORTER_TITLE_BYCUSTOMERS') ?></span></a>				
				</div>			
			</div>
			<div class="icon-wrapper">
				<div class="icon">
					<a href="<?php echo JRoute::_('index.php?option=com_vmreporter&view=bycountries') ?>"><img src="<?php echo JURI::root().'administrator/components/com_vmreporter/assets/images/countries.png'; ?>" alt="<?php echo JText::_('COM_VMREPORTER_TITLE_BYCOUNTRIES') ?>"><span><?php echo JText::_('COM_VMREPORTER_TITLE_BYCOUNTRIES') ?></span></a>				
				</div>			
			</div>			
		</div>
	</div>
	
	<div class="cpanel-right">
		<div class="vm-reporter-description">
			<div class="vm-reporter-inner">
				<h1>VM Reporter</h1>
				<h2>Sales Analysis and Reporting Tool for VirtueMart 2.0 eCommerce Component.</h2>
				<p>VM Reporter is a component for VirtueMart 2.0 ecommerce platform which enables you to generate reports of the orders placed in your site against products, product categories, manufacturers, customers and countries. All generated reports are saved which you can view or download at any point of time as per your convenience. You can also plot graphs and charts with the generated reports for better analysis. </p>
				<p>For more details please visit <a href="http://www.virtueplanet.com" title="www.virtueplanet.com">www.virtueplanet.com</a></p>
				<em>Copyright © 2013 VirtuePlanet Services LLP. All Rights Reserved.</em>
			</div>		
		</div>
	</div>
	
</div>
