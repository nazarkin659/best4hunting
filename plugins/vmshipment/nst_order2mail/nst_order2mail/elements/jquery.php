<?php
/**
 * @package NST_Order2Mail
 * @version 1.3
 *  @author NST nasieti.com
 * @copyright Copyright (c)2013 Nasieti.com
 * @license GNU General Public License version 3, or later
 **/
defined('JPATH_PLATFORM') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldJquery extends JFormField {

    protected $type = 'jquery';

    protected function getInput() {
        $document = JFactory::getDocument();
        $header = $document->getHeadData();
        $mainframe = JFactory::getApplication();
        $template = $mainframe->getTemplate();


$jqmatch[]='jquery';
$jqmatch[]='jqeasy';

$jqPresent=false;

foreach($header['scripts'] as $scriptName => $scriptData){
	foreach($jqmatch as $pat) if(preg_match('/'.$pat.'/i',$scriptName)){ $jqPresent=true; break; }
	if($jqPresent==true) break;
}

if(!$jqPresent) $document->addScript("http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js");



$options[]=array("name"=>JText::_('VMSHIPMENT_ORDER2MAIL_JQUERYAUTO'),"value"=>2);
$options[]=array("name"=>JText::_('JYES'),"value"=>1);
$options[]=array("name"=>JText::_('JNO'),"value"=>0);

       return JHTML::_('select.genericlist',$options, $this->name, "", "value", "name", $this->value, $this->id);
    }

    protected function getLabel() {
        $label = '';
        $text = $this->element['label'] ? (string) $this->element['label'] : (string) $this->element['name'];
        $text = JText::_($text);


        $class = !empty($this->description) ? 'hasTip' : '';
        $label .= '<label id="' . $this->id . '-lbl" for="' . $this->id . '" class="' . $class . '"';

        if (!empty($this->description)) {
            $label .= ' title="' . htmlspecialchars(trim($text, ':') . '::' .
                            JText::_($this->description), ENT_COMPAT, 'UTF-8') . '"';
        }

        $label .= ' style="min-width:150px;max-width:150px;width:150px;display:block;float:left;padding:1px;">' . $text . '</label>';

        return $label;

    }

}
