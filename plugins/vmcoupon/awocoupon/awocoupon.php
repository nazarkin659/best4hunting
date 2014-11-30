<?php
/**
 * @component AwoCoupon for Virtuemart
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @license : GNU/GPL
 * @Website : http://awodev.com
 **/

if( ! defined( '_VALID_MOS' ) && ! defined( '_JEXEC' ) ) die( 'Direct Access to ' . basename( __FILE__ ) . ' is not allowed.' ) ;


class plgVmCouponAwoCoupon extends JPlugin {

	function plgVmCouponAwoCoupon(& $subject, $config){
		parent::__construct($subject, $config);
	}

	function plgVmValidateCouponCode($_code,$_billTotal) {
		$awo_file = JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/vm_coupon.php';
		if(file_exists($awo_file)) {
			require_once $awo_file;
			$vm_coupon = new vm_coupon();
			return $vm_coupon->vm_ValidateCouponCode($_code);
		}
		
		return null;
	}

	function plgVmRemoveCoupon($_code,$_force) {
		$awo_file = JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/vm_coupon.php';
		if(file_exists($awo_file)) {
			require_once $awo_file;
			return vm_coupon::remove_coupon_code($_code);
		}
		
		return null;
	}
	
	function plgVmCouponHandler($_code, & $_cartData, & $_cartPrices) {
		$awo_file = JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/vm_coupon.php';
		if(file_exists($awo_file)) {
			require_once $awo_file;
			return vm_coupon::process_coupon_code($_code, $_cartData, $_cartPrices );
		}
		
		return null;
	}
	
}

// No closing tag