<?php

class ModelExtensionPaymentCoingate extends Model {
  public function install() {
    $this->db->query("
      CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "coingate_order` (
        `coingate_order_id` INT(11) NOT NULL AUTO_INCREMENT,
        `order_id` INT(11) NOT NULL,
        `cg_invoice_id` VARCHAR(120),
        `token` VARCHAR(100) NOT NULL,
        PRIMARY KEY (`coingate_order_id`)
      ) ENGINE=MyISAM DEFAULT COLLATE=utf8_general_ci;
    ");

    // Modify the currency table to allow 4-character currency code
    $query = $this->db->query("
    SELECT character_maximum_length
    FROM information_schema.columns
    WHERE table_name = '" . DB_PREFIX . "currency' and column_name = 'code';
    ");
    if ((int)$query->row['character_maximum_length'] < 4) {
      $this->db->query("
        ALTER TABLE `" . DB_PREFIX . "currency`
        MODIFY `code` VARCHAR(4) COLLATE utf8_general_ci NOT NULL;
      ");
    }

    $this->load->model('setting/setting');

    $defaults = array();

    $defaults['payment_coingate_test_mode'] = 0;
    $defaults['payment_coingate_order_status_id'] = 1;
    $defaults['payment_coingate_pending_status_id'] = 1;
    $defaults['payment_coingate_confirming_status_id'] = 1;
    $defaults['payment_coingate_paid_status_id'] = 2;
    $defaults['payment_coingate_invalid_status_id'] = 10;
    $defaults['payment_coingate_expired_status_id'] = 14;
    $defaults['payment_coingate_canceled_status_id'] = 7;
    $defaults['payment_coingate_refunded_status_id'] = 11;
    $defaults['payment_coingate_receive_currency'] = "BTC";
    $defaults['payment_coingate_sort_order'] = 0;

    $this->model_setting_setting->editSetting('payment_coingate', $defaults);
  }

  public function uninstall() {
    $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "coingate_order`;");
  }
}
