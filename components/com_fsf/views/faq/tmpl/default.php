<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

function start_faq_col_item()
{
	
}

function end_faq_col_item()
{
	
}

?>
<?php echo FSF_Helper::PageStyle(); ?>
<?php echo FSF_Helper::PageTitle("FREQUENTLY_ASKED_QUESTIONS",$this->curcattitle); ?>
<div class="fsf_spacer"></div>

<?php $acl = 1; ?>

<?php if ($this->showcats) : ?>
	
	<?php echo FSF_Helper::PageSubTitle("PLEASE_SELECT_YOUR_QUESTION_CATEGORY"); ?>
	<div class="fsf_faq_catlist" id='fsf_faq_catlist'>
		<?php $colwidth = floor(100 / $this->num_cat_colums) . "%"; ?>
		<?php $column = 1; ?>
		<table width='100%' cellspacing="0" cellpadding="0">

	    <!-- START SEARCH -->
		<?php if (!$this->hide_search) : ?>
			<?php
					if ($column == 1)
					echo "<tr><td width='$colwidth' class='fsf_faq_cat_col_first' valign='top'>";
				else 
					echo "<td width='$colwidth' class='fsf_faq_cat_col' valign='top'>";        
			?>
           	<div class='faq_category'>
	    		<div class='faq_category_image'>
	    			<img src='<?php echo JURI::root( true ); ?>/components/com_fsf/assets/images/search.png' width='64' height='64'>
	    		</div>
				<div class='faq_category_head' style="padding-top:6px;padding-bottom:6px;">
					<?php if ($this->curcatid == -1) : ?><b><?php endif; ?>
					<?php echo JText::_("SEARCH_FAQS"); ?>
					<?php if ($this->curcatid == -1) : ?></b><?php endif; ?>
				</div>
				<form action="<?php echo FSFRoute::x( 'index.php?option=com_fsf&view=faqs' );?>" method="get" name="adminForm">
					<input type='hidden' name='option' value='com_fsf' />
					<input type='hidden' name='Itemid' value='<?php echo JRequest::getVar('Itemid'); ?>' />
					<input type='hidden' name='view' value='faq' />
					<input type='hidden' name='catid' value='<?php echo $this->curcatid; ?>' />
					<input name='search' value="<?php echo JViewLegacy::escape($this->search); ?>">
					<input type='submit' class='button' value='<?php echo JText::_("SEARCH"); ?>' >
				</form>
			</div>
			<div class='faq_category_faqlist'></div>

			<?php 
				if ($column == $this->num_cat_colums)
					echo "</td></tr>";
				else 
					echo "</td>";
	            
				$column++;
				if ($column > $this->num_cat_colums)
					$column = 1;
			?>

	    <?php endif; ?>
	    <!-- END SEARCH -->
		
		<!-- ALL FAQS START -->
		<?php if (!$this->hide_allfaqs) : ?>
			<?php
				if ($column == 1)
					echo "<tr><td width='$colwidth' class='fsf_faq_cat_col_first' valign='top'>";
				else 
					echo "<td width='$colwidth' class='fsf_faq_cat_col' valign='top'>";        
			?>
	    		<div class='faq_category'>
				<div class='faq_category_image'>
	    				<img src='<?php echo JURI::root( true ); ?>/components/com_fsf/assets/images/allfaqs.png' width='64' height='64'>
					</div>
					<div class='faq_category_head'>
						<?php if ($this->curcatid == 0) : ?><b><?php endif; ?>
						<A class="fsf_highlight" href='<?php echo FSFRoute::x( '&limitstart=&catid=' . 0 );?>'><?php echo JText::_("ALL_FAQS"); ?></a>
						<?php if ($this->curcatid == 0) : ?></b><?php endif; ?>
					</div>
					<div class='faq_category_desc'><?php echo JText::_("VIEW_ALL_FREQUENTLY_ASKED_QUESTIONS"); ?></div>
				</div>
				<div class='faq_category_faqlist'></div>
			<?php 
				if ($column == $this->num_cat_colums)
					echo "</td></tr>";
				else 
					echo "</td>";
			
				$column++;
				if ($column > $this->num_cat_colums)
					$column = 1;
			?>

        <?php endif; ?>
		<!-- END ALL FAQS -->
	

		<!-- TAGS START -->
		<?php if (!$this->hide_tags) : ?>
	    	<?php
				if ($column == 1)
					echo "<tr><td width='$colwidth' class='fsf_faq_cat_col_first' valign='top'>";
				else 
					echo "<td width='$colwidth' class='fsf_faq_cat_col' valign='top'>";        
			?>
				<div class='faq_category'>
					<div class='faq_category_image'>
	    				<img src='<?php echo JURI::root( true ); ?>/components/com_fsf/assets/images/tags-64x64.png' width='64' height='64'>
					</div>
					<div class='faq_category_head'>
						<?php if ($this->curcatid == 0) : ?><b><?php endif; ?>
						<A class="fsf_highlight" href='<?php echo FSFRoute::x( '&limitstart=&catid=' . -4 );?>'><?php echo JText::_("TAGS"); ?></a>
						<?php if ($this->curcatid == 0) : ?></b><?php endif; ?>
					</div>
					<div class='faq_category_desc'><?php echo JText::_("VIEW_FAQ_TAGS"); ?></div>
				</div>
				<div class='faq_category_faqlist'></div>
			<?php 
			if ($column == $this->num_cat_colums)
				echo "</td></tr>";
			else 
				echo "</td>";
			
			$column++;
			if ($column > $this->num_cat_colums)
				$column = 1;
			?>

        <?php endif; ?>
		<!-- END TAGS -->
		
		<!-- FEATURED FAQS START -->
		<?php 
			if ($this->show_featured)
			{
			
				if ($column == 1)
					echo "<tr><td width='$colwidth' class='fsf_faq_cat_col_first' valign='top'>";
				else 
					echo "<td width='$colwidth' class='fsf_faq_cat_col' valign='top'>";        
			
				// set up fake $cat object and include the _cat.php template
				$cat = array();
				$cat['image'] = '/components/com_fsf/assets/images/featured.png';
				$cat['id'] = -5;
				$cat['title'] = JText::_('FEATURED_FAQS');
				$cat['description'] = JText::_('VIEW_FEATURED_FREQUENTLY_ASKED_QUESTIONS');
				$cat['faqs'] = array();
				if (!empty($this->featured_faqs))
					$cat['faqs'] = $this->featured_faqs;
			
				include JPATH_SITE.DS.'components'.DS.'com_fsf'.DS.'views'.DS.'faq'.DS.'snippet'.DS.'_cat.php';

				if ($column == $this->num_cat_colums)
					echo "</td></tr>";
				else 
					echo "</td>";
			
				$column++;
				if ($column > $this->num_cat_colums)
					$column = 1;
			}
		 ?>
		<!-- END FEATURED FAQS -->

		<!-- ALL CATS -->
		<?php foreach ($this->catlist as $cat) : ?>
        <?php
        	if ($column == 1)
        		echo "<tr><td width='$colwidth' class='fsf_faq_cat_col_first' valign='top'>";
        	else 
        		echo "<td width='$colwidth' class='fsf_faq_cat_col' valign='top'>";        
        ?>
		<?php include JPATH_SITE.DS.'components'.DS.'com_fsf'.DS.'views'.DS.'faq'.DS.'snippet'.DS.'_cat.php' ?>
	    <?php 
	        if ($column == $this->num_cat_colums)
	            echo "</td></tr>";
	        else 
	            echo "</td>";
	            
	        $column++;
	        if ($column > $this->num_cat_colums)
	            $column = 1;
	    ?>
		<?php endforeach; ?>
	    <!-- END CATS -->

		<!-- CAT LIST END -->
	    <?php 
	        if ($column > 1)
	        { 
	    		while ($column <= $this->num_cat_colums)
	    		{
	    			echo "<td class='fsf_faq_cat_col' valign='top'><div class='faq_category'></div></td>";	
	    			$column++;
	    		}
	            echo "</tr>"; 
	            $column = 1;
	        }
	    ?>
	 
 			<tr><td colspan='<?php echo $this->num_cat_colums; ?>'>
	    	<div class='faq_category_footer'></div>
	    </td></tr>
	    </table>
	</div>


