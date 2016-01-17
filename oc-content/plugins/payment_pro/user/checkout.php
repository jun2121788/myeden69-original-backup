<?php
require_once PAYMENT_PRO_PATH . 'CheckoutDataTable.php';

$products = payment_pro_cart_get();
$extra = array('user' => osc_logged_user_id(), 'email' => osc_logged_user_email());

$checkoutDataTable = new CheckoutDataTable();
$checkoutDataTable->table($products);
$aData = $checkoutDataTable->getData();

$aRawRows   = $checkoutDataTable->rawRows();
$columns    = $aData['aColumns'];
$rows       = $aData['aRows'];

?>
<style type="text/css">
    .payments-ul {
        list-style-type:none;
    }
    .payments-ul li
    {
        display: inline-block;
    }
    .payments-preview {
        float:left;
        width: 40%;
    }
    .payments-options {
        float:left;
        width: 60%;
    }
    table.table {
        width: 100%;
        max-width: 100%;
        border: 1px solid #dddddd;
    }
    table.table tr th,
    table.table tr td{
        vertical-align: top;
        border-top: 1px solid #ddd;
        padding:8px;
    }
    table.table tr:nth-child(odd) {
        background-color: #ffffff;
    }
    table.table tr:nth-child(even) {
        background-color: #f5f5f5;
    }
</style>
<div class="payments-pro-wrapper">
    <h1><?php _e('Checkout page', 'payment_pro'); ?></h1>
    <div class="table-contains-actions">
        <table class="table" cellpadding="0" cellspacing="0">
            <thead align="left">
            <tr>
                <?php foreach($columns as $k => $v) {
                    echo '<th class="col-'.$k.'">'.$v.'</th>';
                }; ?>
            </tr>
            </thead>
            <tbody>
            <?php if( count($rows) > 0 ) { ?>
                <?php foreach($rows as $key => $row) { ?>
                    <tr>
                        <?php foreach($row as $k => $v) { ?>
                            <td class="col-<?php echo $k; ?>"><?php echo $v; ?></td>
                        <?php }; ?>
                    </tr>
                <?php }; ?>
            <?php } else { ?>
                <tr>
                    <td colspan="6" class="text-center">
                        <p><?php _e('Your cart is empty', 'payment_pro'); ?></p>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
        <div id="table-row-actions">
            <p style="font-style: italic"><?php _e('Continue and pay with:', 'payment_pro'); ?></p>
            <ul class="payments-ul">
                <?php payment_pro_buttons($products, $extra); ?>
            </ul>
        </div>
    </div>
</div>
<?php osc_run_hook('payment_pro_checkout_footer'); ?>
