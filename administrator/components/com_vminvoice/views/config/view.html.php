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

defined('_JEXEC') or die('Restrict Access');

invoiceHelper::legacyObjects('view');

class VMInvoiceViewConfig extends JViewLegacy
{

	function display($tpl = null)
	{ 			
		$this->type = JRequest::getVar('type','');
		
		$title='COM_VMINVOICE_CONFIGURATION';
		$active=null;
		
		if ($this->type=='general'){
			$title='COM_VMINVOICE_GLOBAL_CONFIGURATION';
			$active=1;}
		elseif ($this->type=='invoice'){
			$title='COM_VMINVOICE_INVOICE_CONFIGURATION';
			$active=2;}
		elseif ($this->type=='dn'){
			$title='COM_VMINVOICE_DELIVERY_NOTE_CONFIGURATION';
			$active=3;}

		InvoiceHelper::setSubmenu($active);
			
		JToolBarHelper::title('VM Invoice: ' . JText::_($title), 'config');
		JToolBarHelper::save('save', 'COM_VMINVOICE_SAVE');
		JToolBarHelper::cancel('cancel', 'COM_VMINVOICE_CLOSE');
		
		//load data
		
		$this->vminvoice_config = $this->get('Data');

		$this->paramsdata = $this->vminvoice_config->params;
		$this->paramsdefs = JPATH_COMPONENT_ADMINISTRATOR . '/models/config.xml';
		
		require_once (JPATH_ADMINISTRATOR.'/components/com_vminvoice/helpers/config.php');
		$this->translatable = array_combine(InvoiceConfig::$translatable, InvoiceConfig::$translatable); //for faster search
		
		//put all trabslatable params into separarate object. else rendererer would be confused by array inside value
		$this->paramsTranslatable = new InvoiceConfig($this->paramsdefs, $this->paramsdata);
		
		$this->installNewTCPDFFonts();
		$this->availableFonts = $this->getAvailableFonts();
			
		$maxSizeText = ($maxSize = $this->getMaxUploadSize()) ? '<br>'.JText::sprintf('COM_VMINVOICE_MAX_SIZE', $maxSize) : '';
		
		$this->uploadFontButton = ' <a class="hasTip" title="'.JText::_('COM_VMINVOICE_UPLOAD_FONT').'::'.JText::_('COM_VMINVOICE_UPLOAD_FONT_HELP').'"
		 href="javascript:void(0);" onclick="$(\'upload_font\').style.display=\'block\'">'.JText::_('COM_VMINVOICE_UPLOAD_FONT').'</a><div id="upload_font" style="display:none"><input type="file" name="new_font_file">'.$maxSizeText.'</div>';
		
		
    	parent::display($tpl);
	}
	
    /**
     * Check for new font files uploaded to TCPDF fonts folder and installs them. Only when TCPDF library is selected.
     */
    function installNewTCPDFFonts()
    {
    	

    	//find all *.ttf files in fonts folder
    	$fontsPath = JPATH_ADMINISTRATOR.'/components/com_vminvoice/libraries/tcpdf/fonts/';
    	
    	if (function_exists('glob'))
    	{
    		$ttfFiles = glob($fontsPath.'*.ttf');
    		if ($ttfFiles && count($ttfFiles))
    			foreach ($ttfFiles as $key => $file)
    				$ttfFiles[$key] = pathinfo($file, PATHINFO_FILENAME);
    	}
    	else
    	{
    		$ttfFiles = array();
    		if (!($dir = opendir($fontsPath)))
    			JError::raiseWarning(0,'Opendir: Cannot search fonts folder for new fonts');
    		else
		    	while (false !== ($file = readdir($dir)))
		        	if (preg_match('#^(.+)\.ttf$#i',$file, $match))
		        		$ttfFiles[] = $match[1];
    	}

    	if ($ttfFiles && count($ttfFiles)) foreach ($ttfFiles as $file)
    	{
			$checkFile = strtolower(str_replace('-','',$file)); //how TCPDF shorten uploaded file name
    		
    		//not all tcpdf files presented, install
    		if (!file_exists($fontsPath.$checkFile.'.z') || !file_exists($fontsPath.$checkFile.'.ctg.z') || !file_exists($fontsPath.$checkFile.'.php'))
    		{
    			
    			if (!is_writable($fontsPath) && !chmod($fontsPath, 0777)){
    				JError::raiseWarning(0, 'Cannot install new fonts: Folder '.$fontsPath.' is not writable');
    				break;}
    				
    			if (!isset($tcpdf)){
    				require_once(JPATH_ADMINISTRATOR.'/components/com_vminvoice/helpers/'.'invoicetcpdf.php');
    				$tcpdf = new TCPDF();
    			}
    			
    			$mainframe = JFactory::getApplication();
    			if (/*TCPDF_FONTS::*/$tcpdf->addTTFfont($fontsPath.$file.'.ttf')) //http://sourceforge.net/p/tcpdf/discussion/435311/thread/490c1ad9/
    				$mainframe->enqueueMessage(JText::sprintf('COM_VMINVOICE_FONT_INSTALLED', ucfirst($file)));
    			else
    				JError::raiseWarning(0, JText::sprintf('COM_VMINVOICE_FONT_NOT_INSTALLED', ucfirst($file)));
    		}
    	}
    }
    
