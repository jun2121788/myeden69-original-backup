<?php
$item_p = Params::getParam('item');
if(substr($item_p, 0, 3)=='PUB') {
    $id = explode("-", $item);
    $item = Item::newInstance()->findByPrimaryKey($id[count($id)-1]);
    $added = false;
    if(str_replace("PUB", "", $id[0])==$item['fk_i_category_id']) {

        if (!ModelPaymentPro::newInstance()->publishFeeIsPaid($item['pk_i_id'])) {
            $category_fee_pub = ModelPaymentPro::newInstance()->getPublishPrice($item['fk_i_category_id']);
            if ($category_fee_pub > 0) {
                $added = true;
                payment_pro_cart_add('PUB' . $item['fk_i_category_id'] . '-' . $item['pk_i_id'], sprintf(__('Publish fee for listing %d', 'payment_pro'), $item['pk_i_id']), $category_fee_pub);
            }
        }

    }

    if(!$added) {
        osc_add_flash_error_message(__('Product could not be added', 'payment_pro'));
    }

} else if(substr($item_p, 0, 3)=='PRM') {


    $id = explode("-", $item);
    $item = Item::newInstance()->findByPrimaryKey($id[count($id)-1]);
    $added = false;
    if(str_replace("PRM", "", $id[0])==$item['fk_i_category_id']) {
        $category_fee = ModelPaymentPro::newInstance()->getPremiumPrice($item['fk_i_category_id']);
        if($category_fee > 0) {
            $added = true;
            payment_pro_cart_add('PRM' . $item['fk_i_category_id'] . '-' . $item['pk_i_id'], sprintf(__('Premium enchancement for listing %d', 'payment_pro'), $item['pk_i_id']), $category_fee);

            if(!ModelPaymentPro::newInstance()->publishFeeIsPaid($item['pk_i_id'])) {
                $category_fee_pub = ModelPaymentPro::newInstance()->getPublishPrice($item['fk_i_category_id']);
                if($category_fee_pub > 0) {
                    payment_pro_cart_add('PUB' . $item['fk_i_category_id'] . '-' . $item['pk_i_id'], sprintf(__('Publish fee for listing %d', 'payment_pro'), $item['pk_i_id']), $category_fee_pub);
                }
            }

        }
    }

    if(!$added) {
        osc_add_flash_error_message(__('Product could not be added', 'payment_pro'));
    }

} else {
    osc_run_hook('payment_pro_add_to_cart_email', $item_p);
}
payment_pro_js_redirect_to(osc_route_url('payment-pro-checkout'));
