<?php

class ModelPaymentCoingate extends Model
{
    public function getMethod()
    {
        $this->load->language('payment/coingate');

        if ($this->config->get('coingate_status'))
            $status = TRUE;
        else
            $status = FALSE;

        $method_data = [];

        if ($status) {
            $method_data = [
                'code'       => 'coingate',
                'title'      => $this->language->get('text_title'),
                'terms'      => '',
                'sort_order' => $this->config->get('coingate_sort_order')

            ];
        }

        return $method_data;
    }
}



