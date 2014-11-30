<?php
/**
 * @package NST_Order2Mail
 * @version 1.3
 *  @author NST nasieti.com
 * @copyright Copyright (c)2013 Nasieti.com
 * @license GNU General Public License version 3, or later
 **/

defined('_JEXEC') or 	die( 'Direct Access to ' . basename( __FILE__ ) . ' is not allowed.' ) ;

if (!class_exists('vmPSPlugin')) require(JPATH_VM_PLUGINS . DS . 'vmpsplugin.php');
if (!class_exists('VirtueMartModelProduct')) require(JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'product.php');
if (!class_exists('VirtueMartModelOrders')) require(JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'orders.php');
if(!class_exists('VirtueMartModelCustomfields')) require(JPATH_VM_ADMINISTRATOR.DS.'models'.DS.'customfields.php');


class plgVmShipmentNST_Order2mail extends vmPSPlugin {


private $debugtext='';

	function __construct(& $subject, $config) {
		parent::__construct($subject, $config);
	}

	public function plgVmOnUpdateOrderShipment($data,$old_status){
		    $ruleparam=$this->params->get('shippermail', '');
		    if($ruleparam=='') return null;
		    $rules=@json_decode($ruleparam);
		    if(!$rules) return null;
		    $templateEmails=array();
		    $app =& JFactory::getApplication();
		    $debug=$this->params->get('debug', 1);
		    $frontback=$this->params->get('frontback',0);
		    $order=VirtueMartModelOrders::getOrder($data->virtuemart_order_id);
		    jimport( 'joomla.html.html' );
		    $now_hour=JHtml::date('now' , 'H');
		    $now_minute=JHtml::date('now' , 'i');

		    foreach ($rules as $r){
		      $condround=0;
		      $targetEmails=array();
		      $condeval=1;
		    
				  $ostates=$this->getCondByName($r->conditions,'orderstates');
				  if(!in_array($data->order_status,(is_array($ostates) ? $ostates : array($ostates)))){
							  $this->debugtext.="Order status not selected - quitting ".$data->order_status."--".join(',',(is_array($ostates) ? $ostates : array($ostates)))."<br />\n";
							  continue;
				  }

				  $this->debugtext.="Order state matches - ".$data->order_status."--".join(',',(is_array($ostates) ? $ostates : array($ostates)))."<br />\n";

				  foreach($r->conditions as $c){
					 $knam=key((array)$c);
					 $kval=var_export($c->$knam,true);

					$this->debugtext.="[$condround] Testing: ".$knam." [$kval]<br />\n";

					switch($knam){
						  case 'frontback':
							   if($c->frontback==1 && !$app->isSite()) $condeval=0;
							   elseif($c->frontback==2 && !$app->isAdmin()) $condeval=0;
							   else $condeval*=1;
						  break;
						  case 'categories':
						    $condeval*=$this->categoriesFilter($c->categories,$order);
						  break;
						  case 'products':
						    $condeval*=$this->productsFilter((is_array($c->products)) ? $c->products : array($c->products),$order);
						  break;
						  case 'countries':
						    $condeval*=in_array($order['details']['BT']->virtuemart_country_id,(is_array($c->countries)) ? $c->countries : array($c->countries));
						  break;
						  case 'currencies':
						    $condeval*=in_array($order['details']['BT']->order_currency,(is_array($c->currencies)) ? $c->currencies : array($c->currencies));
						  break;
						  case 'vendors':
						    $condeval*=in_array($order['details']['BT']->virtuemart_vendor_id,(is_array($c->vendors)) ? $c->vendors : array($c->vendors));
						  break;
						  case 'manufacturers':
						    $condeval*=$this->manufacturersFilter($c->manufacturers,$order);
						  break;
						  case 'shipmentmethod':
						    $condeval*=in_array($order['details']['BT']->virtuemart_shipmentmethod_id,(is_array($c->shipmentmethod)) ? $c->shipmentmethod : array($c->shipmentmethod));
						    $targetEmails=array_merge($targetEmails,$r->targets);
						  break;
						  case 'paymentmethod':
						    $condeval*=in_array($order['details']['BT']->virtuemart_paymentmethod_id,(is_array($c->paymentmethod)) ? $c->paymentmethod : array($c->paymentmethod));
						  break;
						  case 'time_start':
						    $sat=str_replace(':','',$c->time_start);
						    if(!is_numeric($sat)) continue;
						    if($sat<=($now_hour.$now_minute)) $condeval*=1;
						    else $condeval=0;
						  break;
						  case 'time_stop':

						    $stt=str_replace(':','',$c->time_stop);
						    if(!is_numeric($stt)) continue;
						    if($stt>=($now_hour.$now_minute)) $condeval*=1;
						    else $condeval=0;
						  break;
						  case 'amount_start':
							 if($order['details']['BT']->order_total >= $c->amount_start) $condeval*=1;
							 else $condeval=0;
						  break;
						  case 'amount_stop':
						        if($order['details']['BT']->order_total <= $c->amount_stop) $condeval*=1;
						        else $condeval=0;
						  break;
						}

						if($condeval==0){
						  $this->debugtext.="Condition evaluated false - $knam : ".$kval."<br />\n";
						  break;
						}

				    $condround++;
				}

                                if($condeval){
					  $this->debugtext.= "RULE evaled true - adding:".var_export($r->targets,true)."<br />\n\n";
					  array_push($templateEmails,array('targets' => $r->targets, 'template' => $r->template, 'extras' => $r->extras ));
				}
		    }

		    $this->debugtext.="<br />Sending sets: ".var_export($templateEmails,true)."<br />\n\n";

		    $harddebug=0;
		    if($harddebug==1){
				echo $this->debugtext;
				exit;
		    }

		    $this->sendTemplateEmails($templateEmails,$order);
	}


