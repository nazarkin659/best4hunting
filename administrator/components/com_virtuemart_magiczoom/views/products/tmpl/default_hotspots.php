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

//JHtml::stylesheet(JURI::root().'media/com_virtuemart_magiczoom/backend.css');
//JHtml::stylesheet('backend.css', 'media/com_virtuemart_magiczoom/');
$document = JFactory::getDocument();
$document->addStyleSheet(JURI::root().'media/com_virtuemart_magiczoom/backend.css');

//JHtml::script('http://extjs-public.googlecode.com/svn/tags/extjs-3.3.1/release/adapter/ext/ext-base.js', false, false);
$document->addScript('http://extjs-public.googlecode.com/svn/tags/extjs-3.3.1/release/adapter/ext/ext-base.js');
//JHtml::script('http://extjs-public.googlecode.com/svn/tags/extjs-3.3.1/release/ext-all.js', false, false);
$document->addScript('http://extjs-public.googlecode.com/svn/tags/extjs-3.3.1/release/ext-all.js');
//NOTE: don't work without resources
//JHtml::stylesheet('ext-all.css', 'media/com_virtuemart_magiczoom/extjs2/css/');
$document->addStyleSheet(JURI::root().'media/com_virtuemart_magiczoom/extjs2/css/ext-all.css');

?>

<style>
    ul.MagicZoomPlus-alternates {
        overflow: hidden;
    }
    ul.MagicZoomPlus-alternates li {
        float: left;
        display: block;
        margin: 5px 20px 5px 0px;
    }
    ul.MagicZoomPlus-alternates li input, ul.MagicZoomPlus-alternates li img {
        vertical-align: middle;
    }
    ul.MagicZoomPlus-alternates li img {
        opacity: .8;
    }
    ul.MagicZoomPlus-alternates li img:hover {
        opacity: 1;
    }
    .MagicZoomPlus-hotspot {
        position: absolute;
        cursor: move;
        background: white;
        border: 1px dotted black;
        opacity: 0.4;
        filter: alpha(opacity=40);
    }
    .MagicZoomPlus-hotspot:hover {
        z-index: 1001;
        border: 1px solid black;
        opacity: 0.6;
    }
    .MagicZoomPlus-hotspot-show {
        position: absolute;
        z-index: 1001;
        border: 1px solid black;
        background: white;
        opacity: 0.8;
    }
    /*.stretch {
        width: 95%;
    }
    h2 {
        font-size: 120%;
        margin: 0 0 10px 0;
    }*/
</style>

<!--[if IE]>
<style>
    .MagicZoomPlus-hotspot {
        position: absolute;
        cursor: move;
        background: white;
        border: 1px solid black;
        border-right-width: 2px;
        border-bottom-width: 2px;
        filter: alpha(opacity=40);
    }
    .MagicZoomPlus-hotspot:hover {
        z-index: 1001;
        border: 1px solid black;
        border-right-width: 2px;
        border-bottom-width: 2px;
        filter: alpha(opacity=60);
    }
    .MagicZoomPlus-hotspot-show {
        position: absolute;
        z-index: 1001;
        border: 1px solid black;
        border-right-width: 2px;
        border-bottom-width: 2px;
        background: white;
        filter: alpha(opacity=80);
    }
</style>
<![endif]-->

