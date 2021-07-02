<?php

class ModelExtensionPaymentCoingate extends Model {
  public function install() {
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
  }
}
