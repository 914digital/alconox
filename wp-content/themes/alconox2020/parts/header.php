<!DOCTYPE html>
<html>
<head>
	<title><?php wp_title(''); ?></title>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <link rel="shortcut icon" href="<?php bloginfo('template_directory') ?>/img/favicon.png" />
   <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC6C8bqnIj1Roz4vFfMZ3QPgo0BWej3hVY&callback=initMap"
  type="text/javascript"></script>
  <style type="text/css">iframe.goog-te-banner-frame{ display: none !important;}</style>
<style type="text/css">body {position: static !important; top:0px !important;}</style>
  <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<!-- Search overlay -->
<div id="loginScreen" class="overlay">

  <!-- Button to close the Seach overlay  -->
  <a href="javascript:void(0)" class="closebtn" onclick="closeLogin()">&times;</a>

  <!-- Search overlay content -->
  <div class="overlay-content">
    <div class="container">
      <div class="row">
        <div class="col-md-6 offset-md-3">
          <?php dynamic_sidebar('login-widget-area'); ?>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Login Overlay -->
<div id="searchNav" class="overlay">

  <!-- Button to close the Login overlay  -->
  <a href="javascript:void(0)" class="closebtn" onclick="closeSearch()">&times;</a>

  <!-- Search overlay content -->
  <div class="overlay-content">
    <div class="container">
      <div class="row">
        <div class="col-md-6 offset-md-3">
          <div class="s-title">Search...</div>
          <?php echo do_shortcode( '[wd_asp id=9]' ); ?>
        </div>
      </div>
    </div>
  </div>
</div>

  <div class="top-nav">
    <div class="container">
      <div class="top-nav-btns justify-content-end">
        <div class="dropdown">
         <!-- GTranslate: https://gtranslate.io/ -->

<div class="switcher notranslate">
  <div class="selected">
  <a href="#" onclick="return false;"><img src="//www.alconox.com/wp-content/plugins/gtranslate/flags/24/en-us.png" height="24" width="24" alt="en" /> English</a>
  </div>
  <div class="option">
    <a href="#" onclick="doGTranslate('en|zh-CN');jQuery('div.switcher div.selected a').html(jQuery(this).html());return false;" title="Chinese (Simplified)" class="nturl"><img data-gt-lazy-src="//www.alconox.com/wp-content/plugins/gtranslate/flags/24/zh-CN.png" height="24" width="24" alt="zh-CN" /> 汉字</a><a href="#" onclick="doGTranslate('en|zh-TW');jQuery('div.switcher div.selected a').html(jQuery(this).html());return false;" title="Chinese (Traditional)" class="nturl"><img data-gt-lazy-src="//www.alconox.com/wp-content/plugins/gtranslate/flags/24/zh-TW.png" height="24" width="24" alt="zh-TW" /> 漢字</a><a href="#" onclick="doGTranslate('en|en');jQuery('div.switcher div.selected a').html(jQuery(this).html());return false;" title="English" class="nturl selected"><img data-gt-lazy-src="//www.alconox.com/wp-content/plugins/gtranslate/flags/24/en-us.png" height="24" width="24" alt="en" /> English</a><a href="#" onclick="doGTranslate('en|fr');jQuery('div.switcher div.selected a').html(jQuery(this).html());return false;" title="French" class="nturl"><img data-gt-lazy-src="//alconox.com/wp-content/plugins/gtranslate/flags/24/fr.png" height="24" width="24" alt="fr" /> Français</a><a href="#" onclick="doGTranslate('en|de');jQuery('div.switcher div.selected a').html(jQuery(this).html());return false;" title="German" class="nturl"><img data-gt-lazy-src="//www.alconox.com/wp-content/plugins/gtranslate/flags/24/de.png" height="24" width="24" alt="de" /> Deutsche</a><a href="#" onclick="doGTranslate('en|es');jQuery('div.switcher div.selected a').html(jQuery(this).html());return false;" title="Spanish" class="nturl"><img data-gt-lazy-src="//www.alconox.com/wp-content/plugins/gtranslate/flags/24/es.png" height="24" width="24" alt="es" /> Español</a>
  </div>
</div>
<script>
jQuery('.switcher .selected').click(function() {jQuery('.switcher .option a img').each(function() {if(!jQuery(this)[0].hasAttribute('src'))jQuery(this).attr('src', jQuery(this).attr('data-gt-lazy-src'))});if(!(jQuery('.switcher .option').is(':visible'))) {jQuery('.switcher .option').stop(true,true).delay(100).slideDown(500);jQuery('.switcher .selected a').toggleClass('open')}});
jQuery('.switcher .option').bind('mousewheel', function(e) {var options = jQuery('.switcher .option');if(options.is(':visible'))options.scrollTop(options.scrollTop() - e.originalEvent.wheelDelta);return false;});
jQuery('body').not('.switcher').click(function(e) {if(jQuery('.switcher .option').is(':visible') && e.target != jQuery('.switcher .option').get(0)) {jQuery('.switcher .option').stop(true,true).delay(100).slideUp(500);jQuery('.switcher .selected a').toggleClass('open')}});
</script>
<style>
#goog-gt-tt {display:none !important;}
.goog-te-banner-frame {display:none !important;}
.goog-te-menu-value:hover {text-decoration:none !important;}
.goog-text-highlight {background-color:transparent !important;box-shadow:none !important;}
body {top:0 !important;}
#google_translate_element2 {display:none!important;}
</style>

