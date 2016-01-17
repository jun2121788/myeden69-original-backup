<?php
    // meta tag robots
    osc_add_hook('header','pop_nofollow_construct');

    pop_add_body_class('user user-profile');
    osc_add_filter('meta_title_filter','custom_meta_title');
    function custom_meta_title($data){
        return __('Alerts', 'pop');
    }
    osc_current_web_theme_path('header.php') ;
    $osc_user = osc_user();
?>
<?php osc_current_web_theme_path('user-sidebar.php'); ?>
<div class="user-content box">
    <div class="header">
        <h1><?php _e('Alerts', 'pop'); ?></h1>
    </div>
<?php if(osc_count_alerts() == 0) { ?>
    <h3><?php _e('You do not have any alerts yet', 'pop'); ?>.</h3>
<?php } else { ?>
    <?php
    $i = 1;
    while(osc_has_alerts()) {

        $alert = osc_alert();
//        echo osc_alert_search();
        $_alert = array();

        $a_c = json_decode(osc_alert_search(), true);

        $search = new Search();
        $search->setJsonAlert($a_c);

        $_alert['dt_date'] = osc_alert_date();

        // region
//        $_r = Region::newInstance()->findByPrimaryKey($a_c['regionId']);
//        if(isset($_r['s_name'])) {
//            $_alert['region']= $_r['s_name'];
//        }

        $_alert['title'] = __('All listings', 'pop');
        if($a_c['sPattern']!='') {
            $_alert['title'] = ucfirst($a_c['sPattern']);
            $a_c['sPattern'] = $a_c['sPattern'];
        }

        $_alert['categories'] = __('All categories', 'pop');
        if(count($a_c['aCategories'])>0) {
            $cat_id = array_shift( $a_c['aCategories'] );
            $_c = Category::newInstance()->findByPrimaryKey($cat_id);
            if(isset($_c['s_name'])) {
                $_alert['categories'] = $_c['s_name'];
                $a_c['category'] = $_c['s_slug'];
            }
        }
        ?>
        <div class="userItem" >
            <div class="title-has-actions">
                <h3><?php _e('Alert', 'pop'); ?> <?php echo $i; ?></h3>
            </div>
            <div class="listing-detail">
                <div class="listing-cell">
                    <div class="listing-data">
                        <div class="listing-basicinfo">
                            <p><?php _e('Search pattern'); ?>: <?php echo $_alert['title']; ?></p>
                            <div class="listing-attributes">
                                <span class="category"><?php echo $_alert['categories']; ?></span> -
                                <?php /* <span class="location"> (<?php echo $_alert['region']; ?>)</span>  */ ?>
                                </span><?php _e('Created at'); ?> <?php echo date( 'd/m/Y', strtotime($_alert['dt_date'])); ?>
                            </div>
                            <div class="listing-attributes">
                                <strong><a onclick="javascript:return confirm('<?php echo osc_esc_js(__('This action can\'t be undone. Are you sure you want to continue?', 'pop')); ?>');" href="<?php echo osc_user_unsubscribe_alert_url(); ?>"><?php _e('Delete', 'pop'); ?></a></strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br />
    <?php
    $i++;
    }
    ?>
<?php  } ?>
</div>
<?php osc_current_web_theme_path('footer.php') ; ?>