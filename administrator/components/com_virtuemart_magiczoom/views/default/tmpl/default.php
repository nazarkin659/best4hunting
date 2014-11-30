<?php

/*------------------------------------------------------------------------
# mod_virtuemart_magiczoom - Magic Zoom for Joomla with VirtueMart
# ------------------------------------------------------------------------
# Magic Toolbox
# Copyright 2011 MagicToolbox.com. All Rights Reserved.
# @license - http://www.opensource.org/licenses/artistic-license-2.0  Artistic License 2.0 (GPL compatible)
# Website: http://www.magictoolbox.com/magiczoom/modules/joomla/
# Technical Support: http://www.magictoolbox.com/contact/
/*-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die('Restricted access.');

//NOTE: load tooltip behavior
JHtml::_('behavior.tooltip');

$document = JFactory::getDocument();
$document->addScript(JURI::root().'media/com_virtuemart_magiczoom/options.js');
$document->addStyleSheet(JURI::root().'media/com_virtuemart_magiczoom/backend.css');

?>
<form action="<?php echo JRoute::_('index.php?option=com_virtuemart_magiczoom'); ?>" method="post" name="adminForm" id="adminForm" >
    <input type="hidden" name="option" value="com_virtuemart_magiczoom" />
    <input type="hidden" name="view" value="default" />
    <input type="hidden" name="task" value="displayConfig" />
    <?php echo JHtml::_('form.token'); ?>
</form>
<table class="adminlist magictoolbox" style="width: auto;">
    <tr>
        <th class="title" width="200px">Parameter</th>
        <?php foreach($this->profiles as $profilesId => $profilesTitle) { ?>
        <th class="title<?php if($profilesId == 'default') echo ' def'; ?>" width="100px">
            <?php echo $profilesTitle; ?>
            <br />
            <a class="settings" href="<?php echo JRoute::_('index.php?option=com_virtuemart_magiczoom&profile='.$profilesId); ?>">Edit</a>
        </th>
        <?php } ?>
    </tr>
    <?php foreach($this->groups as $groupName => $params) { ?>
        <tr>
            <th colspan="7" class="subtitle"><?php echo $groupName;?></th>
        </tr>
        <?php
            $i=0;
            foreach($params as $paramId => $paramValue) {
                $i++;
                $description = $this->tool->params->getDescription($paramId);
                if($this->tool->params->getType($paramId) != 'array' && $this->tool->params->valuesExists($paramId)) {
                    $description = empty($description) ? '' : $description.', ';
                    $description .= 'allowed values: '.implode(', ', $this->tool->params->getValues($paramId));
                }
                $labelParts = array();
                preg_match('/^(.*?)\s+\(([^\)]*+)\)$/is', $this->tool->params->getLabel($paramId), $labelParts);
                if(empty($labelParts)) {
                    $labelParts = array('', $this->tool->params->getLabel($paramId), '');
                }
                if($labelParts[2] == 'px') $labelParts[2] = 'in pixels';
        ?>
                <tr class="row<?php echo $i%2; ?>">
                    <th>
                        <b><?php echo $labelParts[1]; if(!empty($description)) { ?> (<a class="magictooltip" title="<?php echo htmlspecialchars($description, ENT_COMPAT, 'UTF-8'); ?>" >?</a>)<?php } ?></b>
                        <span><?php echo $labelParts[2]; ?></span>
                    </th>
        <?php       foreach($this->profiles as $profilesId => $profilesTitle) {
                        $paramsExists = isset($this->paramsMap[$profilesId][$groupName]) && in_array($paramId, $this->paramsMap[$profilesId][$groupName]);
        ?>
                    <td<?php 
                        if($profilesId == 'default') echo ' class="def"';
                        else if($paramsExists && !$this->tool->params->checkValue($paramId, $this->tool->params->getDefaultValue($paramId, $profilesId), $profilesId)) {
                            echo ' class="def_changed"';
                        }
                        ?>><?php
                        if($paramsExists) {
                            $value = $this->tool->params->getValue($paramId, $profilesId);
                            switch($value) {
                                case 'Yes':
                                case 'enable':
                                case 'true':
                                    echo '<span class="yes">'.$value.'</span>';
                                    break;
                                case 'No':
                                case 'disable':
                                case 'false':
                                    echo '<span class="no">'.$value.'</span>';
                                    break;
                                default:
                                    echo $value;
                                    break;
                            }
                        } else {
                            echo '-';
                        }
                    ?></td>
        <?php
                    }
        ?>
                </tr>
        <?php
            }
        ?>
    <?php } ?>
</table>
