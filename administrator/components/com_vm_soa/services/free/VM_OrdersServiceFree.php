<?php

define( '_VALID_MOS', 1 );
define( '_JEXEC', 1 );

/**
 * Virtuemart Order SOA Connector
 *
 * Virtuemart Order SOA Connector (Provide functions getOrdersFromStatus, getOrderStatus, getOrder, getAllOrders)
 *
 * @package    com_vm_soa
 * @subpackage component
 * @author     Mickael cabanas (cabanas.mickael|at|gmail.com)
 * @copyright  2012 Mickael Cabanas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version    $Id:$
 */
 
 /** loading framework **/
include_once('../VM_Commons.php');

/**
 * Class OrderStatus
 *
 * Class "OrderStatus" with attribute : id, name, code,
 * 
 *
 * @author     Mickael cabanas (cabanas.mickael|at|gmail.com)
 * @copyright  Mickael cabanas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version    Release:
 */
	class OrderStatus {
	
		public $order_status_id="";
		public $order_status_code="";		
		public $order_status_name="";	
		public $order_status_description="";
		public $ordering="";		
		public $vendor_id="";
		public $published="";
		
		
		function __construct($order_status_id,$order_status_code,$order_status_name,$order_status_description,$ordering,$vendor_id,$published){
		
			$this->order_status_id			=$order_status_id;
			$this->order_status_code		=$order_status_code;		
			$this->order_status_name		=$order_status_name;	
			$this->order_status_description	=$order_status_description;
			$this->ordering					=$ordering;		
			$this->vendor_id				=$vendor_id;
			$this->published				=$published;			
		
		}
	
	}
	
/**
 * Class Order
 *
 * Class "Order" with attribute : id, user_id, vendor_id,  order_number, user_info_id , order_total order_subtotal
 * order_tax, order_tax_details order_shipment, coupon_discount order_currency ...)
 *
 * @author     Mickael cabanas (cabanas.mickael|at|gmail.com)
 * @copyright  Mickael cabanas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version    Release:
 */
	class Order {
		public $id="";
		public $user_id="";		
		public $vendor_id="";		
		public $order_number="";
		public $order_pass="";
		public $user_info_id="";
		public $order_total="";
		public $order_subtotal="";
		public $order_tax="";
		public $order_tax_details="";
		public $order_shipment="";
		public $order_shipment_tax="";
		public $order_payment="";
		public $order_payment_tax="";
		public $coupon_discount="";
		public $coupon_code="";
		public $order_discount="";
		public $order_currency="";
		public $order_status="";
		public $user_currency_id="";
		public $user_currency_rate="";
		public $virtuemart_paymentmethod_id="";
		public $virtuemart_shipmentmethod_id="";
		public $customer_note="";
		public $ip_address="";
		public $created_on="";
		public $modified_on="";
																					
		//constructeur
		function __construct($id,$user_id,$vendor_id,$order_number,$order_pass,$user_info_id,$order_total,$order_subtotal,$order_tax,$order_tax_details,$order_shipment,$order_shipment_tax,$order_payment,$order_payment_tax,
		$coupon_discount,$coupon_code,$order_discount,$order_currency,$order_status,$user_currency_id,$user_currency_rate,$virtuemart_paymentmethod_id,$virtuemart_shipmentmethod_id,$customer_note,$ip_address,$created_on,$modified_on) {

			$this->id					=$id;
			$this->user_id				=$user_id;	
			$this->vendor_id			=$vendor_id;			
			$this->order_number			=$order_number;
			$this->order_pass			=$order_pass;
			$this->user_info_id			=$user_info_id;
			$this->order_total			=$order_total;
			$this->order_subtotal		=$order_subtotal;
			$this->order_tax			=$order_tax;
			$this->order_tax_details	=$order_tax_details;
			$this->order_shipment		=$order_shipment;
			$this->order_shipment_tax	=$order_shipment_tax;
			$this->order_payment		=$order_payment;
			$this->order_payment_tax	=$order_payment_tax;
			$this->coupon_discount		=$coupon_discount;
			$this->coupon_code			=$coupon_code;
			$this->order_discount		=$order_discount;
			$this->order_currency		=$order_currency;
			$this->order_status			=$order_status;
			$this->user_currency_id		=$user_currency_id;
			$this->user_currency_rate	=$user_currency_rate;
			$this->virtuemart_paymentmethod_id	=$virtuemart_paymentmethod_id;
			$this->virtuemart_shipmentmethod_id		=$virtuemart_shipmentmethod_id;
			$this->customer_note		=$customer_note;
			$this->ip_address			=$ip_address;
			$this->created_on			=$created_on;
			$this->modified_on			=$modified_on;
		}
	}
	
  /**
 * Class ShippingRate
 *
 * Class "ShippingRate" with attribute : shipping_rate_id ...,
 * @author     Mickael cabanas (cabanas.mickael|at|gmail.com)
 * @copyright  Mickael cabanas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version    Release:
 */
	class ShippingRate { //NOT IN VM2
		public $shipping_rate_id="";
		public $shipping_rate_name="";		
		public $shipping_rate_carrier_id="";		
		public $shipping_rate_country="";
		public $shipping_rate_zip_start="";
		public $shipping_rate_zip_end="";
		public $shipping_rate_weight_start="";
		public $shipping_rate_weight_end="";
		public $shipping_rate_value="";
		public $shipping_rate_package_fee="";
		public $shipping_rate_currency_id="";
		public $shipping_rate_vat_id="";
		public $shipping_rate_list_order="";
													
		//constructeur
		function __construct($shipping_rate_id,$shipping_rate_name,$shipping_rate_carrier_id,$shipping_rate_country,$shipping_rate_zip_start,
		$shipping_rate_zip_end,$shipping_rate_weight_start,$shipping_rate_weight_end,$shipping_rate_value,$shipping_rate_package_fee,$shipping_rate_currency_id,
		$shipping_rate_vat_id,$shipping_rate_list_order) {

			$this->shipping_rate_id=$shipping_rate_id;
			$this->shipping_rate_name=$shipping_rate_name;	
			$this->shipping_rate_carrier_id=$shipping_rate_carrier_id;			
			$this->shipping_rate_country=$shipping_rate_country;
			$this->shipping_rate_zip_start=$shipping_rate_zip_start;
			$this->shipping_rate_zip_end=$shipping_rate_zip_end;
			$this->shipping_rate_weight_start=$shipping_rate_weight_start;
			$this->shipping_rate_weight_end=$shipping_rate_weight_end;
			$this->shipping_rate_value=$shipping_rate_value;
			$this->shipping_rate_package_fee=$shipping_rate_package_fee;
			$this->shipping_rate_currency_id=$shipping_rate_currency_id;
			$this->shipping_rate_vat_id=$shipping_rate_vat_id;
			$this->shipping_rate_list_order=$shipping_rate_list_order;
			
		}
	}
	
  	/**
	 * Class Coupon
	 *
	 * Class "Coupon" with attribute : coupon_id ...
	 * @author     Mickael cabanas (cabanas.mickael|at|gmail.com)
	 * @copyright  Mickael cabanas
	 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
	 * @version    Release:
	 */
	class Coupon {
	
		public $coupon_id="";
		public $coupon_code="";
		public $percent_or_total="";		
		public $coupon_type="";	
		public $coupon_value="";
		public $coupon_start_date="";	
		public $coupon_expiry_date="";	
		public $coupon_value_valid="";	
		public $published="";	
		
		function __construct($coupon_id, $coupon_code,$percent_or_total,$coupon_type,$coupon_value,$coupon_start_date,$coupon_expiry_date,$coupon_value_valid,$published){
		
			$this->coupon_id=$coupon_id;
			$this->coupon_code=$coupon_code;
			$this->percent_or_total=$percent_or_total;		
			$this->coupon_type=$coupon_type;	
			$this->coupon_value=$coupon_value;	
			$this->coupon_start_date=$coupon_start_date;	
			$this->coupon_expiry_date=$coupon_expiry_date;	
			$this->coupon_value_valid=$coupon_value_valid;	
			$this->published=$published;	
			
		}
	
	}
	
