<footer>
    <div class="top-footer">
        <div class="container">
            <div class="row">
                <div class="col-md-2 col-6">
                    <div class="ft-main-menu">
                        <h4><a href="/about">About Us</a></h4>
                        <?php wp_nav_menu( array( 'theme_location' => 'footer-main' ) ); ?>
                    </div>
                </div>
                <div class="col-md-2 col-6">
                    <div class="ft-ind-menu">
                        <h4><a href="/industries">Industries</a></h4>
                        <?php wp_nav_menu( array( 'theme_location' => 'footer-ind' ) ); ?>
                    </div>
                </div>
                <div class="col-md-2 col-6">
                    <div class="ft-ind-menu">
                        <h4><a href="/products">Products</a></h4>
                        <?php wp_nav_menu( array( 'theme_location' => 'footer-prod' ) ); ?>
                    </div>
                </div>
                <div class="col-md-2 col-6">
                    <div class="ft-ind-menu">
                        <h4><a href="/resources">Resources</a></h4>
                        <?php wp_nav_menu( array( 'theme_location' => 'res-footer' ) ); ?>
                    </div>
                </div>
                <div class="col-md-4">
                        <div class="footer-logo">
                            <img src="<?php bloginfo('template_directory') ?>/img/alconoxinc-logo-wh.png">
        
                        <div class="ft-address">
                            30 Glenn St.<br>
                            Suite #309<br>
                            White Plains, NY 10603
                        </div>
                        <div class="ft-phone mt-3">
                            <a href="tel:9149484040"><i class="fas fa-mobile-alt"></i> (914) 948-4040</a>
                        </div>
                        <div class="ft-email">
                            <a href="mailto:cleaning@alconox.com"><i class="fas fa-envelope"></i> cleaning@alconox.com</a>
                            <a href="/newsletter"><i class="fas fa-envelope-open-text"></i> Subscribe To Our Newsletter</a>
                        </div>
                        <div class="soc-footer">
                            <a traget="_blank" href="https://www.facebook.com/alconox"><i class="fab fa-facebook-f"></i></a>
                            <a traget="_blank" href="https://twitter.com/alconox"><i class="fab fa-twitter"></i></a>
                            <a traget="_blank" href="https://www.linkedin.com/company/alconox-inc-"><i class="fab fa-linkedin-in"></i></a>
                            <a traget="_blank" href="https://www.youtube.com/channel/UCC_boUUvVzpIUEwcwKOPecQ"><i class="fab fa-youtube"></i></a>
                        </div>
                        <div class="app-store">
                            <a target="_blank" href="https://play.google.com/store/apps/details?id=com.metisentry.alconox"><img src="<?php bloginfo('template_directory') ?>/img/icons/android-app.png"></a>
                            <a target="_blank" href="https://itunes.apple.com/us/app/alconox-inc/id1097101628?mt=8&ign-mpt=uo%3D4"><img src="<?php bloginfo('template_directory') ?>/img/icons/apple-app-store.png"></a>
                        </div>
                        <div class="iso-footer">
                            <a class="btn iso-btn" target="_blank" href="https://technotes.alconox.com/industry/qualityregulatory/alconox-inc-iso90012015-and-iso134852016-certified/"><i class="fas fa-check-circle"></i> ISO 9001:2015<br>ISO 13485:2016<br>CERTIFIED</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="bot-footer">
        <a href="#0" class="scrollup"></a>
      <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="copy">Copyright &copy; <?php echo date('Y'); ?> Alconox Inc. All Rights Reserved | <a href="/terms-of-service">Terms of Service</a> | <a href="/privacy-policy">Privacy Policy</a></div>
            </div>
            <div class="col-md-4">
                <div class="credit"><span class="site-by">site by</span> <a target="_blank" href="https://914digital.com"><img alt="914Digital" src="<?php bloginfo('template_directory') ?>/img/footer-logo-wh.png"></a>
                </div><!-- /credit -->
            </div>
        </div><!--row-->
      </div><!--container-->  
    </div>
</footer>

<div class="help-tab">
    <div class="ht-close float-right">X</div>
    <i class="fas fa-question-circle"></i> <p style="margin-top:25px;font-size:20px;">Not finding what you need?</p> <p>For further assistance please fill out the form at <a href="/ask-alconox?utm_source=site&utm_medium=popup&utm_campaign=not-finding-what-you-need">Ask Alconox</a> or email us at <a href="mailto:cleaning@alconox.com" onClick="ga(‘send’, ‘event’, ‘ Not Finding What You Need Popup’, ‘Click’, 'Email Link');">cleaning@alconox.com</a>.</p>   