        public function plgVmOnSelectCheckShipment (VirtueMartCart &$cart) {
                return false;
        }



	   public function plgVmOnShowOrderBEShipment ($virtuemart_order_id, $virtuemart_shipmentmethod_id) {
				return false;
	   }


        public function plgVmOnShowOrderFEShipment ($virtuemart_order_id, $virtuemart_shipmentmethod_id, &$shipment_name) {
				return false;
        }


        private function categoriesFilter($categories,$order){
		$pmod= new VirtueMartModelProduct();
                foreach ($order['items'] as $o){
				  if($this->testMatch($pmod->getProductSingle($o->virtuemart_product_id)->categories,$categories)) return true;
                }
                return false;
        }

        private function productsFilter($products,$order){
                foreach ($order['items'] as $o){
                        if(in_array($o->virtuemart_product_id,$products)) return true;
                }
                return false;
        }

        private function  manufacturersFilter($manufacturers,$order){
			$pmod= new VirtueMartModelProduct();
                foreach ($order['items'] as $o){
				  if($this->testMatch($pmod->getProductSingle($o->virtuemart_product_id)->virtuemart_manufacturer_id,$manufacturers)) return true;
                }
                return false;
        }

	   private function emailReplacements($text,$order,$extras=array()){

		  $replace['orderTotal']=round($order['details']['BT']->order_total,2);
		  $replace['totalProducts']=$this->getTotalProducts($order);
		  $replace['orderNumber']=$order['details']['BT']->order_number;
		  $replace['taxAmount']=$order['details']['BT']->order_tax;
		  $replace['discountAmount']=$order['details']['BT']->order_discountAmount;
		  $replace['ipAddress']=$_SERVER['REMOTE_ADDR'];
		  $replace['orderShipment']=$order['details']['BT']->order_shipment;
		  $replace['customerEmail']=$order['details']['BT']->email;
		  $replace['customerName']=$order['details']['BT']->first_name.' '.$order['details']['BT']->last_name;
		  $replace['productLink']=JRoute::_(JURI::root()."index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=".$order['items'][0]->virtuemart_product_id);
		  $replace['orderLink']=JURI::root()."administrator/index.php?option=com_virtuemart&view=orders&task=edit&virtuemart_order_id=".$order['details']['BT']->virtuemart_order_id;
		  $replace['products']=$this->getProductList($order,$extras);
		  $replace['date']=strftime('%m/%d/%Y');
		  $replace['datetime']=strftime('%m/%d/%Y %H:%M');		  
		  $replace['time']=strftime('%H:%M');		  
		  $replace['statuscode']=$order['details']['BT']->order_status;
		  $replace['status']=$order['details']['BT']->order_status_name;		  
		  $app =& JFactory::getApplication();
		  $replace['frontback']=($app->isSite()) ? 'Site' : 'Admin';

		  
		  $adrtype='BT';
		  $btcompany=($order['details'][$adrtype]->company) ? $order['details'][$adrtype]->company."<br />" : '';
		  $replace['customerAddress']=$btcompany.$order['details'][$adrtype]->address_1." ".$order['details'][$adrtype]->address_2."<br />".$order['details'][$adrtype]->zip." ".$order['details'][$adrtype]->city."<br />".$this->getCountryById($order['details'][$adrtype]->virtuemart_country_id)."<br />Tel.:".$order['details'][$adrtype]->phone_1;

		  if(isset($order['details']['ST'])){
		     $adrtype='ST';
		     $stcompany=($order['details'][$adrtype]->company) ? $order['details'][$adrtype]->company."<br />" : '';
		     $replace['customerAddressST']=$order['details']['ST']->first_name.' '.$order['details']['ST']->last_name."<br />".$stcompany.$order['details'][$adrtype]->address_1." ".$order['details'][$adrtype]->address_2."<br />".$order['details'][$adrtype]->zip." ".$order['details'][$adrtype]->city."<br />".$this->getCountryById($order['details'][$adrtype]->virtuemart_country_id)."<br />Tel.:".$order['details'][$adrtype]->phone_1;
                  }
                  else $replace['customerAddressST']=$replace['customerAddress'];

                  if(preg_match_all('/\{userfield(.*?)\}/',$text,$ufs)){                  
		      foreach ($ufs[1] as $uf){
			$type=substr($uf,0,2);
			$field=substr($uf,2);
			$replace['userfield'.$type.$field]=$order['details'][$type]->$field;
		      }
                  }
                  
                  
                  
		  foreach ($replace as $rep => $val){
			 $text=preg_replace('/\{'.$rep.'\}/',$val,$text);
		  }
		  return $text;
	   }

	   
	   private function getManufacturers(){
	    $manufs = VmModel::getModel ('Manufacturer');
	    $manufs->getManufacturers(true,true,false);

	      foreach ($manufs->_data as $mf){
		$rmfp[$mf->virtuemart_manufacturer_id]=$mf->mf_name;
	      }
	    return $rmfp;
	   }

	   
	   private function getVendors(){
	    $vends = VmModel::getModel ('Vendor');
	    $vends->getVendors();

	      foreach ($vends->_data as $vd){
		$rmfp[$vd->virtuemart_vendor_id]=$vd->vendor_store_name;
	      }
	    return $rmfp;
	   }
	   
	   
	   private function getProductList($order,$extras=array()){
		 $pmod= new VirtueMartModelProduct();
		  $customfields = VmModel::getModel ('Customfields');
		  $return='<table style="border: 1px solid #cccccc;border-bottom:0;min-width: 400px;text-align: center;margin: 20px 0;" cellspacing=0 cellpadding=0>';
		  
		  $showmanuf=$this->getCondByName($extras,'manufyn');
		  $showvend=$this->getCondByName($extras,'vendyn');		  

		  if($showmanuf) $manufacturers=$this->getManufacturers();
		  if($showvend) $vendors=$this->getVendors();		  
		  
		  foreach( $order['items'] as $it){
		    $pdets='';
		    $prdo=$pmod->getProductSingle($it->virtuemart_product_id);
		    
		    $cusf = $customfields ->getProductCustomsField ($prdo);
		    foreach ($cusf as $cus) $pdets.=$cus->custom_title.": ".preg_replace('/\n/','<br \/>',$cus->custom_value);
		    
		    $return.="<tr><td style='border-bottom: 1px solid #cccccc; padding:7px 0'>".$it->product_quantity."x</td><td style='border-bottom: 1px solid #cccccc'>".$it->order_item_name."</td><td style='width: 100px;border-bottom: 1px solid #cccccc; padding:7px 0; font-size: 11px;'>$pdets</td>";
		    
		    if($showmanuf==1) $return.="<td style='border-bottom: 1px solid #cccccc; padding:7px 0'>".$manufacturers[$prdo->virtuemart_manufacturer_id]."</td>";
		    if($showvend==1) $return.="<td style='border-bottom: 1px solid #cccccc; padding:7px 0'>".$vendors[$prdo->virtuemart_vendor_id]."</td>";
		    
		    $return.="</tr>";
		  }
		    $return.="</table>";
		  return $return;
	   }