/**
 * Class ShippingMethod
 *
 * Class "ShippingMethod" with attribute : shipping_carrier_id ...
 * @author     Mickael cabanas (cabanas.mickael|at|gmail.com)
 * @copyright  Mickael cabanas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version    Release:
 */
	class ShippingMethod {
	
		public $virtuemart_shipmentmethod_id="";
		public $virtuemart_vendor_id="";		
		public $shipment_jplugin_id ="";	
		public $slug="";	
		public $shipment_element="";	
		public $shipment_params="";	
		public $shipment_name="";
		public $shipment_desc="";
		public $virtuemart_shoppergroup_id="";
		public $ordering="";	
		public $shared="";	
		public $published="";	
		
		function __construct($virtuemart_shipmentmethod_id,$virtuemart_vendor_id,$shipment_jplugin_id ,$slug,$shipment_element
							,$shipment_params,$shipment_name,$shipment_desc,$virtuemart_shoppergroup_id,$ordering,$shared,$published){
		
			$this->virtuemart_shipmentmethod_id=$virtuemart_shipmentmethod_id;
			$this->virtuemart_vendor_id=$virtuemart_vendor_id;		
			$this->shipment_jplugin_id 	=$shipment_jplugin_id ;
			$this->slug=$slug;
			$this->shipment_element=$shipment_element;
			$this->shipment_params=$shipment_params;
			$this->shipment_name=$shipment_name;
			$this->shipment_desc=$shipment_desc;
			$this->virtuemart_shoppergroup_id=$virtuemart_shoppergroup_id;
			$this->ordering=$ordering;
			$this->shared=$shared;
			$this->published=$published;
			
		}
	}	
	
	  /**
 * Class PaymentMethod
 *
 * Class "PaymentMethod" with attribute : payment_method_id ...,
 * @author     Mickael cabanas (cabanas.mickael|at|gmail.com)
 * @copyright  Mickael cabanas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version    Release:
 */
	class PaymentMethod {
		public $payment_method_id="";
		public $virtuemart_vendor_id="";		
		public $payment_jplugin_id="";		
		public $payment_name="";
		public $payment_element="";
		public $discount="";
		public $discount_is_percentage="";
		public $discount_max_amount="";
		public $discount_min_amount="";
		public $payment_params="";
		public $shared="";
		public $ordering="";
		public $published="";
		public $payment_enabled="";
		public $accepted_creditcards="";
		public $payment_extrainfo="";
		
		//constructeur
		function __construct($payment_method_id,$virtuemart_vendor_id,$payment_jplugin_id,$payment_name,$payment_element,
		$discount,$discount_is_percentage,$discount_max_amount,$discount_min_amount,$payment_params,$shared,$ordering,$published) {

			$this->payment_method_id=$payment_method_id;
			$this->virtuemart_vendor_id=$virtuemart_vendor_id;	
			$this->payment_jplugin_id=$payment_jplugin_id	;			
			$this->payment_name=$payment_name;
			$this->payment_element=$payment_element;
			$this->discount=$discount;
			$this->discount_is_percentage=$discount_is_percentage;
			$this->discount_max_amount=$discount_max_amount;
			$this->discount_min_amount=$discount_min_amount;
			$this->payment_params=$payment_params;
			$this->shared=$shared;
			$this->ordering=$ordering;
			$this->published=$published;
			
			
		}
	}
	
