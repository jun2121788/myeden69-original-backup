<?php

    $pay = Params::getParam('pay');
    $id = Params::getParam('id');

    $data = ModelPaymentPro::newInstance()->getPublishData($id);
    if(!$data) {
        $item = Item::newInstance()->findByPrimaryKey($id);
        ModelPaymentPro::newInstance()->createItem($item['pk_i_id'], 0, null, null, $item['b_enabled']);
    }

    switch($pay) {
        case 0: // UNPAID
            ModelPaymentPro::newInstance()->unpayPublishFee($id);
            osc_add_flash_ok_message(__('Listing unpaid', 'payment_pro'), 'admin');
            break;
        case 1: // PAID
            ModelPaymentPro::newInstance()->enableItem($id);
            ModelPaymentPro::newInstance()->payPublishFee($id, 'ADMIN');
            osc_add_flash_ok_message(__('Listing paid', 'payment_pro'), 'admin');
            break;
        case 2: // BLOCK
            if(ModelPaymentPro::newInstance()->publishFeeIsPaid($id)) {
                $mItems = new ItemActions(false);
                $mItems->disable($id);
            } else {
                ModelPaymentPro::newInstance()->disableItem($id);
            }
            osc_add_flash_ok_message(__('Listing disabled', 'payment_pro'), 'admin');
            break;
        case 3: // UNBLOCK
            if(ModelPaymentPro::newInstance()->publishFeeIsPaid($id)) {
                //$mItems = new ItemActions(false);
                //$mItems->enable($id);
            } else {
                ModelPaymentPro::newInstance()->enableItem($id);
            }
            osc_add_flash_ok_message(__('Listing enabled', 'payment_pro'), 'admin');
            break;
        default:
            break;
    }

    ob_get_clean();
    osc_redirect_to(osc_admin_base_url(true) . '?page=items');

