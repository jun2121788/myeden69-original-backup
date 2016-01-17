<?php
    // meta tag robots
    osc_add_hook('header','pop_nofollow_construct');

    pop_add_body_class('user user-items');

    osc_current_web_theme_path('header.php') ;

?>

<?php osc_current_web_theme_path('user-sidebar.php'); ?>
<div class="user-content box">
    <div class="header">
        <h1><?php _e('My listings', 'pop'); ?></h1>
    </div>
    <?php if(osc_count_items() == 0) { ?>
        <p class="empty" ><?php _e('No listings have been added yet', 'pop'); ?></p>
    <?php } else { ?>

    <?php
        View::newInstance()->_exportVariableToView("listAdmin", true);
        osc_current_web_theme_path('loop.php');
    ?>
    <div class="clear"></div> 
<?php } ?>
</div>
<?php osc_current_web_theme_path('footer.php') ; ?>
