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

class InvoiceConfig {

	static $translatable = array('mail_subject', 'mail_message', 'invoice_filename','mail_dn_subject', 'mail_dn_message', 'dn_filename');
		
	function __construct($xml,$data)
	{
		if (COM_VMINVOICE_ISJ16)
		{
			//TODO: check if initial values are got from XML in J1.6
			
			$xml = self::convertXMLToJ16(file_get_contents($xml)); //cnvert xml file to J!1.6 format 
			jimport('joomla.form.form');
			$this->params = JForm::getInstance('com_vminvoice.config', $xml);
			$this->params->bind(json_decode($data));
		}
		else //Joomla 1.5
		{ 
			$this->params = new JParameter($data, $xml);

			//convert translatable fields from JSON back to array
			foreach (self::$translatable as $tranField) {
				// 6.8.2013 dajo: fix the newline characters in the string back to their escaped form, otherwise
				//                the json_decode() function fails
				$val = $this->params->get($tranField);
				if (!is_string($val))
					continue;
				
				$val = str_replace("\n", '\\n', $val); // Fix the newline characters
				if (($decoded = json_decode($val))!==null) {
					$this->params->setValue('_default.'.$tranField, (array)$decoded); //little "hack", set() method would convert to string
				}
			}

			//assign default values in J!1.5
            foreach ($this->params->_xml as $group) {
            	foreach ($group->_children as $child){
            		$key = $child->_attributes['name'];
            		if (is_null($this->params->get($key,null))) //not defined, set default from xml
            			if (isset($child->_attributes['default']))
            				$this->params->def($key,$child->_attributes['default']);
            	}
            }
		}
	}
	
	/**
	 * Get one param
	 * @param unknown_type $param
	 * @param unknown_type $default
	 */
	function get($param,$default=null)
	{
		static $params; //cache
		
		if (!isset($params[$param.'.'.$default])) 
		{
			if (COM_VMINVOICE_ISJ16){
				if (is_null($val = $this->params->getValue($param,null,null))){
					$val = $this->params->getFieldAttribute($param,'default',$default); //get default value from xml
					if (is_string($val) AND strpos($val,';')!==false)
						$val = explode(';',$val);
				}
				$params[$param.'.'.$default] = $val;
			}
			else
				$params[$param.'.'.$default] = $this->params->get($param,$default);
		}
		return $params[$param.'.'.$default];
	}
	
	/**
     * Get all current params as asociative array
     */
    function getAllParams(){
    	
    	$allParams = array();
    	
    	if (COM_VMINVOICE_ISJ16)
    	{
    		$fields = $this->params->getFieldset();
    		
    		foreach ($fields as $key => $val)
    			$allParams[$key] = $this->get($key);
    	}
    	else
    	{
    		foreach ($this->params->_xml as $group) {
	        	foreach ($group->_children as $child){
	            	$key = $child->_attributes['name'];
	            	$allParams[$key] = $this->params->get($key);
	            }
	       	}
    	}
    	return $allParams;
    }
	
    static function convertXMLToJ16($xml)
    {
    	$xml = str_replace('<config>','<form>', (string)$xml);
    	$xml = str_replace('</config>','</form>',$xml);
    	$xml = str_replace('<params','<fieldset',$xml);
    	$xml = str_replace('</params>','</fieldset>',$xml);
    	$xml = str_replace('<param ','<field ',$xml);
    	$xml = str_replace('</param>','</field>',$xml);
    	$xml = str_replace(' addpath="',' addfieldpath="',$xml);

    	return $xml;
    }
}

?>