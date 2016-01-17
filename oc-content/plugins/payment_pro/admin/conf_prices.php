<?php

if(Params::getParam('plugin_action')=='add_category') {
    if(Params::getParam('catId')!='') {
        ModelPaymentPro::newInstance()->insertPrice(
            Params::getParam('catId'),
            Params::getParam('publish_price')==''?osc_get_preference('default_publish_cost'):Params::getParam('publish_price'),
            Params::getParam('premium_price')==''?osc_get_preference('default_premium_cost'):Params::getParam('premium_price'));
        osc_add_flash_ok_message(__('Category prices updated correctly', 'payment_pro'), 'admin');
    } else {
        osc_add_flash_error_message(__('Category is not defined', 'payment_pro'), 'admin');
    }
    ob_get_clean();
    osc_redirect_to(osc_route_admin_url('payment-pro-admin-prices'));
} else if(Params::getParam('plugin_action')=='delete') {
    if(Params::getParam('catId')!='') {
        ModelPaymentPro::newInstance()->deletePrices(Params::getParam('catId'));
        osc_add_flash_ok_message(__('Category prices changed to default', 'payment_pro'), 'admin');
    } else {
        osc_add_flash_error_message(__('Category is not defined', 'payment_pro'), 'admin');
    }
    ob_get_clean();
    osc_redirect_to(osc_route_admin_url('payment-pro-admin-prices'));
}

$catMgr = Category::newInstance();
$prices= ModelPaymentPro::newInstance()->getCategoriesPrices();

?>
<style type="text/css">
    .payment-pro-pub {
        background-color: #d8e6ff;
    }
    .payment-pro-prm {
        background-color: #d8e6ff;
    }
</style>
<script type="text/javascript" >
    $(document).ready(function(){
        $("#dialog-new").dialog({
            autoOpen: false,
            width: "500px",
            modal: true,
            title: '<?php echo osc_esc_js( __('Set category prices', 'payment_pro') ); ?>'
        });
        $("#dialog-delete").dialog({
            autoOpen: false,
            width: "500px",
            modal: true,
            title: '<?php echo osc_esc_js( __('Delete category prices', 'payment_pro') ); ?>'
        });
    });
    function new_cat() {
        $('#select_row').show();
        $('#dialog-new').dialog('open');
    };
    function edit_cat(id, pub, prm) {
        $('#catId').prop('value', id);
        $('#publish_price').prop('value', pub);
        $('#premium_price').prop('value', prm);
        $('#select_row').hide();
        $('#dialog-new').dialog('open');
    };
    function delete_cat(id) {
        $('#delete_cat').prop('value', id);
        $('#dialog-delete').dialog('open');
    };
