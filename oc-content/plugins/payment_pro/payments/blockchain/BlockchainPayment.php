<?php

    class BlockchainPayment implements iPayment
    {

        public function __construct()
        {
        }

        public static function button($products, $extra = null) {

            $items = array();
            $amount = 0;
            foreach($products as $p) {
                $amount += $p['amount']*$p['quantity'];
            }

            if(osc_get_preference('currency', 'payment_pro')!='BTC') {
                $amount = osc_file_get_contents("https://blockchain.info/tobtc?currency=".osc_get_preference('currency', 'payment_pro')."&value=".$amount);
                $xrate = osc_file_get_contents("https://blockchain.info/tobtc?currency=".osc_get_preference('currency', 'payment_pro')."&value=1");
                if(is_numeric($xrate)) {
                    $extra['xrate'] = $xrate;
                    osc_set_preference('blockchain_xrate', $xrate, 'payment_pro');
                } else {
                    osc_get_preference('blockchain_xrate', 'payment_pro');
                }
            } else {
                $extra['xrate'] = 1;
            }

            $tx_id = ModelPaymentPro::newInstance()->pendingInvoice($products);

            $r = rand(0,1000);
            $extra['random'] = $r;
            $extra['tx'] = $tx_id;
            $extra = payment_pro_set_custom($extra);
            ?>
            <li class="payment bitcoin-btn">
            <div class="blockchain-btn"
            data-address="<?php echo osc_get_preference('blockchain_btc_address', 'payment_pro'); ?>"
            data-anonymous="false"
            data-callback="<?php echo osc_route_url('blockchain-notify', array('extra' => str_replace("+", "@", $extra))); ?>">
                <div  style="cursor:pointer;cursor:hand" class="blockchain stage-begin">
                    <img src="<?php echo PAYMENT_PRO_URL; ?>payments/blockchain/pay_now_64.png">
                </div>
                <div class="blockchain stage-loading" style="text-align:center">
                    <img src="<?php echo PAYMENT_PRO_URL; ?>payments/blockchain/loading-large.gif">
                </div>
                <div class="blockchain stage-ready">
                    <p align="center"><?php printf(__('Please send %f BTC to <br /> <b>[[address]]</b></p>', 'payment_pro'), $amount); ?>
                    <p align="center" class="qr-code"></p>
                </div>
                <div class="blockchain stage-paid">
                    <p><?php _e('Payment Received <b>[[value]] BTC</b>. Thank You.', 'payment_pro'); ?></p>
                    <a href="<?php echo osc_route_url('payment-pro-done', array('tx' => $tx_id)); ?>"><?php _e('Click here to continue', 'payment_pro'); ?></a>
                </div>
                <div class="blockchain stage-error">
                    <span color="red">[[error]]</span>
                </div>
            </div>
            </li>
        <?php
        }

        public static function recurringButton($subscription, $extra = null) {}

        public static function processPayment() {

            if(Params::getParam('test')==true) {
                return PAYMENT_PRO_FAILED;
            }
            $extra = explode("?", Params::getParam('extra'));
            $data = payment_pro_get_custom(str_replace("@", "+", $extra[0]));
            unset($extra);
            $data['items'] = ModelPaymentPro::newInstance()->getPending(@$data['tx']);
            $transaction_hash = Params::getParam('transaction_hash');
            $value_in_btc = Params::getParam('value')/100000000;
            $my_bitcoin_address = osc_get_preference('blockchain_btc_address', 'payment_pro');
            if(empty($data['items'])) {
                return PAYMENT_PRO_FAILED;
            }
            if(osc_get_preference('currency', 'payment_pro')=='BTC') {
                $status = payment_pro_check_items($data['items'], $value_in_btc);
            } else {
                $status = payment_pro_check_items_blockchain($data['items'], $value_in_btc, $data['xrate']);
            }
            if (Params::getParam('address')!=$my_bitcoin_address) {
                return PAYMENT_PRO_FAILED;
            }

            foreach($data['items'] as $k => $v) {
                $data['items'][$k]['amount'] = $v['amount']/1000000;
            }

            $hosts = gethostbynamel('blockchain.info');
            foreach ($hosts as $ip) {
                // Check payment came from one of blockchain.info's IP
                if ($_SERVER['REMOTE_ADDR']==$ip) {
                    $exists = ModelPaymentPro::newInstance()->getPaymentByCode($transaction_hash, 'BLOCKCHAIN', PAYMENT_PRO_COMPLETED);
                    if(isset($exists['pk_i_id'])) { return PAYMENT_PRO_ALREADY_PAID; }
                    if ((is_numeric(Params::getParam('confirmations')) && Params::getParam('confirmations')>=osc_get_preference('blockchain_confirmations', 'payment_pro')) || Params::getParam('anonymous')== true) {
                        // SAVE TRANSACTION LOG
                        $invoiceId = ModelPaymentPro::newInstance()->saveInvoice(
                            $transaction_hash, // transaction code
                            $value_in_btc, //amount
                            $status,
                            'BTC', //currency
                            $data['email'], // payer's email
                            $data['user'], //user
                            'BLOCKCHAIN',
                            $data['items']
                        );

                        if($status==PAYMENT_PRO_COMPLETED) {
                            foreach($data['items'] as $item) {
                                if (substr($item['id'], 0, 3) == 'PUB') {
                                    $tmp = explode("-", $item['id']);
                                    ModelPaymentPro::newInstance()->payPublishFee($tmp[count($tmp)-1], $invoiceId);
                                } else if (substr($item['id'], 0, 3) == 'PRM') {
                                    $tmp = explode("-", $item['id']);
                                    ModelPaymentPro::newInstance()->payPremiumFee($tmp[count($tmp)-1], $invoiceId);
                                } else if (substr($item['id'], 0, 3) == 'WLT') {
                                    ModelPaymentPro::newInstance()->addWallet($data['user'], $item['amount']);
                                } else {
                                    osc_run_hook('payment_pro_item_paid', $item);
                                }
                            }
                            ModelPaymentPro::newInstance()->deletePending($data['tx']);
                        }

                        return PAYMENT_PRO_COMPLETED;
                    } else {
                        // Maybe we could do something here (the payment was correct, but it didn't get enought confirmations yet)
                        return PAYMENT_PRO_PENDING;
                    }
                    break;
                }
            }
            return $status = PAYMENT_PRO_FAILED;
        }

    }

?>
