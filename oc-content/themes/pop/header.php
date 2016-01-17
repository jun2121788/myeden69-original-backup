<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="<?php echo str_replace('_', '-', osc_current_user_locale()); ?>">
    <head>
        <?php osc_current_web_theme_path('head.php'); ?>
    </head>
    <body <?php pop_body_class(); ?>>


        <div class="custom-wrapper pure-g" id="menu">
            <div class="pure-u-1 pure-u-md-1-3">
                <div class="pure-menu">
                    <?php echo logo_header(); ?>
                    <a href="#" class="custom-toggle" id="toggle"><s class="bar"></s><s class="bar"></s></a>
                </div>
            </div>
            <div class="pure-u-1 pure-u-md-1-3">
                <div class="pure-menu pure-menu-horizontal custom-menu-2 custom-can-transform ">
                    <ul class="pure-menu-list hidden-mobile">
                        <li class="pure-menu-item">
                            <form action="<?php echo osc_base_url(true); ?>" method="get" class="search search-header nocsrf" onsubmit="javascript:return doSearch('search-header');">
                                <input type="hidden" name="page" value="search"/>
                                <input type="text" name="sPattern" id="query" class="input-text" value="<?php echo osc_esc_html(Params::getParam('sPattern')); ?>" placeholder="<?php echo osc_esc_html(__(osc_get_preference('keyword_placeholder', 'pop_theme'), 'pop')); ?>" />

                                <i class="pop-ico-search"></i>
                                <div id="message-seach"></div>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="pure-u-1 pure-u-md-1-3">
                <div class="pure-menu pure-menu-horizontal custom-menu-3 custom-can-transform">
                    <ul class="pure-menu-list">
                        <?php if (osc_users_enabled() || (!osc_users_enabled() && !osc_reg_user_post() )) { ?>
                            <li class="pure-menu-item"><a href="<?php echo osc_item_post_url_in_category(); ?>" class="btn-round btn-gray"><?php _e('Publish', 'pop'); ?></a></li>
                        <?php } ?>
                        <?php if (osc_users_enabled()) { ?>
                            <?php if (osc_is_web_user_logged_in()) { ?>
                                <li class="pure-menu-item"><a href="<?php echo osc_user_dashboard_url(); ?>" class="btn-round btn-gray"><?php _e('My account', 'pop'); ?></a></li>
                                <li class="pure-menu-item hidden-desktop show-mobile"><a href="<?php echo osc_user_logout_url(); ?>" class="btn-round btn-gray"><?php _e('Logout', 'pop'); ?></a></li>
                                <li class="pure-menu-item hidden-mobile user-logo-header-menu">
                                    <div id="dLabel" class="logo-user-menu dropdown-toggle" style="background-image  : url(<?php echo osc_current_web_theme_url('images/User-Profile-32.png'); ?>);"
                                         data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></div>
                                    <ul class="dropdown-menu"  role="menu" aria-labelledby="dLabel">
                                        <li class="hidden-mobile"><a href="<?php echo osc_user_dashboard_url(); ?>"><?php _e('My account', 'pop'); ?></a></li>
                                        <li class="hidden-mobile"><a href="<?php echo osc_user_list_items_url(); ?>"><?php _e('My listings', 'pop'); ?></a></li>
                                        <li class="hidden-mobile"><a href="<?php echo osc_user_alerts_url(); ?>"><?php _e('My alerts', 'pop'); ?></a></li>
                                        <li class="hidden-mobile divider"></li>
                                        <li class="hidden-mobile"><a href="<?php echo osc_user_profile_url(); ?>"><?php _e('Settings', 'pop'); ?></a></li>
                                        <li class="hidden-mobile"><a href="<?php echo osc_user_logout_url(); ?>"><?php _e('Logout', 'pop'); ?></a></li>
                                    </ul>
                                </li>
                            <?php } else { ?>
                                <li class="pure-menu-item"><a href="<?php echo osc_user_login_url(); ?>" class="btn-round btn-gray"><?php _e('Login', 'pop'); ?></a></li>
                                <?php if (osc_user_registration_enabled()) { ?>
                                <li class="pure-menu-item"><a href="<?php echo osc_register_account_url(); ?>" class="btn-round btn-gray"><?php _e('Register', 'pop'); ?></a></li>
                                <?php } ?>
                            <?php } ?>
                        <?php } ?>


                    </ul>
                </div>
            </div>
        </div>


        <?php
        $breadcrumb = osc_breadcrumb('&raquo;', false, get_breadcrumb_lang());
        if ($breadcrumb !== '') {
            ?>
            <div class="breadcrumb">
                <?php echo $breadcrumb; ?>
                <div class="clear"></div>
            </div>
        <?php } ?>
        <?php if (!osc_is_public_profile()) { ?>
            <div class="wrapper ads_header"><!-- header ad 728x60-->
                <?php echo osc_get_preference('header-728x90', 'pop_theme'); ?>
            </div><!-- /header ad 728x60-->
        <?php } ?>
        <?php osc_run_hook('before-main'); ?>
        <div class="wrapper">

        <div class="search-responsive-wrapper">
                <form action="<?php echo osc_base_url(true); ?>" method="get" class="search search-responsive nocsrf" onsubmit="javascript:return doSearch('search-responsive');">
                    <input type="hidden" name="page" value="search"/>
                    <input type="text" name="sPattern" id="query" class="input-text" value="<?php echo osc_esc_html(Params::getParam('sPattern')); ?>" placeholder="<?php echo osc_esc_html(__(osc_get_preference('keyword_placeholder', 'pop_theme'), 'pop')); ?>" />

                    <i class="pop-ico-search"></i>
                    <div class="clear"></div>
                </form>
            </div>
            <?php osc_show_flash_message(); ?>

            <div id="main">
