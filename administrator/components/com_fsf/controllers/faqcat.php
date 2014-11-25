<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

class FsfsControllerFaqcat extends FsfsController
{

	function __construct()
	{
		parent::__construct();

		// Register Extra tasks
		$this->registerTask( 'add'  , 	'edit' );
		$this->registerTask( 'unpublish', 'unpublish' );
		$this->registerTask( 'publish', 'publish' );
		$this->registerTask( 'orderup', 'orderup' );
		$this->registerTask( 'orderdown', 'orderdown' );
		$this->registerTask( 'saveorder', 'saveorder' );
	}

	function cancellist()
	{
		$link = 'index.php?option=com_fsf&view=fsfs';
		$this->setRedirect($link, $msg);
	}


	function edit()
	{
		JRequest::setVar( 'view', 'faqcat' );
		JRequest::setVar( 'layout', 'form'  );
		JRequest::setVar('hidemainmenu', 1);

		parent::display();
	}

	function save()
	{
		$model =& $this->getModel('faqcat');

        $post = JRequest::get('post');
        $post['description'] = JRequest::getVar('description', '', 'post', 'string', JREQUEST_ALLOWRAW);

		if ($model->store($post)) {
			$msg = JText::_("FAQ_CATEGORY_SAVED");
		} else {
			$msg = JText::_("ERROR_SAVING_FAQ_CATEGORY");
		}

		$link = 'index.php?option=com_fsf&view=faqcats';
		$this->setRedirect($link, $msg);
	}


	function remove()
	{
		$model = $this->getModel('faqcat');
		if(!$model->delete()) {
			$msg = JText::_("ERROR_ONE_OR_MORE_FAQ_CATEGORIES_COULD_NOT_BE_DELETED");
		} else {
			$msg = JText::_("FAQ_CATEGORY_S_DELETED");
		}

		$this->setRedirect( 'index.php?option=com_fsf&view=faqcats', $msg );
	}


	function cancel()
	{
		$msg = JText::_("OPERATION_CANCELLED");
		$this->setRedirect( 'index.php?option=com_fsf&view=faqcats', $msg );
	}

	function unpublish()
	{
		$model = $this->getModel('faqcat');
		if (!$model->unpublish())
			$msg = JText::_("ERROR_THERE_HAS_BEEN_AN_ERROR_UNPUBLISHING_AN_FAQ_CATEGORY");

		$this->setRedirect( 'index.php?option=com_fsf&view=faqcats', $msg );
	}

	function publish()
	{
		$model = $this->getModel('faqcat');
		if (!$model->publish())
			$msg = JText::_("ERROR_THERE_HAS_BEEN_AN_ERROR_PUBLISHING_AN_FAQ_CATEGORY");

		$this->setRedirect( 'index.php?option=com_fsf&view=faqcats', $msg );
	}

	function orderup()
	{
		$model = $this->getModel('faqcat');
		if (!$model->changeorder(-1))
			$msg = JText::_("ERROR_THERE_HAS_BEEN_AN_ERROR_CHANGING_THE_ORDER");

		$this->setRedirect( 'index.php?option=com_fsf&view=faqcats', $msg );
	}

	function orderdown()
	{
		$model = $this->getModel('faqcat');
		if (!$model->changeorder(1))
			$msg = JText::_("ERROR_THERE_HAS_BEEN_AN_ERROR_CHANGING_THE_ORDER");

		$this->setRedirect( 'index.php?option=com_fsf&view=faqcats', $msg );
	}

	function saveorder()
	{
		$model = $this->getModel('faqcat');
		if (!$model->saveorder())
			$msg = JText::_("ERROR_THERE_HAS_BEEN_AN_ERROR_CHANGING_THE_ORDER");

		$this->setRedirect( 'index.php?option=com_fsf&view=faqcats', $msg );
	}
}



