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

class TableVm2OrderPaymentOrShipment extends JTable
{
	protected $_pluginName;
	protected $_tableName;
	protected $_pluginType;
	protected $_methodVarName;
	
    /**
     * Create object. Set database connector. Set table name and primary key.
     * 
     * @param JDatabaseMySQL $db
     */
    function __construct(&$db, $pluginName)
    {
    	// Set internal variables.
    	$this->_tbl = $this->_tableName.$pluginName;
		$this->_db		= &$db;
		$this->_pluginName = $pluginName;
		
		if (InvoiceHelper::checkTableExists($this->_tbl))
        	parent::__construct($this->_tbl, 'id', $db);
    }

    //try to get method name from VM plugin
    function renderPluginNameVM()
    {
    	if (!$this->{$this->_methodVarName})
    		return false;

		$className = 'plg' .$this->_pluginType. $this->_pluginName;
		
		if (! class_exists ( $className )) {
			$path = JPATH_PLUGINS . '/'.strtolower($this->_pluginType).'/' . $this->_pluginName . '/' . $this->_pluginName . '.php';
			if (! JFile::exists ( $path )) //j1.5?
				$path = JPATH_PLUGINS . '/'.strtolower($this->_pluginType).'/' . $this->_pluginName . '.php';
			if (! JFile::exists ( $path ))
				return false;
			include_once $path;
		}
		
		if (! class_exists ( $className ))
			return false;
		
		//renderPluginName is protected, so we need to declare own child
		$ourName = 'plg'.$this->_pluginType.'Vminvoice' . substr ( md5 ( $this->_tbl ), 0, 10 );
		if (! class_exists ( $ourName ))
			eval (  "class $ourName extends $className {
    					public function getRenderedPluginName(\$methodId) {
    	
    					if (is_callable(array(\$this, 'renderPluginName')) && is_callable(array(\$this, 'getVmPluginMethod'))){
    					\$method = \$this->getVmPluginMethod(\$methodId);
    					return \$this->renderPluginName(\$method);
    	}
    	}
    	}" );

		// TODO: checknout na 1.5 a 3.2
		
		if (! class_exists ( $ourName ))
			return false;
			
		// ok, now we can try our child
		$dispatcher = JDispatcher::getInstance ();
		$plugin = JPluginHelper::getPlugin ( strtolower($this->_pluginType), $this->_pluginName );
		$ourPlugin = new $ourName ( $dispatcher, (array)($plugin)); // subject, config...
		
		return $ourPlugin->getRenderedPluginName ($this->{$this->_methodVarName});
    }
}

?>