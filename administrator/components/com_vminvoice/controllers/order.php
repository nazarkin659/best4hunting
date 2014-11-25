<?php


/**
 * PDF Invoicing Solution for VirtueMart & Joomla!
 * 
 * @package   VM Invoice
 * @version   2.0.31
 * @author    ARTIO http://www.artio.net
 * @copyright Copyright (C) 2011 ARTIO s.r.o. 
 * @license   GNU/GPLv3 http://www.artio.net/license/gnu-general-public-license
 */

defined('_JEXEC') or die('Restrict Access');

invoiceHelper::legacyObjects('controller');

class VMInvoiceControllerOrder extends JControllerLegacy
{

    function __construct($config = array())
    {
        parent::__construct($config);
        $this->registerTask('userajax', 'display');
        $this->registerTask('orderajax', 'display');
    }

    function exchangerateajax()
    {
    	$from = JRequest::getInt('from');
    	$to = JRequest::getInt('to');
    	
    	if (!$from OR !$to OR $from==$to)
    		$rate = "1.0";
    	else {
	    	InvoiceHelper::importVMFile('helpers/currencydisplay.php');
	    	$currency = CurrencyDisplay::getInstance($to);
	    	$currency->_vendorCurrency = $from;
	    	$rate = $currency->convertCurrencyTo($to ,1.0,false);
    	}
    		
    	echo $rate;exit;
    }
    
    function couponajax()
    {
    	header('Content-Type: text/xml; charset=UTF-8');

    	$coupon = JRequest::getVar('coupon');
    	$currency = JRequest::getVar('currency');
    	
    	if (trim($coupon)=='')
    		exit;
    	
    	if (!$info = InvoiceGetter::getCoupon($coupon))
    		echo '<span class="red">'.JText::_('COM_VMINVOICE_COUPON_NOT_FOUND').'</span>';
    	else
    	{
    		echo '<span class="green">';
    		if ($info->coupon_type=='gift')
    			echo JText::_('COM_VMINVOICE_GIFT_COUPON');
    		else
    			echo JText::_('COM_VMINVOICE_PERMANENT_COUPON');
    	
    		if ($info->percent_or_total=='percent')
    			echo ' ('.($info->coupon_value*1).'% '.JText::_('COM_VMINVOICE_DISCOUNT').')';
    		else
    			echo ' ('.InvoiceCurrencyDisplay::getFullValue($info->coupon_value,$currency).' '.JText::_('COM_VMINVOICE_DISCOUNT').')';
    		echo '</span> ';
    	
    		if (COM_VMINVOICE_ISVM2){
    			
    			$today =  invoiceHelper::gmStrtotime(gmdate('Y-m-d'));
    			$start = invoiceHelper::gmStrtotime($info->coupon_start_date);
    			$expiry = invoiceHelper::gmStrtotime($info->coupon_expiry_date);
    	
    			if ($info->coupon_start_date AND $info->coupon_start_date!='0000-00-00 00:00:00' AND $start>$today)
    				echo '<span class="yellow"> '.JText::sprintf('COM_VMINVOICE_COUPON_FUTURE',date('j.n.Y',$start)).' </span>';
    			 
    			if ($info->coupon_expiry_date AND $info->coupon_expiry_date!='0000-00-00 00:00:00' AND $expiry<$today)
    				echo '<span class="yellow"> '.JText::sprintf('COM_VMINVOICE_COUPON_EXPIRED',date('j.n.Y',$expiry)).' </span>';
    			 
    			if ((float)$info->coupon_value_valid)
    				echo '<span class="yellow">'.JText::sprintf('COM_VMINVOICE_COUPON_MIN_ORDER',InvoiceCurrencyDisplay::getFullValue($info->coupon_value_valid,$currency)).'</span>';
    		}
    		 
    		if ($info->coupon_type=='gift')
    			echo ' <label class="hasTip" title="'.JText::_('COM_VMINVOICE_DELETE_COUPON_AFTER_SAVING').'::'.JText::_('COM_VMINVOICE_DELETE_COUPON_DESC').'"><input type="checkbox" name="coupon_delete" value="1" checked> '.JText::_('COM_VMINVOICE_DELETE_COUPON_AFTER_SAVING').'</label>';
    		 
    		echo ' <input class="btn" type="button" value="'.JText::_('COM_VMINVOICE_PASS').' &raquo;" onclick="passCouponDiscount(\''.$info->percent_or_total.'\',\''.($info->coupon_value*1).'\');"> ';
    	}
    	
    	exit;
    }
    
    function statesajax()
    {
    	header('Content-Type: text/xml; charset=UTF-8');
    	
    	$states = InvoiceGetter::getStates(JRequest::getVar('country_id'));
    	foreach ($states as $state)
    	{
    		echo '<option value="'.$state->id.'">'.$state->name.'</option>'."\n";
    	}
    	
    	exit;
    }
     
    function whisper()
    {
        $type = JRequest::getString('type');
        $model = $this->getModel('order');
        /* @var $model VMInvoiceModelOrder */
        die(JHTML::_('select.genericlist', $model->getAjaxList(JRequest::getString('str'), $type), 'naseptavac', 'multiple="multiple" onclick="getClickHandler($(\'' . $type . '\'));" onchange="getChangeHandler($(\'' . $type . '\'));"', 'id', 'name'));
    }

    function display($cachable = false, $urlparams = false)
    {
        JRequest::setVar('view', 'order');
        parent::display($cachable, $urlparams);
    }

    function apply()
    {
        $this->save(true);
    }

    function save($apply = false)
    {
        $model = $this->getModel('order');

        /* @var $model VMInvoiceModelOrder */
        $id = $model->save(JRequest::get('post',4)); //4 = allow HTML
        
        if ($apply)
            $this->setRedirect('index.php?option=com_vminvoice&controller=invoices&task=editOrder&cid=' . $id, JText::_('COM_VMINVOICE_ORDER_SAVED'));
        else
            $this->cancel('Order saved');
    }

    function cancel($msg = 'Order edit canceled')
    {
        $this->setRedirect('index.php?option=com_vminvoice&controller=invoices', JText::_($msg));
    }
}

?>