    function getAvailableFonts()
    {
    	//TODO: ale přidat tam i to, které už je uvolené. tzn. JEDEN select no matter of knihovna- jen u něho vždy napsat které už jsou k dispizici.
    	
    	$tcpdfFonts = $this->getTCPDFFonts();
    	$mPDFFonts = $this->getMPDFFonts();
    	
    	$final = array();
    	foreach ($tcpdfFonts as $key => $tcpdfFont)
    	{
    		if (!isset($mPDFFonts[$key]))
    			$tcpdfFont.= ' - TCPDF';
    		$final[$key] = $tcpdfFont;
    	}
    	
    	foreach ($mPDFFonts as $key => $mPDFFont)
    	{
    		if (!isset($tcpdfFonts[$key]))
    			$mPDFFont.= ' - mPDF';
    		$final[$key] = $mPDFFont;
    	}
    		
    	asort($final);
    	
    	return $final;
    }
    
    function getMPDFFonts()
    {
    	$fonts = array();
    	
    	
    	return $fonts;
    }
    
    /**
     * Get available fonts for TCPDF
     */
    function getTCPDFFonts()
    {
        //find all *.php files in fonts folder
    	$fontsPath = JPATH_ADMINISTRATOR.'/components/com_vminvoice/libraries/tcpdf/fonts/';
    	$phpFiles = array();
    	
    	if (function_exists('glob'))
    	{
    		if (($files = glob($fontsPath.'*.php'))===false)
    			JError::raiseWarning(0,'GLOB: Cannot search fonts folder for available fonts');
    		else
    			foreach ($files as $file){
    				$filename =  pathinfo($file, PATHINFO_FILENAME);
    				$phpFiles[] = $filename;
    			}
    	}
    	else
    	{
    		if (!($dir = opendir($fontsPath)))
    			JError::raiseWarning(0,'Opendir: Cannot search fonts folder for available fonts');
    		else
		    	while (false !== ($file = readdir($dir)))
		        	if (preg_match('#^(.+)\.php$#i',$file, $match))
		        		$phpFiles[] = $match[1];
    	}
    	
    	asort($phpFiles); //important: sort, because b/i files must come later than original
    	
    	$fonts = array();
    	if (count($phpFiles)) foreach ($phpFiles as $key => $file)
    	{
    		//check if it has .z and .ctg.z neigbours (edit: not neccessary because of core fonts, they have only php files)
    		/*
    		if (!file_exists($fontsPath.$file.'.z') || !file_exists($fontsPath.$file.'.ctg.z'))
    			continue;
    		*/
    		$fonts[$file] = ucfirst(str_replace(array('-','_'),' ',$file));
    	}
    	
    	foreach ($fonts as $key => $val)
    	{
    		//if it is only b/i variantion of existing font, delete from list
    		if (preg_match('#^(.+)bi$#i', $key, $match) || preg_match('#^(.+)(b|i)$#i', $key, $match)) 
    			if (isset($fonts[$match[1]]))
    				unset($fonts[$key]);
    	}
    	
    	return $fonts;
    }
    
    function getMaxUploadSize()
    {
    	$sizes = array();
    	foreach (array('post_max_size','upload_max_filesize','memory_limit') as $iniParam)
    		if (($val = ini_get($iniParam))>0)
    			$sizes[] = $val;
    	
    	if (!$sizes)
    		return false;
    	
    	if (!($bytes = $this->return_bytes(min($sizes))))
    		return false;
    	
    	return $this->convertSize($bytes);
    }
    
