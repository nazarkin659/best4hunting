<?php
/**
 * @version		$Id: helper.php 1053 2011-10-06 12:36:44Z lefteris.kavadas $
 * @package		K2
 * @author		JoomlaWorks http://www.joomlaworks.gr
 * @copyright	Copyright (c) 2006 - 2011 JoomlaWorks Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
 jimport('joomla.application.component.model');
JModel::addIncludePath(JPATH_SITE.'/components/com_content/models');
if(!defined("JPATH_COMPONENT_ADMINISTRATOR")){
	define("JPATH_COMPONENT_ADMINISTRATOR", JPATH_ADMINISTRATOR.DS."components".DS."com_k2");
}
if(defined("K2_JVERSION") && K2_JVERSION == '16'){
		$language = &JFactory::getLanguage();
		$language->load('mod_k2.j16', JPATH_ADMINISTRATOR, null, true);
}
if( !class_exists('IceGroupAccordionK2') ){
	if(file_exists(JPATH_SITE.DS.'components'.DS.'com_k2'.DS.'helpers'.DS.'utilities.php')){
		require_once(JPATH_SITE.DS.'components'.DS.'com_k2'.DS.'helpers'.DS.'utilities.php');
	}
	if(file_exists(JPATH_SITE.DS.'components'.DS.'com_k2'.DS.'helpers'.DS.'route.php')){
		require_once(JPATH_SITE.DS.'components'.DS.'com_k2'.DS.'helpers'.DS.'route.php');
	}
	
class IceGroupAccordionK2 extends IceAccordionGroupBase{
	/**
		 * @var string $__name;
		 *
		 * @access private
		 */
		var $__name = 'k2';
		
		/**
		 * override method: get list image from articles.
		 */
		function getListByParameters( &$params ){ 
			return self::getItems( $params );
		}
	public static function _cleanIntrotext($introtext)
	{
		$introtext = str_replace('<p>', ' ', $introtext);
		$introtext = str_replace('</p>', ' ', $introtext);
		$introtext = strip_tags($introtext, '<a><em><strong>');

		$introtext = trim($introtext);

		return $introtext;
	}

	public static function getItems(&$params, $format = 'html') {
		if(!file_exists(JPATH_SITE.DS.'components'.DS.'com_k2'.DS.'k2.php'))
			return array();
		jimport('joomla.filesystem.file');
		$mainframe = &JFactory::getApplication();
		$isThumb       = $params->get( 'auto_renderthumb',1);
		$image_quanlity = $params->get('image_quanlity', 100);
		$show_preview = $params->get( 'show_k2_preview_image', 1 );
		$thumbWidth    = (int)$params->get( 'k2_preview_width', 200 );
		$thumbHeight   = (int)$params->get( 'k2_preview_height', 210 );
		$imageHeight   = (int)$params->get( 'main_height', 300 ) ;
		$imageWidth    = (int)$params->get( 'main_width', 900 ) ;
		$isStripedTags = $params->get( 'auto_strip_tags', 0 );
		$limit = $params->get( 'limit_items',  5 );
		$cid = $params->get('k2_category_id', NULL);
		$ordering = $params->get('itemsOrdering','');
		$componentParams = &JComponentHelper::getParams('com_k2');
		$limitstart = JRequest::getInt('limitstart');

		$user = &JFactory::getUser();
		$aid = $user->get('aid');
		$db = &JFactory::getDBO();

		$jnow = &JFactory::getDate();
		$now = $jnow->toMySQL();
		$nullDate = $db->getNullDate();

		if($params->get('k2_source_from')=='specific'){

			$value = $params->get('items');
			$current = array();
			if(is_string($value) && !empty($value))
			$current[]=$value;
			if(is_array($value))
			$current=$value;

			$items = array();

			foreach($current as $id){

				$query = "SELECT i.*, c.name AS categoryname,c.id AS categoryid, c.alias AS categoryalias, c.params AS categoryparams 
				FROM #__k2_items as i 
				LEFT JOIN #__k2_categories c ON c.id = i.catid 
				WHERE i.published = 1 ";
				if(K2_JVERSION=='16'){
					$query .= " AND i.access IN(".implode(',', $user->authorisedLevels()).") ";
				}
				else {
					$query .=" AND i.access<={$aid} ";
				}				
				$query .= " AND i.trash = 0 AND c.published = 1 ";
				if(K2_JVERSION=='16'){
					$query .= " AND c.access IN(".implode(',', $user->authorisedLevels()).") ";
				}
				else {
					$query .=" AND c.access<={$aid} ";
				}				
				$query .= " AND c.trash = 0 
				AND ( i.publish_up = ".$db->Quote($nullDate)." OR i.publish_up <= ".$db->Quote($now)." ) 
				AND ( i.publish_down = ".$db->Quote($nullDate)." OR i.publish_down >= ".$db->Quote($now)." ) 
				AND i.id={$id}";
				if(K2_JVERSION=='16'){
					if($mainframe->getLanguageFilter()) {
						$languageTag = JFactory::getLanguage()->getTag();
						$query .= " AND c.language IN (".$db->Quote($languageTag).", ".$db->Quote('*').") AND i.language IN (".$db->Quote($languageTag).", ".$db->Quote('*').")";
					}
				}
				$db->setQuery($query);
				$item = $db->loadObject();
				if($item)
				$items[]=$item;

			}
		}

		else {
			$query = "SELECT i.*, c.name AS categoryname,c.id AS categoryid, c.alias AS categoryalias, c.params AS categoryparams";

			if ($ordering == 'best')
			$query .= ", (r.rating_sum/r.rating_count) AS rating";

			if ($ordering == 'comments')
			$query .= ", COUNT(comments.id) AS numOfComments";

			$query .= " FROM #__k2_items as i LEFT JOIN #__k2_categories c ON c.id = i.catid";

			if ($ordering == 'best')
			$query .= " LEFT JOIN #__k2_rating r ON r.itemID = i.id";

			if ($ordering == 'comments')
			$query .= " LEFT JOIN #__k2_comments comments ON comments.itemID = i.id";

			if(K2_JVERSION=='16'){
				$query .= " WHERE i.published = 1 AND i.access IN(".implode(',', $user->authorisedLevels()).") AND i.trash = 0 AND c.published = 1 AND c.access IN(".implode(',', $user->authorisedLevels()).")  AND c.trash = 0";
			}
			else {
				$query .= " WHERE i.published = 1 AND i.access <= {$aid} AND i.trash = 0 AND c.published = 1 AND c.access <= {$aid} AND c.trash = 0";
			}

			$query .= " AND ( i.publish_up = ".$db->Quote($nullDate)." OR i.publish_up <= ".$db->Quote($now)." )";
			$query .= " AND ( i.publish_down = ".$db->Quote($nullDate)." OR i.publish_down >= ".$db->Quote($now)." )";


			if ($params->get('catfilter')) {
				if (!is_null($cid)) {
					if (is_array($cid)) {
						if ($params->get('getChildren')) {
							if(file_exists(JPATH_SITE.DS.'components'.DS.'com_k2'.DS.'models'.DS.'itemlist.php'))
								require_once (JPATH_SITE.DS.'components'.DS.'com_k2'.DS.'models'.DS.'itemlist.php');
							$categories = K2ModelItemlist::getCategoryTree($cid);
							$sql = @implode(',', $categories);
							$query .= " AND i.catid IN ({$sql})";

						} else {
							JArrayHelper::toInteger($cid);
							$query .= " AND i.catid IN(".implode(',', $cid).")";
						}

					} else {
						if ($params->get('getChildren')) {
							if(file_exists(JPATH_SITE.DS.'components'.DS.'com_k2'.DS.'models'.DS.'itemlist.php'))
								require_once (JPATH_SITE.DS.'components'.DS.'com_k2'.DS.'models'.DS.'itemlist.php');
							$categories = K2ModelItemlist::getCategoryTree($cid);
							$sql = @implode(',', $categories);
							$query .= " AND i.catid IN ({$sql})";
						} else {
							$query .= " AND i.catid=".(int)$cid;
						}

					}
				}
			}

			if ($params->get('FeaturedItems') == '0')
			$query .= " AND i.featured != 1";

			if ($params->get('FeaturedItems') == '2')
			$query .= " AND i.featured = 1";

			if ($params->get('videosOnly'))
			$query .= " AND (i.video IS NOT NULL AND i.video!='')";

			if ($ordering == 'comments')
			$query .= " AND comments.published = 1";
			
			if(K2_JVERSION=='16'){
				if($mainframe->getLanguageFilter()) {
					$languageTag = JFactory::getLanguage()->getTag();
					$query .= " AND c.language IN (".$db->Quote($languageTag).", ".$db->Quote('*').") AND i.language IN (".$db->Quote($languageTag).", ".$db->Quote('*').")";
				}
			}

			switch ($ordering) {

				case 'date':
					$orderby = 'i.created ASC';
					break;

				case 'rdate':
					$orderby = 'i.created DESC';
					break;

				case 'alpha':
					$orderby = 'i.title';
					break;

				case 'ralpha':
					$orderby = 'i.title DESC';
					break;

				case 'order':
					if ($params->get('FeaturedItems') == '2')
					$orderby = 'i.featured_ordering';
					else
					$orderby = 'i.ordering';
					break;

				case 'rorder':
					if ($params->get('FeaturedItems') == '2')
					$orderby = 'i.featured_ordering DESC';
					else
					$orderby = 'i.ordering DESC';
					break;

				case 'hits':
					if ($params->get('popularityRange')){
						$datenow = &JFactory::getDate();
						$date = $datenow->toMySQL();
						$query.=" AND i.created > DATE_SUB('{$date}',INTERVAL ".$params->get('popularityRange')." DAY) ";
					}
					$orderby = 'i.hits DESC';
					break;

				case 'rand':
					$orderby = 'RAND()';
					break;

				case 'best':
					$orderby = 'rating DESC';
					break;

				case 'comments':
					if ($params->get('popularityRange')){
						$datenow = &JFactory::getDate();
						$date = $datenow->toMySQL();
						$query.=" AND i.created > DATE_SUB('{$date}',INTERVAL ".$params->get('popularityRange')." DAY) ";
					}
					$query.=" GROUP BY i.id ";
					$orderby = 'numOfComments DESC';
					break;
					
				case 'modified':
					$orderby = 'i.modified DESC';
					break;

				default:
					$orderby = 'i.id DESC';
					break;
			}


			$query .= " ORDER BY ".$orderby;
			$db->setQuery($query, 0, $limit);
			$items = $db->loadObjectList();
		}


		if(file_exists(JPATH_SITE.DS.'components'.DS.'com_k2'.DS.'models'.DS.'item.php'))
			require_once (JPATH_SITE.DS.'components'.DS.'com_k2'.DS.'models'.DS.'item.php');
		$model = new K2ModelItem;
		$option = JRequest::getVar("option","");
		$view = JRequest::getVar("view","");
		if ($option === 'com_content' && $view === 'article') {
			$active_article_id = JRequest::getInt('id');
		}
		else {
			$active_article_id = 0;
		}
		if (count($items)) {

			foreach ($items as $item) {

				//Clean title
				$item->title = JFilterOutput::ampReplace($item->title);
				if($params->get("title_max_chars")){
					$item->title = self::wordLimit($item->title, $params->get("title_max_chars"));
				}
				// Used for styling the active article
				$item->active = $item->id == $active_article_id ? 'active' : '';
				//Images
				$item = self::parseImages( $item );

				if( $item->mainImage &&  $image=self::renderThumb($item->mainImage, $imageWidth, $imageHeight, $item->title, $isThumb, $image_quanlity ) ){
					$item->mainImage = $image;
				}
				if( $show_preview ){
					if( $item->thumbnail &&  $image = self::renderThumb($item->thumbnail, $thumbWidth, $thumbHeight, $item->title, $isThumb, $image_quanlity, true ) ){
						$item->thumbnail = $image;
					}
				}
				//Read more link
				$item->link = urldecode(JRoute::_(K2HelperRoute::getItemRoute($item->id.':'.urlencode($item->alias), $item->catid.':'.urlencode($item->categoryalias))));

				//Tags
				if ($params->get('itemTags')) {
					$tags = $model->getItemTags($item->id);
					for ($i = 0; $i < sizeof($tags); $i++) {
						$tags[$i]->link = JRoute::_(K2HelperRoute::getTagRoute($tags[$i]->name));
					}
					$item->tags = $tags;
				}

				//Category link
				if ($params->get('itemCategory'))
				$item->categoryLink = urldecode(JRoute::_(K2HelperRoute::getCategoryRoute($item->catid.':'.urlencode($item->categoryalias))));

				//Extra fields
				if ($params->get('itemExtraFields')) {
					$item->extra_fields = $model->getItemExtraFields($item->extra_fields);
				}

				//Comments counter
				if ($params->get('itemCommentsCounter'))
				$item->numOfComments = $model->countItemComments($item->id);

				// Introtext
				$item->text = '';
				if ($params->get('itemIntroText')) {
					// Word limit
					if ($params->get('description_max_chars')) {
						// always strip tags for text
						$item->text .= self::substring($item->introtext, $params->get('description_max_chars'));
					} else {
						$item->text .= $item->introtext;
					}
				
				}
				if ($format != 'feed') {
				
					$params->set('parsedInModule', 1); // for plugins to know when they are parsed inside this module
						
					if($params->get('JPlugins',1)){
						$dispatcher = &JDispatcher::getInstance();
						JPluginHelper::importPlugin('content');
						//Plugins
						$results = $dispatcher->trigger('onBeforeDisplay', array(&$item, &$params, $limitstart));
						$item->event->BeforeDisplay = trim(implode("\n", $results));

						$results = $dispatcher->trigger('onAfterDisplay', array(&$item, &$params, $limitstart));
						$item->event->AfterDisplay = trim(implode("\n", $results));

						$results = $dispatcher->trigger('onAfterDisplayTitle', array(&$item, &$params, $limitstart));
						$item->event->AfterDisplayTitle = trim(implode("\n", $results));

						$results = $dispatcher->trigger('onBeforeDisplayContent', array(&$item, &$params, $limitstart));
						$item->event->BeforeDisplayContent = trim(implode("\n", $results));

						$results = $dispatcher->trigger('onAfterDisplayContent', array(&$item, &$params, $limitstart));
						$item->event->AfterDisplayContent = trim(implode("\n", $results));

						$dispatcher->trigger('onPrepareContent', array(&$item, &$params, $limitstart));
						$item->introtext = $item->text;
							
					}

					//Init K2 plugin events
					$item->event->K2BeforeDisplay = '';
					$item->event->K2AfterDisplay = '';
					$item->event->K2AfterDisplayTitle = '';
					$item->event->K2BeforeDisplayContent = '';
					$item->event->K2AfterDisplayContent = '';
					$item->event->K2CommentsCounter = '';
						
					if($params->get('K2Plugins',1)){
						//K2 plugins
						JPluginHelper::importPlugin('k2');
						$results = $dispatcher->trigger('onK2BeforeDisplay', array(&$item, &$params, $limitstart));
						$item->event->K2BeforeDisplay = trim(implode("\n", $results));

						$results = $dispatcher->trigger('onK2AfterDisplay', array(&$item, &$params, $limitstart));
						$item->event->K2AfterDisplay = trim(implode("\n", $results));

						$results = $dispatcher->trigger('onK2AfterDisplayTitle', array(&$item, &$params, $limitstart));
						$item->event->K2AfterDisplayTitle = trim(implode("\n", $results));

						$results = $dispatcher->trigger('onK2BeforeDisplayContent', array(&$item, &$params, $limitstart));
						$item->event->K2BeforeDisplayContent = trim(implode("\n", $results));

						$results = $dispatcher->trigger('onK2AfterDisplayContent', array(&$item, &$params, $limitstart));
						$item->event->K2AfterDisplayContent = trim(implode("\n", $results));

						$dispatcher->trigger('onK2PrepareContent', array(&$item, &$params, $limitstart));
						$item->introtext = $item->text;
						
						if ($params->get('itemCommentsCounter')) {
							$results = $dispatcher->trigger('onK2CommentsCounter', array ( & $item, &$params, $limitstart));
							$item->event->K2CommentsCounter = trim(implode("\n", $results));
						}
						
					}

				}

				//Clean the plugin tags
				$item->introtext = preg_replace("#{(.*?)}(.*?){/(.*?)}#s", '', $item->introtext);

				//Author
				if ($params->get('itemAuthor')) {
					if (! empty($item->created_by_alias)) {
						$item->author = $item->created_by_alias;
						$item->authorGender = NULL;
						if ($params->get('itemAuthorAvatar'))
						$item->authorAvatar = K2HelperUtilities::getAvatar('alias');
					} else {
						$author = &JFactory::getUser($item->created_by);
						$item->author = $author->name;
						$query = "SELECT `gender` FROM #__k2_users WHERE userID=".(int)$author->id;
						$db->setQuery($query, 0, 1);
						$item->authorGender = $db->loadResult();
						if ($params->get('itemAuthorAvatar')) {
							$item->authorAvatar = K2HelperUtilities::getAvatar($author->id, $author->email, $componentParams->get('userImageWidth'));
						}
						//Author Link
						$item->authorLink = JRoute::_(K2HelperRoute::getUserRoute($item->created_by));
					}
				}
				
				// Extra fields plugins
				if (is_array($item->extra_fields)) {
					foreach($item->extra_fields as $key => $extraField) {
						if($extraField->type == 'textarea' || $extraField->type == 'textfield') {
							$tmp = new JObject();
							$tmp->text = $extraField->value;
							if($params->get('JPlugins',1)){
								if(K2_JVERSION == '16') {
									$dispatcher->trigger('onContentPrepare', array ('mod_k2_content', &$tmp, &$params, $limitstart));
								}
								else {
									$dispatcher->trigger('onPrepareContent', array ( & $tmp, &$params, $limitstart));
								}
							}
							if($params->get('K2Plugins',1)){
								$dispatcher->trigger('onK2PrepareContent', array ( & $tmp, &$params, $limitstart));
							}
							$extraField->value = $tmp->text;
						}
					}
				}
				$item->displayIntrotext = $item->introtext;

				$rows[] = $item;
			}

			return $rows;

		}

	}
	// Word limit
	public static function wordLimit($str, $limit = 100, $end_char = '&#8230;') {
		if (trim($str) == '') return $str;

		// always strip tags for text
		$str = strip_tags($str);

		$find = array("/\r|\n/","/\t/","/\s\s+/");
		$replace = array(" "," "," ");
		$str = preg_replace($find,$replace,$str);

		preg_match('/\s*(?:\S*\s*){'.(int) $limit.'}/', $str, $matches);
		if (strlen($matches[0]) >= strlen($str))
		$end_char = '';

		return rtrim($matches[0]).$end_char;
	}
	/**
		 * looking for image inside the media folder.
		 */
		public static  function lookingForK2Image(&$item, $size='XL')
		{
			//Image
			$item->imageK2Image='';
			if (JFile::exists(JPATH_SITE.DS.'media'.DS.'k2'.DS.'items'.DS.'cache'.DS.md5("Image".$item->id).'_'.$size.'.jpg'))
				$item->imageK2Image = JURI::root().'media/k2/items/cache/'.md5("Image".$item->id).'_'.$size.'.jpg';
			return $item; 
		}
		/**
		 * parser a image in the content of article.
		 *
		 * @param poiter $row .
		 * @return void
		 */
		public function parseImages(&$row)
		{
			$text =  $row->introtext.$row->fulltext;
			$data = self::parserCustomTag($text);
			if(isset($data[1][0])){
				$tmp = self::parseParams($data[1][0]);
				$row->mainImage = isset($tmp['src']) ? $tmp['src']:'';
				$row->thumbnail = $row->mainImage ;// isset($tmp['thumb']) ?$tmp['thumb']:'';	
			} else {
				$row  = self::lookingForK2Image($row);
				
				if($row->imageK2Image != ''){
					$row->thumbnail = $row->mainImage = $row->imageK2Image;	
					return $row;
				}
				$regex = "/\<img.+src\s*=\s*\"([^\"]*)\"[^\>]*\>/";
				preg_match ($regex, $text, $matches); 
				$images = (count($matches)) ? $matches : array();
				if (count($images)){
					$row->mainImage = $images[1];
					$row->thumbnail = $images[1];
				} else {
					$row->thumbnail = '';
					$row->mainImage = '';	
				}
			}
			return $row;
		}

}
}
