<?php

    $confPath = PAYMENT_PRO_PATH . 'payments/';
    $dir = opendir($confPath);
    while($file = readdir($dir)) {
        if(is_dir($confPath . $file) && $file!='.' && $file!='..') {
            if(file_exists($confPath . $file . '/load.php')) {
                include_once $confPath . $file . '/load.php';
            }
        }
    }
    closedir($dir);

    if(Params::getParam('plugin_action')=='done') {
        osc_set_preference('default_premium_cost', Params::getParam("default_premium_cost") ? Params::getParam("default_premium_cost") : '1.0', 'payment_pro', 'STRING');
        osc_set_preference('allow_premium', Params::getParam("allow_premium") ? Params::getParam("allow_premium") : '0', 'payment_pro', 'BOOLEAN');
        osc_set_preference('default_publish_cost', Params::getParam("default_premium_cost") ? Params::getParam("default_publish_cost") : '1.0', 'payment_pro', 'STRING');
        osc_set_preference('pay_per_post', Params::getParam("pay_per_post") ? Params::getParam("pay_per_post") : '0', 'payment_pro', 'BOOLEAN');
        osc_set_preference('premium_days', Params::getParam("premium_days") ? Params::getParam("premium_days") : '7', 'payment_pro', 'INTEGER');
        osc_set_preference('currency', Params::getParam("currency") ? Params::getParam("currency") : 'USD', 'payment_pro', 'STRING');
        osc_set_preference('pack_price_1', Params::getParam("pack_price_1"), 'payment_pro', 'STRING');
        osc_set_preference('pack_price_2', Params::getParam("pack_price_2"), 'payment_pro', 'STRING');
        osc_set_preference('pack_price_3', Params::getParam("pack_price_3"), 'payment_pro', 'STRING');

        osc_run_hook('payment_pro_conf_save');

        // HACK : This will make possible use of the flash messages ;)
        ob_get_clean();
        osc_add_flash_ok_message(__('Congratulations, the plugin is now configured', 'payment_pro'), 'admin');
        osc_redirect_to(osc_route_admin_url('payment-pro-admin-conf'));
    }
?>


