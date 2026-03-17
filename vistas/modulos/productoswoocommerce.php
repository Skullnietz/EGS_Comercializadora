<?php
require __DIR__ . '/vendor/autoload.php';

use Automattic\WooCommerce\Client;

$woocommerce = new Client(
    'http://comercializadoraegs.com/', 
    'ck_b451747031ddc7386b837b8fd7ab55428af21cbf', 
    'cs_49607187b50a9b2095d52da6efffc5bba9a4f91b',
    [
        'version' => 'wc/v3',
    ]
);

print_r($woocommerce->get('orders'));