    //http://php.net/manual/en/function.ini-get.php#106518
    private function return_bytes ($val)
    {
    	if(empty($val))return 0;
    
    	$val = trim($val);
    
    	preg_match('#([0-9]+)[\s]*([a-z]+)#i', $val, $matches);
    
    	$last = '';
    	if(isset($matches[2])){
    		$last = $matches[2];
    	}
    
    	if(isset($matches[1])){
    		$val = (int) $matches[1];
    	}
    
    	switch (strtolower($last))
    	{
    		case 'g':
    		case 'gb':
    			$val *= 1024;
    		case 'm':
    		case 'mb':
    			$val *= 1024;
    		case 'k':
    		case 'kb':
    			$val *= 1024;
    	}
    
    	return (int) $val;
    }

    private function convertSize($size)
    {
    	$unit=array('b','kb','mb','gb','tb','pb');
    	return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
    }
    
    function renderParamsJ25($paramsFieldset)
    {
    	// First pass - skip translatable fields
    	$anyTranslate = false;
    	foreach ($paramsFieldset as $jformfield) {
    		if (isset($this->translatable[$jformfield->fieldname])) {
    			$anyTranslate = true;
    			continue;
    		}
    
    		$this->renderParamJ25($jformfield);
    	}
    
    	// If any translatable, second pass
    	if ($anyTranslate) {
    		
    		foreach (InvoiceGetter::getTranslatableLanguages() as $language) {
    			echo '<li><label class="lang_label">'.$language->title.'</label></li>';
    
    			foreach ($paramsFieldset as $jformfield) {
    				if (!isset($this->translatable[$jformfield->fieldname]))
    					continue;
    
    				$this->renderParamJ25($jformfield, $language);
    			}
    		}
    	}
    }
    
    function renderParamJ25($jformfield, $language = null)
    {
    	echo COM_VMINVOICE_ISJ30 ? '<div class="control-group">' : '<li>';

    	if ($language)
    		list ($label, $input) = $this->vminvConfigReplaceTranslatableValue($language, $jformfield->fieldname, $jformfield->label, $jformfield->input);
    	else
    		list ($label, $input) = array($jformfield->label, $jformfield->input);
    		
    	if (COM_VMINVOICE_ISJ30){
    		echo '<div class="control-label">'.$label.'</div>';
    		echo '<div class="controls">';
    	}
    	else {
    		echo (string)$label;
    		echo '<fieldset class="radio"><div class=”clr”></div>';
    	}
    	
    	echo (string)$input;
    
    	if ($jformfield->fieldname=='mail_message' OR $jformfield->fieldname=='mail_dn_message')
    		echo '<div style="clear:left">'.JText::_('COM_VMINVOICE_MAIL_INFO').'</div>';
    
    	if ($jformfield->fieldname=='font') //font upload button
    		echo $this->uploadFontButton;
    
    	if (!COM_VMINVOICE_ISJ30)
    		echo '</fieldset>';
    	else
    		echo '</div>';

    	echo COM_VMINVOICE_ISJ30 ? '</div>' : '</li>';
    }
    
    //copy of JParameter->render, only with translatable fields handling
    function renderParamsJ15($object, $name = 'params', $group = '_default')
    {
    	if (!isset($object->_xml[$group])) {
    		return false;
    	}
    
    	$params = $object->getParams($name, $group);
    	$html = array ();
    	$html[] = '<table width="100%" class="paramlist admintable" cellspacing="1">';
    
    	if ($description = $object->_xml[$group]->attributes('description')) {
    		// add the params description to the display
    		$desc	= JText::_($description);
    		$html[]	= '<tr><td class="paramlist_description" colspan="2">'.$desc.'</td></tr>';
    	}
    
    	// First pass - skip translatable params
    	$anyTranslate = false;
    	foreach ($params as $param)
    	{
    		// Skip translatable params
    		if (isset($this->translatable[$param[5]])) {
    			$anyTranslate = true;
    			continue;
    		}
    
    		$html[] = '<tr>';
    
    		if ($group=='COM_VMINVOICE_PAGE_APPEARANCE') //add font upload button
    			$param[1] = preg_replace('#(name="params\[font\]".+</select>)#iUs', '$1'.$this->uploadFontButton, $param[1]);
    		
    		if ($param[0]) {
    			$html[] = '<td width="40%" class="paramlist_key"><span class="editlinktip">'.$param[0].'</span></td>';
    			$html[] = '<td class="paramlist_value">'.$param[1].'</td>';
    		} else {
    			$html[] = '<td class="paramlist_value" colspan="2">'.$param[1].'</td>';
    		}
    
    		$html[] = '</tr>';
    	}
    
    	// Second pass - display translatable params in langauge tabs, if any
    	if ($anyTranslate)
    		$this->vminvConfigRenderTranslatable($params, $html);
    
    	if (count($params) < 1) {
    		$html[] = "<tr><td colspan=\"2\"><i>".JText::_('There are no Parameters for this item')."</i></td></tr>";
    	}
    
    	$html[] = '</table>';
    
    	//add info about replacement fields
    	if ($group=='COM_VMINVOICE_INVOICE_MAILS' OR $group=='COM_VMINVOICE_DN_MAILS')
    		$html[] =  ' <table width="100%" class="paramlist admintable" cellspacing="1">
								<tr>
								<td width="40%" class="paramlist_key"><span class="editlinktip"><label id="paramsinvoice_number-lbl" for="paramsinvoice_number"></label></span></td>
								<td class="paramlist_value">'.JText::_('COM_VMINVOICE_MAIL_INFO').'</td>
								</tr>
								</table>';
    	
    	return implode("\n", $html);
    }
    
