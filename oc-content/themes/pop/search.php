<?php
// meta tag robots
if (osc_count_items() == 0 || stripos($_SERVER['REQUEST_URI'], 'search')) {
    osc_add_hook('header', 'pop_nofollow_construct');
} else {
    osc_add_hook('header', 'pop_follow_construct');
}
$listClass = 'item';
pop_add_body_class('search');
if (osc_count_latest_items() == 0) {
    pop_redirect_404( sprintf(__('There are no results matching "%s"', 'pop'), osc_search_pattern()) );
}
?>
<?php osc_current_web_theme_path('header.php'); ?>

<?php if (osc_count_latest_items() == 0) { ?>
    <div class="clear"></div>
    <p class="empty" ><?php printf(__('There are no results matching "%s"', 'pop'), osc_search_pattern()); ?></p>
<?php } else {
    View::newInstance()->_exportVariableToView("listType", 'latestItems');
    View::newInstance()->_exportVariableToView("listClass", $listClass);
    osc_current_web_theme_path('loop.php');
    }

    osc_current_web_theme_path('footer.php'); ?>