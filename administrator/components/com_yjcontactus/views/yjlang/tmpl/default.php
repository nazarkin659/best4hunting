<?php 
/**
 * Settings tmpl for YjContactUS Component
 * 
 * @package    
 * @subpackage Components
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die(); 

JHTML::_('behavior.tooltip');
?>

    <fieldset id="folderview">
        <legend><?php echo JText::_( 'COM_YJCONTACTUS_EDIT_ADMIN_LANG_FILE' ); ?></legend>
<?php
		$writeable		= '<b><font color="green">'. JText::_( 'Writable' ) .'</font></b>';
		$unwriteable	= '<b><font color="red">'. JText::_( 'Unwritable' ) .'</font></b>';
		
		echo $this->admin_lang_file_name ." - ";
		echo is_writable( $this->admin_lang_file_name ) ? $writeable : $unwriteable;
		$disabled = is_writable( $this->admin_lang_file_name ) ? "" : "disabled=\"disabled\"";
?>        
	        <form action="index.php" name="folderForm" id="folderForm" method="post">        
                <textarea style="width:100%; height:400px;" name="admin_lang_file_content" <?php echo $disabled; ?>><?php echo $this->admin_lang_file?></textarea>
                <button type="submit"><?php echo JText::_( 'COM_YJCONTACTUS_UPDATE_LANG_FILE' ); ?></button>
                <?php echo JHTML::_( 'form.token' ); ?>
                <input type="hidden" name="option" value="com_yjcontactus"/>
                <input type="hidden" name="task" value="yjlang.save_admin_lang"/>
                <input type="hidden" name="controller" value="yjlang" />
                <input type="hidden" name="view" value="yjlang" />    
            </form>            
    </fieldset> 
    
    <fieldset id="folderview">
        <legend><?php echo JText::_( 'COM_YJCONTACTUS_EDIT_FRONT_LANG_FILE' ); ?></legend>
<?php
		$writeable		= '<b><font color="green">'. JText::_( 'Writable' ) .'</font></b>';
		$unwriteable	= '<b><font color="red">'. JText::_( 'Unwritable' ) .'</font></b>';
		
		echo $this->front_lang_file_name ." - ";
		echo is_writable( $this->front_lang_file_name ) ? $writeable : $unwriteable;
		$disabled = is_writable( $this->front_lang_file_name ) ? "" : "disabled=\"disabled\"";
?>        
	        <form action="index.php" name="folderForm" id="folderForm" method="post">        
                <textarea style="width:100%; height:400px;" name="front_lang_file_content" <?php echo $disabled; ?>><?php echo $this->front_lang_file?></textarea>
                <button type="submit"><?php echo JText::_( 'COM_YJCONTACTUS_UPDATE_LANG_FILE' ); ?></button>
                <?php echo JHTML::_( 'form.token' ); ?>
                <input type="hidden" name="option" value="com_yjcontactus"/>
                <input type="hidden" name="task" value="yjlang.save_front_lang"/>
                <input type="hidden" name="controller" value="yjlang" />
                <input type="hidden" name="view" value="yjlang" />    
            </form>            
    </fieldset>        

<p><center><?php echo JText::_( 'COM_YJCONTACTUS_YJ_POWERED_BY', true )?> <a href='http://www.youjoomla.com' target='_blank'>YouJoomla.com</a></center></p>