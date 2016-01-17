<?php


class StripePayment implements iPayment
{

    public function __construct() { }

    public static function button($products, $extra = null) {
        if(count($products)==1) {
            $p = current($products);
            $amount = $p['amount']*$p['quantity'];
            $description = $p['description'];
            $product_id = $p['id'];
            //$ids = array(array('id' => $product_id));
        } else {
            $amount = 0;
            //$ids = array();
            foreach($products as $p) {
                $amount += $p['amount']*$p['quantity'];
                //$ids[] = array('id' => $p['id']);
            }
            $description = sprintf(__('%d products', 'payment_pro'), count($products));
            $product_id = 'SVR_PRD';
        }
        $r = rand(0,1000);
        $extra['random'] = $r;
        //$extra['ids'] = $ids;
        $extra['items'] = $products;
        $extra['amount'] = $amount;
        $extra = payment_pro_set_custom($extra);



        echo '<li style="cursor:pointer;cursor:hand" class="payment stripe-btn" onclick="javascript:stripe_pay(\''.$amount.'\',\''.$description.'\',\''.$product_id.'\',\''.$extra.'\');" ><img src="'.PAYMENT_PRO_URL . 'payments/stripe/pay_with_card.png" ></li>';
    }

    public static function dialogJS() { ?>
        <div id="stripe-dialog" title="<?php _e('Stripe', 'payment_pro'); ?>" style="display: none;"><span id="stripe-dialog-text"></span></div>
        <form action="<?php echo osc_base_url(true); ?>" method="post" id="stripe-payment-form" class="nocsrf" >
            <input type="hidden" name="page" value="ajax" />
            <input type="hidden" name="action" value="runhook" />
            <input type="hidden" name="hook" value="stripe" />
            <input type="hidden" name="extra" value="" id="stripe-extra" />
        </form>
        <script type="text/javascript">
            function stripe_pay(amount, description, product_id, extra) {
                var token = function(res){
                    var $input = $('<input type=hidden name=stripeToken />').val(res.id);
                    $('#stripe-extra').attr('value', extra);
                    $('#stripe-payment-form').append($input);
                    $.ajax({
                        type: "POST",
                        url: '<?php echo osc_base_url(true); ?>',
                        data: $("#stripe-payment-form").serialize(),
                        success: function(data)
                        {
                            $('#stripe-dialog-text').html(data);
                        }
                    });
                    setTimeout(openStripeDialog, 150);
                };


                StripeCheckout.open({
                    key:         '<?php echo payment_pro_decrypt(osc_get_preference('stripe_sandbox', 'payment_pro')?osc_get_preference('stripe_public_key_test', 'payment_pro'):osc_get_preference('stripe_public_key', 'payment_pro')); ?>',
                    address:     false,
                    amount:      (amount*100),
                    currency:    '<?php echo strtoupper(osc_get_preference("currency", 'payment_pro'));?>',
                    name:        description,
                    description: product_id,
                    panelLabel:  'Checkout',
                    token:       token
                });


                return false;
            };

            function openStripeDialog() {
                $('#stripe-dialog-text').html('<?php echo osc_esc_js(__("Please wait a moment while we're processing your payment", 'payment_pro')); ?>');
                $('#stripe-dialog').dialog('open')
            }

            $(document).ready(function(){
                $("#stripe-dialog").dialog({
                    autoOpen: false,
                    modal: true
                });
            });

        </script>

    <?php
    }

    public static function recurringButton($subscription, $extra = null) {}

    public static  function ajaxPayment() {
        $status = self::processPayment();

        if ($status==PAYMENT_PRO_COMPLETED) {
            payment_pro_cart_drop();
            osc_add_flash_ok_message(sprintf(__('Success! Please write down this transaction ID in case you have any problem: %s', 'payment_pro'), Params::getParam('stripe_transaction_id')));
        } else {
            if ($status==PAYMENT_PRO_ALREADY_PAID) {
                payment_pro_cart_drop();
                osc_add_flash_warning_message(__('Warning! This payment was already paid', 'payment_pro'));
            } else {
                osc_add_flash_error_message(__('There were an error processing your payment', 'payment_pro'));
            }
        }
        payment_pro_js_redirect_to(osc_route_url('payment-pro-done'), array('tx' => Params::getParam('stripe_transaction_id')));
    }

    public static function processPayment() {
        require_once dirname(__FILE__) . '/lib/Stripe.php';

        if(osc_get_preference('stripe_sandbox', 'payment_pro')==0) {
            $stripe = array(
                "secret_key"      => payment_pro_decrypt(osc_get_preference('stripe_secret_key', 'payment_pro')),
                "publishable_key" => payment_pro_decrypt(osc_get_preference('stripe_public_key', 'payment_pro'))
            );
        } else {
            $stripe = array(
                "secret_key"      => payment_pro_decrypt(osc_get_preference('stripe_secret_key_test', 'payment_pro')),
                "publishable_key" => payment_pro_decrypt(osc_get_preference('stripe_public_key_test', 'payment_pro'))
            );
        }

        Stripe::setApiKey($stripe['secret_key']);

        $token  = Params::getParam('stripeToken');
        $data = payment_pro_get_custom(Params::getParam('extra'));

        if(!isset($data['items']) || !isset($data['amount']) || $data['amount']<=0) {
            return PAYMENT_PRO_FAILED;
        }
        $status = payment_pro_check_items($data['items'], $data['amount']);

        $customer = Stripe_Customer::create(array(
            'email' => $data['email'],
            'card'  => $token
        ));

        try {
            $charge = @Stripe_Charge::create(array(
                'customer' => $customer->id,
                'amount'   => $data['amount']*100,
                'currency' => strtoupper(osc_get_preference("currency", 'payment_pro'))
            ));

            if($charge->__get('paid')==1) {

                $exists = ModelPaymentPro::newInstance()->getPaymentByCode($charge->__get('id'), 'STRIPE', PAYMENT_PRO_COMPLETED);
                if(isset($exists['pk_i_id'])) { return PAYMENT_PRO_ALREADY_PAID; }
                Params::setParam('stripe_transaction_id', $charge->__get('id'));
                // SAVE TRANSACTION LOG
                $invoiceId = ModelPaymentPro::newInstance()->saveInvoice(
                    $charge->__get('id'),
                    $charge->__get('amount')/100, //amount
                    $status,
                    strtoupper($charge->__get('currency')), //currency
                    @$data['email'],
                    @$data['user'], //user
                    'STRIPE',
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
            return PAYMENT_PRO_FAILED;
        } catch(Stripe_CardError $e) {
            return PAYMENT_PRO_FAILED;
        }

        return PAYMENT_PRO_FAILED;

    }

}

?>