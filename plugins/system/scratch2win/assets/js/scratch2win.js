/*----------------------------------------------------------------------
# Scratch2Win - Joomla System Plugin
# ----------------------------------------------------------------------
# Copyright © 2014 VirtuePlanet Services LLP. All rights reserved.
# License - http://www.virtueplanet.com/policies/licenses
# Author: VirtuePlanet Services LLP
# Email: info@virtueplanet.com
# Website:  http://www.virtueplanet.com
----------------------------------------------------------------------*/
if("undefined"===typeof S2W)
{
    var S2W={
    };
    S2W.SWText={
        strings:{
        },_:function(a,b){
            return"undefined"!==typeof this.strings[a.toUpperCase()]?this.strings[a.toUpperCase()]:b
        },load:function(a){
            for(var b in a)this.strings[b.toUpperCase()]=a[b];return this
        }
    };
}

if (typeof scratch2win === "undefined")
{
    var jq = jQuery.noConflict();
    var scratch2win =
    {
        initiateModal: function (element)
        {
            var modalHTML  = '<div id="scratch2win-modal-container-'+element+'" class="scratch2win-modal-container"><div id="scratch2win-modal-'+element+'" class="scratch2win-modal" style="background:#'+S2W.SWText._('BACKGROUNDCOLOR_'+element)+'; color:#'+S2W.SWText._('TEXTCOLOR_'+element)+'; border-color:#'+S2W.SWText._('BORDERCOLOR_'+element)+'">';
            modalHTML += '<div class="scratch2win-modal-wrapper">';
            modalHTML += '<div class="scratch2win-modal-inner">';
            modalHTML += '<div class="scratch2win-modal-header scratch2win-group">';
            modalHTML += S2W.SWText._('HEADER_'+element);
            modalHTML += '</div>';
            modalHTML += '<div class="scratch2win-modal-desc scratch2win-group">';
            modalHTML += S2W.SWText._('DESCRIPTION_'+element);
            modalHTML += '</div>';
            if(S2W.SWText._('COUPON_'+element) != 0)
            {
                modalHTML += '<div class="scratch2win-modal-coupon scratch2win-group">';
                modalHTML += '<div id="scratch2win-coupon-area-'+element+'" class="scratch2win-coupon-area" style="border-color:#'+S2W.SWText._('COUPONBORDERCOLOR_'+element)+'; width:'+S2W.SWText._('SCRATCHPAD_WIDTH_'+element)+'px"></div>';
                modalHTML += '</div>';
            }
            modalHTML += '<div class="scratch2win-modal-footer scratch2win-group">';
            modalHTML += S2W.SWText._('FOOTER_'+element);
            modalHTML += '</div>';
            modalHTML += '<div class="scratch2win-counter-container scratch2win-group">';
            if(S2W.SWText._('SHOW_AUTOCLOSE') != 0)
            {
                modalHTML += '<div class="scratch2win-counter">'+ S2W.SWText._('AUTOCLOSE_MESSAGE').replace('%s', '<span></span>') +'</div>';
            }
            modalHTML += '</div>';
            modalHTML += '</div>';
            modalHTML += '</div>';
            modalHTML += '</div></div>';
            modalHTML += '<a href="#scratch2win-modal-'+element+'" class="scratch2win-hidden-link" id="scratch2win-hidden-link-'+element+'"></a>';

            jq('body').prepend(modalHTML);
        },
        initiateFacybox: function (element)
        {
            jq('#scratch2win-hidden-link-'+element).fancybox({
                    'padding': 0,
                    'cyclic': true,
                    'width': 625,
                    'height': 350,
                    'autoScale': true,
                    'autoDimensions': true,
                    'hideOnOverlayClick': false,
                    'centerOnScroll': true,
                    'margin': 20,
                    'speedIn': 300,
                    'speedOut': 300,
                    'transitionIn': 'elastic',
                    'transitionOut': 'elastic',
                    'easingIn': 'swing',
                    'easingOut': 'swing',
                    'titleShow': false,
                    'onComplete': function() {
                        jq('.scratch2win-counter-container').each(function(){
                                jq(this).removeAttr('style');
                                jq(this).css('min-height',jq(this).height());
                                if(jq(this).is(':empty')) jq(this).hide();
                            });
                        jq('#fancybox-content').find('.scratch2win-modal').parent('div').css({'padding':0});
                    }
                });
        },
        triggerFancybox: function (element, delay, close)
        {
            jq('#scratch2win-hidden-link-'+element).delay(delay).queue(function() {
                    jq(this).trigger('click');
                    scratch2win.autoCloseFancybox(element, close);
                });
        },
        autoCloseFancybox: function(element, close)
        {
            var timeout;
            var n = (close / 1000);
            var contentID = jq('#scratch2win-hidden-link-'+element).attr('href');
            if(!jq(contentID).hasClass('active')) {
                timeout = setTimeout(function() { jq.fancybox.close(); }, close);
            }
            scratch2win.displayCounter(contentID, n);
            jq(contentID).mouseenter(function(){
                    clearTimeout(timeout);
                    jq(this).addClass('active');
                }).mouseleave(function(){
                    jq(this).removeClass('active');
                    if(!jq(contentID).hasClass('active')) {
                        timeout = setTimeout(function() { jq.fancybox.close(); }, close);
                    }
                    scratch2win.displayCounter(contentID, n);
                });
        },
        initiateSlidebox: function (element)
        {
            var slideboxHTML  = '<div id="scratch2win-slidebox-container-'+element+'" class="scratch2win-slidebox-container"><div id="scratch2win-slidebox-'+element+'" class="scratch2win-slidebox" style="background:#'+S2W.SWText._('BACKGROUNDCOLOR_'+element)+'; color:#'+S2W.SWText._('TEXTCOLOR_'+element)+'; border-color:#'+S2W.SWText._('BORDERCOLOR_'+element)+'">';
            slideboxHTML += '<div class="scratch2win-slidebox-wrapper">';
            slideboxHTML += '<div class="scratch2win-slidebox-inner">';
            slideboxHTML += '<div class="scratch2win-slidebox-header scratch2win-group">';
            slideboxHTML += S2W.SWText._('HEADER_'+element);
            slideboxHTML += '</div>';
            slideboxHTML += '<div class="scratch2win-slidebox-desc scratch2win-group">';
            slideboxHTML += S2W.SWText._('DESCRIPTION_'+element);
            slideboxHTML += '</div>';
            if(S2W.SWText._('COUPON_'+element) != 0)
            {
                slideboxHTML += '<div class="scratch2win-slidebox-coupon scratch2win-group">';
                slideboxHTML += '<div id="scratch2win-coupon-area-'+element+'" class="scratch2win-coupon-area" style="border-color:#'+S2W.SWText._('COUPONBORDERCOLOR_'+element)+'; width:'+S2W.SWText._('SCRATCHPAD_WIDTH_'+element)+'px"></div>';
                slideboxHTML += '</div>';
            }
            slideboxHTML += '<div class="scratch2win-slidebox-footer scratch2win-group">';
            slideboxHTML += S2W.SWText._('FOOTER_'+element);
            slideboxHTML += '</div>';
            slideboxHTML += '<div class="scratch2win-counter-container scratch2win-group">';
            if(S2W.SWText._('SHOW_AUTOCLOSE') != 0)
            {
                slideboxHTML += '<div class="scratch2win-counter">'+ S2W.SWText._('AUTOCLOSE_MESSAGE').replace('%s', '<span></span>') +'</div>';
            }
            slideboxHTML += '</div>';
            slideboxHTML += '</div>';
            slideboxHTML += '<a href="#scratch2win-slidebox-'+element+'" class="scratch2win-slidebox-close"></a>';
            slideboxHTML += '</div>';
            slideboxHTML += '</div>';
            slideboxHTML += '</div>';

            jq('body').prepend(slideboxHTML);
            //jq(slideboxHTML).insertBefore('body');
        },
        triggerSlidebox: function (element, delay, close)
        {
            var timeout;
            var that = jq('#scratch2win-slidebox-'+element);
            var n = (close / 1000);
            jq(that).mouseenter(function(){
                    jq(that).addClass('active');
                }).mouseleave(function(){
                    jq(that).removeClass('active');
                });
            jq(that).delay(delay).slideDown('slow', function(){
                    if(!jq(that).hasClass('active')) {
                        timeout = setTimeout(function() { jq(that).slideUp('slow'); }, close);
                    }
                    scratch2win.displayCounter(that, n);
                    jq(that).mouseenter(function(){
                            clearTimeout(timeout);
                            jq(that).addClass('active');
                        }).mouseleave(function(){
                            if(!jq(that).hasClass('active')) {
                                timeout = setTimeout(function() { jq(that).slideUp('slow'); }, close);
                            }
                            jq(that).removeClass('active');
                            scratch2win.displayCounter(that, n);
                        })
                });
            jq('.scratch2win-slidebox-close').click(function(){
                    var id = jq(this).attr('href');
                    jq(id).addClass('closed');
                    jq(id).clearQueue();
                    jq(id).stop();
                    jq(id).slideUp('slow');
                });
        },
        displayCounter: function(parent, n)
        {
            var countDown = jq(parent).find('.scratch2win-counter > span');
            jq(countDown).parents('.scratch2win-counter-container').css('min-height',jq(countDown).parents('.scratch2win-counter-container').height());
            (function loop() {
                    jq(countDown).text(n);
                    if (n-- && !jq(parent).hasClass('active')) {
                        setTimeout(loop, 1000);
                    }
                })();
        },
        triggerScratchPad: function(element)
        {
            jq('#scratch2win-coupon-area-'+element).wScratchPad({
                    'width': S2W.SWText._('SCRATCHPAD_WIDTH_'+element),
                    'height': S2W.SWText._('SCRATCHPAD_HEIGHT_'+element),
                    'image': S2W.SWText._('SCRATCHPAD_IMAGE_'+element),
                    'image2': S2W.SWText._('SCRATCHPAD_IMAGE2_'+element),
                    'color': '#'+S2W.SWText._('SCRATCHPAD_COLOR_'+element),
                    'overlay': 'none',
                    'size': S2W.SWText._('SCRATCHPAD_SIZE_'+element),
                    'realtimePercent': false,
                    'scratchDown': null,
                    'scratchUp': null,
                    'scratchMove': null,
                    'cursor': S2W.SWText._('SCRATCHPAD_CURSOR_'+element)
                });
        }
    }
}
