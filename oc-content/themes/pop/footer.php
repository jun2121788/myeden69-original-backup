
</div><!-- main -->
</div>



<div class="clear"></div>
<div id="footer">
    <div class="footer-wrapper">
        <?php
        osc_reset_static_pages();
        while (osc_has_static_pages()) {
            ?>
            <a class="btn-round" href="<?php echo osc_static_page_url(); ?>"><?php echo osc_static_page_title(); ?></a>
            <?php
        }
        ?>
        <a class="btn-round" href="<?php echo osc_contact_url(); ?>"><?php _e('Contact', 'pop'); ?></a>
    </div>

    <div class="wrapper" style="width: auto;">
        <div style="clear:both;margin-bottom: 1em;"></div>
        <div style="text-align: center; padding-top:15px; font-size:14px;">
            <p style="text-align: center;">
                <?php
        if( osc_get_preference('footer_link', 'pop_theme') !== '0') {
            echo '<div>' . sprintf(__('This website is proudly using the <a title="Osclass web" href="%s">classifieds scripts</a> software <strong>Osclass</strong>'), 'http://osclass.org/') . '</div>';
        }
        ?>
            </p></div>
        <div style="clear:both;margin-bottom: 1em;"></div>
    </div>
    <div style="clear:both;margin-bottom: 1em;"></div>
</div><!-- footer -->

<?php osc_run_hook('footer'); ?>

<script>
    // fix bootstrap dropdown
    $('a.dropdown-toggle, .dropdown-menu a').on('touchstart', function(e) {
        e.stopPropagation();
      });

<?php if( osc_is_search_page() || osc_is_home_page() ) { ?>
    var container = $('.masonry').masonry(
            {"isFitWidth": true, "columnWidth": 220, "itemSelector": ".item", "gutter": 20, "stamp": ".stamp"}
    );
<?php } else { ?>
    var container = $('.masonry').masonry(
            {"isFitWidth": true, "columnWidth": 220, "itemSelector": ".item", "gutter": 20}
    );
<?php } ?>
    // layout Masonry again after all images have loaded
    container.imagesLoaded(function () {
        container.masonry();
    });

    (function (window, document) {
        var menu = document.getElementById('menu'),
                WINDOW_CHANGE_EVENT = ('onorientationchange' in window) ? 'orientationchange' : 'resize';

        function toggleHorizontal() {
            [].forEach.call(
                    document.getElementById('menu').querySelectorAll('.custom-can-transform'),
                    function (el) {
                        el.classList.toggle('pure-menu-horizontal');
                    }
            );
        }
        ;

        function toggleMenu() {
            // set timeout so that the panel has a chance to roll up
            // before the menu switches states
            if (menu.classList.contains('open')) {
                setTimeout(toggleHorizontal, 500);
            }
            else {
                toggleHorizontal();
            }
            menu.classList.toggle('open');
            document.getElementById('toggle').classList.toggle('x');
        }
        ;

        function closeMenu() {
            if (menu.classList.contains('open')) {
                toggleMenu();
            }
        }

        document.getElementById('toggle').addEventListener('click', function (e) {
            toggleMenu();
        });

        window.addEventListener(WINDOW_CHANGE_EVENT, closeMenu);
    })(this, this.document);

</script>

<script type="text/javascript">
    var sUrl = '<?php echo osc_update_search_url(array('sPattern' => ' ')); ?>';
    var sUrl = '<?php echo osc_search_url(); ?>';
    var sQuery = '-';
    function doSearch(_class) {
       if( $('form.' + _class + ' input[name=sPattern]').val().length < 3 ) {
            $('form.' + _class + ' input[name=sPattern]').css('background', '#FFC6C6');
            return false;
        } else {
            <?php if(osc_rewrite_enabled()) { ?>
            var new_pattern_permalinks = "/pattern," + encodeURIComponent($('form.' + _class + ' input[name=sPattern]').val());
            go = sUrl + new_pattern_permalinks;
            <?php } else { ?>
            var new_pattern = "&sPattern=" + encodeURIComponent($('form.' + _class + ' input[name=sPattern]').val());
            go = sUrl + new_pattern ;
            <?php } ?>
            window.location.href = go;
        }
        return false;
    }
    $('.pop-ico-search').click(function(){
        $(this).parent('form').submit();
    });
</script>
</body>
</html>
