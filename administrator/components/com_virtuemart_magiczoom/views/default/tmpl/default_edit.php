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
//JHtml::script(JURI::root().'media/com_virtuemart_magiczoom/options.js');
//JHtml::script('options.js', 'media/com_virtuemart_magiczoom/');
$document->addScript(JURI::root().'media/com_virtuemart_magiczoom/options.js');
//JHtml::stylesheet(JURI::root().'media/com_virtuemart_magiczoom/backend.css');
//JHtml::stylesheet('backend.css', 'media/com_virtuemart_magiczoom/');
$document->addStyleSheet(JURI::root().'media/com_virtuemart_magiczoom/backend.css');

?>
Quick search: <input id="search-query" onkeyup="SearchCollection.keyCallback(this)"/> <span id="search-matches"></span>
<form action="<?php echo JRoute::_('index.php?option=com_virtuemart_magiczoom'); ?>" method="post" name="adminForm" id="adminForm" >
    <input type="hidden" name="task" value="apply" />
    <?php echo JHtml::_('form.token'); ?>
    <input type="hidden" name="profile" value="<?php echo $this->profile; ?>"/>
<?php
    $this->tool->params->setProfile($this->profile);
    foreach($this->paramsMap[$this->profile] as $groupName => $group) {
        $groupId = str_replace(array('_',' '), '-', strtolower($groupName));
        $groupAlias = ucwords(str_replace('_', ' ', $groupName));
?>
    <div id="group-<?php echo $groupId; ?>">
        <h3 class="magictoolbox"><?php echo $groupAlias; ?> parameters</h3>
        <table class="adminlist magictoolbox" style="width: 100%;">
            <tr>
                <th class="title" width="200px">Parameter</span></th>
                <th class="title"><input class="mBtn" type="submit" value="Save"/></th>
            </tr>
<?php
            $i = 0;
            foreach($group as $paramId) {
                $i++;
                $description = $this->tool->params->getDescription($paramId);
                if($this->tool->params->getType($paramId) != 'array' && $this->tool->params->valuesExists($paramId)) {
                    $description = empty($description) ? '' : $description.', ';
                    $description .= 'allowed values: '.implode(', ', $this->tool->params->getValues($paramId));
                }
?>
            <tr class="row<?php echo $i%2; ?>">
                <th>
<?php
                $labelParts = array();
                preg_match('/^(.*?)\s+\(([^\)]*+)\)$/is', $this->tool->params->getLabel($paramId), $labelParts);
                if(empty($labelParts)) {
                    $labelParts = array('', $this->tool->params->getLabel($paramId), '');
                }
                if($labelParts[2] == 'px') $labelParts[2] = 'in pixels';
                if(!empty($labelParts[2])) {
                    $labelParts[2] .= ', ';
                }
                $defaultValue = $this->tool->params->getDefaultValue($paramId);
                $labelParts[2] .= '<i>default:</i> '.(empty($defaultValue) ? 'empty' : $defaultValue);
?>
                    <b class="search-target"><?php echo $labelParts[1]; ?></b><?php if(!empty($description)) { ?> (<a class="magictooltip" title="<?php echo htmlspecialchars($description, ENT_COMPAT, 'UTF-8'); ?>" >?</a>)<?php } ?><span>(<?php echo $labelParts[2]; ?>)</span>
                </th>
                <td>
<?php
                //NOTE: display icon for 'magicscroll' option
                if($paramId == 'magicscroll') {
                    echo "<img id=\"magicscroll_icon\" src=\"{$this->imageUrl}magicscroll.png\" alt=\"\" />";
                }
                switch($this->tool->params->getType($paramId)) {
                    case 'text':
                    case 'num':
                    case 'float':
                        $value = $this->tool->params->getValue($paramId);
                        if($this->tool->params->checkValue($paramId, $defaultValue)) {
                            $style = '';
                        } else {
                            $style = ' style="font-weight: bold;"';
                        }
                        echo "<input name=\"config[{$paramId}]\" value=\"{$value}\"{$style} class=\"mzinp\" />";
                        break;
                    case 'array':
                        $value = $this->tool->params->getValue($paramId);
                        if($this->tool->params->getSubType($paramId) == 'radio') {
                            foreach($this->tool->params->getValues($paramId) as $v) {
                                $label = $v;
                                if(in_array(strtolower($label), array('yes', 'no', 'top', 'bottom', 'left', 'right', 'disable', 'enable', 'true', 'false'))) {
                                    $label = strtolower($label);
                                    if($label == 'disable' || $label == 'false') $label = 'no';
                                    if($label == 'enable' || $label == 'true') $label = 'yes';
                                    $label = '<img src="'.$this->imageUrl.$label.'.gif" />';
                                }
?>
                                <input type="radio" value="<?php echo $v; ?>"<?php echo (($v == $value)?' checked="checked"':''); ?> name="config[<?php echo $paramId; ?>]" id="<?php echo $paramId.'-'.$v; ?>" class="mzr"/><label for="<?php echo $paramId.'-'.$v; ?>"><?php echo $label; ?></label>&nbsp;
<?php
                            }
                        } else if($this->tool->params->getSubType($paramId) == 'select') {
?>
                                <select name="config[<?php echo $paramId; ?>]" id="<?php echo $paramId; ?>">
<?php
                                foreach($this->tool->params->getValues($paramId) as $v) {
?>
                                <option value="<?php echo $v; ?>"<?php echo (($v == $value)?' selected="selected"':''); ?>><?php echo $v; ?></option>
<?php
                                }
?>
                                </select>
<?php
                        }
                        break;
                }
                //NOTE: display description for 'magicscroll' option
                if($paramId == 'magicscroll') {
                    echo '<div class="description">'.$description.'</div>';
                }
?>
                </td>
            </tr>
<?php
            }
?>
        </table>
    </div>
<?php
    }
