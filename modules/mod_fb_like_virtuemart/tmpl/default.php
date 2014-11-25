<?php
/* ------------------------------------------------------------------------------------------------------*
 # @package				: mod_fb_like_virtuemart - Social Discount for VirtueMart
 # ------------------------------------------------------------------------------------------------------*
 # @author				: Thakker Technologies
 # @copyright			: Copyright (C) 2012 Thakker Technologies. All rights reserved.
 # @license				: http://www.gnu.org/copyleft/gpl.html GNU/GPL, see license.txt
 # Demo	url				: http://joomla.thakkertech.com
 # Technical support	: http://www.thakkertech.com/forum/4-joomla-extensions-bug-report.html
 # ------------------------------------------------------------------------------------------------------*
*/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$document = JFactory::getDocument();
$document->addStyleSheet(JURI::root().'modules/mod_fb_like_virtuemart/assets/css/vm_social_discount.css');
?>
<div id="vm_social_discount" class="vm_social_discount vm_social_discount<?php echo $moduleclass_sfx;?>">
  <?php if($facebook || $twitter || $google_plus) { 
  JHTML::_('behavior.modal');
  $document->addScript(JURI::root().'modules/mod_fb_like_virtuemart/assets/js/ajax.js');
  ?>
  <?php if($virtuemart_found) { ?>
  <div id="contains"></div>
  <div class="vm_s_d_providers">
	  <?php if($facebook) { ?>
	  <div id="vm_social_discount_fb">
		<?php $document->addScript('http://connect.facebook.net/'.$lang_tag.'/all.js#xfbml=1');?>
		<fb:like href="<?php echo $facebook_url;?>" layout="box_count" action="like" font="arial" show_faces="true" width="48" height="65"></fb:like><div id="fb-root"></div>
	  </div>
	  <?php } if($twitter) { ?>
	  <div id="vm_social_discount_twt">
		<?php $document->addScript('http://platform.twitter.com/widgets.js'); ;?>
		<a href="http://twitter.com/share" class="twitter-share-button" data-url="<?php echo $twitter_url?>" data-text="<?php echo $twitter_data_text;?>" data-count="vertical" data-via="<?php echo $twitter_username?>"><?php echo $tweet_label;?></a>
	  </div>
	  <?php } if($google_plus) { ?>
	  <div id="vm_social_discount_gplus">
		<?php $document->addScript('https://apis.google.com/js/plusone.js');?>
		<g:plusone size="tall" callback="gplus_callback" href="<?php echo $google_plus_url;?>"></g:plusone>
	  </div>
	  <?php } ?>
  </div>
<script type="text/javascript">
<?php if($google_plus) { ?>
function gplus_callback(response) {
	if(response.state=='on'){
		processSocialDiscount('google_plus','add_discount');
	}
	else{
		processSocialDiscount('google_plus','remove_discount');
	}
}
<?php } if($facebook) { ?>
try{
	if(FB!=undefined){
		FB.Event.subscribe('edge.create',function(href,widget){
			processSocialDiscount('facebook','add_discount');
		});
	
		FB.Event.subscribe('edge.remove',function(href,widget){
			processSocialDiscount('facebook','remove_discount');
		});
	}
}
catch(err){}
<?php } if($twitter) { ?>
try{
	if(twttr!=undefined){
		twttr.events.bind('tweet',function(event){
			processSocialDiscount('twitter','add_discount');
		});
	}
}
catch(err){}
<?php } ?>
function processSocialDiscount(social_network,action){
	 var vmUrl = '<?php echo $callback_url;?>';
	var  vmD = {};
	vmD['action'] = action;
	vmD['social_network'] = social_network;
	vmHttp('POST', vmUrl, vmOnResponse, vmD);
}
function vmOnResponse(res) {
	if(res && res['message']) {
		if(typeof SqueezeBox!='undefined') {
			var vmsdb = '<div id="vmsd_lightbox"><div id="vmsd_lightbox_text">'+res['message']+'</div></div>';
			document.getElementById('vm_social_discount_lgt').innerHTML = vmsdb;
			SqueezeBox.initialize();
			SqueezeBox.open($('vmsd_lightbox'),{handler:'adopt',size:{x:350,y:135}});
		}
		else {
			showVmMsg(res['message_type'], res['message']);
		}
	}
}
function showVmMsg(typ, msg) {
	var typ = typ ? typ : 'notice';
	var msg = '<dd class="'+typ+' message"><ul><li>'+msg+'</li></ul></dd>';
	if(document.getElementById('system-message')) {
		document.getElementById('vm_social_discount_msg').innerHTML='';
		document.getElementById('system-message').innerHTML=msg;
	}
	else {
		msg = '<dl id="system-message">'+msg+'</dl>';
		document.getElementById('vm_social_discount_msg').innerHTML=msg;
	}
}
</script>
	<?php } else { ?>
		<font style="color:red"><b><?php echo JText::_('MOD_FB_LIKE_VIRTUEMART_DISCOUNT_VM_NOT_INSTALLED');?></b></font>
	<?php } 
	} else { ?>
		<font style="color:red"><b><?php echo JText::_('MOD_FB_LIKE_VIRTUEMART_DISCOUNT_VM_NO_SOCIAL_NETWORK');?></b></font>
	<?php }?>
<div id="vm_social_discount_lgt" style="display:none;height:0;"></div>
<div class="wrap"></div>
<div id="vm_social_discount_msg"></div>
<div class="wrap"></div>
</div>