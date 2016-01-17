<?php
    // meta tag robots
    osc_add_hook('header','pop_follow_construct');

    pop_add_body_class('home');

    $listClass   = '';
?>
<?php osc_current_web_theme_path('header.php') ; ?>

<?php
$mSearch = new Search();
$aItems      = $mSearch->doSearch();
$iTotalItems = $mSearch->count();
$iNumPages = ceil($iTotalItems / osc_default_results_per_page_at_search());

View::newInstance()->_exportVariableToView('search_total_pages', $iNumPages);
View::newInstance()->_exportVariableToView('items', $aItems);

if(osc_count_items()==0) { ?>
    <div class="clear"></div>
    <p class="empty"><?php _e("There aren't listings available at this moment", 'pop'); ?></p>
<?php } else { ?>
    <?php
    View::newInstance()->_exportVariableToView("listType", 'latestItems');
    View::newInstance()->_exportVariableToView("listClass",$listClass);
    osc_current_web_theme_path('loop.php');
    ?>


<?php } ?>

<?php osc_current_web_theme_path('footer.php') ; ?>