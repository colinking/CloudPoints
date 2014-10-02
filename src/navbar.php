<div class="navbar navbar-default" role="navigation">
    <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand">Math Honor Society<?php if (explode('.', $_SERVER['CURRENT_VERSION_ID'])[0] === 'dev') echo ' - DEV' ?></a>
    </div>
    <div class="navbar-collapse collapse">
        <ul class="nav navbar-nav navbar-right" style='font-size: 14px;'>
            <li <?php echo ($page_name === 'index' ? ' style="display:none;" ' : ' style="cursor: not-allowed;" '); ?>>
                <a id="yourname">
                    <?php if ($page_name !== 'index') echo $_SESSION['name']; ?>
                </a></li>
            <li class="
            <?php
            echo ($page_name === 'menu' || ($page_name === 'settings' && $_SESSION['access'] == 'teacher') ? 'active ' : '');
            ?>
                " <?php echo ($page_name === 'index' || ($_SESSION['access'] == 'student') ? ' style="display:none;" ' : ''); ?>><a 
                    <?php
                    if ($page_name !== 'index' && in_array($_SESSION['access'], ['officer', 'coder'])) {
                        echo ($page_name !== 'menu' ? 'href="menu" class="redirect"' : '');
                    } else if ($page_name !== 'index' && $_SESSION['access'] == 'teacher') {
                        echo ($page_name !== 'settings' ? 'href="settings" class="redirect"' : '');
                    }
                    ?>
                    >Home</a>
            </li>
            <li <?php echo ($page_name === 'index' ? 'style="display:none;"' : ''); ?>
                ><a class='redirect' onclick='logout()' style='cursor: pointer'>Log Out</a></li>
            <li><a href="https://towsonhsmath.pbworks.com/w/page/58324148/Math%20Club%20%20Math%20Honor%20Society" class='redirect'>Return to PBworks</a></li>
        </ul>
    </div>
</div>
<!--The following text is completely hidden, it is just used to tell jQuery if a @media query has fired. The maximum window size is stored in font-size (or 1000px if there is no max).
-->
<h5 id="mediaTester" style="display: none;"></h5>
<script>
    $(document).ready(function() {
        $('html').click(function(e) {
            //the last part says "and if not IE"
            if (e.target.className !== 'navbar-collapse in' && $('.navbar-collapse').hasClass('in') && !$(e.target).parents().hasClass('navbar-collapse') && !$(e.target).hasClass('redirect') && !/*@cc_on!@*/0) {
                $('.navbar-collapse').collapse('hide');
            }
        });
        $('.navbar-toggle').hover(function() {
            if (!isMobile()) {
                $(this).addClass('toggleHover');
            }
        }, function() {
            if (!isMobile()) {
                $(this).removeClass('toggleHover');
            }
        });
        if (parseInt($('#mediaTester').css('font-size')) < 500) {
            $('.snaptable').removeClass('table-bordered');
        }
        $(window).resize(function() {
            if (parseInt($('#mediaTester').css('font-size')) < 500) {
                $('.snaptable').removeClass('table-bordered');
            } else
                $('.snaptable').addClass('table-bordered');
        });
    });
    < script >
                (function(i, s, o, g, r, a, m) {
                    i['GoogleAnalyticsObject'] = r;
                    i[r] = i[r] || function() {
                        (i[r].q = i[r].q || []).push(arguments)
                    }, i[r].l = 1 * new Date();
                    a = s.createElement(o),
                            m = s.getElementsByTagName(o)[0];
                    a.async = 1;
                    a.src = g;
                    m.parentNode.insertBefore(a, m)
                })(window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');

        ga('create', 'UA-49085739-1', 'towsonmath.info');
        ga('send', 'pageview');

</script>

</script>