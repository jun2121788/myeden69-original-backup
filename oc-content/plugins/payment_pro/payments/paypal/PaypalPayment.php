<?php

    class PaypalPayment implements iPayment
    {

        public function __construct()
        {
        }

        public static function button($products, $extra = null) {

            echo '<li class="payment paypal-btn">';

            self::standardButton($products, $extra);

            echo '</li>';
        }



        public static function standardButton($products, $extra = null) {
            $r = rand(0,1000);
            $extra['random'] = $r;
            $extra = payment_pro_set_custom($extra);

            if(osc_get_preference('paypal_sandbox', 'payment_pro')==1) {
                $ENDPOINT     = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
            } else {
                $ENDPOINT     = 'https://www.paypal.com/cgi-bin/webscr';
            }

            ?>


            <form class="nocsrf" action="<?php echo $ENDPOINT; ?>" method="post" id="paypal_<?php echo $r; ?>">
                <input type="hidden" name="cmd" value="_cart" />
                <input type="hidden" name="notify_url" value="<?php echo osc_route_url('paypal-notify', array('extra' => $extra)); ?>" />
                <input type="hidden" name="return" value="<?php echo osc_route_url('paypal-return', array('extra' => $extra)); ?>" />
                <input type="hidden" name="cancel_return" value="<?php echo osc_route_url('paypal-cancel', array('extra' => $extra)); ?>" />
                <input type="hidden" name="business" value="<?php echo osc_get_preference('paypal_email', 'payment_pro'); ?>" />
                <input type="hidden" name="upload" value="1" />
                <input type="hidden" name="paymentaction" value="sale" />

                <?php $i = 1; foreach($products as $p) { ?>
                    <input type="hidden" name="amount_<?php echo $i; ?>" value="<?php echo $p['amount']; ?>" />
                    <input type="hidden" name="item_name_<?php echo $i; ?>" value="<?php echo $p['description']; ?>" />
                    <input type="hidden" name="item_number_<?php echo $i; ?>" value="<?php echo $p['id']; ?>" />
                    <input type="hidden" name="quantity_<?php echo $i; ?>" value="<?php echo $p['quantity']; ?>" />
                    <?php $i++; } ?>

                <input type="hidden" name="currency_code" value="<?php echo osc_get_preference('currency', 'payment_pro'); ?>" />
                <input type="hidden" name="custom" value="<?php echo $extra; ?>" />
                <input type="hidden" name="rm" value="2" />
                <input type="hidden" name="upload" value="1" />
                <input type="hidden" name="no_note" value="1" />
                <input type="hidden" name="charset" value="utf-8" />
            </form>
            <div class="buttons">
                <div class="right"><a style="cursor:pointer;cursor:hand" id="button-confirm" class="button" onclick="$('#paypal_<?php echo $r; ?>').submit();"><span><img src='<?php echo PAYMENT_PRO_URL; ?>payments/paypal/paypal.gif' border='0' /></span></a></div>
            </div>
        <?php
        }

        public static function recurringButton($subscription, $extra = null) {
            $r = rand(0,1000);
            $extra['random'] = $r;
            $extra = payment_pro_set_custom($extra);

            if(osc_get_preference('paypal_sandbox', 'payment_pro')==1) {
                $ENDPOINT     = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
            } else {
                $ENDPOINT     = 'https://www.paypal.com/cgi-bin/webscr';
            }

            ?>


            <form class="nocsrf" action="<?php echo $ENDPOINT; ?>" method="post" id="paypal_<?php echo $r; ?>">
                <input type="hidden" name="cmd" value="_xclick-subscriptions" />
                <input type="hidden" name="notify_url" value="<?php echo osc_route_url('paypal-notify', array('extra' => $extra)); ?>" />
                <input type="hidden" name="return" value="<?php echo osc_route_url('paypal-return', array('extra' => $extra)); ?>" />
                <input type="hidden" name="cancel_return" value="<?php echo osc_route_url('paypal-cancel', array('extra' => $extra)); ?>" />
                <input type="hidden" name="business" value="<?php echo osc_get_preference('paypal_email', 'payment_pro'); ?>" />

                <input type="hidden" name="item_name" value="<?php echo $subscription['description']; ?>" />
                <input type="hidden" name="a3" value="<?php echo $subscription['amount']; ?>" />
                <input type="hidden" name="p3" value="<?php echo $subscription['duration']; ?>" />
                <input type="hidden" name="t3" value="<?php echo $subscription['period']; ?>" />

                <input type="hidden" name="src" value="1" />

                <input type="hidden" name="currency_code" value="<?php echo osc_get_preference('currency', 'payment_pro'); ?>" />
                <input type="hidden" name="custom" value="<?php echo $extra; ?>" />
                <input type="hidden" name="no_note" value="1" />
                <input type="hidden" name="charset" value="utf-8" />
            </form>
            <div class="buttons">
                <div class="right"><a style="cursor:pointer;cursor:hand" id="button-confirm" class="button" onclick="$('#paypal_<?php echo $r; ?>').submit();"><span><img src='<?php echo PAYMENT_PRO_URL; ?>payments/paypal/subscription.gif' border='0' /></span></a></div>
            </div>
        <?php
        }


        public static function processPayment() {
            return self::processStandardPayment();
        }

        public static function processStandardPayment() {
            if (Params::getParam('payment_status') == 'Completed' || Params::getParam('st') == 'Completed') {
                // Have we processed the payment already?
                $tx = Params::getParam('tx')!=''?Params::getParam('tx'):Params::getParam('txn_id');
                $payment = ModelPaymentPro::newInstance()->getPaymentByCode($tx, 'PAYPAL', PAYMENT_PRO_COMPLETED);
                if (!isset($payment['pk_i_id'])) {

                    if(Params::getParam('cm')!='') {
                        $data = Params::getParam('cm');
                    } else if(Params::getParam('custom')!='') {
                        $data = Params::getParam('custom');
                    } else {
                        $data = Params::getParam('extra');
                    }
                    $data = payment_pro_get_custom($data);

                    $items = array();
                    $num_items = (int)Params::getParam('num_cart_items');
                    for($i=1;$i<=$num_items;$i++) {
                        $id = Params::getParam('item_number' . $i);
                        $tmp = explode("-", $id);
                        $items[] = array(
                            'id' => $tmp[0],
                            'description' => Params::getParam('item_name' . $i),
                            'amount' => Params::getParam('mc_gross_' . $i),
                            'quantity' => Params::getParam('quantity' . $i),
                            'item_id' => $tmp[1]
                            );
                    }

                    $total_amount = Params::getParam('payment_gross')!=''?Params::getParam('payment_gross'):Params::getParam('mc_gross');
                    $status = payment_pro_check_items($items, $total_amount);

                    $product_type = explode('x', Params::getParam('item_number'));
                    // SAVE TRANSACTION LOG
                    $invoiceId = ModelPaymentPro::newInstance()->saveInvoice(
                        $tx,
                        $total_amount, //amount
                        $status,
                        Params::getParam('mc_currency'), //currency
                        Params::getParam('payer_email')!=''?Params::getParam('payer_email'):'', // payer's email
                        @$data['user'], //user
                        'PAYPAL',
                        $items
                        );
                    if($status==PAYMENT_PRO_COMPLETED) {
                        foreach($items as $item) {
                            if (substr($item['id'], 0, 3) == 'PUB') {
                                ModelPaymentPro::newInstance()->payPublishFee($item['item_id'], $invoiceId);
                            } else if (substr($item['id'], 0, 3) == 'PRM') {
                                ModelPaymentPro::newInstance()->payPremiumFee($item['item_id'], $invoiceId);
                            } else if (substr($item['id'], 0, 3) == 'WLT') {
                                ModelPaymentPro::newInstance()->addWallet($data['user'], $item['amount']);
                            } else {
                                osc_run_hook('payment_pro_item_paid', $item);
                            }
                        }
                    }
                    return PAYMENT_PRO_COMPLETED;
                }
                return PAYMENT_PRO_ALREADY_PAID;
            }
            return PAYMENT_PRO_PENDING;
        }



        //Makes an API call using an NVP String and an Endpoint
        public static function httpPost($my_endpoint, $my_api_str) {
            // setting the curl parameters.
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $my_endpoint);
            curl_setopt($ch, CURLOPT_VERBOSE, 1);
            // turning off the server and peer verification(TrustManager Concept).
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            // setting the NVP $my_api_str as POST FIELD to curl
            curl_setopt($ch, CURLOPT_POSTFIELDS, $my_api_str);
            // getting response from server
            $httpResponse = curl_exec($ch);
            if (!$httpResponse) {
                $response = "API failed: " . curl_error($ch) . '(' . curl_errno($ch) . ')';
                return $response;
            }
            $httpResponseAr = explode("&", $httpResponse);
            $httpParsedResponseAr = array();
            foreach ($httpResponseAr as $i => $value) {
                $tmpAr = explode("=", $value);
                if (sizeof($tmpAr) > 1) {
                    $httpParsedResponseAr[$tmpAr[0]] = $tmpAr[1];
                }
            }

            if ((0 == sizeof($httpParsedResponseAr)) || !array_key_exists('ACK', $httpParsedResponseAr)) {
                $response = "Invalid HTTP Response for POST request($my_api_str) to $API_Endpoint.";
                return $response;
            }

            return $httpParsedResponseAr;
        }

    }
