<?php 
//  @copyright  Copyright (C) 2008 - 2011 IceTheme. All Rights Reserved
//  @license  Copyrighted Commercial Software 
//  @author     IceTheme (icetheme.com)

// No direct access.
defined('_JEXEC') or die;

// A code to show the offline.php page for the demo
if(JRequest::getCmd("tmpl","index")== "offline"){  
    if(is_file(dirname(__FILE__).DS."offline.php")){
        require_once(dirname(__FILE__).DS."offline.php");
    }else{
        if(is_file(JPATH_SITE.DS."templates".DS."system".DS."offline.php")){
          require_once(JPATH_SITE.DS."templates".DS."system".DS."offline.php");
      }
  }
}else{
// Include PHP files to the template
    include_once(JPATH_ROOT . "/templates/" . $this->template . '/icetools/default.php');

    ?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>" >
    <head>
    <!-- Bootstrap -->
        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">
        <!-- Optional theme -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap-theme.min.css">
        <!-- Latest compiled and minified JavaScript -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
   <!-- Bootstrap -->
        <!--<script type="text/javascript" src="/new/media/system/jquery.min.js"></script>   -->
        <script type="text/javascript">
         var _gaq = _gaq || [];
         _gaq.push(['_setAccount', 'UA-17081850-1']);
         _gaq.push(['_trackPageview']);

         (function() {
            var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
            ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
            var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
        })();
    </script>
    <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE9" />
    <jdoc:include type="head" />
    <?php
// Include CSS and JS variables
    include_once(JPATH_ROOT . "/templates/" . $this->template . '/css_vars.php');
    ?>
</head>
<body class="<?php echo $pageclass->get('pageclass_sfx'); ?> container-flex">
    <!-- Header -->
    <div id="header" class="clearfix row">
        <div class="col-sm-12">
           <?php if ($this->countModules('language + topmenu + cart')) { ?>
           <!-- TopBar -->
           <div id="topbar" class="col-sm-12">
            <?php if ($this->countModules('language')) { ?> 
            <div id="language">
              <jdoc:include type="modules" name="language" />
          </div>
          <?php } ?>
          <?php if ($this->countModules('topmenu')) { ?>
          <div id="topmenu">
            <jdoc:include type="modules" name="topmenu" />
        </div>
        <?php } ?>
    </div> <!-- TopBar -->
    <?php } ?>
    <div id="logo" class='col-sm-4'>
        <p><a href="<?php echo $this->baseurl ?>"><img src="<?php echo $this->baseurl ?>/<?php echo htmlspecialchars($logo); ?>" alt="<?php echo $sitename;?>"   /></a></p>
    </div>

    <?php if ($this->countModules('search')) { ?>
    <div id="search" class="col-sm-4">
        <jdoc:include type="modules" name="search" />
    </div>
    <?php } ?>

    <?php if ($this->countModules('cart')) { ?>
    <div id="ice-cart" class="col-sm-4">
        <jdoc:include type="modules" name="cart" />
    </div>
    <?php } ?>

    <?php if ($this->countModules('mainmenu')) { ?>
    <div id="mainmenu" class="navbar navbar-default" role="navigation">
        <jdoc:include type="modules" name="mainmenu" />
    </div>
    <?php } ?>  

    <jdoc:include type="modules" name="breadcrumbs" />

    <?php if ($this->countModules('banner')) { ?>
    <div id="banner">
        <jdoc:include type="modules" name="banner" />
    </div>
    <?php } ?>    

    <?php if ($this->countModules('contact')) { ?>
    <div id="contact">
        <jdoc:include type="modules" name="contact" />
    </div>
    <?php } ?>    

</div>

</div><!-- Header Wrapper -->  



<!-- Content -->
<div id="content">    

    <div class="share42init" data-top1="310" data-top2="40" data-margin="3"></div>
    <script type="text/javascript" src="http://www.best4hunting.com/share42/share42.js"></script>

    <div class="wrapper">


      <?php if ($this->countModules('iceslideshow + latest')) { ?>   
      <!-- Content Top -->
      <div id="content_top" class="clearfix">

        <?php if ($this->countModules('iceslideshow')) { ?>
        <div id="iceslideshow">
            <jdoc:include type="modules" name="iceslideshow" />
        </div>
        <?php } ?>                  

        <?php if ($this->countModules('latest')) { ?>
        <div id="latest">
            <jdoc:include type="modules" name="latest" style="block" />
        </div>
        <?php } ?>    

    </div><!-- Content Top -->
    <?php } ?> 



    <?php if ($this->countModules('promo1 + promo2 + promo3 + promo4')) { ?>
    <!-- Promo -->
    <div id="promo">

        <div class="modules-wrap clearfix">

            <?php if ($this->countModules('promo1')) { ?>
            <div class="<?php echo $promomodulewidth; ?> <?php echo $promomodsep1; ?> floatleft">
                <jdoc:include type="modules" name="promo1" style="block"  />
            </div>
            <?php } ?>
            <?php if ($this->countModules('promo2')) { ?>
            <div class="<?php echo $promomodulewidth; ?> <?php echo $promomodsep2; ?> floatleft">
                <jdoc:include type="modules" name="promo2" style="block"  />
            </div>
            <?php } ?>
            <?php if ($this->countModules('promo3')) { ?>
            <div class="<?php echo $promomodulewidth; ?> <?php echo $promomodsep3; ?> floatleft">
                <jdoc:include type="modules" name="promo3" style="block"  />
            </div>
            <?php } ?>
            <?php if ($this->countModules('promo4')) { ?>
            <div class="<?php echo $promomodulewidth; ?> floatleft">
                <jdoc:include type="modules" name="promo4" style="block"  />
            </div>
            <?php } ?>

        </div>     

    </div><!-- Promo -->  
    <?php } ?>  

    <!-- Content Main -->
    <div id="content_main" class="clearfix">



        <?php if ($this->countModules('left')) { ?>
        <!-- Left Column -->
        <div id="left-column">

            <div class="inside">

              <jdoc:include type="modules" name="left" style="colmodule"  />

          </div>    

      </div> <!-- Left Column -->
      <?php } ?>   

      <!-- Middle Column -->   
      <div id="middle-column">


        <div class="inside"> 

            <jdoc:include type="message" />

            <jdoc:include type="component" />

            <?php if ($this->countModules('virtuemart1 + virtuemart2 + virtuemart3 + virtuemart4')) { ?>
            <!-- virtuemart Modules -->
            <div id="virtuemart-mods" class="clearfix">

                <jdoc:include type="modules" name="virtuemart1" style="block"  />

                <jdoc:include type="modules" name="virtuemart2" style="block"  />

                <jdoc:include type="modules" name="virtuemart3" style="block"  />

                <jdoc:include type="modules" name="virtuemart4" style="block"  />                        

            </div><!-- virtuemart Modules -->
            <?php } ?>  

        </div>  

    </div><!-- Middle Column -->


    <?php if ($this->countModules('right')) { ?>
    <!-- Right Column -->
    <div id="right-column">

        <div class="inside">

            <jdoc:include type="modules" name="right" style="colmodule"  />

        </div>

    </div><!-- Right Column -->
    <?php } ?>


</div><!-- Content Main -->


<?php if ($this->countModules('bottom1 + bottom2 + bottom3 + bottom4 + icecarousel')) { ?>
<!-- Bottom -->
<div id="bottom" class="clearfix">

    <?php if ($this->countModules('icecarousel')) { ?>
    <div id="icecarousel">
        <jdoc:include type="modules" name="icecarousel" style="icemodule"  />
    </div>
    <?php } ?>

    <?php if ($this->countModules('bottom1 + bottom2 + bottom3 + bottom4')) { ?>
    <div class="modules-wrap clearfix">

      <?php if ($this->countModules('bottom1')) { ?>
      <div class="<?php echo $botmodwidth; ?> <?php echo $botmodsep1; ?> floatleft">
        <jdoc:include type="modules" name="bottom1" style="block"  />
    </div>
    <?php } ?>
    <?php if ($this->countModules('bottom2')) { ?>
    <div class="<?php echo $botmodwidth; ?> <?php echo $botmodsep2; ?> floatleft">
        <jdoc:include type="modules" name="bottom2" style="block"  />
    </div>
    <?php } ?>
    <?php if ($this->countModules('bottom3')) { ?>
    <div class="<?php echo $botmodwidth; ?> <?php echo $botmodsep3; ?> floatleft">
        <jdoc:include type="modules" name="bottom3" style="block"  />
    </div>
    <?php } ?>
    <?php if ($this->countModules('bottom4')) { ?>
    <div class="<?php echo $botmodwidth; ?> floatleft">
        <jdoc:include type="modules" name="bottom4" style="block"  />
    </div>
    <?php } ?>

</div>  
<?php } ?>


</div><!-- Bottom -->  
<?php } ?>


</div>

</div><!-- Content -->  


<!-- Footer -->
<div id="footer">

    <div class="wrapper">

     <?php if ($this->countModules('footer1 + footer2 + footer3 + footer4 + footer5')) { ?>
     <div class="modules-wrap clearfix">           

        <?php if ($this->countModules('footer1')) { ?>
        <div class="<?php echo $footermodulewidth; ?> <?php echo $footermodsep1; ?> floatleft">
            <jdoc:include type="modules" name="footer1" style="block"  />
        </div>
        <?php } ?>
        <?php if ($this->countModules('footer2')) { ?>
        <div class="<?php echo $footermodulewidth; ?> <?php echo $footermodsep2; ?> floatleft">
            <jdoc:include type="modules" name="footer2" style="block"  />
        </div>
        <?php } ?>
        <?php if ($this->countModules('footer3')) { ?>
        <div class="<?php echo $footermodulewidth; ?> <?php echo $footermodsep3; ?> floatleft">
            <jdoc:include type="modules" name="footer3" style="block"  />
        </div>
        <?php } ?>  
        <?php if ($this->countModules('footer4')) { ?>
        <div class="<?php echo $footermodulewidth; ?>  <?php echo $footermodsep4; ?> floatleft">
            <jdoc:include type="modules" name="footer4" style="block"  />
        </div>
        <?php } ?>  

        <?php if ($this->countModules('footer5')) { ?>
        <div class="<?php echo $footermodulewidth; ?> floatleft">
            <jdoc:include type="modules" name="footer5" style="block"  />
        </div>
        <?php } ?>  

    </div>  
    <?php } ?>  

    <!-- Copyright -->
    <div id="copyright" class="clearfix">   

        <?php if($this->params->get('icelogo')) { ?>
        <div id="icelogo">
            <p><a href="http://www.icetheme.com"><span><?php echo JText::_("ICETHEMECOPY");?></span></a></p>
        </div>
        <?php } ?>

        <?php if ($this->countModules('copyright')) { ?>
        <div id="copyrightmenu">
            <jdoc:include type="modules" name="copyright" />
        </div>
        <?php } ?>

        <?php if ($this->countModules('footer')) { ?>
        <div id="copytext">
            <jdoc:include type="modules" name="footer" />
        </div>
        <?php } ?>

        <?php if($this->params->get('go2top')) { ?>
        <script type="text/javascript">
            window.addEvent('domready',function() { new SmoothScroll({ duration: 800 }); })
        </script>
        <a id="go2top" href="#header" title="<?php echo JText::_("GOTOP");?>" ><span><?php echo JText::_("GOTOP");?></span></a>
        <?php } ?>

    </div><!-- Copyright -->


</div>

</div>
<!-- Footer -->  



<!-- javascript code to make J! tooltips -->
<script type="text/javascript">
    window.addEvent('domready', function() {
      $$('.hasTip').each(function(el) {
        var title = el.get('title');
        if (title) {
          var parts = title.split('::', 2);
          el.store('tip:title', parts[0]);
          el.store('tip:text', parts[1]);
      }
  });
      var JTooltips = new Tips($$('.hasTip'), { fixed: false});
  });
</script>


<jdoc:include type="modules" name="debug" />

</body>
</html>

<?php } ?>