
<h2><?php _e('Publish options', 'payment_pro') ; ?></h2>
<div class="control-group">
    <?php if($payment_pro_premium_fee>0) { ?>
        <div class="controls checkbox">
            <input type="checkbox" name="payment_pro_make_premium" id="payment_pro_make_premium" value="1" checked="yes" /> <label><?php printf(__('Make this ad premium (+%s)', 'payment_pro'), osc_format_price($payment_pro_premium_fee*1000000, osc_get_preference('currency', 'payment_pro'))); ?></label>
      </div>
    <?php };
    if($payment_pro_publish_fee>0) { ?>
    <div class="controls checkbox">
        <label><?php printf(__('Publishing this ad costs %s', 'payment_pro'), osc_format_price($payment_pro_publish_fee*1000000, osc_get_preference('currency', 'payment_pro'))); ?></label>
    </div>
    <?php }; ?>
</div>