</script>
<div id="general-setting">
    <div id="general-settings">
        <h2 class="render-title"><?php _e('Set categories prices', 'payment_pro'); ?> <span><a id="new-price" href="javascript:new_cat();" ><?php _e('Add new price', 'payment_pro'); ?></a></span></h2>
        <ul id="error_list"></ul>
        <form name="payment_pro_form" action="#" method="post">
            <fieldset>
                <div class="form-horizontal">
                    <?php foreach($prices as $price) {
                        $category = $catMgr->findByPrimaryKey($price['fk_i_category_id']); ?>
                        <div class="form-row">
                            <div class="form-label"><?php echo $category['s_name']; ?></div>
                            <div class="form-controls">
                                <span class="payment-pro-pub" ><?php printf(__('Publish cost: %s %s'), $price['i_publish_cost'], osc_get_preference('currency', 'payment_pro')); ?></span>
                                <span class="payment-pro-prm" ><?php printf(__('Premium cost: %s %s'), $price['i_premium_cost'], osc_get_preference('currency', 'payment_pro')); ?></span>
                                <span><a href="javascript:edit_cat(<?php echo $price['fk_i_category_id'].", ".$price['i_publish_cost'].", ".$price['i_premium_cost']; ?>);" ><?php _e('edit', 'payment_pro'); ?></a></span>
                                <span><a href="javascript:delete_cat(<?php echo $price['fk_i_category_id']; ?>);" ><?php _e('delete', 'payment_pro'); ?></a></span>
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <div class="clear"></div>
            </fieldset>
        </form>
    </div>
</div>
<form id="dialog-new" method="post" action="<?php echo osc_admin_base_url(true); ?>" class="has-form-actions hide">
    <input type="hidden" name="page" value="plugins" />
    <input type="hidden" name="action" value="renderplugin" />
    <input type="hidden" name="route" value="payment-pro-admin-prices" />
    <input type="hidden" name="plugin_action" value="add_category" />
    <div class="form-horizontal">
        <div class="form-row" id="select_row" >
            <div class="form-label"><?php _e('Category', 'payment_pro'); ?></div>
            <div class="form-controls">
                <?php ItemForm::category_select(); ?>
            </div>
        </div>
        <div class="form-row">
            <div class="form-label"><?php _e('Publish price', 'payment_pro'); ?></div>
            <div class="form-controls"><input type="text" id="publish_price" name="publish_price" value="" placeholder="<?php echo osc_get_preference('default_publish_cost', 'payment_pro'); ?>" /> <?php echo osc_get_preference('currency', 'payment_pro'); ?></div>
        </div>
        <div class="form-row">
            <div class="form-label"><?php _e('Premium price', 'payment_pro'); ?></div>
            <div class="form-controls"><input type="text" id="premium_price" name="premium_price" value="" placeholder="<?php echo osc_get_preference('default_premium_cost', 'payment_pro'); ?>" /> <?php echo osc_get_preference('currency',
                    'payment'); ?></div>
        </div>
        <div class="form-actions">
            <div class="wrapper">
                <a class="btn" href="javascript:void(0);" onclick="$('#dialog-new').dialog('close');"><?php _e('Cancel', 'payment_pro'); ?></a>
                <input id="payment-pro-submit" type="submit" value="<?php echo osc_esc_html( __('Add', 'payment_pro')); ?>" class="btn btn-red" />
            </div>
        </div>
    </div>
</form>
<form id="dialog-delete" method="post" action="<?php echo osc_admin_base_url(true); ?>" class="has-form-actions hide">
    <input type="hidden" name="page" value="plugins" />
    <input type="hidden" name="action" value="renderplugin" />
    <input type="hidden" name="route" value="payment-pro-admin-prices" />
    <input type="hidden" name="plugin_action" value="delete" />
    <input type="hidden" name="catId" id="delete_cat" value="" />
    <div class="form-horizontal">
        <div class="form-row">
            <?php _e('This will revert back the prices to the default ones. Do you want to continue?', 'payment_pro'); ?>
        </div>
        <div class="form-actions">
            <div class="wrapper">
                <a class="btn" href="javascript:void(0);" onclick="$('#dialog-delete').dialog('close');"><?php _e('Cancel', 'payment_pro'); ?></a>
                <input id="price-delete-submit" type="submit" value="<?php echo osc_esc_html( __('Delete', 'payment_pro')); ?>" class="btn btn-red" />
            </div>
        </div>
    </div>
</form>




<?php
/*

    $mp = ModelPaymentPro::newInstance();

    if(Params::getParam('plugin_action') == 'done') {
        $pub_prices = Params::getParam("pub_prices");
        $pr_prices  = Params::getParam("pr_prices");
        foreach($pr_prices as $k => $v) {
            $mp->insertPrice($k, $pub_prices[$k]==''?NULL:$pub_prices[$k], $v==''?NULL:$v);
        }
        // HACK : This will make possible use of the flash messages ;)
        ob_get_clean();
        osc_add_flash_ok_message(__('Congratulations, the plugin is now configured', 'payment_pro'), 'admin');
        osc_redirect_to(osc_route_admin_url('payment-pro-admin-prices'));
    }

    $categories = Category::newInstance()->toTreeAll();
    $prices     = ModelPaymentPro::newInstance()->getCategoriesPrices();
    $cat_prices = array();
    foreach($prices as $p) {
        $cat_prices[$p['fk_i_category_id']]['i_publish_cost'] = $p['i_publish_cost'];
        $cat_prices[$p['fk_i_category_id']]['i_premium_cost'] = $p['i_premium_cost'];
    }

    function drawCategories($categories, $depth = 0, $cat_prices) {
        foreach($categories as $c) { ?>
            <tr>
                <td>
                    <?php for($d=0;$d<$depth;$d++) { echo "&nbsp;&nbsp;"; }; echo $c['s_name']; ?>
                </td>
                <td>
                    <input style="width:150px;text-align:right;" type="text" name="pub_prices[<?php echo $c['pk_i_id']?>]" id="pub_prices[<?php echo $c['pk_i_id']?>]" value="<?php echo isset($cat_prices[$c['pk_i_id']]) ? $cat_prices[$c['pk_i_id']]['i_publish_cost'] : ''; ?>" />
                </td>
                <td>
                    <input style="width:150px;text-align:right;" type="text" name="pr_prices[<?php echo $c['pk_i_id']?>]" id="pr_prices[<?php echo $c['pk_i_id']?>]" value="<?php echo isset($cat_prices[$c['pk_i_id']]) ? $cat_prices[$c['pk_i_id']]['i_premium_cost'] : ''; ?>" />
                </td>
            </tr>
        <?php drawCategories($c['categories'], $depth+1, $cat_prices);
        }
    };


?>
    <div style="padding: 20px;">
        <div style="float: left; width: 100%;">
            <fieldset>
                <legend><?php _e('Paypal Options', 'payment_pro'); ?></legend>
                <div style="float: left; width: 100%;">
                    <form name="payment_pro_form" id="payment_pro_form" action="<?php echo osc_admin_base_url(true);?>" method="POST" enctype="multipart/form-data" >
                        <input type="hidden" name="page" value="plugins" />
                        <input type="hidden" name="action" value="renderplugin" />
                        <input type="hidden" name="route" value="payment-pro-admin-prices" />
                        <input type="hidden" name="plugin_action" value="done" />
                        <table border="0" cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="width:300px;"><?php _e('Category Name', 'payment_pro'); ?></td>
                                <td style="width:175px;"><?php echo sprintf(__('Publish fee (%s)', 'payment_pro'), osc_get_preference('currency', 'payment_pro')); ?></td>
                                <td style="width:175px;"><?php echo sprintf(__('Premium fee (%s)', 'payment_pro'), osc_get_preference('currency', 'payment_pro')); ?></td>
                            </tr>
                            <?php drawCategories($categories, 0, $cat_prices); ?>
                        </table>
                        <button type="submit" style="float: right;"><?php _e('Update', 'payment_pro'); ?></button>
                    </form>
                </div>
            </fieldset>
        </div>
        <div style="clear:both;">
            <div style="float: left; width: 100%;">
                <fieldset>
                    <legend><?php _e('Help', 'payment_pro'); ?></legend>
                    <h3><?php _e('Setting up your fees', 'payment_pro'); ?></h3>
                    <p>
                        <?php _e('You could set up different prices for each category', 'payment_pro'); ?>. <?php _e('If the price of a category is left empty, the default value will be applied', 'payment_pro'); ?>.
                    </p>
                </fieldset>
            </div>
            <div style="clear: both;"></div>
        </div>
    </div>
 */ ?>
