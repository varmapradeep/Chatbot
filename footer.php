<script>
    $(document).ready(function() {
        fixFooter(true);

        $(window).resize(function() {
            fixFooter(true);
        });

        $(window).scroll(function() {
            fixFooter(false);
        });
    });

    function fixFooter(setPlaceholder = false) {
        if (setPlaceholder) {
            if ($(document).height() >= $(window).height()) {
                $('#footer-place-holder').outerHeight($('#footer').outerHeight(true));
                $('#footer').fadeOut(4000);
            }
        }

        if ($(window).scrollTop() == $(document).height() - $(window).height()) {
            $('#footer').fadeIn(500)
        } else {
            $('#footer').fadeOut(500);
        }
    }
</script>
<!-- /Footer always visible at the end of the page -->

<div id="footer-place-holder"></div>
<section class="w3l-copyright fixed-bottom nav-fixed" id="footer" style="background-color:black; padding: 20px 10px 10px 10px;">
    <div class="container">
        <div class="row bottom-copies">
            <div class="col-lg-3 footer-logo text-center mb-0">
                <a href="home.php">
                    <img class="footer-logo" src='assets/images/bot_avatar.png'>
                </a>
            </div>

            <p class="col-lg-4 text-center" style="line-height:45px;"> © <?= date("Y") ?> Chatbot. All rights reserved.</p>

            <div class="col-lg-4 footer-list-29 text-right">
                <div class="main-social-footer-29">
                    <a href="mailto:chatbotvp@gmail.com" class="mail"><span class="fa fa-envelope"></span></a>
                    <a href="tel:+919061497342" class="phone"><span class="fa fa-phone"></span></a>
                    <span class="fa fa-star"><span class="fa fa-circle-thin"></span><span class="fa fa-circle"></span><span class="fa fa-circle-thin"></span><span class="fa fa-star">&nbsp;
                            <a href="https://www.facebook.com/" target="_blank" class="facebook"><span class="fa fa-facebook"></span></a>
                            <a href="https://www.twitter.com/" target="_blank" class="twitter"><span class="fa fa-twitter"></span></a>
                            <a href="https://www.instagram.com/" target="_blank" class="instagram"><span class="fa fa-instagram"></span></a>
                            <a href="https://uk.linkedin.com/in/shalvin" target="_blank" class="linkedin"><span class="fa fa-linkedin"></span></a>

                </div>
            </div>
        </div>
    </div>

    <!-- move top -->
    <button onclick="topFunction()" id="movetop" title="Go to top">
        &#10548;
    </button>
</section