<?php if(PAYMENT_PRO_CRYPT_KEY=='randompasswordchangethis') {
    echo '<div style="text-align:center; font-size:22px; background-color:#dd0000;"><p>' . sprintf(__('Please, change the crypt key (PAYMENT_PRO_CRYPT_KEY) in %s. <a id="howto" href="javascript:void(0);" onclick="$(\'#dialog-howto\').dialog(\'open\');">How to do it.</a>', 'payment_pro'), PAYMENT_PRO_PATH.'config.php') . '</p></div>';
}; ?>
<div id="general-setting">
    <div id="general-settings">
        <h2 class="render-title"><?php _e('Payments settings', 'payment_pro'); ?></h2>
        <ul id="error_list"></ul>
        <form name="payment_pro_form" action="<?php echo osc_admin_base_url(true); ?>" method="post">
            <input type="hidden" name="page" value="plugins" />
            <input type="hidden" name="action" value="renderplugin" />
            <input type="hidden" name="route" value="payment-pro-admin-conf" />
            <input type="hidden" name="plugin_action" value="done" />
            <fieldset>
                <div class="form-horizontal">
                    <div class="form-row">
                        <div class="form-label"><?php _e('Premium ads', 'payment_pro'); ?></div>
                        <div class="form-controls">
                            <div class="form-label-checkbox">
                                <label>
                                    <input type="checkbox" <?php echo (osc_get_preference('allow_premium', 'payment_pro') ? 'checked="true"' : ''); ?> name="allow_premium" value="1" />
                                    <?php _e('Allow premium ads', 'payment_pro'); ?>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-label"><?php _e('Default premium cost', 'payment_pro'); ?></div>
                        <div class="form-controls"><input type="text" class="xlarge" name="default_premium_cost" value="<?php echo osc_get_preference('default_premium_cost', 'payment_pro'); ?>" /></div>
                    </div>
                    <div class="form-row">
                        <div class="form-label"><?php _e('Premium days', 'payment_pro'); ?></div>
                        <div class="form-controls"><input type="text" class="xlarge" name="premium_days" value="<?php echo osc_get_preference('premium_days', 'payment_pro'); ?>" /></div>
                    </div>
                    <div class="form-row">
                        <div class="form-label"><?php _e('Publish fee', 'payment_pro'); ?></div>
                        <div class="form-controls">
                            <div class="form-label-checkbox">
                                <label>
                                    <input type="checkbox" <?php echo (osc_get_preference('pay_per_post', 'payment_pro') ? 'checked="true"' : ''); ?> name="pay_per_post" value="1" />
                                    <?php _e('Pay per post ads', 'payment_pro'); ?>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-label"><?php _e('Default publish cost', 'payment_pro'); ?></div>
                        <div class="form-controls"><input type="text" class="xlarge" name="default_publish_cost" value="<?php echo osc_get_preference('default_publish_cost', 'payment_pro'); ?>" /></div>
                    </div>
                    <div class="form-row">
                        <div class="form-label"><?php _e('Default currency', 'payment_pro'); ?></div>
                        <div class="form-controls">
                            <select name="currency" id="currency">
                                <option value="AUD" <?php if(osc_get_preference('currency', 'payment_pro')=="AUD") { echo 'selected="selected"';}; ?> >AUD</option>
                                <option value="BRL" <?php if(osc_get_preference('currency', 'payment_pro')=="BRL") { echo 'selected="selected"';}; ?> >BRL</option>
                                <option value="CAD" <?php if(osc_get_preference('currency', 'payment_pro')=="CAD") { echo 'selected="selected"';}; ?> >CAD</option>
                                <option value="CHF" <?php if(osc_get_preference('currency', 'payment_pro')=="CHF") { echo 'selected="selected"';}; ?> >CHF</option>
                                <option value="CZK" <?php if(osc_get_preference('currency', 'payment_pro')=="CZK") { echo 'selected="selected"';}; ?> >CZK</option>
                                <option value="DKK" <?php if(osc_get_preference('currency', 'payment_pro')=="DKK") { echo 'selected="selected"';}; ?> >DKK</option>
                                <option value="EUR" <?php if(osc_get_preference('currency', 'payment_pro')=="EUR") { echo 'selected="selected"';}; ?> >EUR</option>
                                <option value="GBP" <?php if(osc_get_preference('currency', 'payment_pro')=="GBP") { echo 'selected="selected"';}; ?> >GBP</option>
                                <option value="HKD" <?php if(osc_get_preference('currency', 'payment_pro')=="HKD") { echo 'selected="selected"';}; ?> >HKD</option>
                                <option value="HUF" <?php if(osc_get_preference('currency', 'payment_pro')=="HUF") { echo 'selected="selected"';}; ?> >HUF</option>
                                <option value="INR" <?php if(osc_get_preference('currency', 'payment_pro')=="INR") { echo 'selected="selected"';}; ?> >INR</option>
                                <option value="JPY" <?php if(osc_get_preference('currency', 'payment_pro')=="JPY") { echo 'selected="selected"';}; ?> >JPY</option>
                                <option value="NOK" <?php if(osc_get_preference('currency', 'payment_pro')=="NOK") { echo 'selected="selected"';}; ?> >NOK</option>
                                <option value="NZD" <?php if(osc_get_preference('currency', 'payment_pro')=="NZD") { echo 'selected="selected"';}; ?> >NZD</option>
                                <option value="PLN" <?php if(osc_get_preference('currency', 'payment_pro')=="PLN") { echo 'selected="selected"';}; ?> >PLN</option>
                                <option value="SEK" <?php if(osc_get_preference('currency', 'payment_pro')=="SEK") { echo 'selected="selected"';}; ?> >SEK</option>
                                <option value="SGD" <?php if(osc_get_preference('currency', 'payment_pro')=="SGD") { echo 'selected="selected"';}; ?> >SGD</option>
                                <option value="USD" <?php if(osc_get_preference('currency', 'payment_pro')=="USD") { echo 'selected="selected"';}; ?> >USD</option>
                                <option value="BTC" <?php if(osc_get_preference('currency', 'payment_pro')=="BTC") { echo 'selected="selected"';}; ?> >BTC*</option>
                            </select>
                        </div>
                        <span class="help-box"><?php _e('BTC are not currently supported by Paypal (use only with Blockchain).', 'payment_pro'); ?></span>
                    </div>
                    <?php /* <div class="form-row">
                        <span class="help-box">
                            <?php _e("You could specify up to 3 'packs' that users can buy, so they don't need to pay each time they publish an ad. The credit from the pack will be stored for later uses.",'payment'); ?>
                        </span>
                    </div>
                    <div class="form-row">
                        <div class="form-label"><?php echo sprintf(__('Price of pack #%d', 'payment_pro'), '1'); ?></div>
                        <div class="form-controls"><input type="text" class="xlarge" name="pack_price_1" value="<?php echo osc_get_preference('pack_price_1', 'payment_pro'); ?>" /></div>
                    </div>
                    <div class="form-row">
                        <div class="form-label"><?php echo sprintf(__('Price of pack #%d', 'payment_pro'), '2'); ?></div>
                        <div class="form-controls"><input type="text" class="xlarge" name="pack_price_2" value="<?php echo osc_get_preference('pack_price_2', 'payment_pro'); ?>" /></div>
                    </div>
                    <div class="form-row">
                        <div class="form-label"><?php echo sprintf(__('Price of pack #%d', 'payment_pro'), '3'); ?></div>
                        <div class="form-controls"><input type="text" class="xlarge" name="pack_price_3" value="<?php echo osc_get_preference('pack_price_3', 'payment_pro'); ?>" /></div>
                    </div> */ ?>
                    <?php osc_run_hook('payment_pro_conf_form'); ?>
                    <div class="clear"></div>
                    <div class="form-actions">
                        <input type="submit" id="save_changes" value="<?php echo osc_esc_html( __('Save changes') ); ?>" class="btn btn-submit" />
                    </div>
                </div>
            </fieldset>
        </form>
    </div>
