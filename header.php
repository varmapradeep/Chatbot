<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="robots" content="noindex, nofollow">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" href="assets/images/bot_avatar.png" type="image/x-icon">

    <link href="//fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;1,400;1,600;1,700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="assets/css/style-starter.css">
    <link rel="stylesheet" href="assets/css/style.css">

    <script src="assets/js/jquery-3.5.1.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
</head>

<body>
    <header id="site-header" class="fixed-top">
        <div class="container">
            <nav class="navbar navbar-expand-lg stroke px-0">
                <h1>
                    <a class="navbar-brand" href="home.php">
                        <img src='assets/images/bot_avatar.png'>
                    </a>
                </h1>
                <button class="navbar-toggler  collapsed bg-gradient" type="button" data-toggle="collapse" data-target="#navbarToggler" aria-controls="navbarToggler" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon fa icon-expand fa-bars"></span>
                    <span class="navbar-toggler-icon fa icon-close fa-times"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarToggler">
                    <ul class="navbar-nav mx-lg-auto">

                        <?php

                        $pageuri = urlencode($_SERVER['REQUEST_URI']);

                        if (isset($_SESSION['user'])) {
                            $session_user_name = $_SESSION['user']['name'];
                            if ($_SESSION['user']['group'] === 'ADMIN') {
                                $pages = [
                                    //"Home" => "admin_home.php",
                                    "Bot" => "home.php",
                                    "Articles" => ["admin_list_articles.php", "admin_create_article.php", "admin_update_article.php", "admin_import_articles.php"],
                                    "Users" => ["admin_list_users.php", "register.php", "admin_update_user.php"],
                                    "Resources" => "admin_resources.php",
                                    "Server" => "admin_server_info.php",
                                    //"Change Password" => "change_password.php?returnuri=$pageuri", //Admin can change their password from Users page
                                    "Logout [$session_user_name]" => "index.php?logout"
                                ];
                            } else {
                                $pages = [
                                    "Home" => "home.php",
                                    "Change Password" => "change_password.php?returnuri=$pageuri",
                                    "Logout [$session_user_name]" => "index.php?logout"
                                ];
                            }
                        } else {
                            $pages = [
                                "Login" => "index.php",
                                "Register" => "register.php"
                            ];
                        }

                        $currentfile = basename($_SERVER['PHP_SELF']);

                        foreach ($pages as $page => $urls) {
                            if (is_array($urls)) {
                                $url = $urls[0];
                                $active = in_array($currentfile, $urls) ? ' active' : '';
                            } else {
                                $url = $urls;
                                $active = substr($url, 0, strlen($currentfile)) === $currentfile ? ' active' : '';
                            }

                            echo '<li class="nav-item' . $active . '"> <a class="nav-link' . $active . '" href="' . $url . '">' . $page . '</a></li>';
                        }

                        ?>

                    </ul>
                </div>

                <!-- toggle switch for light and dark theme -->
                <div class="cont-ser-position">
                    <nav class="navigation">
                        <div class="theme-switch-wrapper">
                            <label class="theme-switch" for="themeswitch">
                                <input type="checkbox" id="themeswitch">
                                <div class="mode-container">
                                    <i class="gg-sun"></i>
                                    <i class="gg-moon"></i>
                                </div>
                            </label>
                        </div>
                    </nav>
                </div>

                <script>
                    const toggleSwitch = document.querySelector('#themeswitch');
                    const localTheme = localStorage.getItem('theme');

                    var currentTheme = localTheme ? localTheme : '<?= isset($conf['DefaultTheme']) ? $conf['DefaultTheme'] : 'light' ?>';
                    document.documentElement.setAttribute('data-theme', currentTheme);
                    toggleSwitch.checked = currentTheme === 'dark';

                    function switchTheme(e) {
                        if (e.target.checked) {
                            document.documentElement.setAttribute('data-theme', 'dark');
                            localStorage.setItem('theme', 'dark');
                        } else {
                            document.documentElement.setAttribute('data-theme', 'light');
                            localStorage.setItem('theme', 'light');
                        }
                    }

                    toggleSwitch.addEventListener('change', switchTheme, false);
                </script>

                <!-- //toggle switch for light and dark theme -->
            </nav>
        </div>
    </header>

    <!-- MENU-JS -->
    <script>
        $(window).on("scroll", function() {
            var scroll = $(window).scrollTop();

            if (scroll >= 80) {
                $("#site-header").addClass("nav-fixed");
            } else {
                $("#site-header").removeClass("nav-fixed");
            }
        });

        //Main navigation Active Class Add Remove
        $(".navbar-toggler").on("click", function() {
            $("header").toggleClass("active");
        });
        $(document).on("ready", function() {
            if ($(window).width() > 991) {
                $("header").removeClass("active");
            }
            $(window).on("resize", function() {
                if ($(window).width() > 991) {
                    $("header").removeClass("active");
                }
            });
        });
    </script>
    <!-- //MENU-JS -->
    <!-- disable body scroll which navbar is in active -->
    <script>
        $(function() {
            $('.navbar-toggler').click(function() {
                $('body').toggleClass('noscroll');
            })
        });
    </script>
    <!-- //disable body scroll which navbar is in active -->

    <script>
        // When the user scrolls down 20px from the top of the document, show the button
        window.onscroll = function() {
            scrollFunction();
        };

        function scrollFunction() {
            if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
                document.getElementById("movetop").style.display = "block";
            } else {
                document.getElementById("movetop").style.display = "none";
            }
        }

        // When the user clicks on the button, scroll to the top of the document
        function topFunction() {
            document.body.scrollTop = 0;
            document.documentElement.scrollTop = 0;
        }
    </script>
    <!-- /move top -->

    <script>
        function formatTime(date) {
            var now = date === undefined ? new Date() : new Date(date);
            var hh = now.getHours();
            var min = now.getMinutes();
            var ampm = (hh >= 12) ? 'PM' : 'AM';
            hh = hh % 12;
            hh = hh ? hh : 12;
            hh = hh < 10 ? '0' + hh : hh;
            min = min < 10 ? '0' + min : min;
            var time = hh + ":" + min + " " + ampm;
            return time;
        }

        function localTime(dateTime) {
            var offset = new Date().getTimezoneOffset();
            var dateClientSide = new Date(dateTime + offset);
            return dateClientSide;
        }
    </script>

    <div style="background-color: black; padding-bottom: 70px; padding-top: 20px;"></div>
</body>

</html>