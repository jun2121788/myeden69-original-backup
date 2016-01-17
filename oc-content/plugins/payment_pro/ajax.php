<?php

if(Params::getParam('prm')!='') {
    $prm = Params::getParam('prm');
    if(!ModelPaymentPro::newInstance()->isEnabled($prm)) {
        echo json_encode(array('error' => 1, 'msg' => __('This listing is not enabled yet', 'payment_pro')));
        die;
    }
    $item = Item::newInstance()->findByPrimaryKey($prm);
    if($item['fk_i_user_id']==null || ($item['fk_i_user_id']!=null && $item['fk_i_user_id']==osc_logged_user_id())) {
        $category_fee = ModelPaymentPro::newInstance()->getPremiumPrice($item['fk_i_category_id']);
        if($category_fee > 0) {
            payment_pro_cart_add('PRM' . $item['fk_i_category_id'] . '-' . $item['pk_i_id'], sprintf(__('Premium enchancement for listing %d', 'payment_pro'), $item['pk_i_id']), $category_fee);

            if(!ModelPaymentPro::newInstance()->publishFeeIsPaid($item['pk_i_id'])) {
                $category_fee_pub = ModelPaymentPro::newInstance()->getPublishPrice($item['fk_i_category_id']);
                if($category_fee_pub > 0) {
                    payment_pro_cart_add('PUB' . $item['fk_i_category_id'] . '-' . $item['pk_i_id'], sprintf(__('Publish fee for listing %d', 'payment_pro'), $item['pk_i_id']), $category_fee_pub);
                }
            }

            echo json_encode(array('error' => 0, 'msg' => __('Product added to your cart', 'payment_pro')));
            die;
        }
    }

    echo json_encode(array('error' => 1, 'msg' => __('This listing does not belong to you', 'payment_pro')));
    die;
}

if(Params::getParam('pub')!='') {
    $pub = Params::getParam('pub');
    if(!ModelPaymentPro::newInstance()->isEnabled($pub)) {
        echo json_encode(array('error' => 1, 'msg' => __('This listing is not enabled yet', 'payment_pro')));
        die;
    }
    if($pub!=Params::getParam('prm')) {
        $item = Item::newInstance()->findByPrimaryKey($pub);
    }
    if($item['fk_i_user_id']==null || ($item['fk_i_user_id']!=null && $item['fk_i_user_id']==osc_logged_user_id())) {
        $category_fee = ModelPaymentPro::newInstance()->getPublishPrice($item['fk_i_category_id']);
        if($category_fee > 0) {
            payment_pro_cart_add('PUB' . $item['fk_i_category_id'] . '-' . $item['pk_i_id'], sprintf(__('Publish fee for listing %d', 'payment_pro'), $item['pk_i_id']), $category_fee);
            echo json_encode(array('error' => 0, 'msg' => __('Product added to your cart', 'payment_pro')));
            die;
        }
    }

    echo json_encode(array('error' => 1, 'msg' => __('This listing does not belong to you', 'payment_pro')));
    die;
}