<?php
/**
 * sh404SEF - SEO extension for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier 2014
 * @package     sh404SEF
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     4.4.0.1725
 * @date		2014-04-09
 */

// Security check to ensure this file is being included by a parent file.
if (!defined('_JEXEC'))
	die('Direct Access to this location is not allowed.');

jimport('joomla.application.application');

/**
 * Implement Google analytics handling
 *
 * @author shumisha
 *
 */
class Sh404sefAdapterAnalyticsga extends Sh404sefClassBaseanalytics
{
	protected $_endPoint = 'https://www.googleapis.com/analytics/v2.4/';
	protected $_authPoint = 'https://www.google.com/accounts/ClientLogin';
	protected $_appKeys = array('AIzaSyCW0BXHyqBdvk_pjjrQzUVn9TGGuSCOo8s', 'AIzaSyAA43aNDAI-G_hLc8ZuDboKcF63lFgBIOE',
		'AIzaSyBPE9Vv4RMn7j6lxY2ddp-Tz7M9ldALmgk');

	// specific data
	protected $_SID = '';
	protected $_LSID = '';

	/**
	 * Get tracking snippet
	 *
	 */
	public function getSnippet()
	{
		// should we insert tracking code snippet ?
		if (!$this->_shouldInsertSnippet())
		{
			return '';
		}
		
		//$config->analyticsType = 'uga';
		switch (Sh404sefFactory::getConfig()->analyticsEdition)
		{
			case 'ga':
				$snippet = $this->_getSnippetGa();
				break;
			case 'uga':
				$snippet = $this->_getSnippetUga();
				break;
			case 'ga_and_uga':
				$snippet = $this->_getSnippetGa() . "\n" . $this->_getSnippetUga();
				break;
			case 'gtm':
				$snippet = $this->_getSnippetGtm();
				break;
			default:
				$snippet = '';
				break;
		}

		return $snippet . "\n";
	}
	/**
	 * Get Classic Ananlytics tracking snippet
	 *
	 */
	protected function _getSnippetGa()
	{
		// get config
		$config = Sh404sefFactory::getConfig();
		$pageInfo = Sh404sefFactory::getPageInfo();
		
		// fire event so that plugin(s) attach custom vars
		$customVars = array();
		$dispatcher = ShlSystem_factory::dispatcher();
		$dispatcher->trigger('onShInsertAnalyticsSnippet', array(&$customVars, $config));

		// in case of 404, we use a custom page url so that 404s can also be tracked in GA
		$customUrl = !empty($pageInfo->httpStatus) && $pageInfo->httpStatus == 404 ? "/__404__" : '';

		$displayData = array();
		$displayData['tracking_code'] = trim($config->analyticsId);
		$displayData['custom_vars'] = $customVars;
		$displayData['custom_url'] = $customUrl;
		
		$snippet = ShlMvcLayout_Helper::render('com_sh404sef.analytics.snippet_ga', $displayData);
		
		return $snippet;
	}

	/**
	 * Get Universal Analytics tracking snippet
	 *
	 */
	protected function _getSnippetUga()
	{
		// get config
		$config = Sh404sefFactory::getConfig();
		$pageInfo = Sh404sefFactory::getPageInfo();

		// in case of 404, we use a custom page url so that 404s can also be tracked in GA
		$customUrl =!empty($pageInfo->httpStatus) && $pageInfo->httpStatus == 404 ? '/__404__' : '';

		$displayData = array();
		$displayData['tracking_code'] = trim($config->analyticsUgaId);
		$displayData['custom_domain'] = 'auto';
		$displayData['custom_url'] = $customUrl;
		
		$snippet = ShlMvcLayout_Helper::render('com_sh404sef.analytics.snippet_uga', $displayData);
		
		return $snippet;
	}

	/**
	 * Get Google Tags manager snippet
	 *
	 */
	protected function _getSnippetGtm()
	{
		// get config
		$config = Sh404sefFactory::getConfig();
		$pageInfo = Sh404sefFactory::getPageInfo();

		$displayData = array();
		$displayData['tracking_code'] = trim($config->analyticsGtmId);
		
		// finalize snippet : add user tracking code
		$snippet = ShlMvcLayout_Helper::render('com_sh404sef.analytics.snippet_gtm', $displayData);

		return $snippet;
	}
	/**
	 * Set client object to perform request
	 * for connection to analytics service
	 *
	 */
	protected function _prepareConnectRequest()
	{

		$hClient = Sh404sefHelperAnalytics::getHttpClient();

		// set params
		$hClient->setUri($this->_authPoint);
		$hClient->setConfig(array('maxredirects' => 0, 'timeout' => 10));

		// request details
		$hClient->setMethod(Zendshl_Http_Client::POST);
		$hClient->setEncType('application/x-www-form-urlencoded');

		// request data
		$postData = array('accountType' => 'GOOGLE', 'Email' => $this->_config->analyticsUser, 'Passwd' => $this->_config->analyticsPassword,
			'service' => 'analytics', 'source' => JFactory::getApplication()->getCfg('sitename') . '-sh404sef-' . $this->_config->version);

		$hClient->setParameterPost($postData);
	}

