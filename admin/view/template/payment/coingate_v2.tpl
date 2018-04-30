<?php echo $header; ?>

<?php echo $column_left; ?>

<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <button type="submit" form="form-coingate" data-toggle="tooltip" title="<?php echo $button_save; ?>"
                        class="btn btn-primary"><i class="fa fa-save"></i></button>
                <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>"
                   class="btn btn-default"><i class="fa fa-reply"></i></a></div>
            <h1><?php echo $heading_title; ?></h1>
            <ul class="breadcrumb">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
                <?php } ?>
            </ul>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $edit_text; ?></h3>
        </div>

        <div class="panel-body">
            <div class="alert alert-info">
                Plugin not working? View <a href="https://developer.coingate.com/docs/issues" target="_blank">common issues</a> or contact <a href="mailto:support@coingate.com">support@coingate.com</a>
            </div>

            <?php if(in_array($_SERVER['REMOTE_ADDR'], array('127.0.0.1', '::1'))) { ?>
            <div class="row">
                <div class="col-sm-12">
                    <div class="alert alert-warning">
                        <i class="fa fa-exclamation-circle"></i>
                We have noticed that your website is hosted on <i>localhost</i>. Please note that we may
                not
                be able to send payment callback to localhost. You must make your website publicly accessible.
                    </div>
                </div>
            </div>
            <?php } ?>

            <?php if ($error_warning) { ?>
            <div class="row">
                <div class="col-sm-12">
                    <div class="alert alert-danger">
                        <i class="fa fa-exclamation-circle"></i>
                        <?php echo $error_warning; ?>
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                </div>
            </div>
            <?php } ?>


            <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-coingate"
                  class="form-horizontal">
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="coingate_status"><?php echo $status_label; ?></label>

                    <div class="col-sm-10">
                        <select name="coingate_status" class="form-control">
                            <?php if ($coingate_status) { ?>
                            <option value="1" selected="selected"><?php echo $status_on; ?></option>
                            <option value="0"><?php echo $status_off; ?></option>
                            <?php } else { ?>
                            <option value="1"><?php echo $status_on; ?></option>
                            <option value="0" selected="selected"><?php echo $status_off; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="form-group required">
                    <label class="col-sm-2 control-label" for="coingate_api_auth_token"><?php echo $api_auth_token_label; ?></label>

                    <div class="col-sm-10">
                        <input type="text" name="coingate_api_auth_token" value="<?php echo $coingate_api_auth_token; ?>"
                               class="form-control">

                        <?php if ($api_auth_token_error) { ?>
                        <div class="text-danger"><?php echo $api_auth_token_error; ?></div>
                        <?php } ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="coingate_test"><?php echo $test_label; ?></label>

                    <div class="col-sm-10">
                        <select name="coingate_test" class="form-control">
                            <?php if($coingate_test == 0): ?>
                            <option value="0" selected="selected"><?php echo $test_off; ?></option>
                            <option value="1"><?php echo $test_on; ?></option>
                            <?php else: ?>
                            <option value="0"><?php echo $test_off; ?></option>
                            <option value="1" selected="selected"><?php echo $test_on; ?></option>
                            <?php endif;?>
                        </select>

                        <div class="help-block">
                            Enable "test mode" to test on <a href="https://sandbox.coingate.com/" target="_blank">Sandbox</a>.
                            Please note that API credentials generated on coingate.com will not work for "Test"
                            mode. For "Test" mode you must create a separate account on <a href="https://sandbox.coingate.com/"
                            target="_blank">sandbox.coingate.com</a> and generate API credentials there.
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label"
                           for="coingate_receive_currency"><?php echo $receive_currency_label; ?></label>

                    <div class="col-sm-10">
                        <select name="coingate_receive_currency" class="form-control">
                            <?php foreach(array('eur', 'usd', 'btc') as $currency): ?>
                            <option value="<?php echo $currency; ?>"
                            <?php echo $currency == $coingate_receive_currency ? 'selected' : ''; ?>
                            ><?php echo $currencies_label[$currency]; ?></option>
                            <?php endforeach; ?>
                        </select>

                        <div class="help-block">
                            Choose the currency in which your payouts will be made (BTC, EUR or USD). For real-time EUR or USD settlements, 
                            you must verify as a merchant on CoinGate. Do not forget to add your Bitcoin address or bank details for payouts 
                            on <a href="https://coingate.com" target="_blank">your CoinGate account</a>.
                        </div>
                    </div>

                    <?php if ($receive_currency_error) { ?>
                    <div class="text-danger"><?php echo $receive_currency_error; ?></div>
                    <?php } ?>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label"
                           for="coingate_new_order_status_id"><?php echo $new_order_status_label; ?></label>

                    <div class="col-sm-10">
                        <select name="coingate_new_order_status_id" class="form-control">
                            <?php foreach ($order_statuses as $order_status) { ?>
                            <?php if ($order_status['order_status_id'] == $coingate_new_order_status_id) { ?>
                            <option value="<?php echo $order_status['order_status_id']; ?>"
                                    selected="selected"><?php echo $order_status['name']; ?></option>
                            <?php } else { ?>
                            <option
                                    value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                            <?php } ?>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label"
                           for="coingate_cancelled_order_status_id"><?php echo $cancelled_order_status_label; ?></label>

                    <div class="col-sm-10">
                        <select name="coingate_cancelled_order_status_id" class="form-control">
                            <?php foreach ($order_statuses as $order_status) { ?>
                            <?php if ($order_status['order_status_id'] == $coingate_cancelled_order_status_id) { ?>
                            <option value="<?php echo $order_status['order_status_id']; ?>"
                                    selected="selected"><?php echo $order_status['name']; ?></option>
                            <?php } else { ?>
                            <option
                                    value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                            <?php } ?>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label"
                           for="coingate_expired_order_status_id"><?php echo $expired_order_status_label; ?></label>

                    <div class="col-sm-10">
                        <select name="coingate_expired_order_status_id" class="form-control">
                            <?php foreach ($order_statuses as $order_status) { ?>
                            <?php if ($order_status['order_status_id'] == $coingate_expired_order_status_id) { ?>
                            <option value="<?php echo $order_status['order_status_id']; ?>"
                                    selected="selected"><?php echo $order_status['name']; ?></option>
                            <?php } else { ?>
                            <option
                                    value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                            <?php } ?>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label"
                           for="coingate_failed_order_status_id"><?php echo $failed_order_status_label; ?></label>

                    <div class="col-sm-10">
                        <select name="coingate_failed_order_status_id" class="form-control">
                            <?php foreach ($order_statuses as $order_status) { ?>
                            <?php if ($order_status['order_status_id'] == $coingate_failed_order_status_id) { ?>
                            <option value="<?php echo $order_status['order_status_id']; ?>"
                                    selected="selected"><?php echo $order_status['name']; ?></option>
                            <?php } else { ?>
                            <option
                                    value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                            <?php } ?>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label"
                           for="coingate_completed_order_status_id"><?php echo $completed_order_status_label; ?></label>

                    <div class="col-sm-10">
                        <select name="coingate_completed_order_status_id" class="form-control">
                            <?php foreach ($order_statuses as $order_status) { ?>
                            <?php if ($order_status['order_status_id'] == $coingate_completed_order_status_id) { ?>
                            <option value="<?php echo $order_status['order_status_id']; ?>"
                                    selected="selected"><?php echo $order_status['name']; ?></option>
                            <?php } else { ?>
                            <option
                                    value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                            <?php } ?>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label"
                           for="refunded_completed_order_status_id"><?php echo $refunded_order_status_label; ?></label>

                    <div class="col-sm-10">
                        <select name="coingate_refunded_order_status_id" class="form-control">
                            <?php foreach ($order_statuses as $order_status) { ?>
                            <?php if ($order_status['order_status_id'] == $coingate_refunded_order_status_id) { ?>
                            <option value="<?php echo $order_status['order_status_id']; ?>"
                                    selected="selected"><?php echo $order_status['name']; ?></option>
                            <?php } else { ?>
                            <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                            <?php } ?>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-total">
                        <span data-toggle="tooltip"
                              title="<?php echo $help_total; ?>"><?php echo $entry_total; ?></span>
                    </label>
                    <div class="col-sm-10">
                        <input type="text" name="coingate_total" value="<?php echo $coingate_total; ?>"
                               placeholder="<?php echo $entry_total; ?>" id="input-total" class="form-control"/>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-geo-zone"><?php echo $entry_geo_zone; ?></label>

                    <div class="col-sm-10">
                        <select name="coingate_geo_zone_id" id="input-geo-zone" class="form-control">
                            <option value="0"><?php echo $text_all_zones; ?></option>
                            <?php foreach ($geo_zones as $geo_zone) { ?>
                            <?php if ($geo_zone['geo_zone_id'] == $coingate_geo_zone_id) { ?>
                            <option value="<?php echo $geo_zone['geo_zone_id']; ?>"
                                    selected="selected"><?php echo $geo_zone['name']; ?></option>
                            <?php } else { ?>
                            <option value="<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $geo_zone['name']; ?></option>
                            <?php } ?>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label"><?php echo $sort_order_label; ?></label>

                    <div class="col-sm-10">
                        <input type="text" name="coingate_sort_order" value="<?php echo $coingate_sort_order; ?>"
                               class="form-control"/>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>
</div>

<?php echo $footer; ?>