/**
 * Class Creditcard
 *
 * Class "Creditcard" with attribute : creditcard_id ...
 * @author     Mickael cabanas (cabanas.mickael|at|gmail.com)
 * @copyright  Mickael cabanas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version    Release:
 */
	class Creditcard {
	
		public $creditcard_id="";
		public $vendor_id="";		
		public $creditcard_name="";	
		public $creditcard_code="";	
		public $shared="";	
		public $published="";	
		
		function __construct($creditcard_id,$vendor_id,$creditcard_name,$creditcard_code,$shared,$published){
		
			$this->creditcard_id	=$creditcard_id;
			$this->vendor_id		=$vendor_id;		
			$this->creditcard_name	=$creditcard_name;	
			$this->creditcard_code	=$creditcard_code;
			$this->shared			=$shared;
			$this->published		=$published;
		}
	}
	
	/**
	 * Class Plugin
	 *
	 * Class "Plugin" with attribute : extension_id ...
	 * @author     Mickael cabanas (cabanas.mickael|at|gmail.com)
	 * @copyright  Mickael cabanas
	 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
	 * @version    Release:
	 */
	class Plugin {
	
		public $extension_id="";
		public $name="";
		public $type="";		
		public $element="";	
		public $folder="";
		public $client_id="";
		public $enabled="";	
		public $access="";	
		public $protected="";	
		public $manifest_cache="";	
		public $params="";	
		public $custom_data="";	
		public $system_data="";	
		public $ordering="";	
		public $state="";	
		
		function __construct($extension_id, $name,$type,$element,$folder,$client_id,$enabled,$access,$protected,$manifest_cache
				,$params,$custom_data,$system_data,$ordering,$state){
		
			$this->extension_id=$extension_id;
			$this->name=$name;
			$this->type=$type;		
			$this->element=$element;	
			$this->folder=$folder;	
			$this->client_id=$client_id;	
			$this->enabled=$enabled;	
			$this->access=$access;	
			$this->protected=$protected;	
			$this->manifest_cache=$manifest_cache;	
			$this->params=$params;
			$this->custom_data=$custom_data;
			$this->system_data=$system_data;
			$this->ordering=$ordering;
			$this->state=$state;
			
		}
	
	}
