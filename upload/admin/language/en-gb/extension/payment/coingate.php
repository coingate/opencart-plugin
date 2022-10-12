<?php

$_['heading_title'] = 'CoinGate Payment Gateway';

$_['text_extension'] = 'Extensions';
$_['text_success'] = 'CoinGate Payment Gateway details have been successfully updated.';
$_['text_test_mode_on'] = 'On';
$_['text_test_mode_off'] = 'Off';

$_['entry_status'] = 'Payment Method Enabled';
$_['entry_test_mode'] = 'Test Mode';
$_['entry_api_auth_token'] = 'API Auth Token';
$_['entry_receive_currency'] = 'Receive Currency';
$_['entry_total'] = 'Total';
$_['entry_geo_zone'] = 'Geo Zone';
$_['entry_sort_order'] = 'Sort Order';
$_['entry_order_status'] = 'Order Status';
$_['entry_pending_status'] = 'Pending Status';
$_['entry_confirming_status'] = 'Confirming Status';
$_['entry_paid_status'] = 'Paid Status';
$_['entry_invalid_status'] = 'Invalid Status';
$_['entry_expired_status'] = 'Expired Status';
$_['entry_canceled_status'] = 'Canceled Status';
$_['entry_refunded_status'] = 'Refunded Status';
$_['entry_sort_order'] = 'Sort Order';
$_['entry_prefill_coingate_invoice_email'] = 'Prefill CoinGate Invoice Email';

$_['select_do_not_convert'] = "Do not convert";

$_['help_prefill_coingate_invoice_email'] = 'When enabled, the customer\'s email will be passed to CoinGate\'s checkout form automatically. <br><br> Email will be used to contact customers by the CoinGate team if any payment issues arise.';
$_['help_total'] = 'The checkout total the order must reach before this payment method becomes active.';
$_['help_receive_currency'] = 'Currency you want to receive when making withdrawal at CoinGate. Please take a note what if you choose EUR or USD you will be asked to verify your business before making a withdrawal at CoinGate.';
$_['help_test_mode'] = 'Enable "test mode" to test on sandbox.coingate.com. Please note, that for "Test" mode (sandbox) you must generate separate API credentials on sandbox.coingate.com. API credentials generated on coingate.com will not work for "Test" mode.';

$_['error_permission'] = 'Warning: You do not have permission to modify CoinGate!';
$_['error_composer'] = 'Unable to load coingate-php. Please download a compiled vendor folder or run composer.';
$_['error_connection'] = 'Error: Could not connect to CoinGate. Please check your API token and test mode settings.';

$_['text_coingate'] = '<a href="https://coingate.com/" target="_blank" rel="noopener"><img src="view/image/payment/coingate.png" alt="CoinGate" title="CoinGate" style="border: 1px solid #EEEEEE;" /></a>';
