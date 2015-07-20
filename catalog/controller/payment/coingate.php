<?php

require_once DIR_SYSTEM . 'library/vendor/coingate/coingate_merchant.class.php';

class ControllerPaymentCoingate extends Controller
{

    public function __construct($registry)
    {
        parent::__construct($registry);

        $this->load->language('payment/coingate');
    }

    public function index()
    {
        $data['button_confirm'] = $this->language->get('button_confirm');
        $data['button_back'] = $this->language->get('button_back');

        $data['confirm'] = $this->url->link('payment/coingate/confirm', '', $this->config->get('config_secure'));

        if ($this->request->get['route'] != 'checkout/guest/confirm') {
            $data['back'] = $this->url->link('checkout/payment', '', $this->config->get('config_secure'));
        } else {
            $data['back'] = $this->url->link('checkout/guest', '', $this->config->get('config_secure'));
        }

        return $this->load->view($this->get_view_path('coingate.tpl'), $data);
    }

    public function confirm()
    {
        $this->load->model('checkout/order');

        $order = $this->model_checkout_order->getOrder($this->session->data['order_id']);

        $coingate = new CoingateMerchant(array('app_id' => $this->config->get('coingate_app_id'), 'api_key' => $this->config->get('coingate_api_key'), 'api_secret' => $this->config->get('coingate_api_secret'), 'mode' => $this->config->get('coingate_test') == 1 ? 'sandbox' : 'live'));

        $token = $this->generate_token($order['order_id']);

        $description = array();
        foreach ($this->cart->getProducts() as $product) {
            $description[] = $product['quantity'] . ' × ' . $product['name'];
        }

        $coingate->create_order(array(
            'order_id'         => $order['order_id'],
            'price'            => number_format($order['total'], 2, '.', ''),
            'currency'         => $order['currency_code'],
            'receive_currency' => $this->config->get('coingate_receive_currency'),
            'cancel_url'       => $this->url->link('payment/coingate/cancel', '', $this->config->get('config_secure')),
            'callback_url'     => $this->url->link('payment/coingate/callback', '', $this->config->get('config_secure')) . '&cg_token=' . $token,
            'success_url'      => $this->url->link('payment/coingate/accept', '', $this->config->get('config_secure')),
            'title'            => $this->config->get('config_meta_title') . ' Order #' . $order['order_id'],
            'description'      => join($description, ', ')
        ));

        if ($coingate->success) {
            $this->model_checkout_order->addOrderHistory($order['order_id'], $this->config->get('coingate_new_order_status_id'));

            $coingate_response = json_decode($coingate->response, TRUE);

            $this->response->redirect($coingate_response['payment_url']);
        } else {
            $this->response->redirect($this->url->link('checkout/checkout', '', $this->config->get('config_secure')));
        }
    }

    public function accept()
    {
        if (isset($this->session->data['token'])) {
            $this->response->redirect($this->url->link('checkout/success', 'token=' . $this->session->data['token'], $this->config->get('config_secure')));
        } else {
            $this->response->redirect($this->url->link('checkout/success', '', $this->config->get('config_secure')));
        }
    }

    public function cancel()
    {
        $this->response->redirect($this->url->link('checkout/cart', '', $this->config->get('config_secure')));
    }

    public function callback()
    {
        $this->load->model('checkout/order');

        $order = $this->model_checkout_order->getOrder($_REQUEST['order_id']);

        try {
            if (!$order || !$order['order_id'])
                throw new Exception('Order #' . $_REQUEST['order_id'] . ' does not exists');

            $token = $this->generate_token($order['order_id']);

            if ($token == '' || $_GET['cg_token'] != $token)
                throw new Exception('Token: ' . $_GET['cg_token'] . ' do not match');

            $coingate = new CoingateMerchant(array('app_id' => $this->config->get('coingate_app_id'), 'api_key' => $this->config->get('coingate_api_key'), 'api_secret' => $this->config->get('coingate_api_secret'), 'mode' => $this->config->get('coingate_test') == 1 ? 'sandbox' : 'live'));
            $coingate->get_order($_REQUEST['id']);

            if (!$coingate->success)
                throw new Exception('CoinGate Order does not exists');

            $coingate_response = json_decode($coingate->response, TRUE);

            if (!is_array($coingate_response))
                throw new Exception('Something wrong with callback');

            if ($coingate_response['status'] == 'paid') {
                $this->model_checkout_order->addOrderHistory($order['order_id'], $this->config->get('coingate_completed_order_status_id'));
            } elseif ($coingate_response['status'] == 'canceled') {
                $this->model_checkout_order->addOrderHistory($order['order_id'], $this->config->get('coingate_cancelled_order_status_id'));
            } elseif ($coingate_response['status'] == 'expired') {
                $this->model_checkout_order->addOrderHistory($order['order_id'], $this->config->get('coingate_expired_order_status_id'));
            } else {
                $this->model_checkout_order->addOrderHistory($order['order_id'], $this->config->get('coingate_failed_order_status_id'));
            }
        } catch (Exception $e) {
            echo get_class($e) . ': ' . $e->getMessage();
        }
    }

    private function generate_token($order_id)
    {
        return hash('sha256', $order_id + $this->config->get('coingate_api_secret'));
    }

    private function get_view_path($template)
    {
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/' . $template)) {
            return $this->config->get('config_template') . '/template/payment/' . $template;
        } else {
            return 'default/template/payment/' . $template;
        }
    }
}