	/**
	 *
	 * Handle response from connect request
	 *
	 */
	protected function _handleConnectResponse($response)
	{

		// check if authentified
		Sh404sefHelperAnalytics::verifyAuthResponse($response);

		// we are authorized, collect Auth token from body
		$this->_extractAuthToken($response->getBody());

		return true;
	}

	protected function _fetchAccountsList()
	{

		$hClient = Sh404sefHelperAnalytics::getHttpClient();
		$hClient->resetParameters($clearAll = true);

		// build the request
		$sefConfig = Sh404sefFactory::getConfig();
		$accountIdBits = explode('-', trim($sefConfig->analyticsId));
		if (empty($accountIdBits) || count($accountIdBits) < 3)
		{
			throw new Sh404sefExceptionDefault(JText::sprintf('COM_SH404SEF_ERROR_CHECKING_ANALYTICS', 'Invalid account Id'));
		}
		$accoundId = $accountIdBits[1];

		// set target API url
		$hClient
			->setUri(
				$this->_endPoint . 'management/accounts/' . $accoundId . '/webproperties/' . trim($sefConfig->analyticsId) . '/profiles?key='
					. $this->_getAppKey());

		// make sure we use GET
		$hClient->setMethod(Zendshl_Http_Client::GET);

		// set headers required by Google Analytics
		$headers = array('GData-Version' => 2, 'Authorization' => 'GoogleLogin auth=' . $this->_Auth);

		$hClient->setHeaders($headers);

		//perform request
		// establish connection with available methods
		$adapters = array('Zendshl_Http_Client_Adapter_Curl', 'Zendshl_Http_Client_Adapter_Socket');
		$rawResponse = null;

		// perform connect request
		foreach ($adapters as $adapter)
		{
			try
			{
				$hClient->setAdapter($adapter);
				$response = $hClient->request();
				break;
			}
			catch (Exception $e)
			{
				// we failed, let's try another method
			}
		}

		// return if error
		if (empty($response))
		{
			$msg = 'unknown code';
			throw new Sh404sefExceptionDefault(JText::sprintf('COM_SH404SEF_ERROR_CHECKING_ANALYTICS', $msg));
		}
		if (empty($response) || !is_object($response) || $response->isError())
		{
			$msg = method_exists($response, 'getStatus') ? $response->getStatus() : 'unknown code';
			throw new Sh404sefExceptionDefault(JText::sprintf('COM_SH404SEF_ERROR_CHECKING_ANALYTICS', $msg));
		}

		// analyze response
		// check if authentified
		Sh404sefHelperAnalytics::verifyAuthResponse($response);
		$xml = simplexml_load_string($response->getBody());

		if (!empty($xml->entry))
		{
			foreach ($xml->entry as $entry)
			{
				$account = new StdClass();
				$bits = explode('/', (string) $entry->id);
				$account->id = array_pop($bits);
				$account->title = str_replace('Google Analytics Profile ', '', (string) $entry->title);
				$account->title = str_replace('Google Analytics View (Profile) ', '', $account->title);
				$this->_accounts[] = clone ($account);
			}
		}
	}

