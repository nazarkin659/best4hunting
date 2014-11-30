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
        <legend><?php echo JText::_( 'COM_YJCONTACTUS_UPLOAD_FOLDER' ); ?></legend>
		<form action="index.php" name="folderForm" id="folderForm" method="post">    
            <div class="view">
            <?php
                foreach($this->list_folders as $folders){
                    $putG[] = JHTML::_( 'select.option',  JText::_($folders->name), JText::_($folders->name));
                }
                echo  JHTML::_('select.genericlist', $putG, 'upload_folder', array('class'=>'inputbox', 'size'=>'1', 'id'=>'upload_folder'),'value','text', $this->folder);
            ?>
            <button type="submit"><?php echo JText::_( 'COM_YJCONTACTUS_SET_UPLOAD_FOLDER' ); ?></button>
            </div>
            <input type="hidden" name="option" value="com_yjcontactus"/>
            <input type="hidden" name="task" value="yjsettings.upload"/>
            <input type="hidden" name="boxchecked" value="0" />
            <input type="hidden" name="controller" value="yjsettings" />
            <input type="hidden" name="view" value="yjsettings" />    
            <input type="hidden" name="id" value="1" />    
            <?php echo JHTML::_( 'form.token' ); ?>        
        </form>
	    </fieldset>

		<fieldset id="folderview">                
        <legend><?php echo JText::_( 'COM_YJCONTACTUS_CREATE_FOLDER' ); ?></legend>
        <form action="index.php" name="folderForm" id="folderForm" method="post">
            <div class="path">
                <input class="inputbox" type="text" id="folderpath" style="width:20%" readonly="readonly" value="<?php echo JPATH_ROOT.DS."images".DS; ?>"/>
                <input class="inputbox" type="text" id="foldername" name="foldername"  />
                <input class="update-folder" type="hidden" name="folderbase" id="folderbase" value="<?php echo JPATH_ROOT.DS."images"; ?>" />
                <button type="submit"><?php echo JText::_( 'COM_YJCONTACTUS_CREATE_FOLDER_BUTTON' ); ?></button>
            </div>        
            <?php echo JHTML::_( 'form.token' ); ?>
            <input type="hidden" name="option" value="com_yjcontactus"/>
            <input type="hidden" name="task" value="yjsettings.create_folder"/>
            <input type="hidden" name="boxchecked" value="0" />
            <input type="hidden" name="controller" value="yjsettings" />
            <input type="hidden" name="view" value="yjsettings" />    
        </form>
   	    </fieldset>    

    <fieldset id="folderview">
        <legend><?php echo JText::_( 'Files' ); ?></legend>
        <div class="view">
            <iframe src="index.php?option=com_media&amp;view=mediaList&amp;tmpl=component&amp;folder=<?php echo $this->folder;?>&layout=details" id="folderframe" name="folderframe" width="100%" height="200" marginwidth="0" marginheight="0" scrolling="auto" frameborder="0"></iframe>
        </div>
    </fieldset>

<p><center><?php echo JText::_( 'COM_YJCONTACTUS_YJ_POWERED_BY', true )?> <a href='http://www.youjoomla.com' target='_blank'>YouJoomla.com</a></center></p>