</div>

<div class="cookie-policy" id="cookiePolicy" style="display:none;">
    <div class="container">
        <div class="row">
            <div class="col-md-9">
                <p>Please note that at Alconox Inc, we may use cookies to enhance your experience and improve site performance. To learn more about our cookies, how we use them and their benefits, <a href="/cookie-policy">please read our Cookie Policy.</a> </p>
            </div>
            <div class="col-md-3">
                <a class="btn btn-primary close-policy" href="#0">I agree</a>
            </div>
        </div>
    </div>
</div>


<?php wp_footer(); ?>


<!-- Carousel/Home Page Slider -->
<script>
    $('.carousel-indicators li:nth-child(1)').addClass('active');
    $('.carousel-inner .carousel-item:nth-child(1)').addClass('active');
</script>

<!-- Product Tabs -->
<script>
    $('.nav-item:nth-child(1) .prod-tab').addClass('active');
    $('.prod-content:nth-child(1)').addClass('show active');
    $('.nav-item:nth-child(1) .doc-tab').addClass('active');
    $('.doc-content:nth-child(1)').addClass('show active');
</script>


<!-- Search full screen -->
<script>
    /* Open when someone clicks on the span element */
    function openSearch() {
    document.getElementById("searchNav").style.width = "100%";
    }

    /* Close when someone clicks on the "x" symbol inside the overlay */
    function closeSearch() {
    document.getElementById("searchNav").style.width = "0%";
}
</script>

<!-- Login full screen -->
<script>
    /* Open when someone clicks on the span element */
    function openLogin() {
    document.getElementById("loginScreen").style.width = "100%";
    }

    /* Close when someone clicks on the "x" symbol inside the overlay */
    function closeLogin() {
    document.getElementById("loginScreen").style.width = "0%";
}
</script>

<!-- Animation Init -->
<script>
    AOS.init({
        once: true,
    });
</script>

<!-- paralax page banner -->
<script>
    $(window).on('scroll', function(e) {
        parallax();
        }).trigger('scroll');
    
        function parallax(){
        var scrolled = $(window).scrollTop();
        $('.page-single-header').css('transform', 'translate3d(0,' + scrolled * 0.52 + 'px,0)');
    }    
</script>

<!-- Simple Lightbox Init -->
<script>
    new SimpleLightbox({elements: '.prod-gallery a, .ba-img, .ab-gal'});
</script>

<script>
$("#accordionCV").on("hide.bs.collapse show.bs.collapse", e => {
  $(e.target)
    .prev()
    .find("i:last-child")
    .toggleClass("fa-angle-up fa-angle-down");
});

$("#accordionUM").on("hide.bs.collapse show.bs.collapse", e => {
  $(e.target)
    .prev()
    .find("i:last-child")
    .toggleClass("fa-angle-up fa-angle-down");
});
</script>

<script>
$(window).load(function(){
    $(".goog-logo-link").empty();
    $('.goog-te-gadget').html($('.goog-te-gadget').children());
})
</script>

<!-- Back To Top Button -->
<script>
    $(document).ready(function () {

    $(window).scroll(function () {
        if ($(this).scrollTop() > 100) {
            $('.scrollup').fadeIn();
        } else {
            $('.scrollup').fadeOut();
        }
    });

    $('.scrollup').click(function () {
        $("html, body").animate({
            scrollTop: 0
        }, 600);
        return false;
        });

    });
</script>


<!-- Help Tab Pop Out -->
<script>
    var num = 500;
    $(window).bind('scroll', function() {
    if($(window).scrollTop() > num) {

        $('.help-tab').addClass('show-tab animated slideInRight ');

    } else {

        $('.help-tab').removeClass('show-tab animated slideInRight ');
    }
});

$('.ht-close').click(function(e) {
  $('.help-tab').fadeOut(); 
});

</script>

<script>
if(localStorage.getItem('cookieSeen') != 'shown'){
    $(".cookie-policy").delay(2000).fadeIn();
    localStorage.setItem('cookieSeen','shown')
}

$('.close-policy').click(function(e) {
  $('.cookie-policy').fadeOut(); 
});
</script>

</body>
</html>
