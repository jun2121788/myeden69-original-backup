<?php
    $packs = array();
    if(osc_get_preference("pack_price_1", 'payment_pro')!='' && osc_get_preference("pack_price_1", 'payment_pro')!='0') {
        $packs[] = osc_get_preference("pack_price_1", 'payment_pro');
    }
    if(osc_get_preference("pack_price_2", 'payment_pro')!='' && osc_get_preference("pack_price_2", 'payment_pro')!='0') {
        $packs[] = osc_get_preference("pack_price_2", 'payment_pro');
    }
    if(osc_get_preference("pack_price_3", 'payment_pro')!='' && osc_get_preference("pack_price_3", 'payment_pro')!='0') {
        $packs[] = osc_get_preference("pack_price_3", 'payment_pro');
    }
    @$user = User::newInstance()->findByPrimaryKey(osc_logged_user_id());
    $wallet = ModelPaymentPro::newInstance()->getWallet(osc_logged_user_id());

    if(osc_get_preference('currency', 'payment_pro')=='BTC') {
        $amount = isset($wallet['formatted_amount'])?$wallet['formatted_amount']:0;
        $formatted_amount = payment_pro_format_btc($amount);
        $credit_msg = sprintf(__('Credit packs. Your current credit is %s', 'payment_pro'), $formatted_amount);
    } else {
        $amount = isset($wallet['i_amount'])?$wallet['i_amount']:0;
        if($amount!=0) {
            $formatted_amount = osc_format_price($amount/1000000, osc_get_preference('currency', 'payment_pro'));
            $credit_msg = sprintf(__('Credit packs. Your current credit is %s', 'payment_pro'), $formatted_amount);
        } else {
            $credit_msg = __('Your wallet is empty. Buy some credits.', 'payment_pro');
        }
    }

?>

<h2><?php echo $credit_msg; ?></h2>
<?php $pack_n = 0;
foreach($packs as $pack) { $pack_n++; ?>
    <div>
        <h3><?php echo sprintf(__('Credit pack #%d', 'payment_pro'), $pack_n); ?></h3>
        <div><label><?php _e("Price", 'payment_pro');?>:</label> <?php echo $pack." ".osc_get_preference('currency', 'payment_pro'); ?></div>
        <ul class="payments-ul">
        <?php payment_pro_buttons($pack, sprintf(__("Credit for %s %s at %s", 'payment_pro'), $pack, osc_get_preference("currency", 'payment_pro'), osc_page_title()), '301x'.$pack, array('user' => @$user['pk_i_id'], 'itemid' => @$user['pk_i_id'], 'email' => @$user['s_email'])); ?>
        </ul>
    </div>
    <div style="clear:both;"></div>
    <br/>
<?php } ?>
<?php payment_pro_buttons_js(); ?>