	   private function getCountryById($id){
                $db = & JFactory::getDBO();

                $db->setQuery("SELECT country_name FROM #__virtuemart_countries where virtuemart_country_id='".$id."'");
                $cntry = $db->loadRow();
			 return $cntry[0];
	   }


	   private function getTotalProducts($order){
		  $total=0;
		  foreach( $order['items'] as $it) $total+=$it->product_quantity;
		  return $total;
	   }

	  private function testMatch($needle,$haystack){
	             if(!is_array($haystack)) $haystack=array($haystack);
                  if(!is_array($needle)) $needle=array($needle);

			   foreach($needle as $n) if(in_array($n,$haystack)) return true;

			   return false;
	  }

	  private function sendTemplateEmails($tes,$order){

		    foreach ($tes as $t){
		    	  
					$emailBody='';
					 //do not process if empty templates or empty targets
					 if(($t['template'] == '' && $this->params->get('emailbody', '') == '') || count($t['targets'])==0) continue;
					 elseif($t['template'] !='' && is_readable($t['template'])){
						$fp=fopen($t['template'],"r");
						while(!feof($fp)){
						    $emailBody.=fgets($fp,2048);
						}
						fclose($fp);
					 }
					 else{
						$emailBody=$this->params->get('emailbody', '');
					 }
				    $emailBody=$this->emailReplacements($emailBody,$order,$t['extras']);
				    if($this->params->get('debug', 1)){
				      if($this->params->get('from', '')=='') $this->debugtext="NO From address - quitting<br />".$this->debugtext;
				      $emailBody.="<hr />\n\n".$this->debugtext;
				    }
				    //we do not have mail from address - quitting
				    if($this->params->get('from', '')=='') break;

				    
				    
				    $mail = JFactory::getMailer()
						  ->setSender(
								array(
									   $this->params->get('from', ''),
									   $this->params->get('namefrom', '')
								)
						  )
						  ->addRecipient($t['targets'])
						  ->setSubject($this->emailReplacements($this->params->get('emailsubject'),$order))
						  ->setBody($emailBody);
						  $mail->isHTML(true);
						  $mail->Encoding = 'base64';
					  if($this->params->get('replyto', '')!='') $mail->addReplyTo($this->params->get('replyto', ''));
				    if (!$mail->Send()) {
				    }
		    }



	  }

	  private function getCondByName($conds,$name){
		  foreach ($conds as $c){
			   $kn=key((array)$c);
			   if($kn==$name) return $c->$kn;
		  }
	  }


}
// No closing tag