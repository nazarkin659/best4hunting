<?php

/**
 * PDF Invoicing Solution for VirtueMart & Joomla!
 * 
 * @package   VM Invoice
 * @version   2.0.31
 * @author    ARTIO http://www.artio.net
 * @copyright Copyright (C) 2011 ARTIO s.r.o. 
 * @license   GNU/GPLv3 http://www.artio.net/license/gnu-general-public-license
 */

jimport('joomla.language.language');

/**
 * Little extend to access some protected properties and maintain compatibility.
 */
class InvoiceLanguage extends JLanguage {

	public function load($extension = 'joomla', $basePath = JPATH_BASE, $lang = null, $reload = false, $default = true)
	{
		//resolve J1.5 compatibility (last parameter missing)
		if (COM_VMINVOICE_ISJ16)
			return parent::load($extension, $basePath, $lang, $reload, $default);
		else
			return parent::load($extension, $basePath, $lang, $reload);	
	}
	
	/**
	 * Possiblity to load frontend overrides also from frontend
	 */
	public function loadOverrides($path){
		
		if (!COM_VMINVOICE_ISJ16) //J1.5 doesnt have overrides
			return ;
		
		$filename = $path . "/language/overrides/".$this->lang.".override.ini";
		
		if (file_exists($filename) AND ($contents = $this->parse($filename))) {
			if (is_array($contents)) {
				$this->override = $contents;
			}
			unset($contents);
		}
	}
}

?>