<form action="<?php echo JRoute::_('index.php?option=com_virtuemart_magiczoom'); ?>" method="post" name="adminForm" id="adminForm" >
    <input type="hidden" name="option" value="com_virtuemart_magiczoom" />
    <input type="hidden" name="view" value="products" />
    <input type="hidden" name="task" value="hotspots" />
    <?php echo JHtml::_('form.token'); ?>
    <input type="hidden" name="productId" value="<?php echo $this->productId; ?>" />
    <input type="hidden" name="target" value="<?php echo $this->targetImageId; ?>"/>
    <input type="hidden" name="height" value="<?php echo $this->dimentions[1]; ?>"/>
    <input type="hidden" name="width" value="<?php echo $this->dimentions[0]; ?>"/>

    <div style="margin: 20px">

        <div style="overflow: hidden; background: #F9F9F9">
            <div id="main_image" style="float: left; margin: 10px; padding: 10px;">
                <img src="<?php echo $this->mainImage->imageUrl; ?>" <?php echo $this->dimentions[3]; ?> alt="product image"/>
            </div>
            <div style="float: left; margin: 10px; padding: 10px;">
                <h2><?php echo $this->mainImage->file_title; ?></h2>
                <?php echo $this->mainImage->file_description; ?>
            </div>
        </div>

        <div style="margin-top: 20px">
                <table class="adminlist table">
                    <tr>
                        <th class="title" width="5%">ID</th>
                        <th class="title" width="10%" style="display:none;">Mode</th>
                        <th class="title" width="30%">Options</th>
                        <th class="title" width="10%">Coords</th>
                        <th class="title" nowrap></th>
                        <th class="title">
                            Active
                        </th>
                        <th class="title">
                            To delete
                        </th>
                    </tr>
                    <?php for($i = 0; $i < count($this->productHotspots); $i++) { $hs = $this->productHotspots[$i]; ?>
                    <tr class="row<?php echo $i%2; ?>" id="im_<?php echo $hs->id; ?>">
                        <td>
                            <?php echo $hs->id; ?>
                        </td>
                        <td style="display:none;">
                            <select id="mode_<?php echo $hs->id; ?>" name="hotspots[<?php echo $hs->id; ?>][mode]">
                                <option value="magicthumb" <?php echo $hs->mode == 'magicthumb' ? 'selected' : ''; ?>>MagicThumb</option>
                                <option disabled value="alert" <?php echo $hs->mode == 'alert' ? 'selected' : ''; ?>>Alert</option>
                                <option disabled value="link" <?php echo $hs->mode == 'link' ? 'selected' : ''; ?>>Link</option>
                                <option disabled value="custom" <?php echo $hs->mode == 'custom' ? 'selected' : ''; ?>>Custom</option>
                            </select>
                        </td>
                        <td>
                            <select id="options_filelist_<?php echo $hs->id; ?>" name="hotspots[<?php echo $hs->id; ?>][file]" <?php echo in_array($hs->mode, array('magicthumb', 'download')) ? '' : 'style="display:none"'; ?>>
                            <?php foreach($this->imageFiles as $key => $value) { ?>
                                <option value="<?php echo $key; ?>" <?php echo $key == $hs->linked_file_id ? 'selected' : ''; ?>><?php echo $value; ?></option>
                            <?php } ?>
                            </select>
                            <div id="options_input_<?php echo $hs->id; ?>"  <?php echo !in_array($hs->mode, array('magicthumb', 'download')) ? '' : 'style="display:none"'; ?>>
                                <input class="stretch" name="hotspots[<?php echo $hs->id; ?>][input]" value="<?php echo htmlentities(stripslashes($hs->option)); ?>"/>
                            </div>
                        </td>
                        <td colspan="2">
                            <div id="coord_<?php echo $hs->id; ?>">
                                x1: <input id="x1_<?php echo $hs->id; ?>" name="hotspots[<?php echo $hs->id; ?>][coord][x1]" maxlength="4" size="3" value="<?php echo round($hs->x1*$this->dimentions[0]); ?>"/>
                                y1: <input id="y1_<?php echo $hs->id; ?>" name="hotspots[<?php echo $hs->id; ?>][coord][y1]" maxlength="4" size="3" value="<?php echo round($hs->y1*$this->dimentions[1]); ?>"/><br/>
                                x2: <input id="x2_<?php echo $hs->id; ?>" name="hotspots[<?php echo $hs->id; ?>][coord][x2]" maxlength="4" size="3" value="<?php echo round($hs->x2*$this->dimentions[0]); ?>"/>
                                y2: <input id="y2_<?php echo $hs->id; ?>" name="hotspots[<?php echo $hs->id; ?>][coord][y2]" maxlength="4" size="3" value="<?php echo round($hs->y2*$this->dimentions[1]); ?>"/>
                            </div>
                        </td>
                        <td align="center">
                            <input type="checkbox" name="hotspots[<?php echo $hs->id; ?>][active]" <?php echo $hs->active ? 'checked' : ''; ?>/>
                        </td>
                        <td align="center">
                            <input type="checkbox" name="hotspots[<?php echo $hs->id; ?>][delete]"/>
                        </td>
                    </tr>
                    <?php } ?>
                </table>
        </div>

    </div>