<div id="google_translate_element2"></div>
<script>
function googleTranslateElementInit2() {new google.translate.TranslateElement({pageLanguage: 'en',autoDisplay: false}, 'google_translate_element2');}
</script><script src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit2"></script>

<script>
function GTranslateGetCurrentLang() {var keyValue = document['cookie'].match('(^|;) ?googtrans=([^;]*)(;|$)');return keyValue ? keyValue[2].split('/')[2] : null;}
function GTranslateFireEvent(element,event){try{if(document.createEventObject){var evt=document.createEventObject();element.fireEvent('on'+event,evt)}else{var evt=document.createEvent('HTMLEvents');evt.initEvent(event,true,true);element.dispatchEvent(evt)}}catch(e){}}
function doGTranslate(lang_pair){if(lang_pair.value)lang_pair=lang_pair.value;if(lang_pair=='')return;var lang=lang_pair.split('|')[1];if(GTranslateGetCurrentLang() == null && lang == lang_pair.split('|')[0])return;var teCombo;var sel=document.getElementsByTagName('select');for(var i=0;i<sel.length;i++)if(sel[i].className.indexOf('goog-te-combo')!=-1){teCombo=sel[i];break;}if(document.getElementById('google_translate_element2')==null||document.getElementById('google_translate_element2').innerHTML.length==0||teCombo.length==0||teCombo.innerHTML.length==0){setTimeout(function(){doGTranslate(lang_pair)},500)}else{teCombo.value=lang;GTranslateFireEvent(teCombo,'change');GTranslateFireEvent(teCombo,'change')}}
if(GTranslateGetCurrentLang() != null)jQuery(document).ready(function() {var lang_html = jQuery('div.switcher div.option').find('img[alt="'+GTranslateGetCurrentLang()+'"]').parent().html();if(typeof lang_html != 'undefined')jQuery('div.switcher div.selected a').html(lang_html.replace('data-gt-lazy-', ''));});
</script>
        </div>
        <ul class="nav">
          <li class="nav-item desktop" data-toggle="tooltip" data-placement="bottom" title="Search">
            <span class="nav-link" onclick="openSearch()"><i class="fas fa-search"></i></a>
          </li>
          <li>
            <div class="dropdown d-inline-block login ml-1">
            <?php if(is_user_logged_in()) { ?>
            <button class="btn dropdown-toggle" role="button" id="dropdownMenuButtonAdmin" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><i class="fas fa-user mr-2"></i> <?php 
              $user_info = get_userdata(get_current_user_id());
              $first_name = $user_info->first_name;
              $last_name = $user_info->last_name;
              echo "$first_name $last_name";
            ?>
            </button>
              <div class="dropdown-menu" aria-labelledby="dropdownMenuButtonAdmin">
              <?php dynamic_sidebar('login-widget-area'); ?>
              </div>
            </div>
            <?php } else { ?>
              <span class="nav-link" onclick="openLogin()">
                <i class="fas fa-user mr-2"></i> Login
              </a>
            <?php } ?> 
          </li>
          <li>
            <a href="/products" class="store-btn btn"><i class="fas fa-store"></i> Shop</a>
          </li>
          <?php echo do_shortcode("[woo_cart_but]"); ?>
        </ul>
      </div>
    </div>
  </div>
  <nav class="navbar navbar-expand-lg fixed">
			<div class="container">
				<a class="navbar-brand" href="<?php echo site_url(); ?>">
          <img src="<?php bloginfo('template_directory') ?>/img/alconoxinc-logo-cl.svg" alt="Alconox Logo">
				</a>
				<button class="navbar-toggler collapsed" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
					<span class="icon-bar top-bar"></span>
					<span class="icon-bar middle-bar"></span>
					<span class="icon-bar bottom-bar"></span>
				</button>
				<div class="collapse navbar-collapse justify-content-end" id="navbarNavDropdown">
        <?php				
            $args = array(
              'theme_location' => 'upper-bar',
              'depth' => 0,
              'container'	=> false,
              'fallback_cb' => false,
              'menu_class' => 'nav navbar-nav',
              'walker' => new WP_Bootstrap_Navwalker()
            );
            wp_nav_menu($args);
        ?>
        
      </div><!-- /.navbar-collapse -->
    </div>
  </nav>