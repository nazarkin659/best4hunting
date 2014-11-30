<?php
// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * $ModDesc
 * 
 * @version		$Id: helper.php $Revision
 * @package		modules
 * @subpackage	$Subpackage
 * @copyright	Copyright (C) May 2010 LandOfCoder.com <@emai:landofcoder@gmail.com>. All rights reserved.
 * @website 	htt://landofcoder.com
 * @license		GNU General Public License version 2
 */
 jimport('joomla.application.component.model');
JModel::addIncludePath(JPATH_SITE.'/components/com_content/models');
if( !class_exists('IceGroupAccordionJomsocial') ){ 
	if( file_exists( JPATH_ROOT.DS.'components'.DS.'com_community'.DS.'community.php')){
		require_once ( JPATH_ROOT.DS.'components'.DS.'com_community'.DS.'defines.community.php');

		// Require the base controller
		require_once (COMMUNITY_COM_PATH.DS.'libraries'.DS.'error.php');
		require_once (COMMUNITY_COM_PATH.DS.'controllers'.DS.'controller.php');
		require_once (COMMUNITY_COM_PATH.DS.'libraries'.DS.'apps.php' );
		require_once (COMMUNITY_COM_PATH.DS.'libraries'.DS.'core.php');
		require_once (COMMUNITY_COM_PATH.DS.'libraries'.DS.'template.php');
		require_once (COMMUNITY_COM_PATH.DS.'views'.DS.'views.php');
		require_once (COMMUNITY_COM_PATH.DS.'helpers'.DS.'url.php');
		require_once (COMMUNITY_COM_PATH.DS.'helpers'.DS.'ajax.php');
		require_once (COMMUNITY_COM_PATH.DS.'helpers'.DS.'time.php');
		require_once (COMMUNITY_COM_PATH.DS.'helpers'.DS.'owner.php');
		require_once (COMMUNITY_COM_PATH.DS.'helpers'.DS.'videos.php');
		require_once (COMMUNITY_COM_PATH.DS.'helpers'.DS.'azrul.php');
		require_once (COMMUNITY_COM_PATH.DS.'helpers'.DS.'string.php');
		require_once (COMMUNITY_COM_PATH.DS.'events'.DS.'router.php');

		JTable::addIncludePath( COMMUNITY_COM_PATH . DS . 'tables' );
	}
jimport('joomla.utilities.date');
	class IceGroupAccordionJomsocial extends IceAccordionGroupBase{
		
		/**
		 * @var string $__name;
		 *
		 * @access private
		 */
		var $__name = 'jomsocial';
		
		/**
		 * override method: get list image from articles.
		 */
		function getListByParameters( &$params ){ 
			return self::getList( $params );
		}
	public static function getList(&$params){
		$sourceFrom = $params->get("js_source_from","users");
		$db = &JFactory::getDBO();
		CFactory::load( 'libraries' , 'activities');
		$limit = (int)$params->get("limit_items",10);
		  $thumbWidth    = (int)$params->get( 'js_preview_width', 200 );
		  $thumbHeight   = (int)$params->get( 'js_preview_height', 210 );
		  $imageHeight   = (int)$params->get( 'main_height', 300 ) ;
		  $imageWidth    = (int)$params->get( 'main_width', 660 ) ;
		  $show_preview = $params->get( 'show_preview_image', 1);
		  $isThumb       = $params->get( 'auto_renderthumb',1);
		  $image_quanlity = $params->get('image_quanlity', 100);
		 $config	= CFactory::getConfig();
		 
		$my 	       = &JFactory::getUser();
		$aid	       = $my->get( 'aid', 0 );
		$data = array();
		switch($sourceFrom){
			case "photos":
				$show_nr_view = $params->get("show_nr_view",1);
				$show_upload_by = $params->get("show_upload_by",1);
				$show_photo_date = $params->get("show_photo_date",1);
				$show_location = $params->get("show_location",1);
				$show_nr_comments = $params->get("show_nr_comments",1);
				$tmpOrdering = $params->get("photos_ordering","p.hits__DESC");
				if($tmpOrdering == "random"){
					$ordering = " RAND() ";
				}
				else{
					$ordering = str_replace("__"," ",$tmpOrdering);
				}
				if(!empty($ordering))
					$ordering =" ORDER BY ".$ordering;
				$rows1 = $rows2 = $rows = array();
				$itemids = array();
				if($tmpOrdering == "comments__DESC"){
					$ordering = "";
					/*
					$query1 = 'SELECT w.contentid, p.id,p.albumid,p.caption AS title,p.creator,p.image,p.original,p.created,p.hits,p.status,p.ordering,a.groupid,a.name,a.description,a.location,u.name AS user_name, count(w.id) AS comments FROM #__community_photos AS p ';
					$query1 .= ' LEFT JOIN #__community_photos_albums AS a ON p.albumid=a.id ';
					$query1 .= ' LEFT JOIN #__users AS u ON p.creator = u.id ';
					$query1 .= ' LEFT JOIN #__community_wall as w on p.id=w.contentid AND w.type="photos" ';
					$where1 = ' WHERE p.published = 1 ';
					$query1 .= $where1." GROUP BY w.contentid ORDER BY comments DESC LIMIT 0,".$limit;

					$db->setQuery( $query1 );
					$rows1 = $db->loadObjectList();
					if(!empty($rows1)){
						$limit = $limit - count( $rows1 );
						foreach($rows1 as $row){
							$itemids[] = $row->id;
						}
					}
					*/
				}

				if( $limit > 0){
					$query = 'SELECT p.id,p.albumid,p.caption AS title,p.creator,p.image,p.original,p.created,p.hits,p.status,p.ordering,a.groupid,a.name,a.description,a.location,u.name AS user_name FROM #__community_photos AS p ';
					$query .= ' LEFT JOIN #__community_photos_albums AS a ON p.albumid=a.id ';
					$query .= ' LEFT JOIN #__users AS u ON p.creator = u.id ';
					
					//$query .= ' LEFT JOIN #__community_wall as w on p.id=w.contentid ';
					$where = ' WHERE p.published = 1 ';
					if(!empty($itemids)){
						$where .= 'AND p.id NOT IN('.implode(",",$itemids).') ';
					}
					$query .= $where.$ordering." LIMIT 0,".$limit;
					
					//echo $query;die();
					$db->setQuery($query);
					$rows2 = $db->loadObjectList();
					$rows = array_merge( $rows1, $rows2 );
				}
				CFactory::load( 'libraries' , 'activities');
				if(!empty($rows)){
					foreach($rows as $row){
						if($show_photo_date){
							if( $row->created != '0000-00-00 00:00:00' )
							{
								$created	= new JDate( $row->created );
								$row->created		= CActivityStream::_createdLapse( $created );
							}
						}
						$url	= 'index.php?option=com_community&view=photos&task=photo&albumid=' . $row->albumid;
						$url	.= $row->groupid ? '&groupid=' . $row->groupid : '&userid=' . $row->creator;
						$row->link = CRoute::_( $url , true ) . '#photoid=' . $row->id;
						$row->user_link =  CRoute::_('index.php?option=com_community&view=profile&userid=' . $row->creator);
						$row->thumbnail = $row->mainImage =  $row->original;
						if( $row->mainImage &&  $image=self::renderThumb($row->mainImage, $imageWidth, $imageHeight, $row->title, $isThumb, $image_quanlity ) ){
						  $row->mainImage = $image;
						}
						if( $show_preview ){
							if( $row->thumbnail &&  $image = self::renderThumb($row->thumbnail, $thumbWidth, $thumbHeight, $row->title, $isThumb, $image_quanlity, true ) ){
							  $row->thumbnail = $image;
							}
						}
						if(!isset($row->comments)){
							$row->comments = 0;
							if($show_nr_comments){
								$query = "SELECT count(contentid) AS comments FROM #__community_wall WHERE type='photos' AND published=1 AND contentid=".$row->id;
								$db->setQuery( $query );
								$row->comments = $db->loadResult();
							}
						}
						
						$data[] = $row;
					}
					
				}
			break;
			case "groups":
				$categories = $params->get("group_categories","");
				$categories = is_array($categories)?implode(",",$categories):$categories;
				$where = "";
				if(!empty($categories)){
					$where .= " AND g.categoryid  IN (".$categories.")";
				}
				$show_js_category = $params->get("show_js_category", 1);
				$show_group_desc = $params->get("show_group_desc", 1);
				$show_create_date = $params->get("show_create_date", 1);
				$show_nr_members = $params->get("show_nr_members", 1);
				$show_nr_discussion = $params->get("show_nr_discussion", 1);
				$show_wall_post = $params->get("show_wall_post", 1);
				$ordering = $params->get("groups_ordering","g.membercount__DESC");
				if($ordering == "random"){
					$ordering = " RAND() ";
				}
				else{
					$ordering = str_replace("__"," ",$ordering);
				}
				if(!empty($ordering))
					$ordering =" ORDER BY ".$ordering;
				$query = "SELECT g.*,g.name AS title,gc.name AS category_title FROM #__community_groups AS g ";
				$query .= " LEFT JOIN #__community_groups_category AS gc ON g.categoryid = gc.id ";
				$query .= " WHERE g.published = 1";
				$query .= $where.$ordering." LIMIT ".$limit;
				
				$db->setQuery($query);
				$rows = $db->loadObjectList();
				if(!empty($rows)){
					foreach($rows as $row){
						$group	=& JTable::getInstance( 'Group' , 'CTable' );
						$group->bind($row);
						$group->title = $group->name = $row->title;
						$group->category_title = $row->category_title;
						$group->updateStats(); //ensure that stats are up-to-date
						$group->description = CStringHelper::clean(CStringHelper::truncate( $group->description, $config->get('tips_desc_length') ));
						if($show_create_date){
							if( $group->created != '0000-00-00 00:00:00' )
								{
									$created	= new JDate( $group->created );
									$group->created		= CActivityStream::_createdLapse( $created );
								}
						}
						$group->viewmember_link = CRoute::_( 'index.php?option=com_community&view=groups&task=viewmembers&groupid=' . $group->id );
						$group->category_link = CRoute::_('index.php?option=com_community&view=groups&categoryid=' . $group->categoryid );
						$group->link = CRoute::_( 'index.php?option=com_community&view=groups&task=viewgroup&groupid=' . $group->id );
						$group->thumbnail = $group->getAvatar("");
						$group->mainImage =   '<img class="avatar" src="'.$group->getThumbAvatar().'"  alt="'.CStringHelper::escape( $group->name ).'">';
						/*
						if( $row->mainImage &&  $image=self::renderThumb($row->mainImage, $imageWidth, $imageHeight, $row->title, $isThumb, $image_quanlity ) ){
						  $row->mainImage = $image;
						}
						if( $show_preview ){
							if( $row->thumbnail &&  $image = self::renderThumb($row->thumbnail, $thumbWidth, $thumbHeight, $row->title, $isThumb, $image_quanlity, true ) ){
							  $row->thumbnail = $image;
							}
						}
						*/
						$data[] = $group;
					}
				}
			break;
			case "videos":
				$categories = $params->get("video_categories","");
				$categories = is_array($categories)?implode(",",$categories):$categories;
				$where = "";
				if(!empty($categories)){
					$where .= " AND v.category_id  IN (".$categories.")";
				}
				$show_video_desc = $params->get("show_video_desc",1);
				$show_video_category = $params->get("show_video_category",1);
				$show_added_date = $params->get("show_added_date", 1);
				$show_video_upload_by = $params->get("show_video_upload_by",1);
				$show_video_nr_view = $params->get("show_video_nr_view", 1);
				$show_video_nr_comments = $params->get("show_video_nr_comments", 1);
				$show_video_location = $params->get("show_video_location", 1);
				$ordering = $params->get("videos_ordering","v.hits__DESC");
				$tmpOrdering = $ordering;
				$itemids = $rows1 = array();
				if($ordering == "random"){
					$ordering = " RAND() ";
				}
				else{
					$ordering = str_replace("__"," ",$ordering);
				}
				if(!empty($ordering))
					$ordering =" ORDER BY ".$ordering;

				if($tmpOrdering == "comments__DESC"){
					$ordering = "";
					$query1 = 'SELECT v.*,vc.name AS category_name,u.name AS username,w.contentid, count(w.id) AS comments FROM #__community_videos AS v ';
					$query1 .= ' LEFT JOIN #__community_videos_category AS vc ON v.category_id=vc.id ';
					$query1 .= ' LEFT JOIN #__users AS u ON v.creator = u.id ';
					$query1 .= ' LEFT JOIN #__community_wall as w on v.id=w.contentid AND w.type="videos" ';
					$where1 = ' WHERE v.published = 1 ';
					$query1 .= $where1." GROUP BY w.contentid ORDER BY comments DESC LIMIT 0,".$limit;
					$db->setQuery( $query1 );
					$rows1 = $db->loadObjectList();
					if(!empty($rows1)){
						$limit = $limit - count( $rows1 );
						foreach($rows1 as $row){
							$itemids[] = $row->id;
						}
					}
				}
			
				$query = "SELECT v.*,vc.name AS category_name,u.name AS username FROM #__community_videos AS v";
				$query .= " LEFT JOIN #__community_videos_category AS vc ON v.category_id=vc.id ";
				$query .= ' LEFT JOIN #__users AS u ON v.creator = u.id ';
				$query .= " WHERE v.published=1 ";
				if(!empty($itemids)){
					$where .= 'AND v.id NOT IN('.implode(",",$itemids).') ';
				}
				$query .= $where.$ordering." LIMIT ".$limit;
				if($limit > 0){
					$db->setQuery($query);
					$rows = $db->loadObjectList( );
					if(is_array($rows) && is_array($rows1)){
						$tmp = array_merge( $rows1, $rows );
						$rows = $tmp;
					}
				}
				else{
					$rows = $rows1;
				}
				if(!empty($rows)){
					foreach($rows as $row){
						$video	=& JTable::getInstance( 'Video' , 'CTable' );
						$video->bind($row);
						$video->username = $row->username;
						$video->category_name = $row->category_name;
						if($show_added_date){
							if( $video->created != '0000-00-00 00:00:00' )
								{
									$created	= new JDate( $video->created );
									$video->created		= CActivityStream::_createdLapse( $created );
								}
						}
						if($video->duration != 0)
						{
							$video->duration = CVideosHelper::formatDuration( (int)($video->duration), 'HH:MM:SS' );
							$video->duration = CVideosHelper::toNiceHMS( $video->duration );
						}
						else
						{
							$video->duration = JText::_('COM_COMMUNITY_VIDEOS_DURATION_NOT_AVAILABLE');
						}
						$url	= 'index.php?option=com_community&view=videos&task=video';
						if ($video->creator_type == VIDEO_GROUP_TYPE)
						{
							$url .= '&groupid='.$video->groupid;
						}
						else
						{
							// defaul as user type, VIDEO_USER_TYPE
							$url .= '&userid='.$video->creator;
						}
						$url	.= '&videoid='.$video->id;
						$video->link = CRoute::_( $url );
						$video->category_link = CRoute::_('index.php?option=com_community&view=videos&catid='.$video->id);
						$video->user_link = CRoute::_('index.php?option=com_community&view=profile&userid=' . $video->creator );
						$video->thumbnail = $video->getThumbnail();
						$video->mainImage =   '<img class="avatar" src="'.$video->getThumbnail().'"  alt="'.CStringHelper::escape( $video->title ).'">';
						/*
						if( $video->mainImage &&  $image=self::renderThumb($row->mainImage, $imageWidth, $imageHeight, $row->title, $isThumb, $image_quanlity ) ){
						  $row->mainImage = $image;
						}
						if( $show_preview ){
							if( $row->thumbnail &&  $image = self::renderThumb($row->thumbnail, $thumbWidth, $thumbHeight, $row->title, $isThumb, $image_quanlity, true ) ){
							  $row->thumbnail = $image;
							}
						}
						*/
						$video->comments = 0;
						if($show_video_nr_comments){
							$query = "SELECT count(contentid) AS comments FROM #__community_wall WHERE type='videos' AND published=1 AND contentid=".$video->id;
							$db->setQuery( $query );
							$video->comments = $db->loadResult();
						}
						$data[] = $video;
					}
				}
			break;
			case "events":
				$categories = $params->get("event_categories","");
				$show_past_events = $params->get("show_past_events",1);
				$categories = is_array($categories)?implode(",",$categories):$categories;
				$where = "";
				if(!empty($categories)){
					$where .= " AND e.catid  IN (".$categories.")";
				}
				if($show_past_events){
					$nowDate	= $db->Quote(JFactory::getDate()->toMySQL());
					$where .= " AND e.enddate < ".$nowDate;
				}
				$show_event_summary = $params->get("show_event_summary", 1);
				$show_event_category = $params->get("show_event_category", 1);
				$show_event_time = $params->get("show_event_time", 1);
				$show_nr_users_attending = $params->get("show_nr_users_attending", 1);
				$ordering = $params->get("events_ordering", "e.confirmedcount__DESC");
				if($ordering == "random"){
					$ordering = " RAND() ";
				}
				elseif($ordering == "comments__DESC"){
					$ordering = "";
				}
				else{
					$ordering = str_replace("__"," ",$ordering);
				}
				if($ordering == "comments__DESC")
					$ordering = "";
				if(!empty($ordering))
					$ordering =" ORDER BY ".$ordering;
				
				$query = "SELECT e.*,ec.name AS category_title FROM #__community_events AS e ";
				$query .= " LEFT JOIN #__community_events_category AS ec ON e.catid = ec.id ";
				$query .= " WHERE e.published = 1 ";
				$query .= $where.$ordering." LIMIT ".$limit;
				$db->setQuery( $query );
				$rows = $db->loadObjectList();
				$config	=   CFactory::getConfig();
				$timeFormat	=   ($config->get('eventshowampm')) ?  JText::_('COM_COMMUNITY_DATE_FORMAT_LC2_12H') : JText::_('COM_COMMUNITY_DATE_FORMAT_LC2_24H');

				if(!empty($rows)){
					foreach($rows as $row){
						$event	    =&	JTable::getInstance( 'Event' , 'CTable' );
						$event->bind( $row );
						$event->link = $event->getLink();
						$event->category_title = $row->category_title;
						$event->startdate = CTimeHelper::getFormattedTime($event->startdate, $timeFormat);
						$event->enddate = CTimeHelper::getFormattedTime($event->enddate, $timeFormat);
						$evnet->category_link = CRoute::_('index.php?option=com_community&view=events&categoryid=' . $event->catid );
						$event->thumbnail = $event->getAvatar();
						$event->mainImage =   '<img class="avatar" src="'.$event->getThumbAvatar().'"  alt="'.CStringHelper::escape( $event->title ).'">';
						/*
						if( $event->mainImage &&  $image=self::renderThumb($event->mainImage, $imageWidth, $imageHeight, $event->title, $isThumb, $image_quanlity ) ){
						  $event->mainImage = $image;
						}
						if( $show_preview ){
							if( $event->thumbnail &&  $image = self::renderThumb($event->thumbnail, $thumbWidth, $thumbHeight, $event->title, $isThumb, $image_quanlity, true ) ){
							  $event->thumbnail = $image;
							}
						}
						*/
						$data[] = $event;
					}
				}
			break;
			case "users":
			default:
				$show_profile_view = $params->get("show_profile_view",0);
				$featured = $params->get("featured_member","0");
				$show_last_online = $params->get("show_last_online", 0);
				$show_user_status = $params->get("show_user_status", 0);
				$show_karma = $params->get("show_karma",0);
				
				if($featured){
					$query = "SELECT f.cid AS userid FROM #__community_featured AS f ";//WHERE `type`='users' ORDER BY `created` DESC LIMIT 0, ".$limit;
					$query .= " LEFT JOIN #__community_users AS u ON f.cid = u.userid ";
					$query .= " LEFT JOIN #__users AS us ON f.cid = us.id ";
				}
				else{
					$query = "SELECT userid FROM #__community_users AS u ";
					$query .= " LEFT JOIN #__users AS us ON u.userid = us.id ";
				}
				
				//$query .= " LEFT JOIN #__community_profiles AS p ON u.profile_id=p.id ";
				$onlyUpdatedAvatar = $params->get("avatar_only","0");
				CFactory::load( 'libraries' , 'userpoints');
				
				$where = "";
				if($featured){
					$where .=" AND f.type = 'users' ";
				}
				if($onlyUpdatedAvatar){
					$where .= " AND u.avatar <>'' ";
				}
				$ordering = $params->get("users_ordering","u.view__DESC");
				if($ordering == "random"){
					$ordering = " RAND() ";
				}
				else{
					$ordering = str_replace("__"," ",$ordering);
				}
				if(!empty($ordering))
					$ordering =" ORDER BY ".$ordering;
				$query .= " WHERE us.block=0 ";
				$query .= $where.$ordering." LIMIT 0,".$limit;
				//echo $query;die();
				$db->setQuery($query);
				$rows = $db->loadObjectList();
				
				if(!empty($rows)){
					foreach($rows as $row){
						$user = CFactory::getUser( $row->userid );
						if(!empty($user)){
							$user->lastLogin = "";
							if($show_last_online){
								$lastLogin	= JText::_('COM_COMMUNITY_PROFILE_NEVER_LOGGED_IN');
								if( $user->lastvisitDate != '0000-00-00 00:00:00' )
								{
									$userLastLogin	= new JDate( $user->lastvisitDate );
									$lastLogin		= CActivityStream::_createdLapse( $userLastLogin );
									$user->lastLogin = $lastLogin;
								}
							}
							if( $show_karma){
								$user->karma = CUserPoints::getPointsImage($user);
							}
							$user->title = $user->name;
							$user->link = CRoute::_('index.php?option=com_community&view=profile&userid=' . $user->id );
							$user->thumbnail = $user->getAvatar();
							$user->mainImage =  '<img class="avatar" src="'.$user->getThumbAvatar().'"  alt="'.$user->getDisplayName().'">';
							/*
							if( $user->mainImage &&  $image=self::renderThumb($user->mainImage, $imageWidth, $imageHeight, $user->name, $isThumb, $image_quanlity ) ){
							  $user->mainImage = $image;
							}
							if( $show_preview ){
								if( $user->thumbnail &&  $image = self::renderThumb($user->thumbnail, $thumbWidth, $thumbHeight, $user->name, $isThumb, $image_quanlity, true ) ){
								  $user->thumbnail = $image;
								}
								
							}
							*/
						}
						$data[] = $user;
					}
				}
			break;
		}
		return $data;
	}
	
	public static function _cleanIntrotext($introtext)
	{
		$introtext = str_replace('<p>', ' ', $introtext);
		$introtext = str_replace('</p>', ' ', $introtext);
		$introtext = strip_tags($introtext, '<a><em><strong>');

		$introtext = trim($introtext);

		return $introtext;
	}

	/**
	* This is a better truncate implementation than what we
	* currently have available in the library. In particular,
	* on index.php/Banners/Banners/site-map.html JHtml's truncate
	* method would only return "Article...". This implementation
	* was taken directly from the Stack Overflow thread referenced
	* below. It was then modified to return a string rather than
	* print out the output and made to use the relevant JString
	* methods.
	*
	* @link http://stackoverflow.com/questions/1193500/php-truncate-html-ignoring-tags
	* @param mixed $html
	* @param mixed $maxLength
	*/
	public static function truncate($html, $maxLength = 0)
	{
		$printedLength = 0;
		$position = 0;
		$tags = array();

		$output = '';

		if (empty($html)) {
			return $output;
		}

		while ($printedLength < $maxLength && preg_match('{</?([a-z]+)[^>]*>|&#?[a-zA-Z0-9]+;}', $html, $match, PREG_OFFSET_CAPTURE, $position))
		{
			list($tag, $tagPosition) = $match[0];

			// Print text leading up to the tag.
			$str = JString::substr($html, $position, $tagPosition - $position);
			if ($printedLength + JString::strlen($str) > $maxLength) {
				$output .= JString::substr($str, 0, $maxLength - $printedLength);
				$printedLength = $maxLength;
				break;
			}

			$output .= $str;
			$lastCharacterIsOpenBracket = (JString::substr($output, -1, 1) === '<');

			if ($lastCharacterIsOpenBracket) {
				$output = JString::substr($output, 0, JString::strlen($output) - 1);
			}

			$printedLength += JString::strlen($str);

			if ($tag[0] == '&') {
				// Handle the entity.
				$output .= $tag;
				$printedLength++;
			}
			else {
				// Handle the tag.
				$tagName = $match[1][0];

				if ($tag[1] == '/') {
					// This is a closing tag.
					$openingTag = array_pop($tags);

					$output .= $tag;
				}
				else if ($tag[JString::strlen($tag) - 2] == '/') {
					// Self-closing tag.
					$output .= $tag;
				}
				else {
					// Opening tag.
					$output .= $tag;
					$tags[] = $tagName;
				}
			}

			// Continue after the tag.
			if ($lastCharacterIsOpenBracket) {
				$position = ($tagPosition - 1) + JString::strlen($tag);
			}
			else {
				$position = $tagPosition + JString::strlen($tag);
			}

		}

		// Print any remaining text.
		if ($printedLength < $maxLength && $position < JString::strlen($html)) {
			$output .= JString::substr($html, $position, $maxLength - $printedLength);
		}

		// Close any open tags.
		while (!empty($tags))
		{
			$output .= sprintf('</%s>', array_pop($tags));
		}

		$length = JString::strlen($output);
		$lastChar = JString::substr($output, ($length - 1), 1);
		$characterNumber = ord($lastChar);

		if ($characterNumber === 194) {
			$output = JString::substr($output, 0, JString::strlen($output) - 1);
		}

		$output = JString::rtrim($output);

		return $output.'&hellip;';
	}
	}
}
?>
