<?php
/*
 * Copyright 2015 Osclass
 *
 * You shall not distribute this plugin and any its files (except third-party libraries) to third parties.
 * Rental, leasing, sale and any other form of distribution are not allowed and are strictly forbidden.
 */


/*
Plugin Name: Osclass Payments Pro
Plugin URI: http://market.osclass.org/plugins/payments/osclass-payments-pro_203
Description: Complete and professional payments system for all your needs
Version: 1.1.6
Author: Osclass
Author URI: http://www.osclass.org/
Short Name: osclass_payment_pro
Plugin update URI: osclass_payment_pro
*/

    define('PAYMENT_PRO_PATH', PLUGINS_PATH . 'payment_pro/');
    define('PAYMENT_PRO_URL', WEB_PATH . 'oc-content/plugins/payment_pro/');
    define('PAYMENT_PRO_PLUGIN_FOLDER', 'payment_pro/');

    @include_once PAYMENT_PRO_PATH . 'config.php';
    if(!defined('PAYMENT_PRO_CRYPT_KEY')) {
        define('PAYMENT_PRO_CRYPT_KEY', 'randompasswordchangethis');
    }
    // PAYMENT STATUS
    define('PAYMENT_PRO_FAILED', 0);
    define('PAYMENT_PRO_COMPLETED', 1);
    define('PAYMENT_PRO_PENDING', 2);
    define('PAYMENT_PRO_ALREADY_PAID', 3);
    define('PAYMENT_PRO_WRONG_AMOUNT_TOTAL', 4);
    define('PAYMENT_PRO_WRONG_AMOUNT_ITEM', 5);
    define('PAYMENT_PRO_DISABLED', 6);
    define('PAYMENT_PRO_ENABLED', 7);

    // load necessary functions
    require_once PAYMENT_PRO_PATH . 'functions.php';
    require_once PAYMENT_PRO_PATH . 'ModelPaymentPro.php';
    require_once PAYMENT_PRO_PATH . 'payments/Payment.php';
    // Load different methods of payments
    $services = json_decode(osc_get_preference('services', 'payment_pro'), true);
    if(is_array($services)) {
        foreach ($services as $service => $file) {
            @include_once $file;
        }
        View::newInstance()->_exportVariableToView('_payment_pro_services', $services);
    }
    unset($services);


    function payment_pro_install() {
        ModelPaymentPro::newInstance()->install();
    }

    function payment_pro_uninstall() {
        ModelPaymentPro::newInstance()->uninstall();
    }

    function payment_pro_admin_menu() {
        osc_add_admin_submenu_divider('plugins', 'Osclass Payments Pro', 'payment_pro_divider', 'administrator');
        osc_add_admin_submenu_page('plugins', __('Payment options', 'payment_pro'), osc_route_admin_url('payment-pro-admin-conf'), 'payment_pro_settings', 'administrator');
        osc_add_admin_submenu_page('plugins', __('Categories fees', 'payment_pro'), osc_route_admin_url('payment-pro-admin-prices'), 'payment_pro_prices', 'administrator');
        osc_add_admin_submenu_page('plugins', __('History of payments', 'payment_pro'), osc_route_admin_url('payment-pro-admin-log'), 'payment_pro_log', 'administrator');
    }

    function payment_pro_publish($item, $do_edit = false) {
        $item = Item::newInstance()->findByPrimaryKey($item['pk_i_id']);
        if(!$do_edit) {
            ModelPaymentPro::newInstance()->createItem($item['pk_i_id'], 0, null, null, $item['b_enabled']);
        }
        $checkout = false;
        $category_fee = 0;
        $premium_fee = 0;
        // Need to pay to publish ?
        if(osc_get_preference('pay_per_post', 'payment_pro')==1) {
            $is_paid = false;
            if($do_edit) {
                $is_paid = ModelPaymentPro::newInstance()->publishFeeIsPaid($item['pk_i_id']);
            }
            if(!$is_paid) {
                $category_fee = ModelPaymentPro::newInstance()->getPublishPrice($item['fk_i_category_id']);
                if ($category_fee > 0) {
                    // Catch and re-set FlashMessages
                    osc_resend_flash_messages();
                    $mItems = new ItemActions(false);
                    $mItems->disable($item['pk_i_id']);
                    if($item['b_enabled']==1) {
                        ModelPaymentPro::newInstance()->enableItem($item['pk_i_id']);
                        payment_pro_cart_add(
                            'PUB' . $item['fk_i_category_id'] . '-' . $item['pk_i_id'],
                            sprintf(__('Publish fee for item %s', 'payment_pro'), $item['pk_i_id']),
                            $category_fee,
                            1
                        );
                        $checkout = true;
                    }
                } else {
                    // PRICE IS ZERO
                    if(!$do_edit) {
                        ModelPaymentPro::newInstance()->payPostItem($item['pk_i_id']);
                        ModelPaymentPro::newInstance()->createItem($item['pk_i_id'], 1, null, null, $item['b_enabled']);
                    }
                }
            }
        }
        if(osc_get_preference('allow_premium', 'payment_pro')==1) {
            $premium_fee = ModelPaymentPro::newInstance()->getPremiumPrice($item['fk_i_category_id']);
            if($premium_fee>0) {
                if(Params::getParam('payment_pro_make_premium')==1) {
                    if($item['b_enabled']==1) {
                        payment_pro_cart_add(
                            'PRM' . $item['fk_i_category_id'] . '-' . $item['pk_i_id'],
                            sprintf(__('Premium fee for item %s', 'payment_pro'), $item['pk_i_id']),
                            $premium_fee,
                            1
                        );
                        $checkout = true;
                    }
                }
            }
        }

        if($checkout && !OC_ADMIN) {
            ModelPaymentPro::newInstance()->addQueue(date('Y-m-d H:i:s', time()+1800), $item['pk_i_id'], $category_fee!=0, $premium_fee!=0);
            osc_redirect_to(osc_route_url('payment-pro-checkout', array('itemId' => $item['pk_i_id'])));
        }
    }

    function payment_pro_edited_item($item) {
        payment_pro_publish($item, true);
    }

    function payment_pro_user_menu() {
        echo '<li class="opt_payment" ><a href="'.osc_route_url('payment-pro-user-menu').'" >'.__("Listings payment status", 'payment_pro').'</a></li>' ;
        if((osc_get_preference('pack_price_1', 'payment_pro')!='' && osc_get_preference('pack_price_1', 'payment_pro')!='0')
            || (osc_get_preference('pack_price_2', 'payment_pro')!='' && osc_get_preference('pack_price_2', 'payment_pro')!='0')
            || (osc_get_preference('pack_price_3', 'payment_pro')!='' && osc_get_preference('pack_price_3', 'payment_pro')!='0')) {
                echo '<li class="opt_payment_pro_pack" ><a href="'.osc_route_url('payment-pro-user-pack').'" >'.__("Buy credit for payments", 'payment_pro').'</a></li>' ;
        }
    }

    function payment_pro_cron() {
        ModelPaymentPro::newInstance()->purgeExpired();
        ModelPaymentPro::newInstance()->purgePending();

        $date = date('Y-m-d H:i:s');
        $emails = ModelPaymentPro::newInstance()->getQueue($date);
        foreach($emails as $email) {
            payment_pro_send_email($email);
        }
        ModelPaymentPro::newInstance()->purgeQueue($date);
    }

    function payment_pro_premium_off($id) {
        ModelPaymentPro::newInstance()->premiumOff($id);
    }

    function payment_pro_before_edit($item) {
        if((osc_get_preference('pay_per_post', 'payment_pro') == '1' && ModelPaymentPro::newInstance()->publishFeeIsPaid($item['pk_i_id']))|| (osc_get_preference('allow_premium','payment') == '1' && ModelPaymentPro::newInstance()->premiumFeeIsPaid($item['pk_i_id']))) {
            $cat[0] = Category::newInstance()->findByPrimaryKey($item['fk_i_category_id']);
            View::newInstance()->_exportVariableToView('categories', $cat);
        }
    }

    function payment_pro_show_item($item) {
        if(osc_get_preference("pay_per_post", 'payment_pro')=="1" && !ModelPaymentPro::newInstance()->publishFeeIsPaid($item['pk_i_id']) ) {
            if( osc_is_admin_user_logged_in() ) {
                osc_get_flash_message('pubMessages', true);
                osc_add_flash_warning_message( __('The listing hasn\'t been paid', 'payment_pro') );
            } else if(osc_is_web_user_logged_in() && osc_logged_user_id()==$item['fk_i_user_id']) {
                osc_get_flash_message('pubMessages', true);
                osc_add_flash_warning_message( sprintf(__('To make this listing available to others, you need to pay a publish fee. <a href="%s">Continue and make the ad public</a>', 'payment_pro'), osc_route_url('payment-pro-user-menu') ));
            } else {
                ob_get_clean();
                Rewrite::newInstance()->set_location('error');
                header('HTTP/1.1 400 Bad Request');
                osc_current_web_theme_path('404.php');
                exit;
            }
        };
    };

    function payment_pro_item_delete($itemId) {
        ModelPaymentPro::newInstance()->deleteItem($itemId);
    }

    function payment_pro_configure_link() {
        osc_redirect_to(osc_route_admin_url('payment-pro-admin-conf'));
    }

    function payment_pro_form($category_id = null, $item_id = null) {
        $payment_pro_premium_fee = ModelPaymentPro::newInstance()->getPremiumPrice($category_id);
        $payment_pro_publish_fee = ModelPaymentPro::newInstance()->getPublishPrice($category_id);
        if($item_id==null) { // POST
            if($payment_pro_publish_fee>0 || $payment_pro_premium_fee>0) {
                require 'user/item_edit.php';
            }
        } else {
            $item = Item::newInstance()->findByPrimaryKey($item_id);
            if($item['b_premium']!=1) {
                require 'user/item_edit.php';
            }
        }
    }

    function payment_pro_enable_item($id) {
        ModelPaymentPro::newInstance()->enableItem($id);
    }

    function payment_pro_disable_item($id) {
        ModelPaymentPro::newInstance()->disableItem($id);
    }

    function payment_pro_cart_add_filter($item) {
        $str = substr($item['id'], 0, 3);
        if($str=='PRM' || $str=='PUB') {
            $item['quantity'] = 1;
        }
        return $item;
    }
    osc_add_filter('payment_pro_add_to_cart', 'payment_pro_cart_add_filter');

    function payment_pro_cart_quantity_filter($quantity, $item) {
        $str = substr($item['id'], 0, 3);
        if($str=='PRM' || $str=='PUB') {
            $quantity = 1;
        }
        return $quantity;
    }
    osc_add_filter('payment_pro_add_quantity', 'payment_pro_cart_quantity_filter');

     /* admin_page_header hook*/
    function payment_pro_admin_page_header() {
        switch (Params::getParam('route')) {
            case 'payment-pro-admin-conf':
                osc_remove_hook('admin_page_header', 'customPageHeader');
                osc_add_hook('admin_page_header',  'payments_pro_admin_page_header_settings' );
                break;
            case 'payment-pro-admin-prices':
                osc_remove_hook('admin_page_header', 'customPageHeader');
                osc_add_hook('admin_page_header',  'payments_pro_admin_page_header_categories_fees');
                break;
            case 'payment-pro-admin-log':
                osc_remove_hook('admin_page_header', 'customPageHeader');
                osc_add_hook('admin_page_header',  'payments_pro_admin_page_header_log');
                break;
            default:
                break;
        }
    }
    function payments_pro_admin_page_header_settings() {
        ?>
        <h1><?php _e('Payments settings', 'payments_pro'); ?></h1>
        <?php
    }

    function payments_pro_admin_page_header_categories_fees() {
        ?>
        <h1><?php _e('Categories fees', 'payments_pro'); ?></h1>
        <?php
    }

    function payments_pro_admin_page_header_log() {
        ?>
        <h1><?php _e('History of payments'); ?></h1>
        <?php
    }

    function payment_pro_admin_title() {
        switch (Params::getParam('route')) {
            case 'payment-pro-admin-conf':
                osc_remove_filter('admin_title', 'customPageTitle');
                osc_add_filter('admin_title',  'payments_pro_admin_title_settings' );
                break;
            case 'payment-pro-admin-prices':
                osc_remove_filter('admin_title', 'customPageTitle');
                osc_add_filter('admin_title',  'payments_pro_admin_title_settings' );
                break;
            case 'payment-pro-admin-log':
                osc_remove_filter('admin_title', 'customPageTitle');
                osc_add_filter('admin_title',  'payments_pro_admin_title_log' );
                break;
            default:
                break;
        }
    }

    function payment_pro_item_row($row, $aRow) {
        $enabled = ModelPaymentPro::newInstance()->isEnabled($aRow['pk_i_id'])?__('Active'):__('Blocked');
        $row['status'] = str_replace(__('Blocked'), $enabled, str_replace(__('Active'), $enabled, $row['status']));
        if(ModelPaymentPro::newInstance()->publishFeeIsPaid($aRow['pk_i_id'])) {
            $row['status'] .= ' ' . __('Paid', 'payment_pro');
        } else {
            $row['status'] .= ' ' . __('No-paid', 'payment_pro');
        };
        return $row;
    }

    function payment_pro_item_row_class($class, $item) {
        $enabled = ModelPaymentPro::newInstance()->isEnabled($item['pk_i_id']);
        if($item['b_enabled']!=$enabled) {
            foreach ($class as $k => $v) {
                if ($v == 'status-blocked') {
                    $class[$k] = 'status-active';
                } else if($v == 'status-active') {
                    $class[$k] = 'status-blocked';
                }
            }
        }
        if(ModelPaymentPro::newInstance()->publishFeeIsPaid($item['pk_i_id'])) {
            $class[] = 'payment-pro-paid';
        } else {
            $class[] = 'payment-pro-nopaid';
        };
        return $class;
    }

    function payment_pro_item_actions($actions, $aRow) {
        foreach($actions as $k => $v) {
            if(strpos($v, 'value=DISABLE')!==false || strpos($v, 'value=ENABLE')!==false) {
                if(ModelPaymentPro::newInstance()->isEnabled($aRow['pk_i_id'])) {
                    $actions[$k] = '<a href="' . osc_route_admin_url('payment-pro-admin-pay', array('pay' => 2, 'id' => $aRow['pk_i_id'])) . '" >' . __('Block', 'payment_pro') . '</a>';
                } else {
                    $actions[$k] = '<a href="' . osc_route_admin_url('payment-pro-admin-pay', array('pay' => 3, 'id' => $aRow['pk_i_id'])) . '" >' . __('Unblock', 'payment_pro') . '</a>';
                }
                break;
            }
        }
        if(ModelPaymentPro::newInstance()->publishFeeIsPaid($aRow['pk_i_id'])) {
            array_unshift($actions, '<a href="' . osc_route_admin_url('payment-pro-admin-pay', array('pay' => 0, 'id' => $aRow['pk_i_id'])) . '" >' . __('Unpay publish fee', 'payment_pro') . '</a>');
        } else {
            array_unshift($actions, '<a href="' . osc_route_admin_url('payment-pro-admin-pay', array('pay' => 1, 'id' => $aRow['pk_i_id'])) . '" >' . __('Pay publish fee', 'payment_pro') . '</a>');
        }
        return $actions;
    }

    function payments_pro_admin_title_settings($string) {
        return __('Settings, Osclass payments pro').$string;
    }
    function payments_pro_admin_title_categories_fees($string) {
        return __('Categories fees, Osclass payments pro').$string;
    }
    function payments_pro_admin_title_log($string) {
        return __('History of payments, Osclass payments pro').$string;
    }
    /**
     * ADD ROUTES (VERSION 3.2+)
     */
    osc_add_route('payment-pro-admin-pay', 'paymentpro/admin/pay/([0-1]+)/(.+)', 'paymentpro/admin/pay/{pay}/{id}', PAYMENT_PRO_PLUGIN_FOLDER . 'admin/pay.php');
    osc_add_route('payment-pro-admin-conf', 'paymentpro/admin/conf', 'paymentpro/admin/conf', PAYMENT_PRO_PLUGIN_FOLDER . 'admin/conf.php');
    osc_add_route('payment-pro-admin-prices', 'paymentpro/admin/prices', 'paymentpro/admin/prices', PAYMENT_PRO_PLUGIN_FOLDER . 'admin/conf_prices.php');
    osc_add_route('payment-pro-admin-log', 'paymentpro/admin/log', 'paymentpro/admin/log', PAYMENT_PRO_PLUGIN_FOLDER . 'admin/log.php');
    osc_add_route('payment-pro-checkout', 'paymentpro/checkout', 'paymentpro/checkout', PAYMENT_PRO_PLUGIN_FOLDER . 'user/checkout.php', false, 'custom', 'custom', __('Checkout', 'payment_pro'));
    osc_add_route('payment-pro-cart-delete', 'paymentpro/cart/delete/(.+)', 'paymentpro/cart/delete/{id}', PAYMENT_PRO_PLUGIN_FOLDER . 'user/cart-delete.php');
    osc_add_route('payment-pro-addcart', 'paymentpro/cart/add/(.+)', 'paymentpro/cart/add/{item}', PAYMENT_PRO_PLUGIN_FOLDER . 'user/cart-add.php');
    osc_add_route('payment-pro-done', 'paymentpro/done/(.*)', 'paymentpro/done/{tx}', PAYMENT_PRO_PLUGIN_FOLDER . 'user/done.php', false, 'custom', 'custom', __('Checkout', 'payment_pro'));
    osc_add_route('payment-pro-ajax', 'paymentpro/ajax', 'paymentpro/ajax', PAYMENT_PRO_PLUGIN_FOLDER . 'ajax.php');
    osc_add_route('payment-pro-user-menu', 'paymentpro/menu', 'paymentpro/menu', PAYMENT_PRO_PLUGIN_FOLDER . 'user/menu.php', true, 'custom', 'custom', __('Payment status', 'payment_pro'));



    /**
     * ADD HOOKS
     */
    osc_register_plugin(osc_plugin_path(__FILE__), 'payment_pro_install');
    osc_add_hook(osc_plugin_path(__FILE__)."_configure", 'payment_pro_configure_link');
    osc_add_hook(osc_plugin_path(__FILE__)."_uninstall", 'payment_pro_uninstall');

    osc_add_hook('admin_menu_init', 'payment_pro_admin_menu');

    osc_add_hook('init', 'payment_pro_admin_title');
    osc_add_hook('admin_header', 'payment_pro_admin_page_header');
    osc_add_hook('posted_item', 'payment_pro_publish', 8);
    osc_add_hook('edited_item', 'payment_pro_edited_item', 8);
    osc_add_hook('user_menu', 'payment_pro_user_menu');
    osc_add_hook('cron_hourly', 'payment_pro_cron');
    osc_add_hook('item_premium_off', 'payment_pro_premium_off');
    osc_add_hook('before_item_edit', 'payment_pro_before_edit');
    osc_add_hook('show_item', 'payment_pro_show_item');
    osc_add_hook('delete_item', 'payment_pro_item_delete');

    osc_add_hook('enable_item', 'payment_pro_enable_item');
    osc_add_hook('disable_item', 'payment_pro_disable_item');

    osc_add_hook('item_form', 'payment_pro_form');
    osc_add_hook('item_edit', 'payment_pro_form');

    osc_add_filter('items_processing_row', 'payment_pro_item_row', 8);
    osc_add_filter('datatable_listing_class', 'payment_pro_item_row_class', 8);
    osc_add_filter('more_actions_manage_items', 'payment_pro_item_actions', 3);

