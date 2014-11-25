<?php
/**
 * IceVmCategory Extension for Joomla 2.5 By IceTheme
 * 
 * 
 * @copyright	Copyright (C) 2008 - 2012 IceTheme.com. All rights reserved.
 * @license		GNU General Public License version 2
 * 
 * @Website 	http://www.icetheme.com/Joomla-Extensions/iceaccordion.html
 * @Support 	http://www.icetheme.com/Forums/IceVmCategory/
 *
 */
 
 
 class vmCategoryHelper{
	
	function getCategories($order = 'id', $ordering = 'asc', $publish = 0, $params) {
		$db =& JFactory::getDBO();
        
        $add_where = ($publish)?(" C.published = '1' "):("");        
        if ($order=="id") $orderby = "category_id";
        if ($order=="name") $orderby = "`name`";
        if ($order=="ordering") $orderby = "ordering";
        if (!$orderby) $orderby = "ordering";
        
		$query = 'SELECT L.category_name AS name,L.category_description as description,L.virtuemart_category_id AS category_id,C.published as category_publish, C.ordering,CC.category_parent_id  FROM `#__virtuemart_categories_'.VMLANG.'` as L';
		$query .= ' JOIN `#__virtuemart_categories` as C using (`virtuemart_category_id`)';
		$query .= ' LEFT JOIN `#__virtuemart_category_categories` as CC on C.`virtuemart_category_id` = CC.`category_child_id`';
		$query .= " WHERE " . $add_where. " ORDER BY ".$orderby." ".$ordering;
        $db->setQuery($query);
        $categories = $db->loadObjectList();
        $imageWidth = $params->get('image_width', 20);
		$imageHeight = $params->get('image_heigth', 20); 

		$isThumb = true;
		if( empty($categories) ) return '';
		if(!class_exists('VirtueMartModelCategory')) require(JPATH_VM_ADMINISTRATOR.DS.'models'.DS.'category.php'); 
		$categoryModel = VmModel::getModel('category');
		if(!class_exists('TableCategory_medias')) require(JPATH_VM_ADMINISTRATOR.DS.'tables'.DS.'category_medias.php'); 
        foreach ($categories as $key => $value){
			$xrefTable = new TableCategory_medias($db);
			$categories[$key]->virtuemart_media_id = $xrefTable->load($value->category_id);

            $categories[$key]->category_link = JRoute::_ ( 'index.php?option=com_virtuemart&view=category&virtuemart_category_id=' . $value->category_id );
        }
		$categoryModel->addImages($categories,1);
		
		if(!empty($categories)){
			foreach($categories as $key=>$category){
				$image = isset($category->images[0])?$category->images[0]:null;
				$categories[$key]->category_image = "";
				if(!empty($image)){
					if(file_exists( $image->file_url)){
						if( $image->file_url &&  $image= self::renderThumb($image->file_url, $imageWidth, $imageHeight, $categories[$key]->name, true) ){
							$categories[$key]->category_image = $image;
						}
					}
				}
			}
		}
		$children = array();
		if ( $categories )
		{
			foreach ( $categories as $v )
			{				
				$pt 	= $v->category_parent_id;
				$list 	= @$children[$pt] ? $children[$pt] : array();
				array_push( $list, $v );
				$children[$pt] = $list;
			}
		}
		return $children;
    }
	
	function getHtml($order = 'id', $ordering = 'asc', $publish = 0, $params){
		$children = self::getCategories($order, $ordering, $publish, $params);
		$html = '';
		self::getHtmlCate($children,0,$html, 0 ,$params);
		$html = "<ul class='lofmenu'>".$html."</ul>";
		return $html;
	}
	static $_listcates = array();

	function getListCates( ){
		static $_listcates;
		if(empty( $_listcates )){
			$category_id = JRequest::getCmd('virtuemart_category_id', 0);
			if(!empty($category_id)){
				$db = &JFactory::getDBO();
				$tmp[ $category_id ] = $category_id;
				/*Select children category ids*/
				$query = "SELECT L.category_name as name, L.virtuemart_category_id AS category_id, CC.category_parent_id, C.published as category_publish FROM `#__virtuemart_categories_'.VMLANG.'` as L
				JOIN `#__virtuemart_categories` as C using (`virtuemart_category_id`)
				LEFT JOIN `#__virtuemart_category_categories` as CC on C.`virtuemart_category_id` = CC.`category_child_id`
				WHERE C.published = '1' ORDER BY category_parent_id, C.ordering";
				
				$db->setQuery($query);
				$all_cats = $db->loadObjectList();
				$tmp2 = array();
				if(count($all_cats)) {
					foreach ($all_cats as $key => $value) {
						$tmp2[ $value->category_id ] = $value->category_parent_id;
						if(!empty( $value->category_id ) && in_array($value->category_id, $tmp)){
							$tmp[ $value->category_parent_id ] = $value->category_parent_id;
							foreach($tmp2 as $key=>$val){
								if( !empty($key)  && !empty($val) && in_array($key, $tmp)){
									$tmp[ $val ] = $val;
								}
							}
						}
					}
				}
				$_listcates = $tmp;
				return $_listcates;
			}
		}
		else{
			return $_listcates;
		}
	}
	function getHtmlCate($children, $id = 0 , & $str, $leve = 0 , $params){
		$show_image = $params->get('show_image', 0); 
		$showcounter = $params->get('showcounter', 0); 
		$cates = self::getListCates();
		if(empty($cates)){
			$cates = array();
		}
		$leve ++;
		if(!empty($children[$id])){
			foreach($children[$id] as $item){
				$class = "";
				if(in_array($item->category_id, $cates)){
					$class = " ice-current ";
				}
				if(!empty($children[$item->category_id])){
					$class .= " ice-parent ";
				}
				$str .= "<li class='lofitem".$leve.$class."'>";
				$str .= "<a href='".$item->category_link."' >".($show_image ? $item->category_image : "")."<span>".$item->name.($showcounter ? " <span class=\"counter\">(".self::getTotalItem($children,$item->category_id).") </span>" : "")."</span>";
				if(!empty($children[$item->category_id])){
					$str .= "<i></i></a>";
					$str .= "<ul>";
					self::getHtmlCate($children, $item->category_id ,$str ,$leve, $params);
					$str .= "</ul>";
				}else{
					$str .= "</a>";
				}
				$str .="</li>";
			}
		}
		return $str;
	}
	/*
	* get Total item in  category
	* return integer
	*/
	function getTotalItem($children, $category_id){
		$arrCate = array();
		$arrCate = self::getAllSubcates($category_id);
		if(empty($arrCate)){
			return 0;
		}
		if(count($arrCate) == 1){
			$where = " WHERE pc.virtuemart_category_id = ".$arrCate[0]. " ";
		}else{
			$strCate = implode(',',$arrCate);
			$where = " WHERE pc.virtuemart_category_id IN (".$strCate.") ";
		}
		$db =& JFactory::getDBO();
		$query = "SELECT COUNT(DISTINCT pc.virtuemart_product_id) AS total FROM `#__virtuemart_product_categories` pc ".$where;
		$db->setQuery($query);
        $total = $db->loadObject();
		return $total->total;
	}
	/*
	* get all subcategories
	* return array
	*/
	function getAllSubcates($category_id){
		$db =& JFactory::getDBO();
		$tmp[] = $category_id;
		$query = "SELECT L.category_name as name, L.virtuemart_category_id AS category_id, CC.category_parent_id, C.published as category_publish FROM `#__virtuemart_categories_'.VMLANG.'` as L
				JOIN `#__virtuemart_categories` as C using (`virtuemart_category_id`)
				LEFT JOIN `#__virtuemart_category_categories` as CC on C.`virtuemart_category_id` = CC.`category_child_id`
				WHERE C.published = '1' ORDER BY category_parent_id, C.ordering";
		$db->setQuery($query);
		$all_cats = $db->loadObjectList();

		if(count($all_cats)) {
			foreach ($all_cats as $key => $value) {
				if(!empty( $value->category_parent_id ) && in_array($value->category_parent_id, $tmp)){
					$tmp[] = $value->category_id;
				}
			}
		}
		return $tmp;
	}
	/**
     *  check the folder is existed, if not make a directory and set permission is 755
     *
     * @param array $path
     * @access public,
     * @return boolean.
     */
     function renderThumb( $path, $width = 100, $height = 100, $title = '', $isThumb = true ){
      if( !preg_match("/.jpg|.png|.gif/",strtolower($path)) ) return '&nbsp;';
      if( $isThumb ){
		
        $path = str_replace( JURI::base(), '', $path );
        $imagSource = str_replace( '/', DS,  $path );
		
        if( file_exists($imagSource)  ) {
			
          $path =  $width."x".$height.'/'.$path;
          $thumbPath = JPATH_SITE.DS.'images'.DS.'mod_ice_vm_categories'.DS. str_replace( '/', DS,  $path );
          if( !file_exists($thumbPath) ) {
            $thumb = PhpThumbFactory::create( $imagSource  );  
            if( !self::makeDir( $path ) ) {
                return '';
            }   
            $thumb->adaptiveResize( $width, $height);
            
            $thumb->save( $thumbPath  ); 
          }
          $path = JURI::base().'images/mod_ice_vm_categories/'.$path;
        } 
      }
      return '<img src="'.$path.'" title="'.$title.'" alt="'.$title.'" width="'.$width.'px" height="'.$height. 'px">';
    }
	/**
     *  check the folder is existed, if not make a directory and set permission is 755
     *
     * @param array $path
     * @access public,
     * @return boolean.
     */
    function makeDir( $path ){
      $folders = explode ( '/',  ( $path ) );
      $tmppath =  JPATH_SITE.DS.'images'.DS.'mod_ice_vm_categories'.DS;
      if( !file_exists($tmppath) ) {
        JFolder::create( $tmppath, 0755 );
      }; 
      for( $i = 0; $i < count ( $folders ) - 1; $i ++) {
        if (! file_exists ( $tmppath . $folders [$i] ) && ! JFolder::create( $tmppath . $folders [$i], 0755) ) {
          return false;
        } 
        $tmppath = $tmppath . $folders [$i] . DS;
      }   
      return true;
    }
}
?>