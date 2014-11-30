<?php // no direct access
/*======================================================================*\
|| #################################################################### ||
|| # Youjoomla LLC - YJ- Licence Number 4719UB372
|| # Licensed to - alexs malov
|| # ---------------------------------------------------------------- # ||
|| # Copyright (C) 2006-2009 Youjoomla LLC. All Rights Reserved.        ||
|| # This file may not be redistributed in whole or significant part. # ||
|| # ---------------- THIS IS NOT FREE SOFTWARE ---------------- #      ||
|| # http://www.youjoomla.com | http://www.youjoomla.com/license.html # ||
|| #################################################################### ||
\*======================================================================*/
defined('_JEXEC') or die('Restricted access');
	//if no form published, display the msg and not display a form
	if(isset($this->display_msg) && $this->display_msg != 'published'){
		echo JTEXT::_($this->display_msg)."<br><br>";
	}else{
?> <div id="formcontainer">
       
        <p><?php echo JText::_('COM_YJCONTACTUS_INTRO_CONTACT_US'); ?></p>
        <form method="post" action="index.php<?php echo $this->append_session?>" id="contactForm" target="send_my_form" enctype="multipart/form-data">
            
            <table id="fomrbg">
                <tr>
                    <td><label for="category"><?php echo JText::_('COM_YJCONTACTUS_DEPARTMENT'); ?></label></td>
                    <td><select name="category" id="category"><option value=""><?php echo JText::_('COM_YJCONTACTUS_ENABLE_JS'); ?></option></select></td>
                </tr>
                <tr>
                    <td><label for="name"><?php echo JText::_('COM_YJCONTACTUS_NAME'); ?></label></td>
                    <td><input type="text" name="name" id="name" value="" /></td>
                </tr>
                <tr>
                    <td><label for="email"><?php echo JText::_('COM_YJCONTACTUS_EMAIL'); ?></label></td>
                    <td><input type="text" name="email" id="email" value="" /></td>
                </tr>
                <tr>
                    <td><label for="subject"><?php echo JText::_('COM_YJCONTACTUS_SUBJECT'); ?></label></td>
                    <td><input type="text" name="subject" id="subject" value="" /></td>
                </tr>
                <tr>
                    <td><label for="message"><?php echo JText::_('COM_YJCONTACTUS_MESSAGE'); ?></label></td>
                    <td><textarea name="message" id="message" rows="10" cols="5"><?php echo JText::_('COM_YJCONTACTUS_ALERT_MESSAGE'); ?></textarea></td>
                </tr>
                <tr id="upload_row">
                    <td><label for="archive"><?php echo JText::_('COM_YJCONTACTUS_UPLOAD'); ?></label></td>
                   <td class="hasTip" title="<?php echo JText::_('COM_YJCONTACTUS_UPLOAD'); ?>:<?php echo JText::_('COM_YJCONTACTUS_ALOWED_FILES'); ?>" ><input type="file" name="archive" id="archive" /></td>
                </tr>
                <tr id="captcha_row">
                    <td><label for="captcha"><?php echo JText::_('COM_YJCONTACTUS_CAPTCHA_DESCRITION'); ?></label></td>
                    <td><input type="text" name="captcha" id="captcha" <?php echo $this->form_details->captcha == 0 ? 'disabled="disabled"' : '' ?>/><img src="<?php echo JURI::base() ?>components/com_yjcontactus/helpers/captcha.php<?php echo $this->append_session?>" alt="<?php echo JText::_('COM_YJCONTACTUS_ALT_CAPTCHA'); ?>" id="captcha_image"/> <div id="captx"><a onclick="reload_captcha('<?php echo JURI :: base() ?>','captcha_image','<?php echo $this->append_session?>','captcha');"><?php echo JText::_('COM_YJCONTACTUS_RELOAD_CAPTCHA'); ?></a><br /><?php echo JText::_('COM_YJCONTACTUS_ALT_CAPTCHA'); ?></div></td>
                </tr>
                <tr>
                    <td colspan="2">
                    <div class="left"><input type="submit" class="submit" id="submit_btn" value="<?php echo JText::_('COM_YJCONTACTUS_SUBMIT'); ?>" onfocus="this.blur();"  /></div>
                    <div class="right"><div id="show_response"></div></div>
                    </td>
                </tr>
            </table>
            <input type="hidden" name="option" value="com_yjcontactus" />
            <input type="hidden" name="task" value="send" />
            <input type="hidden" name="form_id" value="<?php echo $this->form_details->id ?>" />
            <?php echo JHTML::_( 'form.token' ); ?>
        </form>
        </div>
        <iframe name="send_my_form" frameborder="0" height="0" width="0" src="#"></iframe>
<?php
	}
?>