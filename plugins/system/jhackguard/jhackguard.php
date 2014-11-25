<?php

/**
 * @package	jHackGuard
 * @subpackage	System
 * @copyright	Copyright (C) 2009 - 2013 SiteGround.com - All Rights Reserved.
 * @author		Val Markov <val@siteground.com>
 * @version		1.4.2
 * @license		GNU/GPL, see LICENSE
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 *
 * This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See LICENSE for more details.
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');
jimport('joomla.log.log');

/**
 * jHackGuard Plugin
 *
 * @package	jHackGuard
 * @subpackage	System
 */
	class plgSystemjhackguard extends JPlugin {

    private $pluginParams;
    private $log;

    function plgSystemjhackguard(&$subject) { 
    $plugin = JPluginHelper::getPlugin('system', 'jhackguard');
    $this->pluginParams = new JRegistry();
    $this->pluginParams->loadString($plugin->params,'JSON');
    $this->onAfterInitialise(); //We want to run this as soon as the plugin is available.
    }

    /**
     * jhackguard onAfterInitialize method
     *
     * Method is called after the system is being initalised
     *
     * @return 	void
     */
    public function onAfterInitialise() {
    //Now why are we globaling the mainframe variable? TODO: test and remove it if not required.    
    //    global $mainframe; //Doesn't seems to be required.
    //Different from 1.5. There is no usertype anymore, thus checking the ACL rule instead.
	if(JFactory::getUser()->authorise('core.login.admin')){
			//User has backend access.
			//$this->log("Checks skipped, user pretends to be an administrator");
        } else {
            $this->start();
	}
    }

    function onAfterRender() {
	
	$app = JFactory::getApplication();
	$menu = $app->getMenu();
	if ($menu->getActive() == $menu->getDefault()) {
          $frontpage = TRUE; 
	} else { $frontpage = FALSE; }

	if ($frontpage AND $this->pluginParams->get('link_back_sg',1)) {
                        $output = JResponse::getBody();
                        if (!(preg_match("/sgfooter/", $output))) {
                                $pattern = '/<\/body>/';
                                $replacement = "<center><span class='modifydate' id='sgfooter' style='padding: 5px;'>Extensions by Siteground <a title='Joomla hosting by SiteGround' href='http://www.siteground.com/joomla-hosting.htm'>Joomla hosting</a></span><center></body>";
                                $output = preg_replace($pattern, $replacement, $output, 1);
                        }
                        JResponse::setBody($output);
                }
    }

    /**
     * Add a line to the log file. Helper function.
     *
     * @param object $text Text to be added
     *
     * @return void
     */
    private function add_log($text) {
        if ($this->pluginParams->get('logging')) {
	    JLog::addLogger(array('logger'=>'formattedtext','text_file' => $this->pluginParams->get('log_file'),JLog::CRITICAL,array('jhackguard')), JLog::CRITICAL);
	    JLog::add($text,JLog::CRITICAL,'jhackguard');
	}
    }

    // Instance of the security class.
    protected static $instance;
    protected $magic_quotes_gpc = FALSE;

    /**
     * Gets the instance of the Security class.
     *
     * @return object Instance of Security
     */
    public static function instance() {
        if (self::$instance === NULL) {
            return new Security;
        }

        return self::$instance;
    }

    /**
     * Sanitizes global data GET, POST and COOKIE data.
     * Also makes sure those pesty magic quotes and register globals
     * don't bother us. This is protected because it really only needs
     * to be run once.
     *
     * @return void
     */
    protected function start() {
        if (self::$instance === NULL) {
            // Check for magic quotes
            if (get_magic_quotes_runtime ()) {
                // Dear lord!! This is bad and depreciated. Sort it out ;)
                set_magic_quotes_runtime(0);
                $this->add_log("Magic_quotes_runtime disabled");
            }

            if (get_magic_quotes_gpc ()) {
                // This is also bad and deprecated. See http://php.net/magic_quotes for more information.
                $this->magic_quotes_gpc = TRUE;
            }

            // Check for register globals and prevent security issues from arising.
            if (ini_get('register_globals')) {
                if (isset($_REQUEST['GLOBALS'])) {
                    // No no no.. just kill the script here and now
                    exit('Illegal attack on global variable.');
                }

                // Get rid of REQUEST
                $_REQUEST = array();

                // The following globals are standard and shouldn't really be removed
                $preserve = array('GLOBALS', '_REQUEST', '_GET', '_POST', '_FILES', '_COOKIE', '_SERVER', '_ENV', '_SESSION');

                // Same effect as disabling register_globals
                foreach ($GLOBALS as $key => $value) {
                    if (!in_array($key, $preserve)) {
                        global $$key;
                        $$key = NULL;

                        unset($GLOBALS[$key], $$key);
                    }
                }
            }

            // Sanitize global data
	    
	    /* Shall we try and strip tags from the user_agent variable */
	    if($this->pluginParams->get('strip_user_agent',1)) {
		$_SERVER['HTTP_USER_AGENT'] = strip_tags($_SERVER['HTTP_USER_AGENT']);
		//TODO: check if user agent has been changed and log it.
	    }
            /* Shall we clean POST? */

	    /* Check if "Disable File Uploads" is enabled and clear the upload */
	    if($this->pluginParams->get('disable_file_uploads',0) AND JFactory::getUser()->guest){
		$_FILES = array();
	    }

            if ($this->pluginParams->get('check_post', 1)) {
                if (is_array($_POST)) {
                    foreach ($_POST as $key => $value) {
                        $cleaned_key = $this->clean_input_keys($key);
                        $cleaned_value = $this->clean_input_data($value);
                        if ($key != $cleaned_key) {
                            $this->add_log("Changed POST key from:" . $key . " to:" . $cleaned_key);
                        }
                        if ($value != $cleaned_value) {
                            $this->add_log("Changed POST value from:" . $value . " to:" . $cleaned_value);
                        }
                        $_POST[$cleaned_key] = $cleaned_value;
                    }
                } else {
                    $_POST = array();
                }
            }

            /* Shall we clean GET? */
            if ($this->pluginParams->get('check_get', 1)) {
                if (is_array($_GET)) {
                    foreach ($_GET as $key => $value) {
                        $cleaned_key = $this->clean_input_keys($key);
                        $cleaned_value = $this->clean_input_data($value);
                        if ($key != $cleaned_key) {
                            $this->add_log("Changed GET key from: " . $key . " to: " . $cleaned_key);
                        }
                        if ($value != $cleaned_value) {
                            $this->add_log("Changed GET value from:  \t" . $value . " to: \t" . $cleaned_value);
                        }
                        $_GET[$cleaned_key] = $cleaned_value;
                    }
                } else {
                    $_GET = array();
                }
            }

            /* How about a cookie now? */
            if ($this->pluginParams->get('check_cookies', 1)) {
                if (is_array($_COOKIE)) {
                    foreach ($_COOKIE as $key => $value) {
                        $cleaned_key = $this->clean_input_keys($key);
                        $cleaned_value = $this->clean_input_data($value);
                        if ($key != $cleaned_key) {
                            $this->add_log("Changed COOKIE key from:" . $key . " to:" . $cleaned_key);
                        }
                        if ($value != $cleaned_value) {
                            $this->add_log("Changed COOKIE value from:" . $value . " to:" . $cleaned_value);
                        }
                        $_COOKIE[$cleaned_key] = $cleaned_value;
                    }
                } else {
                    $_COOKIE = array();
                }
            }

            /* Disable allow fopen? */
            if ($this->pluginParams->get('check_urlfopen', 0)) {
                if (ini_get('allow_url_fopen') == 1) {
                    /* Enabling workaround with php.ini file */
                    /* Check if there is an existing php.ini file already in place */
                    if (file_exists("php.ini")) {
                        if (is_readable("php.ini")) {
                            $ini = parse_ini_file("php.ini");
                            $ini['allow_url_fopen'] = 0;
                            //Save it now.
                            $this->write_php_ini($ini, "php.ini");
                        }
                    } else {
                        //File does not exist. Creating one.
                        $ini['allow_url_fopen'] = 0;
                        $this->write_php_ini($ini, "php.ini");
                    }
                }
            }

            /* Disable allow urlinclude? */
            if ($this->pluginParams->get('check_urlinclude', 0)) {
                if (ini_get('allow_url_include') == 1) {
                    /* Enabling workaround with php.ini file */
                    /* Check if there is an existing php.ini file already in place */
                    if (file_exists("php.ini")) {
                        if (is_readable("php.ini")) {
                            $ini = parse_ini_file("php.ini");
                            $ini['allow_url_include'] = 0;
                            //Save it now.
                            $this->write_php_ini($ini, "php.ini");
                        }
                    } else {
                        //File does not exist. Creating one.
                        $ini['allow_url_include'] = 0;
                        $this->write_php_ini($ini, "php.ini");
                    }
                }
            }

            // Just make REQUEST a merge of POST and GET. Who really wants cookies in it anyway?
            $_REQUEST = array_merge($_GET, $_POST);

            self::$instance = $this;
        }
    }

    /**
     * Cross site filtering (XSS). Recursive.
     *
     * @param  string Data to be cleaned
     * @return mixed
     */
    public function xss_clean($data) {
        // If its empty there is no point cleaning it :\
        if (empty($data))
            return $data;

        // Recursive loop for arrays
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->xss_clean($data);
            }

            return $data;
        }

        // Fix &entity\n;
        $data = str_replace(array('&amp;', '&lt;', '&gt;'), array('&amp;amp;', '&amp;lt;', '&amp;gt;'), $data);
        $data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
        $data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);

        $data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');

        // Remove any attribute starting with "on" or xmlns
        $data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);

	// Remove location.href and String.fromCharCode() from data. We do not want input data to have javascript valid code really, as this might cause XSS attacks.
	// This check might be a little bit too strict, as it will filter any location.href in input data, thus it is executed only in "Strict" mode.
	// Example: "The javascript location.href function is used to..." will get filtered, even though it is perfectly clean data.
	if($this->pluginParams->get('strict_xss', 1)) {
	    $data = preg_replace('#location.href#iu', '"$1>', $data);
	    $data = preg_replace('#string.fromcharcode#iu','$1',$data);
	}

        // Remove javascript: and vbscript: protocols
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);

        // Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);

        // Remove namespaced elements (we do not need them)
        $data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);

	// Some generic SQL stuff

	if($this->pluginParams->get('check_sql',1)){
	    // Or injection
	    $data = preg_replace("/\w*((%27)|')\s*(o|(%6F)|(%4F))(r|(%72)|(%52))/ix", '$1', $data);
	    // Union injection
	    $data = preg_replace("/((%27)|')\s*(u|%75|%55)(n|%6E|%4E)(i|%69|%49)(o|%6F|%4F)(n|%6E|%4E)/ix", '$1', $data);
	    // Select injection
	    $data = preg_replace("/((%27)|')\s*(s|%73|%53)(e|%65|%45)(l|%6C|%4C)(e|%65|%45)(c|%63|%43)(t|%74|%54)/ix", '$1', $data);
	    // Delete injection
	    $data = preg_replace("/((%27)|')\s*(d|%64|%44)(e|%65|%45)(l|%6C|%4C)(e|%65|%45)(t|%74|%54)(e|%65|%45)/ix", '$1', $data);
	}


        /* Remove eval() */
        if ($this->pluginParams->get('check_eval', 1)) {
            $data = str_ireplace('eval(', '', $data);
        }

        /* Remove base64_decode() */
        if ($this->pluginParams->get('check_base64', 1)) {
            $data = str_ireplace('base64_decode(', '', $data);
        }


        /* Remove CONCAT SQL command */
        if ($this->pluginParams->get('check_sql', 1)) {
            $data = str_ireplace('CONCAT', '', $data);
        }


        /* Remove jos_users to prevent SQL injections into users table */
        if ($this->pluginParams->get('check_sql', 1)) {
            $data = str_ireplace('jos_users', '', $data);
        }


        do {
            // Remove really unwanted tags
            $old_data = $data;
            $data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
        } while ($old_data !== $data);

        return $data;
    }

    /**
     * Enforces W3C specifications to prevent malicious exploitation.
     *
     * @param  string Key to clean
     * @return string
     */
    protected function clean_input_keys($data) {

	if($this->pluginParams->get('scan_input_keys',0)){
	    $chars = '\pL';
	    if (!preg_match('#^[' . $chars . '0-9:_.-]++$#uD', $data)) {
	        exit('\'Scan input keys\' is enabled and illegal input key characters were detected.');
            }
	}
        return $data;
    }

    /**
     * Escapes data.
     *
     * @param  mixed Data to clean
     * @return mixed
     */
    protected function clean_input_data($data) {
        if (is_array($data)) {
            $new_array = array();
            foreach ($data as $key => $value) {
                $new_array[$this->clean_input_keys($key)] = $this->clean_input_data($value);
            }

            return $new_array;
        }

        if ($this->magic_quotes_gpc === TRUE) {
            // Get rid of those pesky magic quotes!
            $data = stripslashes($data);
        }

        $data = $this->xss_clean($data);


        return $data;
    }

    /**
     * Writes down php.ini file for the disallow functions
     *
     * @param object $array
     * @param object $file
     * @return void
     */
    function write_php_ini($array, $file) {
        $res = array();
        foreach ($array as $key => $val) {
            if (is_array($val)) {
                $res[] = "[$key]";
                foreach ($val as $skey => $sval)
                    $res[] = "$skey = " . (is_numeric($sval) ? $sval : '"' . $sval . '"');
            } else
                $res[] = "$key = " . (is_numeric($val) ? $val : '"' . $val . '"');
        }
        $this->safefilerewrite($file, implode("\r\n", $res));
    }

    /**
     * Function to obtain a file lock safely.
     *
     * @param object $fileName
     * @param object $dataToSave
     * @return void
     */
    function safefilerewrite($fileName, $dataToSave) {
        if ($fp = fopen($fileName, 'w+')) {
            $startTime = microtime();
            $maxTries = 0;
            do {
                $canWrite = flock($fp, LOCK_EX);
                // If lock not obtained sleep for 0 - 100 milliseconds, to avoid collision and CPU load
                if (!$canWrite) {
                    usleep(round(rand(0, 100) * 1000));
                    $maxTries++;
                }
            } while ((!$canWrite) and ((microtime() - $startTime) < 1000) and $maxTries < 15);

            //file was locked so now we can store information
            if ($canWrite) {
                fwrite($fp, $dataToSave);
                flock($fp, LOCK_UN);
            }
            fclose($fp);
        }
    }
}