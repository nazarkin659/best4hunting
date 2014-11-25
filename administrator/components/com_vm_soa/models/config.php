<?php
/**
 * Classe de modèle spécifique aux clients
 *
 */
require_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_vm_soa'.DS.'conf.php';

class vm_soaModelconfig extends JModel
{
	function saveConf()
	{
		JRequest::checkToken() or jexit( 'Invalid Token (model config)' );
		
		JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'tables');
		
		if(!class_exists('VmConfig'))require(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'config.php');
		if(!class_exists('VirtueMartModelConfig'))require(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'models'.DS.'config.php');
		$model = new VirtueMartModelConfig();

		$data = JRequest::get('post');
		//var_dump($data);
		$data['offline_message'] = JRequest::getVar('offline_message','','post','STRING',JREQUEST_ALLOWHTML);

		if(strpos($data['offline_message'],'|')!==false){
			$data['offline_message'] = str_replace('|','',$data['offline_message']);
		}
		
		if ($model->store($data)) {
			$msg = JText::_('COM_VIRTUEMART_CONFIG_SAVED');
			// Load the newly saved values into the session.
			VmConfig::loadConfig(true);
		}
		else {
			$msg = $model->getError();
		}

		$redir = 'index.php?option=com_virtuemart';
		if(JRequest::getCmd('task') == 'apply'){
			//$redir = $this->redirectPath;
		}
		
		if (!FSOAP) {
			$this->loadConf();
		}
		//$this->setRedirect($redir, $msg);
	
	}
	
	function loadConf()
	{
		include('settings.php');		
	}
	
	
	function renewConfig() {
		
		$token  = JUtility::getToken();
		$_REQUEST[$token] = $token;
		$_POST[$token] = $token;
		
		$data = $this->readConfigFile("");
		
		if(!class_exists('VirtueMartModelConfig'))require(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'models'.DS.'config.php');
		$model = new VirtueMartModelConfig();

		if ($model->store($data)) {
			$msg = JText::_('COM_VIRTUEMART_CONFIG_SAVED');
			// Load the newly saved values into the session.
			VmConfig::loadConfig(true);
		}
	
	}
	
	function readConfigFile($returnDangerousTools){

		define('JPATH_SOA_ADMINISTRATOR' , JPATH_ADMINISTRATOR.DS.'components'.DS.'com_vm_soa');
		$_datafile = JPATH_SOA_ADMINISTRATOR.DS.'soap.cfg';
		if (!file_exists($_datafile)) {
			if (file_exists(JPATH_SOA_ADMINISTRATOR.DS.'com_vm_soa_defaults.cfg-dist')) {
				if(!class_exists('JFile')) require(JPATH_VM_LIBRARIES.DS.'joomla'.DS.'filesystem'.DS.'file.php');
				JFile::copy('com_vm_soa_defaults.cfg-dist','soap.cfg',JPATH_SOA_ADMINISTRATOR);
			} else {
				JError::raiseWarning(500, 'The data file with the default configuration could not be found. You must configure the shop manually.');
				return false;
			}

		} else {
			vmInfo('Taking config from file');
		}

		$_section = '[CONFIG]';
		$_data = fopen($_datafile, 'r');
		$_configData = array();
		$_switch = false;
		while ($_line = fgets ($_data)) {
			$_line = trim($_line);

			if (strpos($_line, '#') === 0) {
				continue; // Commentline
			}
			if ($_line == '') {
				continue; // Empty line
			}
			if (strpos($_line, '[') === 0) {
				// New section, check if it's what we want
				if (strtoupper($_line) == $_section) {
					$_switch = true; // Ok, right section
				} else {
					$_switch = false;
				}
				continue;
			}
			if (!$_switch) {
				continue; // Outside a section or inside the wrong one.
			}

			if (strpos($_line, '=') !== false) {

				$pair = explode('=',$_line);
				$dataConf[$pair[0]] = $pair[1];
				
				/*if(isset($pair[1])){
					if(strpos($pair[1], 'array:') !== false){
						$pair[1] = substr($pair[1],6);
						$pair[1] = explode('|',$pair[1]);
					}
					// if($pair[0]!=='offline_message' && $pair[0]!=='dateformat'){
					if($pair[0]!=='offline_message'){
						$_line = $pair[0].'='.serialize($pair[1]);
					} else {
						$_line = $pair[0].'='.base64_encode(serialize($pair[1]));
					}

					if($returnDangerousTools && $pair[0] == 'dangeroustools' ){
						vmdebug('dangeroustools'.$pair[1]);
						if($pair[1]=="0") return false; else return true;
					}

				} else {
					$_line = $pair[0].'=';
				}*/
				$_configData[] = $_line;

			}

		}

		fclose ($_data);
		//var_dump($dataConf);die;
		
		if (!$dataConf) {
			return false; // Nothing to do
		} else {
			return $dataConf;
		}
		
		/*if (!$_configData) {
			return false; // Nothing to do
		} else {
			return $_configData;
		}*/
	}
}
?>