</div>
<form id="dialog-howto" method="get" action="#" class="has-form-actions hide">
    <div class="form-horizontal">
        <div class="form-row">
            <h3><?php _e('How to change PAYMENT_PRO_CRYPT_KEY', 'payment_pro'); ?></h3>
            <p>
                <?php _e('PAYMENT_PRO_CRYPT_KEY is an unique key of <b>16, 24 or 32 characters long</b> to encrypt your payment information. You need to change it to something unique and not too short.', 'payment_pro'); ?>.
                <br/>
                <?php printf(__('PAYMENT_PRO_CRYPT_KEY should be defined in %s . If you do not have said file, create it with the following content: ', 'payment_pro'), PAYMENT_PRO_PATH.'config.php'); ?>.
                <br/>
                <pre>
                    &lt;?php
                    if(!defined('PAYMENT_PRO_CRYPT_KEY')) {
                        define('PAYMENT_PRO_CRYPT_KEY', 'randompasswordchangethis');
                    }
                </pre>
                <?php _e('You need to tell Paypal where is your IPN file', 'payment_pro'); ?>
            </p>
        </div>
        <div class="form-actions">
            <div class="wrapper">
                <a class="btn" href="javascript:void(0);" onclick="$('#dialog-howto').dialog('close');"><?php _e('Cancel'); ?></a>
            </div>
        </div>
    </div>
</form>
<script type="text/javascript" >
    $(document).ready(function(){
        $("#dialog-howto").dialog({
            autoOpen: false,
            modal: true,
            width: '90%',
            title: '<?php echo osc_esc_js( __('How to change PAYMENT_PRO_CRYPT_KEY', 'payment_pro') ); ?>'
        });
    });
</script>
<?php
    osc_run_hook('payment_pro_conf_footer');
