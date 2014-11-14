<?php

define( '_VALID_MOS', 1 );
define( '_JEXEC', 1 );

/**
 * Virtuemart Category SOA Connector
 *
 * Virtuemart SQLQueries SOA Connector (Provide functions execute generic SQL queries, INSERT, UPDATE, SELECT queries)
 * The return classe is a "SQLResult" 
 * 
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
 * Class SQLResult
 *
 * Class "SQLResult" 
 *
 * @author     Mickael cabanas (cabanas.mickael|at|gmail.com)
 * @copyright  Mickael cabanas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version    Release:
 */
	class SQLResult {
		public $columnsAndValues;

		//constructeur
		function __construct($columnsAndValues) {
			$this->columnsAndValues = $columnsAndValues;
		}
	}
 
 /**
 * Class SQLResult
 *
 * Class "SQLResult" 
 *
 * @author     Mickael cabanas (cabanas.mickael|at|gmail.com)
 * @copyright  Mickael cabanas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version    Release:
 */
	class columnAndValue {
		public $idx="";
		public $column="";
		public $value="";

		//constructeur
		function __construct($idx,$column,$value) {
			$this->idx = $idx;
			$this->column = $column;
			$this->value = $value;
		}
	}
 /**
 * Class SQLResult
 *
 * Class "SQLResult" with attribute : id, name, description,  image, fulliamage , parent category
 * attributes, parent produit, child id)
 *
 * @author     Mickael cabanas (cabanas.mickael|at|gmail.com)
 * @copyright  Mickael cabanas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version    Release:
 */
	
 
	/**
    * This function get Childs of a category for a category ID
	* (expose as WS)
    * @param string The id of the category
    * @return array of Categories
   */
	function ExecuteSQLSelectQuery($params) {
	
		$SQLSelectRequest= $params;
		/* Authenticate*/
		$result = onAdminAuthenticate($params->loginInfo->login, $params->loginInfo->password,$params->loginInfo->isEncrypted);
		
		$vmConfig = getVMconfig();
		if ($vmConfig->get('soap_auth_execsql_select')==0){
			$result = "true";
		}
		
		//Auth OK
		if ($result == "true"){
	
			$SQLSelectRequest= $_SQLSelectRequest;
			
			$query = "SELECT " ;//FROM #__{vm}_category WHERE 1 ";
			$strTmp;
						
			if (is_array($params->columns->column)){
				$count = count($params->columns->column);
				for ($i = 0; $i < $count; $i++) {
					if ($i==$count-1){
						$strTmp .= $params->columns->column[$i]." ";
					}else{
						$strTmp .= $params->columns->column[$i].", ";
					}
				}
			}else {
					$count = 1;
					$strTmp .= $params->columns->column." ";
			}
			
			/*$q .= $strTmp;
			$q .= " FROM $params->table ";
			$q .= $params->whereClause;

			/*$db = new ps_DB;
			$db->setQuery($q);
			$db->query();*/
			
			$db = JFactory::getDBO();
			$query .= $strTmp;
			$query .= " FROM $params->table ";
			$query .= $params->whereClause;
			$db->setQuery($query);
			
			$rows = $db->loadAssocList();
			
			foreach ($rows as $row) {
			
				$strResult=null;
				$arrayCol;
				$strResult;
				
				if ($count == 1){
					$columnAndValue = new columnAndValue(0,$params->columns->column,$row[$params->columns->column]);
					$columnAndValueArray[] = $columnAndValue;
				
				} else {
					for ($i = 0; $i < $count; $i++) {
						$columnAndValue = new columnAndValue($i,$params->columns->column[$i],$row[$params->columns->column[$i]]);
						$columnAndValueArray[] = $columnAndValue;
						/*$arrayCol=  array( $params->columns->column[$i] =>$db->f($params->columns->column[$i]));
						$strResult .=  $params->columns->column[$i]." : ".$db->f($params->columns->column[$i])." | ";*/
					}
				}
				$SQLResult= new SQLResult($columnAndValueArray);
				$resultArray[] = $SQLResult;
				$columnAndValueArray=null;
			
			}
			
			$errMsg=  $db->getErrorMsg();
			
			if ($errMsg==null){
				return $resultArray;
			} else {
				return new SoapFault("JoomlaExecuteSQLSelectQueryFault", "cannot execute SQL Select Query  ".$q." | ERRLOG : ".$errMsg);				
			}

			
			
		}else if ($result== "false"){
			return new SoapFault("JoomlaServerAuthFault", "Autification KO for : ".$params->loginInfo->login);
		}else if ($result == "no_admin"){
			return new SoapFault("JoomlaServerAuthFault", "User is not a Super Administrator : ".$params->loginInfo->login);
		}else{
			return new SoapFault("JoomlaServerAuthFault", "User does not exist : ".$params->loginInfo->login);
		}		
	
	}
		
		
	/* SOAP SETTINGS */
	
	if ($vmConfig->get('soap_ws_sql_on')==1){

		/* SOAP SETTINGS */
		ini_set("soap.wsdl_cache_enabled", $vmConfig->get('soap_ws_sql_cache_on')); // wsdl cache settings
		$options = array('soap_version' => SOAP_1_2);
		
		$uri = str_replace("/free", "", JURI::root(false));
		if (empty($conf['BASESITE']) && empty($conf['URL'])){
			$server = new SoapServer('..'.DS.'VM_SQLQueries.wsdl');
			//$server = new SoapServer($uri.'/VM_SQLQueriesWSDL.php');
		}else if (!empty($conf['BASESITE'])){
			$server = new SoapServer('http://'.$conf['URL'].'/'.$conf['BASESITE'].'/administrator/components/com_virtuemart/services/VM_SQLQueriesWSDL.php');
		}else {
			$server = new SoapServer('http://'.$conf['URL'].'/administrator/components/com_virtuemart/services/VM_SQLQueriesWSDL.php');
		}
		

		/* Add Functions */
		$server->addFunction("ExecuteSQLSelectQuery");
		$server->handle();
	}else{
		echoXmlMessageWSDisabled('SQL queries');
	}
?> 