	/**
	 * prepare html filters to allow user to select the way she likes
	 * to view reports
	 */
	protected function _prepareFilters()
	{
		// array to hold various filters
		$filters = array();

		// find if we must display all filters. On dashboard, only a reduced set
		$allFilters = $this->_options['showFilters'] == 'yes';

		// select account to retrieve data for (or rather, profile
		$customSubmit = ' onchange="shSetupAnalytics({' . ($allFilters ? '' : 'showFilters:\'no\'') . '});"';

		if (version_compare(JVERSION, '3.0', 'ge'))
		{
			$select = '<div class="btn-group">';
			$select .= Sh404sefHelperHtml::buildSelectList($this->_accounts, $this->_options['accountId'], 'accountId', $autoSubmit = false,
				$addSelectAll = false, $selectAllTitle = '', $customSubmit);
			$select .= '</div>';
			$filters[] = $select;

			// dashboard only has account selection, no room for anything else
			// only shows main selection drop downs on analytics view
			if ($allFilters)
			{
				$select = '<div class="btn-group">';
				$select .= '<label for="startDate">' . JText::_('COM_SH404SEF_ANALYTICS_START_DATE') . '</label>';
				$select .= JHTML::_('calendar', $this->_options['startDate'], 'startDate', 'startDate', '%Y-%m-%d', array('class' => 'class="textinput"'));
				$select .= '</div>';
				$filters[] = $select;

				$select = '<div class="btn-group">';
				$select .= '<label for="endDate">' . JText::_('COM_SH404SEF_ANALYTICS_END_DATE') . '</label>';
				$select .= JHTML::_('calendar', $this->_options['endDate'], 'endDate', 'endDate', '%Y-%m-%d', array('class' => 'class="textinput"'));
				$select .= '</div>';
				$filters[] = $select;

				// select groupBy (day, week, month)
				$select = '<div class="btn-group">';
				$select .= '<label for="groupBy">' . JText::_('COM_SH404SEF_ANALYTICS_GROUP_BY') . '</label>';
				$select .= Sh404sefHelperAnalytics::buildAnalyticsGroupBySelectList($this->_options['groupBy'], 'groupBy', $autoSubmit = false,
					$addSelectAll = false, $selectAllTitle = '', $customSubmit);
				$select .= '</div>';
				$filters[] = $select;

				// add a click to update link
				$filters[] = '<a class="btn btn-link" href="javascript: void(0);" onclick="javascript: shSetupAnalytics({forced:1'
					. ($allFilters ? '' : ',showFilters:\'no\'') . '});" > [' . JText::_('COM_SH404SEF_CHECK_ANALYTICS') . ']</a>';

			}
			else
			{
				// on dashboard, there is no date select, so we must display the date range
				$filters[] = '&nbsp;' . JText::_('COM_SH404SEF_ANALYTICS_DATE_RANGE') . '&nbsp;<div class="largertext">'
					. $this->_options['startDate'] . '&nbsp;&nbsp;>>&nbsp;&nbsp;' . $this->_options['endDate'] . '</div>';
			}
		}
		else
		{
			$select = Sh404sefHelperHtml::buildSelectList($this->_accounts, $this->_options['accountId'], 'accountId', $autoSubmit = false,
				$addSelectAll = false, $selectAllTitle = '', $customSubmit);
			$filters[] = JText::_('COM_SH404SEF_ANALYTICS_ACCOUNT') . ':&nbsp;' . $select;

			// dashboard only has account selection, no room for anything else
			// only shows main selection drop downs on analytics view
			if ($allFilters)
			{
				// select start date
				$select = JHTML::_('calendar', $this->_options['startDate'], 'startDate', 'startDate', '%Y-%m-%d', array('class' => 'class="textinput"'));
				$filters[] = '&nbsp;' . JText::_('COM_SH404SEF_ANALYTICS_START_DATE') . ':&nbsp;' . $select;

				// select end date
				$select = JHTML::_('calendar', $this->_options['endDate'], 'endDate', 'endDate', '%Y-%m-%d', array('class' => 'class="textinput"'));
				$filters[] = '&nbsp;' . JText::_('COM_SH404SEF_ANALYTICS_END_DATE') . ':&nbsp;' . $select;

				// select groupBy (day, week, month)
				$select = Sh404sefHelperAnalytics::buildAnalyticsGroupBySelectList($this->_options['groupBy'], 'groupBy', $autoSubmit = false,
					$addSelectAll = false, $selectAllTitle = '', $customSubmit);
				$filters[] = '&nbsp;' . JText::_('COM_SH404SEF_ANALYTICS_GROUP_BY') . ':&nbsp;' . $select;

				// add a click to update link
				$filters[] = '&nbsp;<a href="javascript: void(0);" onclick="javascript: shSetupAnalytics({forced:1'
					. ($allFilters ? '' : ',showFilters:\'no\'') . '});" > [' . JText::_('COM_SH404SEF_CHECK_ANALYTICS') . ']</a>';
			}
			else
			{
				// on dashboard, there is no date select, so we must display the date range
				$filters[] = '&nbsp;' . JText::_('COM_SH404SEF_ANALYTICS_DATE_RANGE') . '&nbsp;<div class="largertext">'
					. $this->_options['startDate'] . '&nbsp;&nbsp;>>&nbsp;&nbsp;' . $this->_options['endDate'] . '</div>';
			}
		}
		// use layout to render
		return $filters;
	}

	protected function _extractAuthToken($body)
	{
		$SID = explode('LSID=', $body);
		$this->_SID = trim($SID[0]);
		$this->_SID = ltrim($this->_SID, 'SID=');

		$LSID = explode('Auth=', $SID[1]);
		$this->_LSID = trim($LSID[0]);

		$this->_Auth = trim($LSID[1]);
	}
}
