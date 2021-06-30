<?php

class ModelExtensionPaymentCoingate extends Model {
  public function install() {
    // Modify the currency table to allow 4-character currency code
    $this->db->query("
      ALTER TABLE `" . DB_PREFIX . "currency`
      MODIFY `code` VARCHAR(4) COLLATE utf8_general_ci NOT NULL;
    ");
  }
}
