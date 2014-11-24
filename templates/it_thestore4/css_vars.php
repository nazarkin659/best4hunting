<?php
//  @copyright	Copyright (C) 2008 - 2012 IceTheme. All Rights Reserved
//  @license	Copyrighted Commercial Software 
//  @author     IceTheme (icetheme.com)

// No direct access.
defined('_JEXEC') or die;

// Add CSS
$doc =&JFactory::getDocument();
$doc->addStyleSheet($this->baseurl. '/templates/system/css/system.css');
$doc->addStyleSheet($this->baseurl. '/templates/system/css/general.css');
$doc->addStyleSheet($this->baseurl. '/templates/' .$this->template. '/css/reset.css');
$doc->addStyleSheet($this->baseurl. '/templates/' .$this->template. '/css/typography.css');
$doc->addStyleSheet($this->baseurl. '/templates/' .$this->template. '/css/forms.css');
$doc->addStyleSheet($this->baseurl. '/templates/' .$this->template. '/css/modules.css');
$doc->addStyleSheet($this->baseurl. '/templates/' .$this->template. '/css/joomla.css');
//$doc->addStyleSheet($this->baseurl. '/templates/' .$this->template. '/css/layout.css');
$doc->addStyleSheet($this->baseurl. '/templates/' .$this->template. '/css/virtuemart.css');
$doc->addStyleSheet($this->baseurl. '/templates/' .$this->template. '/css/custom.css');
?>

<?php
    $document = JFactory::getDocument();
    $document->addScript("$this->baseurl/templates/$this->template/fixes.js");
?>

<style type="text/css" media="screen">
/* Select the style */
/*\*/@import url("<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/<?php echo $TemplateStyle; ?>.css");/**/

<?php if ($this->countModules('left')) { ?>
/* Left Columns Parameters */
#left-column { width: <?php echo $layout_leftcol_width ?>px;}
#middle-column { width: <?php echo $middle_col_width ?>px !important; float:right;}
<?php } ?>	

<?php if ($this->countModules('right')) { ?>
/* Right Column Parameters */
#right-column { width: <?php echo $layout_rightcol_width ?>px;}
#middle-column { width: <?php echo $middle_col_width2 ?>px; float:left;}
<?php } ?>	

<?php if ($this->countModules('left and right')) { ?>
#middle-column { width:<?php echo $middle_col_width3 ?>px; margin-left:20px;}
<?php } ?>	


/* Fix the "latest" module */
#latest { width:340px;}

<?php if ($this->countModules('breadcrumbs')) { ?>
#contact { margin-top:1px;}
<?php } ?>


<?php if($fix_copyright=="false") : ?>
#copyright { border:none; box-shadow:none;}	
<?php endif; ?>	

</style>	

<!-- Google Fonts -->
<link href='https://fonts.googleapis.com/css?family=Droid+Sans' rel='stylesheet' type='text/css' />


<!--[if lte IE 8]>
<link rel="stylesheet" type="text/css" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/ie.css" />
<![endif]-->

<!--[if lte IE 9]>
<style type="text/css" media="screen">
#left-column  .col-module h3.mod-title span:after {
	border-width: 0.82em;
</style>	
<![endif]-->