<?php

ob_get_clean();
$status = BlockchainPayment::processPayment();
if($status==PAYMENT_PRO_COMPLETED) {
    echo '*ok*';
}
die;
