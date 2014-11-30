jQuery(document).ready(function(){
                       jQuery(".vm_cart_empy").text("Корзина пустая");
                       jQuery(".col-module-header:has(.mod-title:contains('Выбор валюты'))").css(
                       {'box-shadow':'none',
                       'border-bottom':'none',
                       'padding-left':'5px'});
                       jQuery('.inside:has(.item-page:has(h2:contains("для охоты из США")))').css('background-color','inherit').find('.item-page').css('background-color','#fff');
                       
 
    			jQuery("table.custom td:contains('Не выбран способ доставки'),table.custom td:contains('Не выбран способ оплаты')").css('color','red');
			jQuery('a:contains("Powered by")').parent().remove();
			jQuery('#users-profile-core').css('margin-top','0');
			jQuery('dd.error:has(li:contains("vmError: VmTable #__virtuemart_vendors_ru_ru"))').remove();
			jQuery("dd.error:has(li:contains('vmError: Vendor image given image is not complete'))").remove();
			jQuery(".info.message:has(li:contains('Info: weigth_countries _weightCond orderWeight'))").remove();
			jQuery(".fsf_main>h2:first").remove();
			
			
			if (typeof String.prototype.startsWith != 'function') {
				  String.prototype.startsWith = function (str){
				    return this.indexOf(str) == 0;
				  };
				}
				
			function isNullOrWhiteSpace(str){
				    return str === null || str.match(/^ *$/) !== null;
				}	
			
			
			if(window.location.pathname.startsWith("/proizvoditeli/manufacturer") || window.location.pathname.startsWith("/manufacturer/"))
			{
				jQuery(".category-view").remove();
			}
			
			console.log('before change');
			if(jQuery("#proopc-shipments input:radio[name=virtuemart_shipmentmethod_id]").change(function(){
				console.log('change')
				return true;
			}))
			
			if(window.location.pathname.startsWith("/cart"))
			{
				
				jQuery(".vmshipment").each(function(index){
				
				        var value = jQuery(this).text();
					if(!isNullOrWhiteSpace(value))
					{
					  var firstScope = value.indexOf("(");
					  if(firstScope>0)
					  {
					    var secondScope = value.indexOf(")");
					    if(secondScope>0)
					    {
					       var subValue = value.substring(firstScope,secondScope);
				               if(!subValue.contains("$"))
					       {
						    jQuery(this).text(value.replace(subValue+')',""));      
					       }
				            }
			                  }
					}
				})
			};
			
			jQuery("[name='ialLogout']").attr("action","/account");
			jQuery("#com-form-login,h1:contains('Ваши данные')").remove();
                       });
                      