<?php endif; ?>

<?php if ($this->showfaqs) : ?>

	<div class='faq_category'>
	    <?php if ($this->curcatimage) : ?>
		<div class='faq_category_image'>
			<?php if (substr($this->curcatimage,0,1) == "/") : ?>
			<img src='<?php echo JURI::root( true ); ?><?php echo $this->curcatimage; ?>' width='64' height='64'>
			<?php else: ?>
			<img src='<?php echo JURI::root( true ); ?>/images/fsf/faqcats/<?php echo $this->curcatimage; ?>' width='64' height='64'>
			<?php endif; ?>
	    </div>
	    <?php endif; ?>
			<div class='fsf_spacer contentheading' style="padding-top:6px;padding-bottom:6px;">

			<?php if (FSF_Settings::Get('faq_cat_prefix') || !$this->curcattitle): ?>
				<?php echo JText::_("FAQS"); ?>
				<?php if ($this->curcattitle) echo " - "; ?> 
			<?php endif; ?>
			<?php echo $this->curcattitle; ?>

			</div>
		<div class='faq_category_desc'><?php echo $this->curcatdesc; ?></div>
	</div>
	<div class='fsf_clear'></div>
	

	<?php if ($this->curcatid == -1): ?>
		<div class='faq_category'>
			<div class='faq_category_image'>
				<img src='<?php echo JURI::root( true ); ?>/components/com_fsf/assets/images/search.png' width='64' height='64'>
			</div>
			<div class='faq_category_head' style="padding-top:6px;padding-bottom:6px;">
				<?php echo JText::_("SEARCH_FAQS"); ?>
			</div>

			<form action="<?php echo FSFRoute::x( 'index.php?option=com_fsf&view=faqs' );?>" method="get" name="adminForm">
				<input type='hidden' name='option' value='com_fsf' />
				<input type='hidden' name='Itemid' value='<?php echo JRequest::getVar('Itemid'); ?>' />
				<input type='hidden' name='view' value='faq' />
				<input type='hidden' name='catid' value='<?php echo $this->curcatid; ?>' />
				<input name='search' value="<?php echo JViewLegacy::escape($this->search); ?>">
				<input type='submit' class='button' value='<?php echo JText::_("SEARCH"); ?>' >
			</form>
		</div>
		<div class='faq_category_faqlist'></div>
	<?php endif; ?>
	
	
	<div class='fsf_faqs' id='fsf_faqs'>
	<?php if (count($this->items)) foreach ($this->items as $faq) : ?>
	<?php include JPATH_SITE.DS.'components'.DS.'com_fsf'.DS.'views'.DS.'faq'.DS.'snippet'.DS.'_faq.php';
	//include "components/com_fsf/views/faq/snippet/_faq.php" ?>
	<?php endforeach; ?>
	<?php if (count($this->items) == 0): ?>
	<div class="fsf_no_results"><?php echo JText::_("NO_FAQS_MATCH_YOUR_SEARCH_CRITERIA");?></div>
	<?php endif; ?>
	</div>

	<?php if ($this->enable_pages): ?>
		<form id="adminForm" action="<?php echo FSFRoute::x( 'index.php?option=com_fsf&view=faq&catid=' . $this->curcatid );?>" method="post" name="adminForm">
		<input type='hidden' name='catid' value='<?php echo $this->curcatid; ?>' />
		<input type='hidden' name='enable_pages' value='<?php echo $this->enable_pages; ?>' />
		<input type='hidden' name='view_mode' value='<?php echo $this->view_mode; ?>' />
			<?php echo $this->pagination->getListFooter(); ?>
		</form>
	<?php endif; ?>

<?php endif; ?>

<?php include JPATH_SITE.DS.'components'.DS.'com_fsf'.DS.'_powered.php'; ?>
<?php if (FSF_Settings::get( 'glossary_faqs' )) echo FSF_Glossary::Footer(); ?>

<?php echo FSF_Helper::PageStyleEnd(); ?>

<!--  -->
