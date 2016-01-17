<?php
    $itemsPerPage = (Params::getParam('itemsPerPage')!='')?Params::getParam('itemsPerPage'):10;
    $page         = (Params::getParam('iPage') > 0) ? Params::getParam('iPage') -1 : 0;
    $itemType     = 'all';
    $total_items  = Item::newInstance()->countItemTypesByUserID(osc_logged_user_id(), $itemType);
    $total_pages  = ceil($total_items/$itemsPerPage);
    $items        = Item::newInstance()->findItemTypesByUserID(osc_logged_user_id(), $page*$itemsPerPage, $itemsPerPage, $itemType);

    View::newInstance()->_exportVariableToView('items', $items);
    View::newInstance()->_exportVariableToView('list_total_pages', $total_pages);
    View::newInstance()->_exportVariableToView('list_total_items', $total_items);
    View::newInstance()->_exportVariableToView('items_per_page', $itemsPerPage);
    View::newInstance()->_exportVariableToView('list_page', $page);
?>
<div class="wrapper wrapper-flash">
    <div id="flash_js"></div>
</div>
<h2><?php _e('Your listings', 'payment_pro'); ?></h2>
<?php if(osc_count_items() == 0) { ?>
    <h3><?php _e('You don\'t have any listing yet', 'payment_pro'); ?></h3>
<?php } else { ?>
    <?php while(osc_has_items()) { ?>
            <div class="item" >
                    <h3>
                        <a href="<?php echo osc_item_url(); ?>"><?php echo osc_item_title(); ?></a>
                    </h3>
                    <p>
                    <?php _e('Publication date', 'payment_pro') ; ?>: <?php echo osc_format_date(osc_item_pub_date()) ; ?><br />
                    <?php _e('Price', 'payment_pro') ; ?>: <?php echo osc_format_price(osc_item_price()); ?>
                    </p>
                    <p class="options">
                        <?php if(ModelPaymentPro::newInstance()->isEnabled(osc_item_id())) {
                            if (osc_get_preference("pay_per_post", 'payment_pro') == "1") { ?>
                                <?php if (ModelPaymentPro::newInstance()->publishFeeIsPaid(osc_item_id())) { ?>
                                    <strong><?php _e('Paid!', 'payment_pro'); ?></strong>
                                <?php } else { ?>
                                    <strong>
                                        <button id="pub_<?php echo osc_item_id(); ?>"
                                                onclick="javascript:addPublish(<?php echo osc_item_id(); ?>);"><?php _e('Publish this item', 'payment_pro'); ?></button>
                                    </strong>
                                <?php }; ?>
                            <?php }
                        ; ?>
                            <?php if (osc_get_preference("pay_per_post", 'payment_pro') == "1" && osc_get_preference("allow_premium", 'payment_pro') == "1") { ?>
                                <span>|</span>
                            <?php }
                        ; ?>
                            <?php if (osc_get_preference("allow_premium", 'payment_pro') == "1") { ?>
                                <?php if (ModelPaymentPro::newInstance()->premiumFeeIsPaid(osc_item_id())) { ?>
                                    <strong><?php _e('Already premium!', 'payment_pro'); ?></strong>
                                <?php } else { ?>
                                    <strong>
                                        <button id="prm_<?php echo osc_item_id(); ?>"
                                                onclick="javascript:addPremium(<?php echo osc_item_id(); ?>);"><?php _e('Make premium', 'payment_pro'); ?></button>
                                    </strong>
                                <?php }; ?>
                            <?php };
                        } else { ?>
                            <strong><?php _e('This listing is blocked', 'payment_pro'); ?></strong>
                        <?php }; ?>
                    </p>
                    <br />
            </div>
    <?php } ?>
    <br />
    <div class="paginate">
    <?php for($i = 0 ; $i < osc_list_total_pages() ; $i++) {
        if($i == osc_list_page()) {
            printf('<a class="searchPaginationSelected" href="%s">%d</a>', osc_route_url('payment-pro-user-menu-page', array('iPage' => $i)), ($i + 1));
        } else {
            printf('<a class="searchPaginationNonSelected" href="%s">%d</a>', osc_route_url('payment-pro-user-menu-page', array('iPage' => $i)), ($i + 1));
        }
    } ?>
    </div>
    <script type="text/javascript">
        function addPremium(id) {
            $("#prm_" + id).attr('disabled', true);
            $.ajax({
                type: "POST",
                url: '<?php echo osc_ajax_plugin_url(PAYMENT_PRO_PLUGIN_FOLDER . 'ajax.php'); ?>&prm=' + id,
                dataType: 'json',
                success: function(data){
                    if(data.error==0) {
                        window.location = '<?php echo osc_route_url('payment-pro-checkout'); ?>';
                    } else {
                        $("#prm_" + id).attr('disabled', false);
                        var flash = $("#flash_js");
                        var message = $('<div>').addClass('flashmessage').addClass('flashmessage-error').attr('id', 'flashmessage').html(data.msg);
                        flash.html(message);
                        $("#flashmessage").slideDown('slow').delay(3000).slideUp('slow');
                        $("html, body").animate({ scrollTop: 0 }, "slow");
                    }
                }
            });
        }
        function addPublish(id) {
            $("#pub_" + id).attr('disabled', true);
            $.ajax({
                type: "POST",
                url: '<?php echo osc_ajax_plugin_url(PAYMENT_PRO_PLUGIN_FOLDER . 'ajax.php'); ?>&pub=' + id,
                dataType: 'json',
                success: function(data){
                    if(data.error==0) {
                        window.location = '<?php echo osc_route_url('payment-pro-checkout'); ?>';
                    } else {
                        $("#pub_" + id).attr('disabled', false);
                        var flash = $("#flash_js");
                        var message = $('<div>').addClass('flashmessage').addClass('flashmessage-error').attr('id', 'flashmessage').html(data.msg);
                        flash.html(message);
                        $("#flashmessage").slideDown('slow').delay(3000).slideUp('slow');
                        $("html, body").animate({ scrollTop: 0 }, "slow");
                    }
                }
            });
        }
    </script>
<?php } ?>
