<?php
/**
 * @package SjCore
 * @subpackage Fields
 * @version 1.0
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2012 YouTech Company. All Rights Reserved.
 * @author YouTech Company http://www.smartaddons.com
 *
 */
defined('_JEXEC') or die;
defined('_CORE') or die;
JFormHelper::loadFieldClass('list');

class _Core_Field_ImageFunctions extends JFormFieldList {
	protected $type = 'Thumbnail mode';
	var $_mode = array('none', 'center', 'fill', 'fit', 'stretch');

	public function getInput(){
		return parent::getInput();
	}
	public function getLabel(){
		$this->description = $this->_image_thumbnail_mode();
		return parent::getLabel();
	}

	private function _image_thumbnail_mode(){
		return "<img style=\"height: 60px;\" src=\"data:;base64,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\" />";
	}

	public function getOptions(){
		$options = parent::getOptions();
		if (count($options)==0){
			foreach ($this->_mode as $mode){
				$options[] = JHtml::_('select.option', $mode, ucwords($mode));
			}
		}
		return $options;
	}
}