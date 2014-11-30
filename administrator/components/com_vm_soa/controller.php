<?php
/**
 * @package    	com_vm_soa (WebServices for virtuemart)
 * @author		Mickael Cabanas (cabanas.mickael|at|gmail.com)
 * @link 		http://www.virtuemart-datamanager.com
 * @license    	GNU/GPL
*/
jimport('joomla.application.component.controller');

error_reporting(E_ERROR  | E_PARSE );

class vm_soaController extends JController{

	function read_config()
	{
		
		// On appelle le mod�le pour lire la configuration
		$model = $this->getModel('vm_soa');
		switch(JRequest::getVar('task', ''))
		{
			case 'save':
				$conf = $model->save_conf();
				$model->isLicenceValid();
			break;
		}
		$conf = $model->read_conf();
		
		JRequest::setVar( 'conf', $conf );
		
	}
/**
 * Dans un controleur Joomla, la fonction display est la fonction qui est appel�e s'il la variable act (action) n'est pas renseign�e
 */
  function display($cachable = false, $urlparams = false){

    parent::display(false,false);

  }
/**
 * La fonction config est appel�e quand act contient config : ce param�tre est initialis� dans le fichier xml d'installation � la configuration des menus
 */
  function config(){
	
	echo '<br>'.__FILE__;
	echo '<br>config';
    parent::display();

  }
}?>