/**
 * Class CommonReturn
 *
 * Class "CommonReturn" with attribute : returnCode, message, code, 
 *
 * @author     Mickael cabanas (cabanas.mickael|at|gmail.com)
 * @copyright  Mickael cabanas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version    Release:
 */
	class CommonReturn {
		public $returnCode="";
		public $message="";
		public $returnData="";

		//constructeur
		/**
		 * Enter description here...
		 *
		 * @param String $returnCode
		 * @param String $message
		 */
		function __construct($returnCode, $message, $returnData) {
			$this->returnCode = $returnCode;
			$this->message = $message;	
			$this->returnData = $returnData;				
		}
	}		

	
	
  	/**
    * This function getOrderStatus return all status avalaible
	* (expose as WS)
    * @param 
    * @return array of Status
    */
	function getOrderStatus($params) {
				
		/* Authenticate*/
		$result = onAdminAuthenticate($params->login, $params->password, $params->isEncrypted);
		
		$vmConfig = getVMconfig();
		if ($vmConfig->get('soap_auth_order_otherget')==0){
			$result = "true";
		}	
		
		//Auth OK
		if ($result == "true"){
		
			if (!class_exists( 'VirtueMartModelOrderstatus' )) require (JPATH_VM_ADMINISTRATOR.DS.'models'.DS.'orderstatus.php');
			$VirtueMartModelOrderstatus = new VirtueMartModelOrderstatus;
			
			$listStatus = $VirtueMartModelOrderstatus->getOrderStatusList();
			
			foreach ($listStatus as $status)
			{
				$OrderStatus = new OrderStatus($status->virtuemart_orderstate_id,
									$status->order_status_code,
									$status->order_status_name,
									$status->order_status_description,
									$status->ordering,
									$status->virtuemart_vendor_id,
									$status->published);
				$arrayStatus[]= $OrderStatus;
			}
			return $arrayStatus;
			
		}else if ($result == "false"){
			return new SoapFault("JoomlaServerAuthFault", "Authentication KO for : ".$params->login);
		}else if ($result == "no_admin"){
			return new SoapFault("JoomlaServerAuthFault", "User is not a Super Administrator : ".$params->login);
		}else{
			return new SoapFault("JoomlaServerAuthFault", "User does not exist : ".$params->login);
		}
	}
	
	
	/**
    * This is generic function to get order details 
	* (NOT expose as WS)
    * @param Object
    * @return order details
    */
	function getOrderGeneric($params) {
			
			if (empty($params->limite_start)){
				$params->limite_start="0";
			}
			if (empty($params->limite_end)){
				$params->limite_end="300";
			}
			
			$db = JFactory::getDBO();	
			$query  = "SELECT * FROM `#__virtuemart_orders` WHERE 1 ";
			
			if (!empty($params->status)){
				$query .= " AND order_status = '$params->status' ";
			}
			if (!empty($params->order_id)){
				$query .= " AND virtuemart_order_id = '$params->order_id' ";
			}
			if (!empty($params->order_number)){
				$query .= " AND order_number = '$params->order_number' ";
			}
			
			//format date en entree : '2011-07-25 00:00:00' ou '2011-07-18'
			if (!empty($params->date_start)){
				$query .= " AND created_on BETWEEN '$params->date_start' AND '$params->date_end' ";
			}
			
			$query .= " ORDER BY virtuemart_order_id desc ";
			$query .= " LIMIT $params->limite_start, $params->limite_end "; 
			
			$db->setQuery($query);
			
			$rows = $db->loadObjectList();
			//return new SoapFault("TEST", "TEST VM_ID: ".$query."\n".$row->created_on."\n".$date_end);
			foreach ($rows as $row){
				
				$Order = new Order($row->virtuemart_order_id,
									$row->virtuemart_user_id,
									$row->virtuemart_vendor_id,
									$row->order_number,
									$row->order_pass,
									$row->virtuemart_userinfo_id ,
									$row->order_total,
									$row->order_subtotal,
									$row->order_tax,
									$row->order_tax_details,
									$row->order_shipment,
									$row->order_shipment_tax,
									$row->order_payment,
									$row->order_payment_tax,
									$row->coupon_discount,
									$row->coupon_code,
									$row->order_discount,
									$row->order_currency,
									$row->order_status,
									$row->user_currency_id,
									$row->user_currency_rate,
									$row->virtuemart_paymentmethod_id,
									$row->virtuemart_shipmentmethod_id,
									$row->customer_note,
									$row->ip_address,
									$row->created_on, 
									$row->modified_on 
									);
				$orderArray[]=$Order;
			
			}
			return $orderArray;
	}
	
	
	/**
    * This function get order details from order id
	* (expose as WS)
    * @param string The id of the order
    * @return order details
    */
	function getOrder($params) {
	
		$order_id=$params->order_id;
			
		/* Authenticate*/
		$result = onAdminAuthenticate($params->loginInfo->login, $params->loginInfo->password,$params->loginInfo->isEncrypted);
		
		$vmConfig = getVMconfig();
		if ($vmConfig->get('soap_auth_getorder')==0){
			$result = "true";
		}	
		
		//Auth OK
		if ($result == "true"){
		
			
			$ord = getOrderGeneric($params);
			return $ord[0];
			/*if (!class_exists( 'VirtueMartModelOrders' )) require (JPATH_VM_ADMINISTRATOR.DS.'models'.DS.'orders.php');
			$VirtueMartModelOrders = new VirtueMartModelOrders;
			
			$orderInfo = $VirtueMartModelOrders->getOrder($order_id);
			$orderInfo['details'];
			$orderInfo['history'];
			$orderInfo['items'];
			
			//TODO : A terminer 
			$Order = new Order($orderInfo['details']->order_id,$orderInfo['details']->user_id,$orderInfo['details']->vendor_id, $orderInfo['details']->order_number, $orderInfo['details']->user_info_id, $orderInfo['details']->order_total, $orderInfo['details']->order_subtotal,
				$orderInfo['details']->order_tax, $orderInfo['details']->order_tax_details, $orderInfo['details']->order_shipping, $orderInfo['details']->order_shipping_tax, $orderInfo['details']->coupon_discount, $orderInfo['details']->coupon_code, $orderInfo['details']->order_discount, $orderInfo['details']->order_currency,
				$orderInfo['details']->order_status, $orderInfo['details']->cdate, $orderInfo['details']->mdate, $orderInfo['details']->ship_method_id, $orderInfo['details']->customer_note, $orderInfo['details']->ip_address);
			
			return $Order;*/
			
			
		}else if ($result == "false"){
			return new SoapFault("JoomlaServerAuthFault", "Authentication KO for : ".$params->loginInfo->login);
		}else if ($result == "no_admin"){
			return new SoapFault("JoomlaServerAuthFault", "User is not a Super Administrator : ".$params->loginInfo->login);
		}else{
			return new SoapFault("JoomlaServerAuthFault", "User does not exist : ".$params->loginInfo->login);
		}
	}
	
	/**
    * This function get all orders with specified status P, C, R etc...
	* (expose as WS)
    * @param string params (limiteStart, LimitEnd, Status)
    * @return array of orders
    */
	function getOrdersFromStatus($params) {
	
		/* Authenticate*/
		
		$result = onAdminAuthenticate($params->loginInfo->login, $params->loginInfo->password,$params->loginInfo->isEncrypted);
		
		$vmConfig = getVMconfig();
		if ($vmConfig->get('soap_auth_getorder')==0){
			$result = "true";
		}	
		//Auth OK
		if ($result == "true"){
		
			if (empty($params->limite_start)){
				$params->limite_start="0";
			}
			if (empty($params->limite_end)){
				$params->limite_end="100";
			}
			
			return getOrderGeneric($params);
			
		}else if ($result == "false"){
			return new SoapFault("JoomlaServerAuthFault", "Authentication KO for : ".$params->loginInfo->login);
		}else if ($result == "no_admin"){
			return new SoapFault("JoomlaServerAuthFault", "User is not a Super Administrator : ".$params->loginInfo->login);
		}else{
			return new SoapFault("JoomlaServerAuthFault", "User does not exist : ".$params->loginInfo->login);
		}
	}
	
	/**
    * This function get all Orders
	* (expose as WS)
    * @param string params (limiteStart, LimitEnd)
    * @return array of Categories
    */
	function getAllOrders($params) {
			
		/* Authenticate*/
		$result = onAdminAuthenticate($params->loginInfo->login, $params->loginInfo->password,$params->loginInfo->isEncrypted);
		
		$vmConfig = getVMconfig();
		if ($vmConfig->get('soap_auth_getorder')==0){
			$result = "true";
		}	
		//Auth OK
		if ($result == "true"){
		
		
			if (empty($params->limite_start)){
				$params->limite_start="0";
			}
			if (empty($params->limite_end)){
				$params->limite_end="100";
			}
			
			return getOrderGeneric($params);
			
			
		}else if ($result == "false"){
			return new SoapFault("JoomlaServerAuthFault", "Authentication KO for : ".$params->loginInfo->login);
		}else if ($result == "no_admin"){
			return new SoapFault("JoomlaServerAuthFault", "User is not a Super Administrator : ".$params->loginInfo->login);
		}else{
			return new SoapFault("JoomlaServerAuthFault", "User does not exist : ".$params->loginInfo->login);
		}
	}

	
	 /**
    * This function get user_info_id (copy ps_chekout.php)
	* (not expose as WS)
    * @param 
    * @return array of Status
    */
	function getUserInfoId($user_id){
		
		///////////////////TODO///////////////////
		$db = JFactory::getDBO();	
		$query  = "SELECT user_info_id from `#__vm_user_info` WHERE ";
		$query .= "user_id='" . $user_id . "' ";
		$query .= "AND address_type='BT'";
		$db->setQuery($query);
		
		$rows = $db->loadObjectList();
		foreach ($rows as $row){
			$user_info_id=$row->user_info_id;
		}
		return $user_info_id;
		/*$db = new ps_DB();

		/* Select all the ship to information for this user id and
		* order by modification date; most recently changed to oldest
		*/
		/*$q  = "SELECT user_info_id from `#__{vm}_user_info` WHERE ";
		$q .= "user_id='" . $user_id . "' ";
		$q .= "AND address_type='BT'";
		$db->query($q);
		$db->next_record();
		return $db->f("user_info_id");*/
	}
	
	
	
	/**
    * This function get all coupon code
	* (expose as WS)
    * @param string
    * @return Coupon details
    */
	function GetAllCouponCode($params) {
		
		/* Authenticate*/
		$result = onAdminAuthenticate($params->loginInfo->login, $params->loginInfo->password,$params->loginInfo->isEncrypted);
		
		$vmConfig = getVMconfig();
		if ($vmConfig->get('soap_auth_order_otherget')==0){
			$result = "true";
		}	
		
		//Auth OK
		if ($result == "true"){
		
			$db = JFactory::getDBO();	
			
			$query  = "SELECT * FROM #__virtuemart_coupons WHERE 1 ";
			
			if (!empty($params->coupon->coupon_id)){
				$query  .= "AND virtuemart_coupon_id = '".$params->coupon->coupon_id."' ";
			}
			if (!empty($params->coupon->coupon_code)){
				$query  .= "AND coupon_code = '".$params->coupon->coupon_code."' ";
			}
			if (!empty($params->coupon->percent_or_total)){
				$query  .= "AND percent_or_total = '".$params->coupon->percent_or_total."' ";
			}
			if (!empty($params->coupon->coupon_type)){
				$query  .= "AND coupon_type = '".$params->coupon->coupon_type."' ";
			}
			if (!empty($params->coupon->coupon_value)){
				$query  .= "AND coupon_value = '".$params->coupon->coupon_value."' ";
			}
			if (!empty($params->coupon->coupon_start_date)){
				$query  .= "AND coupon_start_date > '".$params->coupon->coupon_start_date."' ";
			}
			if (!empty($params->coupon->coupon_expiry_date)){
				$query  .= "AND coupon_expiry_date < '".$params->coupon->coupon_expiry_date."' ";
			}
			if (!empty($params->coupon->coupon_value_valid)){
				$query  .= "AND coupon_value_valid = '".$params->coupon->coupon_value_valid."' ";
			}
			if (!empty($params->coupon->published)){
				$query  .= "AND published = '".$params->coupon->published."' ";
			}
			
			$db->setQuery($query);
			
			$rows = $db->loadObjectList();
			
			foreach ($rows as $row){
				$Coupon = new Coupon($row->virtuemart_coupon_id ,
									$row->coupon_code,
									$row->percent_or_total, 
									$row->coupon_type, 
									$row->coupon_value,
									$row->coupon_start_date, 
									$row->coupon_expiry_date, 
									$row->coupon_value_valid, 
									$row->published								
									);
				$arrayCoupon[]=$Coupon;
			}
			return $arrayCoupon;
			
			
		}else if ($result == "false"){
			return new SoapFault("JoomlaServerAuthFault", "Authentication KO for : ".$params->loginInfo->login);
		}else if ($result == "no_admin"){
			return new SoapFault("JoomlaServerAuthFault", "User is not a Super Administrator : ".$params->loginInfo->login);
		}else{
			return new SoapFault("JoomlaServerAuthFault", "User does not exist : ".$params->loginInfo->login);
		}
	}
	
	

	/**
    * This function get shipping rate
	* (expose as WS)
    * @param string
    * @return shipping rate
    */
	function GetAllShippingRate($params) {
					
		/* Authenticate*/
		$result = onAdminAuthenticate($params->login, $params->password);
		
		$vmConfig = getVMconfig();
		if ($vmConfig->get('soap_auth_order_otherget')==0){
			$result = "true";
		}	
		
		//Auth OK
		if ($result == "true"){
			
			return getNotInFreeSoap();
			
		}else if ($result == "false"){
			return new SoapFault("JoomlaServerAuthFault", "Authentication KO for : ".$params->login);
		}else if ($result == "no_admin"){
			return new SoapFault("JoomlaServerAuthFault", "User is not a Super Administrator : ".$params->login);
		}else{
			return new SoapFault("JoomlaServerAuthFault", "User does not exist : ".$params->login);
		}
	}
	
	
	
	/**
    * This function get GetAllShippingMethod
	* (expose as WS)
    * @param string
    * @return shipping carrier
    */
	function GetAllShippingMethod($params) {
	
		/* Authenticate*/
		$result = onAdminAuthenticate($params->login, $params->password);
		
		$vmConfig = getVMconfig();
		if ($vmConfig->get('soap_auth_order_otherget')==0){
			$result = "true";
		}
		
		//Auth OK
		if ($result == "true"){
		
			$db = JFactory::getDBO();	
			$query  = "SELECT * FROM #__virtuemart_shipmentmethods sm ";
			$query .= "JOIN `#__virtuemart_shipmentmethods_".VMLANG."` lang ";
			$query .= "on sm.virtuemart_shipmentmethod_id = lang.virtuemart_shipmentmethod_id ";
			
			$db->setQuery($query);
			
			$rows = $db->loadObjectList();
				
			foreach ($rows as $row){
				//return new SoapFault("JoomlaServerAuthFault", $row->virtuemart_shipmentmethod_id);
				$shippingMethod = new ShippingMethod($row->virtuemart_shipmentmethod_id,
														$row->virtuemart_vendor_id,
														$row->shipment_jplugin_id,
														$row->slug,
														$row->shipment_element,
														$row->shipment_params,
														$row->shipment_name,
														$row->shipment_desc,
														$row->virtuemart_shoppergroup_id,
														$row->ordering,
														$row->shared,
														$row->published);
				$arrayShippingMethod[]=$shippingMethod;
			}
			
			return $arrayShippingMethod;
			
			////////////
			/*$db = new ps_DB;

			$list  = "SELECT * FROM #__{vm}_shipping_carrier WHERE 1";
			$db->query($list);
			
			while ($db->next_record()) {
			
				$ShippingCarrier = new ShippingCarrier($db->f("shipping_carrier_id"),$db->f("shipping_carrier_name"),$db->f("shipping_carrier_list_order"));
				$arrayShippingCarrier[]=$ShippingCarrier;
			
			}
			return $arrayShippingCarrier;*/
			
		}else if ($result == "false"){
			return new SoapFault("JoomlaServerAuthFault", "Authentication KO for : ".$params->login);
		}else if ($result == "no_admin"){
			return new SoapFault("JoomlaServerAuthFault", "User is not a Super Administrator : ".$params->login);
		}else{
			return new SoapFault("JoomlaServerAuthFault", "User does not exist : ".$params->login);
		}
	}

	/**
    * This function Get All Payment Method
	* (expose as WS)
    * @param string
    * @return shipping rate
    */
	function GetAllPaymentMethod($params) {
					
		/* Authenticate*/
		$result = onAdminAuthenticate($params->loginInfo->login, $params->loginInfo->password,$params->loginInfo->isEncrypted);
		
		$vmConfig = getVMconfig();
		if ($vmConfig->get('soap_auth_order_otherget')==0){
			$result = "true";
		}	
		
		//Auth OK
		if ($result == "true"){
		
			$query   = "SELECT * FROM #__virtuemart_paymentmethods pm ";
			$query  .= "JOIN #__virtuemart_paymentmethods_".VMLANG." lang on lang.virtuemart_paymentmethod_id=pm.virtuemart_paymentmethod_id ";
			if ($params->payment_enabled == "Y" || $params->payment_enabled == "N"){
				$query  .= "WHERE published = '$params->payment_enabled' ";
			} else {
				$query .= "WHERE 1 ";
			}
			if (!empty($params->payment_method_id)){
				$query  .= "AND pm.virtuemart_paymentmethod_id  = '$params->payment_method_id' ";
			}
			
			$db = JFactory::getDBO();	
			$db->setQuery($query);
			
			$rows = $db->loadObjectList();
			//return new SoapFault("JoomlaServerAuthFault",$query);
			foreach ($rows as $row){
				$PaymentMethod = new PaymentMethod($row->virtuemart_paymentmethod_id ,
												$row->virtuemart_vendor_id ,
												$row->payment_jplugin_id ,
												$row->payment_name,
												$row->payment_element 	,
												$row->discount ,
												$row->discount_is_percentage,
												$row->discount_max_amount ,
												$row->discount_min_amount ,
												$row->payment_params,
												$row->shared ,
												$row->ordering ,
												$row->published
													);
				$arrayPaymentMethod[]=$PaymentMethod;
			}
			return $arrayPaymentMethod;
			
			
			
		}else if ($result == "false"){
			return new SoapFault("JoomlaServerAuthFault", "Authentication KO for : ".$params->loginInfo->login);
		}else if ($result == "no_admin"){
			return new SoapFault("JoomlaServerAuthFault", "User is not a Super Administrator : ".$params->loginInfo->login);
		}else{
			return new SoapFault("JoomlaServerAuthFault", "User does not exist : ".$params->loginInfo->login);
		}
	}
	
	
	/**
    * This function Get All Payment Method
	* (expose as WS)
    * @param string
    * @return shipping rate
    */
	function GetOrderPaymentInfo($params) {
			
		/* Authenticate*/
		$result = onAdminAuthenticate($params->loginInfo->login, $params->loginInfo->password,$params->loginInfo->isEncrypted);
		
		$vmConfig = getVMconfig();
		if ($vmConfig->get('soap_auth_order_otherget')==0){
			$result = "true";
		}
		
		//Auth OK
		if ($result == "true"){
		
			$order_id = $params->order_id;
			
			$db = JFactory::getDBO();	
			$query  = "SELECT virtuemart_paymentmethod_id FROM #__virtuemart_orders WHERE 1 ";
			
			if (!empty($params->order_id)){
				$query  .= "AND virtuemart_order_id = '$order_id' ";
			}
			if (!empty($params->order_number)){
				$query  .= "AND order_number = '$params->order_number' ";
			}
			
			$db->setQuery($query);
			
			$rows = $db->loadObjectList();
			
			foreach ($rows as $row){
				$params->payment_method_id = $row->virtuemart_paymentmethod_id;
			}
			$pm = GetAllPaymentMethod($params);
			//return new SoapFault("JoomlaServerAuthFault",$query);
			return $pm[0];
			//return $pm;
			
			/*$list  = "SELECT * FROM #__{vm}_payment_method pm join #__{vm}_order_payment op on pm.payment_method_id= op.payment_method_id  ";
			$list  .= "WHERE order_id = '$order_id' ";
			$db = new ps_DB;
			
			$db->query($list);*/
			
			/*while ($db->next_record()) {
			
				$PaymentMethod = new PaymentMethod($db->f("payment_method_id"),$db->f("vendor_id"),$db->f("payment_method_name"), $db->f("payment_class"),
				$db->f("shopper_group_id"), $db->f("payment_method_discount"), $db->f("payment_method_discount_is_percent"), $db->f("payment_method_discount_max_amount"), $db->f("payment_method_discount_min_amount"),
				$db->f("list_order"), $db->f("payment_method_code"), $db->f("enable_processor"), $db->f("is_creditcard"), $db->f("payment_enabled"), $db->f("accepted_creditcards"), $db->f("payment_extrainfo"));
				
			
			}
			return $PaymentMethod;*/
			
		}else if ($result == "false"){
			return new SoapFault("JoomlaServerAuthFault", "Authentication KO for : ".$params->loginInfo->login);
		}else if ($result == "no_admin"){
			return new SoapFault("JoomlaServerAuthFault", "User is not a Super Administrator : ".$params->loginInfo->login);
		}else{
			return new SoapFault("JoomlaServerAuthFault", "User does not exist : ".$params->loginInfo->login);
		}
	}
	
	
	/**
    * This function Get Order between date
	* (expose as WS)
    * @param string 
    * @return array of orders
    */
	function GetOrderFromDate($params) {
	
		/* Authenticate*/
		
		$result = onAdminAuthenticate($params->loginInfo->login, $params->loginInfo->password,$params->loginInfo->isEncrypted);
		
		$vmConfig = getVMconfig();
		if ($vmConfig->get('soap_auth_getorder')==0){
			$result = "true";
		}	
		//Auth OK
		if ($result == "true"){
			
			return getOrderGeneric($params);
			/*
			//MARCHE PAS A DEBUG
			global $mosConfig_offset;
			// format : 2010-01-30
			$date_start_Y = substr($params->date_start, 0, 4);
			$date_start_M = substr($params->date_start, 5, 2);
			$date_start_D = substr($params->date_start, 8, 2);
			$date_start = gmmktime(0, 0, 0, (int)$date_start_M, (int)$date_start_D, (int)$date_start_Y);
			 
			$date_end_Y = substr($params->date_end, 0, 4);
			$date_end_M = substr($params->date_end, 5, 2);
			$date_end_D = substr($params->date_end, 8, 2);
			$date_end = gmmktime(23, 59, 59, (int)$date_end_M, (int)$date_end_D,(int)$date_end_Y);
			
			$db = JFactory::getDBO();	
			$query  = "SELECT * FROM #__vm_orders WHERE ";
			if (!empty($params->order_status)){
				$query .= "order_status = '$params->order_status' AND ";
				$query .= "cdate BETWEEN '$date_start' AND '$date_end' ";
				
			}else {
				$query .= "cdate BETWEEN '$date_start' AND '$date_end' ";
			
			}
			$query .= " ORDER BY cdate ASC"; 
			
			$db->setQuery($query);
			
			$rows = $db->loadObjectList();
			
			foreach ($rows as $row){
				$Order = new Order($row->order_id,$row->user_id, $row->vendor_id, $row->order_number, $row->user_info_id, $row->order_total, $row->order_subtotal,
				$row->order_tax, $row->order_tax_details, $row->order_shipping, $row->order_shipping_tax, $row->coupon_discount, $row->coupon_code, $row->order_discount, $row->order_currency,
				$row->order_status, $row->cdate, $row->mdate , $row->ship_method_id, $row->customer_note, $row->ip_address);
				$orderArray[]=$Order;
			}
			return $orderArray;*/
			
			
			
			////////////////////////////////////////
			/*$db = new ps_DB;
			
			// format : 2010-01-30
			 $date_start_Y = substr($params->date_start, 0, 4);
			 $date_start_M = substr($params->date_start, 5, 2);
			 $date_start_D = substr($params->date_start, 8, 2);
			 $date_start = gmmktime(0, 0, 0, (int)$date_start_M, (int)$date_start_D, (int)$date_start_Y);
			 
			 $date_end_Y = substr($params->date_end, 0, 4);
			 $date_end_M = substr($params->date_end, 5, 2);
			 $date_end_D = substr($params->date_end, 8, 2);
			 $date_end = gmmktime(23, 59, 59, (int)$date_end_M, (int)$date_end_D,(int)$date_end_Y);

			$list  = "SELECT * FROM #__{vm}_orders WHERE ";
			if (!empty($params->order_status)){
				$list .= "order_status = '$params->order_status' AND ";
				$list .= "cdate BETWEEN '$date_start' AND '$date_end' ";
				
			}else {
				$list .= "cdate BETWEEN '$date_start' AND '$date_end' ";
			
			}
			$list .= $q . " ORDER BY cdate ASC"; 
			
			
			$db = new ps_DB;
			$db->query($list);
			
			while ($db->next_record()) {
			
				$Order = new Order($db->f("order_id"),$db->f("user_id"), $db->f("vendor_id"), $db->f("order_number"), $db->f("user_info_id"), $db->f("order_total"), $db->f("order_subtotal"),
				$db->f("order_tax"), $db->f("order_tax_details"), $db->f("order_shipping"), $db->f("order_shipping_tax"), $db->f("coupon_discount"), $db->f("coupon_code"), $db->f("order_discount"), $db->f("order_currency"),
				$db->f("order_status"), $db->f("cdate"), $db->f("mdate") , $db->f("ship_method_id"), $db->f("customer_note"), $db->f("ip_address"));
				$orderArray[]=$Order;
			
			}
			return $orderArray;*/
			
		}else if ($result == "false"){
			return new SoapFault("JoomlaServerAuthFault", "Authentication KO for : ".$params->loginInfo->login);
		}else if ($result == "no_admin"){
			return new SoapFault("JoomlaServerAuthFault", "User is not a Super Administrator : ".$params->loginInfo->login);
		}else{
			return new SoapFault("JoomlaServerAuthFault", "User does not exist : ".$params->loginInfo->login);
		}
	}
	
		/**
    * This function get GetAllCreditCard
	* (expose as WS)
    * @param string
    * @return AllCreditCard
    */
	function GetAllCreditCard($params) {
		
		/* Authenticate*/
		$result = onAdminAuthenticate($params->login, $params->password);
		
		$vmConfig = getVMconfig();
		if ($vmConfig->get('soap_auth_order_otherget')==0){
			$result = "true";
		}	
		
		//Auth OK
		if ($result == "true"){
		
			return new SoapFault("JoomlaServerAuthFault", "No credit card in VM2");
			
		}else if ($result == "false"){
			return new SoapFault("JoomlaServerAuthFault", "Authentication KO for : ".$params->login);
		}else if ($result == "no_admin"){
			return new SoapFault("JoomlaServerAuthFault", "User is not a Super Administrator : ".$params->login);
		}else{
			return new SoapFault("JoomlaServerAuthFault", "User does not exist : ".$params->login);
		}
	}
	
	/**
    * This function ChangeOrderBillTo
	* (expose as WS)
    * @param string
    * @return 
    */
	function GetPluginsInfo($params) {
			
		/* Authenticate*/
		$result = onAdminAuthenticate($params->loginInfo->login, $params->loginInfo->password,$params->loginInfo->isEncrypted);
		
		$vmConfig = getVMconfig();
		if ($vmConfig->get('soap_auth_order_otherget')==0){
			$result = "true";
		}
		
		//Auth OK
		if ($result == "true"){
						
			$db = JFactory::getDBO();	
			$query  = "SELECT * FROM `#__extensions` WHERE 1 ";
			
			if (!empty($params->extension_id)){
				$extension_id = $params->plugin->extension_id;
				$query .= " AND extension_id = '$extension_id' ";
			}
	
			if (!empty($params->plugin->name)){
				$name = $params->plugin->name;
				$query .= " AND name like '%$name%' ";
			}
			
			if (!empty($params->plugin->type)){
				$type = $params->plugin->type;
				$query .= " AND type = '$type' ";
			}
			if (!empty($params->plugin->element)){
				$element = $params->plugin->element;
				$query .= " AND element = '$element' ";
			}
			if (!empty($params->plugin->folder)){
				$folder = $params->plugin->folder;
				$query .= " AND folder like '%$folder%' ";
			}
			
			
			$query .= " ORDER BY extension_id desc ";
			//$query .= " LIMIT $params->limite_start, $params->limite_end "; 
			
			$db->setQuery($query);
			
			$rows = $db->loadObjectList();
			
			foreach ($rows as $row){
				//return new SoapFault("TEST", "TEST VM_ID: ".$query."\n".$row->extension_id);
				$plugin = new Plugin($row->extension_id,
									$row->name,
									$row->type,
									$row->element,
									$row->folder,
									$row->client_id ,
									$row->enabled,
									$row->access,
									$row->protected,
									$row->manifest_cache,
									$row->params,
									$row->custom_data,
									$row->system_data,
									$row->ordering,
									$row->state
									
									);
				$pluginArray[]=$plugin;
			
			}
			return $pluginArray;
			//return new SoapFault("JoomlaServerAuthFault","Not implemented yet");
			
			
		}else if ($result == "false"){
			return new SoapFault("JoomlaServerAuthFault", "Authentication KO for : ".$params->loginInfo->login);
		}else if ($result == "no_admin"){
			return new SoapFault("JoomlaServerAuthFault", "User is not a Super Administrator : ".$params->loginInfo->login);
		}else{
			return new SoapFault("JoomlaServerAuthFault", "User does not exist : ".$params->loginInfo->login);
		}
	}
	
	
	
	
	/* SOAP SETTINGS */
	
	if ($vmConfig->get('soap_ws_order_on')==1){
			
		ini_set("soap.wsdl_cache_enabled", $vmConfig->get('soap_ws_order_cache_on')); // wsdl cache settings
		$options = array('soap_version' => SOAP_1_2);

		/** SOAP SERVER **/
		$uri = str_replace("/free", "", JURI::root(false));
		if (empty($conf['BASESITE']) && empty($conf['URL'])){
			$server = new SoapServer('..'.DS.'VM_Orders.wsdl');
			//$server = new SoapServer($uri.'/VM_OrderWSDL.php');
		}else if (!empty($conf['BASESITE'])){
			$server = new SoapServer('http://'.$conf['URL'].'/'.$conf['BASESITE'].'/administrator/components/com_virtuemart/services/VM_OrderWSDL.php');
		}else {
			$server = new SoapServer('http://'.$conf['URL'].'/administrator/components/com_virtuemart/services/VM_OrderWSDL.php');
		}
		
		/* Add Functions */
		$server->addFunction("getOrdersFromStatus");
		$server->addFunction("getOrder");
		$server->addFunction("getOrderStatus");
		$server->addFunction("getAllOrders");
		$server->addFunction("GetAllCouponCode");
		$server->addFunction("GetAllShippingRate");	
		$server->addFunction("GetAllShippingMethod");	
		$server->addFunction("GetAllPaymentMethod");	
		$server->addFunction("GetOrderFromDate");	
		$server->addFunction("GetAllCreditCard");
		$server->addFunction("GetOrderPaymentInfo");
		$server->addFunction("GetPluginsInfo");
		
		$server->handle();
		
	}else{
		echoXmlMessageWSDisabled('Order');
	}
?> 