</form>

<script type="text/javascript">
//<![CDATA[

    //.constrainTo bugfix
    Ext.dd.DDProxy.prototype.constrainTo = function(constrainTo, pad, inContent) {
        if(typeof pad == 'number') {
            pad = {left:pad, right:pad, top:pad, bottom:pad};
        }
        pad = pad || this.defaultPadding;
        var b = Ext.get(this.getEl()).getBox();
        var ce = Ext.get(constrainTo);
        var c = ce.dom == document.body ? { x: 0, y: 0,
            width: YAHOO.util.Dom.getViewportWidth(),
            height: YAHOO.util.Dom.getViewportHeight()} : ce.getBox(inContent || false);
        var topSpace = b.y - c.y;
        var leftSpace = b.x - c.x;

        this.resetConstraints();
        this.setXConstraint(leftSpace - (pad.left||0), // left
            c.width - leftSpace - b.width - (pad.right||0) //right
        );
        this.setYConstraint(topSpace - (pad.top||0), //top
            c.height - topSpace - b.height - (pad.bottom||0) //bottom
        );
    }
    //ebd of .constrainTo bugfix

    mainImage = Ext.get('main_image');

    mainImageBox = mainImage.getBox(true);
    mainImageBox.left = mainImage.getBox(true).x;
    mainImageBox.top = mainImage.getBox(true).y;

    pad = {
        left: mainImage.getPadding('l'),
        right: mainImage.getPadding('r'),
        top: mainImage.getPadding('t'),
        bottom: mainImage.getPadding('b')
    }

    //alert(pad.left+' '+pad.right+' '+pad.top+' '+pad.bottom)
    //alert(mainImageBox.left+' '+mainImageBox.right+' '+mainImageBox.top+' '+mainImageBox.bottom+' '+mainImageBox.height+' '+mainImageBox.width)

    //console.log(offset)

    if(!window.console) {
        window.console = {
            log: function() {},
            debug: function() {}
        }
    }

    function createHotspot(el) {
        var id = el.dom.id.substring(3);
        //console.log(this);

        var select = el.child('select[id^=mode]');

        var optFileList = el.child('select[id^=options_filelist]').setVisibilityMode(Ext.Element.DISPLAY);
        var optInput = el.child('div[id^=options_input]').setVisibilityMode(Ext.Element.DISPLAY);

        var x1 = el.child('input[id^=x1]');
        var x2 = el.child('input[id^=x2]');
        var y1 = el.child('input[id^=y1]');
        var y2 = el.child('input[id^=y2]');

        var coords = Ext.get([x1,x2,y1,y2]);

        var box = mainImage.createChild(
            {
                tag: 'div',
                id: 'img_'+id,
                style: 'border-color: '+colorGen(),
                'class': 'MagicZoomPlus-hotspot'
            }
        );

        el.on("mouseover", function(e) {
            box.replaceClass('MagicZoomPlus-hotspot','MagicZoomPlus-hotspot-show');
        });
        el.on("mouseout", function(e) {
            box.replaceClass('MagicZoomPlus-hotspot-show','MagicZoomPlus-hotspot');
        });

        select.on('change', function(e) {
            switch(this.getValue()) {
                case 'magicthumb':
                case 'download':
                    optFileList.show();
                    optInput.hide();
                    break;
                default:
                    optFileList.hide();
                    optInput.show();
                    break;
            }
        });

        coords.on('mousewheel',function(e) {
            var delta = e.getWheelDelta();
            KeyUpDownWheelListener(delta, e);
        },this,{stopEvent:true})

        coords.on('keyup',function(e) {
            var delta = 0;
            switch(e.button){
                case 37://key arrow up
                    delta++
                    break;
                case 39://key arrow down
                    delta--
                    break;
            }
            KeyUpDownWheelListener(delta, e);
        },this,{stopEvent:true})

        function KeyUpDownWheelListener(delta,e) {
            if(e.shiftKey){
                delta = parseInt(delta*10);
            }

            var old_x1 = x1.getValue(true);
            var old_y1 = y1.getValue(true);
            var old_x2 = x2.getValue(true);
            var old_y2 = y2.getValue(true);

            var trg = Ext.get(e.getTarget());
            trg.dom.value = trg.getValue(true) + delta;

            var co = updateImageBox(id,x1.getValue(true),y1.getValue(true),x2.getValue(true),y2.getValue(true))
            if(co !== false){
                x1.dom.value = co[0];
                y1.dom.value = co[1];
                x2.dom.value = co[2];
                y2.dom.value = co[3];
            } else {
                x1.dom.value = old_x1;
                y1.dom.value = old_y1;
                x2.dom.value = old_x2;
                y2.dom.value = old_y2;
            }
        }

        new Ext.Resizable(box, {
            wrapped: false,
            pinned: false,
            maxWidth: mainImageBox.width,
            maxHeight: mainImageBox.height,
            //animate: true,
            dynamic: false,
            handles: 'all',
            listeners : {
                'resize' : function(resizable, height, width) {
                    var box = resizable.el.getBox();

                    if(box.right > mainImageBox.right) {
                        box.width -= box.right - mainImageBox.right;
                        box.right = mainImageBox.right;
                    }
                    if(box.bottom > mainImageBox.bottom) {
                        box.height -= box.bottom - mainImageBox.bottom;
                        box.bottom = mainImageBox.bottom;
                    }
                    if(box.x < mainImageBox.x) {
                        box.width -= Math.abs(box.x - mainImageBox.x);
                        box.x = mainImageBox.x;
                    }
                    if(box.y < mainImageBox.y) {
                        box.height -= Math.abs(box.y - mainImageBox.y);
                        box.y = mainImageBox.y;
                    }

                    resizable.el.setBounds(box.x, box.y, box.width, box.height, true)

                    //console.log(box);
                    //console.log(offset);
                    updateBoxInput(box, x1, y1, x2, y2)
                }
            }
        });

        box.dd = new Ext.dd.DDProxy(box,'test');

        box.dd.startDrag = function() {
            this.constrainTo("main_image",pad);
        };

        box.dd.endDrag = function() {
            var dragEl = Ext.get(this.getDragEl());
            var el = Ext.get(this.getEl());

            el.setXY(dragEl.getXY());

            updateBoxInput(el.getBox(), x1, y1, x2, y2)
        };

        updateImageBox(
            id,
            x1.getValue(true),
            y1.getValue(true),
            x2.getValue(true) || 20,
            y2.getValue(true) || 20
        );
    }

    Ext.get(Ext.query('tr[id^=im]')).each(function(){createHotspot(this)})

    function updateImageBox(id,x1,y1,x2,y2) {
        var box = Ext.get('img_'+id);
        var out = false;

        if(x1<0) {x1 = 0; out = true;}
        if(y1<0) {y1 = 0; out = true;}
        if(x1>=x2) {x1 = x2 - 1;out = true;}
        if(y1>=y2) {y1 = y2 - 1;out = true;}

        if(x2>=mainImageBox.right - mainImageBox.left) x2 = mainImageBox.right - mainImageBox.left
        if(y2>=mainImageBox.bottom - mainImageBox.top) y2 = mainImageBox.bottom - mainImageBox.top

        var _x1 = x1 + mainImageBox.left;
        var _y1 = y1 + mainImageBox.top;
        var _x2 = x2 - x1;
        var _y2 = y2 - y1;

        if(!out) {
            box.setBounds(_x1,_y1,_x2,_y2);
            return [x1,y1,x2,y2];
        } else {
            return false;
        }
    }

    function updateBoxInput(box,x1,y1,x2,y2) {
        x1.dom.value = box.x - mainImageBox.left;
        y1.dom.value = box.y - mainImageBox.top;
        x2.dom.value = box.right - mainImageBox.left;
        y2.dom.value = box.bottom - mainImageBox.top;
    }

    function colorGen() {
        var colors = ['red','maroon','purple','darkgreen','orange','navy','blue']
        return colors[Math.floor(colors.length * Math.random())];
    }

//]]>
</script>
