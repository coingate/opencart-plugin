<?php

class ModelPaymentCoingate extends Model
{
    public function getMethod($address, $total)
    {
        $this->load->language('payment/coingate');

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('coingate_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

        if ($total <= 0 || ($this->config->get('coingate_total') > 0 && $this->config->get('coingate_total') > $total)) {
            $status = false;
        } elseif (!$this->config->get('coingate_geo_zone_id')) {
            $status = true;
        } elseif ($query->num_rows) {
            $status = true;
        } else {
            $status = false;
        }

        $method_data = array();

        if ($status) {
            $method_data = array(
                'code'       => 'coingate',
                'title'      => $this->language->get('text_title'),
                'terms'      => '',
                'sort_order' => $this->config->get('coingate_sort_order')

            );
        }

        return $method_data;
    }
}
