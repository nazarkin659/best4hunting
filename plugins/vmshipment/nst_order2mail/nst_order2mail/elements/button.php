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

class JFormFieldButton extends JFormField {

    protected $type = 'button';

    protected function getInput() {
        // Initialize variables.
        $document = JFactory::getDocument();
	   if($this->element['js']==1) $document->addScriptDeclaration("
jQuery.fn.serializeObject = function()
{
    var o = {};
    var a = this.serializeArray();
    jQuery.each(a, function() {
        if (o[this.name] !== undefined) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
};

function getKey(obj){
    var keys='';
    for(var k in obj){
         if(k != '') return k;
    }
    return '';
}

function getRuleByName(name,rules){
  var myrule;
  jQuery(rules).each(function(index){
     if(this.conditions[0].rulename==name){ myrule=index; return; }
  });
  return myrule;
}


function showMsg(msg){
    if(jQuery(jQuery('.rulemessage')).is(':visible')) jQuery('#rulemessage').fadeOut(function(){ jQuery(this).fadeIn(function(){jQuery(this).html(msg)})});
    else jQuery(jQuery('.rulemessage')).html(msg).fadeIn();
}

function getConds(){
	 var form=jQuery('#style-form').serializeObject();
	 var targs=jQuery.map(form['jform[params][targets]'].split(\"\\n\"), jQuery.trim);
	   targs = jQuery.grep(targs, function (a) {
		  var pattern = /^([\w!.%+\-])+@([\w\-])+(?:\.[\w\-]+)+$/;
		  return a.match(pattern);
	   });

	 if(targs.length==0){
	    showMsg('". JText::_('VMSHIPMENT_ORDER2MAIL_INVALID_EMAIL')."');
	   return;
         }

	 var conds= {
		conditions: [],
		targets: targs,
		extras: [],
		template: jQuery('#jform_params_emailfile').val(),
	 };
	 conds.conditions.push({'rulename': jQuery('#jform_params_rulename').val()});
	 if(form['jform[params][orderstates][]']) conds.conditions.push({'orderstates': form['jform[params][orderstates][]']});
	 conds.conditions.push({'frontback': jQuery('#jform_params_frontback input:checked').val()});
	 if(form['jform[params][categories][]']) conds.conditions.push({'categories': form['jform[params][categories][]']});
	 if(form['jform[params][products][]']) conds.conditions.push({'products': form['jform[params][products][]']});
	 if(form['jform[params][countries][]']) conds.conditions.push({'countries': form['jform[params][countries][]']});
	 if(form['jform[params][currencies][]']) conds.conditions.push({'currencies': form['jform[params][currencies][]']});
	 if(form['jform[params][vendors][]']) conds.conditions.push({'vendors': form['jform[params][vendors][]']});
	 if(form['jform[params][manufacturers][]']) conds.conditions.push({'manufacturers': form['jform[params][manufacturers][]']});
	 if(form['jform[params][shipmentmethod][]']) conds.conditions.push({'shipmentmethod': form['jform[params][shipmentmethod][]']});
	 if(form['jform[params][paymentmethod][]']) conds.conditions.push({'paymentmethod': form['jform[params][paymentmethod][]']});
	 if(jQuery('#jform_params_time_start').val()!='' && jQuery('#jform_params_time_stop').val()!=''){
	   conds.conditions.push({'time_start': jQuery('#jform_params_time_start').val()});
	   conds.conditions.push({'time_stop': jQuery('#jform_params_time_stop').val()});
	 }
	 if(jQuery('#jform_params_amount_start').val()) conds.conditions.push({'amount_start': jQuery('#jform_params_amount_start').val()});
	 if(jQuery('#jform_params_amount_stop').val()) conds.conditions.push({'amount_stop': jQuery('#jform_params_amount_stop').val()});
	 
	 conds.extras.push({'manufyn': jQuery('#jform_params_manufyn input:checked').val()});
	 conds.extras.push({'vendyn': jQuery('#jform_params_vendyn input:checked').val()});	 
	 
	 return conds;
}


function clearConds(){
                    jQuery('#jform_params_rulename').val('');
                    jQuery('#jform_params_orderstates').val('');
                    jQuery('#jform_params_categories').val('');
                    jQuery('#jform_params_products').val('');
                    jQuery('#jform_params_countries').val('');
                    jQuery('#jform_params_vendors').val('');
                    jQuery('#jform_params_manufacturers').val('');
                    jQuery('#jform_params_currencies').val('');
                    jQuery('#jform_params_shipmentmethod').val('');
                    jQuery('#jform_params_paymentmethod').val('');
                    jQuery('#jform_params_time_start').val('');
                    jQuery('#jform_params_time_stop').val('');
                    jQuery('#jform_params_amount_start').val('');
                    jQuery('#jform_params_amount_stop').val('');
                    jQuery('#jform_params_targets').val('');
		    jQuery('#jform_params_emailfile').val('');
		    jQuery('#jform_params_frontback0').prop('checked',true);
		    jQuery('#jform_params_manufyn0').prop('checked',true);
		    jQuery('#jform_params_vendyn0').prop('checked',true);		    
}


var selectedrule=-1;

	   jQuery(document).ready(function(\$) {
	 jQuery('#jform_params_categories').parent().parent().prepend('<label>". JText::_('VMSHIPMENT_ORDER2MAIL_EDITING')."</label><div style=\"float: left;width: 200px\" id=\"rulesheader\"><select id=\"ruleselect\"><option value=\"-1\">". JText::_('VMSHIPMENT_ORDER2MAIL_NEW')."</option></select></div>');

		jQuery('#rulesheader').append('<div id=\"rulesets\" style=\"float: right; width: 100px;\"><a style=\"margin: 0 10px; float: left;\" id=\"newrul\" href=\"#\">". JText::_('VMSHIPMENT_ORDER2MAIL_CLEAR')."</a></div><br />');

		if(jQuery('#jform_params_shippermail').val()!=''){
			rules=JSON.parse(jQuery('#jform_params_shippermail').val());
			var ii=0;
			jQuery(rules).each(function(){
				var i=ii;
				ii++;
				jQuery('#ruleselect').append('<option value=\"'+this.conditions[0].rulename+'\" id=\"rule_'+this.conditions[0].rulename+'\" >'+this.conditions[0].rulename+'</option>');
			});
			 selectedrule=-1;
		}

                jQuery('a#newrul').click(function(e){
                    e.preventDefault();
			    selectedrule=-1;
			    jQuery('#ruleselect').val(-1);
			    clearConds();
			});

                jQuery('#ruleselect').change(function(e){
				  if(jQuery('#jform_params_shippermail').val() == '') return;
				  rules=JSON.parse(jQuery('#jform_params_shippermail').val());
				  clearConds();
				  if(jQuery('#ruleselect option:selected').val()==-1){selectedrule=-1; return;}
				  var caller=getRuleByName(jQuery('#ruleselect option:selected').val(),rules);

				  jQuery(rules[caller].conditions).each(function(){
				    if(getKey(this)=='frontback') jQuery('#jform_params_'+getKey(this)+this[getKey(this)]).prop('checked',true);
				    else jQuery('#jform_params_'+getKey(this)).val(this[getKey(this)]);
				    selectedrule=caller;
				});

				 jQuery(rules[caller].extras).each(function(){
				    if(getKey(this)=='manufyn') jQuery('#jform_params_'+getKey(this)+this[getKey(this)]).prop('checked',true);
				    else if(getKey(this)=='vendyn') jQuery('#jform_params_'+getKey(this)+this[getKey(this)]).prop('checked',true);
				    else jQuery('#jform_params_'+getKey(this)).val(this[getKey(this)]);
				});				
				
				  jQuery('#jform_params_targets').val('');
				jQuery(rules[caller].targets).each(function(){
				    jQuery('#jform_params_targets').val(jQuery('#jform_params_targets').val()+jQuery.trim(this)+'\\n');
				});
				  jQuery('#jform_params_emailfile').val(rules[caller].template);
			 });

                jQuery('.ordernotify_addrule').click(function(e){
				e.preventDefault();
				var conds=getConds();
                                if(!jQuery('#jform_params_orderstates').val()){
                                  showMsg('". JText::_('VMSHIPMENT_ORDER2MAIL_NOORDER_STATE')."');
                                  return;
                                }
				if(!conds) return;
				var ret=new Array();
				if(jQuery('#jform_params_shippermail').val()!='') ret=JSON.parse(jQuery('#jform_params_shippermail').val());
				ret.push(conds);
				jQuery('#jform_params_shippermail').val(JSON.stringify(ret));
                                Joomla.submitbutton('plugin.apply');
                });

                jQuery('.ordernotify_saverule').click(function(e){
				 e.preventDefault();
				 if(selectedrule==-1){
				    jQuery('.ordernotify_addrule').trigger('click');
				    return;
				 }

                      if(!jQuery('#jform_params_orderstates').val()){
						  showMsg('". JText::_('VMSHIPMENT_ORDER2MAIL_NOORDER_STATE')."');
                                      return;
				 }
				 var newrule=getConds();
				  var rules;
				  if(jQuery('#jform_params_shippermail').val()=='') rules=new Array;
				  else rules=JSON.parse(jQuery('#jform_params_shippermail').val());

				 rules[selectedrule]=newrule;
				 jQuery('#jform_params_shippermail').val(JSON.stringify(rules));
                                Joomla.submitbutton('plugin.apply');
		    });

                jQuery('.ordernotify_deleterule').click(function(e){
				 e.preventDefault();
				var rules=JSON.parse(jQuery('#jform_params_shippermail').val());
				rules.splice(selectedrule,1);

				if(rules.length==0) jQuery('#jform_params_shippermail').val('');
				else jQuery('#jform_params_shippermail').val(JSON.stringify(rules));

                                Joomla.submitbutton('plugin.apply');
			 });

		jQuery('#ruleselect').val(jQuery('#jform_params_rulename').val()).change();
                });

	   ");

	   return "<div style='margin: 10px 0; padding: 4px 0; float: left;border-top: 1px solid #cccccc;border-bottom: 1px solid #cccccc;width: 100%;text-align: center'><div style='float: left; margin: 0 0 0 60px;'><a href='#' class='ordernotify_addrule' style='color: #ffffff;background-color: #6d850a;border-radius: 3px;margin: 2px 5px;box-shadow: 1px 1px 1px rgba(0,0,0,0.5);display: inline-block;height: 26px;line-height: 28px;padding: 0 18px;text-indent: 0!important;width: auto;float: left'>". JText::_('VMSHIPMENT_ORDER2MAIL_ADD')."</a>
	   <a href='#' class='ordernotify_saverule' style='color: #ffffff;background-color: #6d850a;border-radius: 3px;margin: 2px 5px;box-shadow: 1px 1px 1px rgba(0,0,0,0.5);display: inline-block;height: 26px;line-height: 28px;padding: 0 18px;text-indent: 0!important;width: auto;float: left'>". JText::_('VMSHIPMENT_ORDER2MAIL_SAVE')."</a>
	   <a href='#' class='ordernotify_deleterule' style='color: #ffffff;background-color: #6d850a;border-radius: 3px;margin: 2px 5px;box-shadow: 1px 1px 1px rgba(0,0,0,0.5);display: inline-block;height: 26px;line-height: 28px;padding: 0 18px;text-indent: 0!important;width: auto;float: left'>". JText::_('VMSHIPMENT_ORDER2MAIL_DELETE')."</a></div>
	   </div><div class='rulemessage' style='display:none;height: 25px; width: 100%; float: left; text-align: center;background: #FFFF99;padding: 8px 4px;'></div>
	   ";
       return JHTML::_('select.genericlist',$options, $this->name, $attr, "value", "name", $this->value, $this->id);
    }

    protected function getLabel() {
		return '';
    }

}