?>
</form>
<script type="text/javascript">
//<![CDATA[
if(window.console === undefined) {
    window.console = {
        log: function() {},
        debug: function() {}
    };
}

Cookie = {
    set: function(name, value, expires, path, domain, secure) {
          document.cookie = name + "=" + escape(value) +
            ((expires) ? "; expires=" + expires : "") +
            ((path) ? "; path=" + path : "") +
            ((domain) ? "; domain=" + domain : "") +
            ((secure) ? "; secure" : "");
        },

    get: function(name) {
        var cookie = " " + document.cookie;
        var search = " " + name + "=";
        var setStr = null;
        var offset = 0;
        var end = 0;
        if(cookie.length > 0) {
            offset = cookie.indexOf(search);
            if(offset != -1) {
                offset += search.length;
                end = cookie.indexOf(";", offset)
                if(end == -1) {
                    end = cookie.length;
                }
                setStr = unescape(cookie.substring(offset, end));
            }
        }
        return(setStr);
    }
}

function GroupOptions() {
    this.total  = 0;
    this.hidden = 0;
};

function SearchTarget(elem) {
    this.elem = elem;
    this.index = [];
    this.init();
}

SearchTarget.prototype.init = function() {
    this.initGroup();
}

SearchTarget.prototype.initGroup = function() {
    if(!this.getGroupNode().options){
        this.getGroupNode().options = new GroupOptions();
    }
    this.getGroupNode().options.total++;
};

SearchTarget.prototype.getParentNode = function() {
    return this.elem.parentNode.parentNode;
};

SearchTarget.prototype.getGroupNode = function() {
    return this.elem.parentNode.parentNode.parentNode.parentNode.parentNode;
};

SearchTarget.prototype.updateIndex = function() {
    this.index = [];
    this.index.push(this.getTitle());
    this.index.push(this.getText());
    return this;
};

SearchTarget.prototype.getTitle = function() {
    return this.getParentNode().title;
};

SearchTarget.prototype.getText = function() {
    return this.elem.childNodes[0].textContent;
};

SearchTarget.prototype.getSearchData = function() {
    if(!this.index.length) {
        this.updateIndex();
    }
    return this.index.join(' ');
};

SearchTarget.prototype.hide = function() {
    if(!this.getParentNode().className.match(/hidden/)) {
        this.getParentNode().className += ' hidden';
    }
    this.hideGroup();
};

SearchTarget.prototype.show = function() {
    this.getParentNode().className = this.getParentNode().className.replace(' hidden', '');
    this.showGroup();
};

SearchTarget.prototype.showGroup = function() {
    var group = this.getGroupNode();
    group.options.hidden = 0;
    group.className = group.className.replace(' hidden', '');
}

SearchTarget.prototype.hideGroup = function() {
    var group = this.getGroupNode();
    group.options.hidden++;
    if(group.options.hidden == group.options.total && !group.className.match(/hidden/)) {
        group.className += ' hidden';
    }
}

SearchCollection = {
    elements    : [],
    matchesElem : null,
    searchElem  : null,
    lastCheck   : new Date(),
    delay       : 400,//ms
    waiting     : 0,
    matches     : 0,
    key         : 'mz-search-query'
}

SearchCollection.init = function() {
    this.matchesElem = document.getElementById('search-matches');
    this.searchElem = document.getElementById('search-query');
    if(!this.matchesElem || !this.searchElem) return;
    var query = Cookie.get(this.key);
    if(query) {
        this.keyCallback(query, false);
        this.searchElem.value = query;
    }
    var targets = document.getElementsByTagName('b');
    for(var i in targets) {
        if(typeof(targets[i]) === 'object' && targets[i].className === 'search-target' && targets[i].tagName.toLowerCase() === 'b') {
            SearchCollection.push(targets[i]);
        }
    }
}

SearchCollection.push = function(el) {
    if(typeof(el) === 'object') {
        if(!(el instanceof SearchTarget)) {
            el = new SearchTarget(el);
        }
        this.elements.push(el);
    }
};

SearchCollection.filter = function(text, delayed) {
    this.matches = 0;
    if(delayed === undefined) {
        delayed = '';
    } else {
        delayed = ', Delayed search';
    }

    //console.log('Searching: ' + text + delayed);
    if(text === '') {
        text = '-';
    }
    var reg = new RegExp(text.replace(/[^a-z0-9]/gi, '.*'), 'i');
    //console.log(reg);
    for(var i in this.elements) if(this.elements[i] instanceof SearchTarget) {
        if(this.elements[i].getSearchData().match(reg)) {
            this.elements[i].show();
            this.matches++;
        } else {
            this.elements[i].hide();
        }
    }
    this.matchesElem.innerHTML = 'Found ' + this.matches + ' parameters';
};

SearchCollection.checkDate = function() {
    var newDate = new Date(),
        timeDiff = newDate.getTime() - this.lastCheck.getTime(),
        pass = false;
    if(timeDiff > this.delay) {
        this.lastCheck = newDate;
        pass = true;
    }
    return pass;
};

SearchCollection.keyCallback = function(el, delayed) {
    clearTimeout(this.waiting);
    var text = '';
    if(typeof(el) === 'object') {
        text = el.value;
    } else {
        text = el;
    }
    Cookie.set(this.key, text);
    if(this.checkDate()) {
        this.filter(text, delayed);
    } else {
        var _self = this;
        this.waiting = setTimeout(function() {
            _self.keyCallback(text, true);
        }, this.delay);
    }
};

SearchCollection.init();
//]]>
</script>
