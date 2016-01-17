<?php

    class CcavenuePayment implements iPayment
    {

        public function __construct() { }

        public static function button($products, $extra = null) {

            $Amount = 0;
            foreach($products as $p) {
                $Amount += $p['amount']*$p['quantity'];
            }

            $r = rand(0,1000);
            $extra['random'] = $r;
            $extra['items'] = $products;
            $extra['amount'] = $Amount;
            $extra = payment_pro_set_custom($extra);

            $tx_id = ModelPaymentPro::newInstance()->pendingInvoice($products);

            $Merchant_Id   = osc_get_preference('ccavenue_merchant_id', 'payment_pro');
            $Order_Id         = $tx_id;  // use order id/invoice id instead of product_id
            $WorkingKey    = osc_get_preference('ccavenue_working_key', 'payment_pro');
            $Redirect_Url   = osc_route_url('ccavenue-redirect');
            $Checksum      = self::_getCheckSum($Merchant_Id,$Amount,$Order_Id ,$Redirect_Url,$WorkingKey);

            ?>
            <li class="payment ccavenue-btn">
                <form id="ccavenue_<?php echo $r; ?>" name="paymentform" method="post" action="https://www.ccavenue.com/shopzone/cc_details.jsp">
                    <input type="hidden" name="Merchant_Id" value="<?php echo $Merchant_Id; ?>">
                    <input type="hidden" name="Amount" value="<?php echo $Amount; ?>">
                    <input type="hidden" name="Order_Id" value="<?php echo $Order_Id; ?>">
                    <input type="hidden" name="Redirect_Url" value="<?php echo $Redirect_Url; ?>">
                    <input type="hidden" name="Checksum" value="<?php echo $Checksum; ?>">
                    <input type="hidden" name="Merchant_Param" value="<?php echo $extra; ?>">
                </form>
                <a id="button-confirm" class="button" onclick="$('#ccavenue_<?php echo $r; ?>').submit();"><span><img  style="cursor:pointer;cursor:hand" src='<?php echo PAYMENT_PRO_URL; ?>payments/ccavenue/ccavenue.gif' border='0' /></span></a>
            </li>
            <?php
        }

        public static function recurringButton($subscription, $extra = null) {}

        public static function processPayment()
        {
            $working_key   = osc_get_preference('ccavenue_working_key', 'payment_pro');
            $Merchant_Id   = Params::getParam('Merchant_Id');
            $Amount          = Params::getParam('Amount');
            $Order_Id         = Params::getParam('Order_Id');
            $Checksum      = Params::getParam('Checksum');
            $AuthDesc       = Params::getParam('AuthDesc');
            $extra               = Params::getParam('Merchant_Param');

            $verify = self::_verifyCheckSum($Merchant_Id, $Order_Id, $Amount, $AuthDesc, $Checksum, $working_key);

            $data                = payment_pro_get_custom($extra);
            if(empty($data['items']) || !$verify || $AuthDesc == 'N') {
                return PAYMENT_PRO_FAILED;
            }
            $status   = payment_pro_check_items($data['items'], $Amount);

            if ($AuthDesc == "B") {
                return PAYMENT_PRO_PENDING;
            }

           $invoiceId = ModelPaymentPro::newInstance()->saveInvoice(
                $Order_Id, // transaction code
                $Amount, //amount
                $status,
                osc_get_preference("currency", 'payment_pro'), //currency
                $data['email'], // payer's email
                $data['user'], //user
                'CCAVENUE',
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
            }
            return PAYMENT_PRO_COMPLETED;
        }

        private static function _getchecksum($MerchantId,$Amount,$OrderId ,$URL,$WorkingKey) {
            $str ="$MerchantId|$OrderId|$Amount|$URL|$WorkingKey";
            $adler = 1;
            $adler = self::_adler32($adler,$str);
            return $adler;
        }

        private static function _verifychecksum($MerchantId,$OrderId,$Amount,$AuthDesc,$CheckSum,$WorkingKey) {
            $str = "$MerchantId|$OrderId|$Amount|$AuthDesc|$WorkingKey";
            $adler = 1;
            $adler = self::_adler32($adler,$str);
            if($adler == $CheckSum) {
                return true;
            } else {
                return false;
            }
        }

        //functions
        private static function _adler32($adler, $str) {
            $BASE = 65521;
            $s1 = $adler & 0xffff;
            $s2 = ($adler >> 16) & 0xffff;
            for ($i = 0; $i < strlen($str); $i++) {
                $s1 = ($s1 + Ord($str[$i])) % $BASE;
                $s2 = ($s2 + $s1) % $BASE;
            }
            return self::_leftshift($s2, 16) + $s1;
        }

        //leftshift function
        private static function _leftshift($str, $num) {
            $str = DecBin($str);
            for ($i = 0; $i < (64 - strlen($str)); $i++) {
                $str = "0" . $str;
            }
            for ($i = 0; $i < $num; $i++) {
                $str = $str . "0";
                $str = substr($str, 1);
            }
            return self::_cdec($str);
        }
        //cdec function
        private static function _cdec($num) {
            $dec = 0;
            for ($n = 0 ; $n < strlen($num) ; $n++)  {
                $temp = $num[$n] ;
                $dec = $dec + $temp*pow(2 , strlen($num) - $n - 1);
            }
            return $dec;
        }
}