    //replace param label, input and input's value by language mutation
    function vminvConfigReplaceTranslatableValue($language, $name, $label, $input)
    {
    	$values = (array)$this->paramsTranslatable->get($name, array());
    	$newValue = '';
    	if (isset($values[$language->lang_code])) {
    		$newValue = $values[$language->lang_code];
    	}
    	else {
    		// Try to find default value
    		foreach ($values as $key => $val) {
    			if (is_numeric($key)) {
    				$newValue = $val;
    				break;
    			}
    		}
    	}
    
    	// Replace input names
    	$input = preg_replace('#(params\['.preg_quote($name, '#').'\])#i', '$1['.$language->lang_code.']', $input);
    
    	// Replace id
    	$id = '';
    	$oldId = '';
    	if (preg_match('#(id\s*=\s*)"([^"]+)"#i', $input, $matches)) {
    		$oldId = $matches[2];
    		$id = $matches[2].'_'.$language->lang_code;
    		$input = str_replace($matches[0], $matches[1].'"'.$id.'"', $input);
    	}
    
    	// Replace id in label
    	if (!empty($id) && !empty($oldId)) {
    		$label = str_replace('for="'.$oldId.'"', 'for="'.$id.'"', $label);
    	}
    	
    	//TODO: but js already put in header has old id
    	// If editor, fix also javascripts and lins in modals
    	if ($oldId){
    		$input = preg_replace('#(on\w+\s*=\s*"[^"]*)'.preg_quote($oldId, '#').'([^"]*")#is', '$1'.$id.'$2', $input); 
    		$input = str_ireplace('e_name='.$oldId, 'e_name='.$id, $input); //and links in modal links
    	}
    	
    	// Add language title to tooltip
    	$label = preg_replace('#(title=")([^":]+)(::)#', '$1$2 ('.$language->title.')$3', $label);
    	
    	// Replace value
    	$isTextArea = (stripos($input, 'textarea') !== false);
    
    	if ($isTextArea) //editor
    		$input = preg_replace('#(<\s*textarea[^>]+>).*(<\s*\/textarea\s*>)#is', '$1'.$newValue.'$2', $input);
    	else	//normal input
    		$input = preg_replace('#(value\s*=\s*)"([^"]*)"#i', '$1"'.$newValue.'"', $input);
    	
    	return array($label, $input);
    }
    
    function vminvConfigRenderTranslatable($params, &$html)
    {
    	foreach (InvoiceGetter::getTranslatableLanguages() as $language) {
    		$html[] = '<tr><td colspan="2">&nbsp;</td></tr>';
    		$html[] = '<tr><td colspan="2" class="paramlist_lang">'.$language->title.'</td></tr>';
    
    		foreach ($params as $param)
    		{
    			// Skip not translatable params
    			if (!isset($this->translatable[$param[5]]))
    				continue;
    
    			list ($label, $input) = $this->vminvConfigReplaceTranslatableValue($language, $param[5], $param[0], $param[1]);
    			$param[0] = $label;
    			$param[1] = $input;
    			
    			$html[] = '<tr>';
    
    			if ($param[0]) {
    				$html[] = '<td width="40%" class="paramlist_key"><span class="editlinktip">'.$param[0].'</span></td>';
    				$html[] = '<td class="paramlist_value">'.$param[1].'</td>';
    			} else {
    				$html[] = '<td class="paramlist_value" colspan="2">'.$param[1].'</td>';
    			}
    
    			$html[] = '</tr>';
    		}
    	}
    }
}
?>