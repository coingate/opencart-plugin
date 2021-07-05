<?php

require_once(DIR_SYSTEM . 'library/vendor/coingate/init.php');
require_once(DIR_SYSTEM . 'library/vendor/coingate/version.php');

class ControllerExtensionPaymentCoingate extends Controller
{
    public function index()
    {
        $this->load->language('extension/payment/coingate');
        $this->load->model('checkout/order');

        $data['button_confirm'] = $this->language->get('button_confirm');
        $data['action'] = $this->url->link('extension/payment/coingate/checkout', '', true);

        return $this->load->view('extension/payment/coingate', $data);
    }

    public function checkout()
    {
        $this->setupCoingateClient();
        $this->load->model('checkout/order');
        $this->load->model('extension/payment/coingate');

        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

        $token = md5(uniqid(rand(), true));
        $description = [];

        foreach ($this->cart->getProducts() as $product) {
            $description[] = $product['quantity'] . ' × ' . $product['name'];
        }

        $cg_order = \CoinGate\Merchant\Order::create(array(
            'order_id' => $order_info['order_id'],
            'price_amount' => number_format($order_info['total'] * $this->currency->getvalue($order_info['currency_code']), 8, '.', ''),
            'price_currency' => $order_info['currency_code'],
            'receive_currency' => $this->config->get('payment_coingate_receive_currency'),
            'cancel_url' => $this->url->link('extension/payment/coingate/cancel', '', true),
            'callback_url' => $this->url->link('extension/payment/coingate/callback', array('cg_token' => $token), true),
            'success_url' => $this->url->link('extension/payment/coingate/success', array('cg_token' => $token), true),
            'title' => $this->config->get('config_meta_title') . ' Order #' . $order_info['order_id'],
            'description' => join(', ', $description),
            'token' => $token
        ));

        if ($cg_order) {
            $this->model_extension_payment_coingate->addOrder(array(
                'order_id' => $order_info['order_id'],
                'token' => $token,
                'cg_invoice_id' => $cg_order->id
            ));

            $this->model_checkout_order->addOrderHistory($order_info['order_id'], $this->config->get('payment_coingate_order_status_id'));

            $this->response->redirect($cg_order->payment_url);
        } else {
            $this->log->write("Order #" . $order_info['order_id'] . " is not valid. Please check CoinGate API request logs.");
            $this->response->redirect($this->url->link('checkout/checkout', '', true));
        }
    }

    public function cancel()
    {
        $this->response->redirect($this->url->link('checkout/cart', ''));
    }

    public function success()
    {
        $this->load->model('checkout/order');
        $this->load->model('extension/payment/coingate');

        $order = $this->model_extension_payment_coingate->getOrder($this->session->data['order_id']);

        if (empty($order) || strcmp($order['token'], $this->request->get['cg_token']) !== 0) {
            $this->response->redirect($this->url->link('common/home', '', true));
        } else {
            $this->response->redirect($this->url->link('checkout/success', '', true));
        }
    }

    public function callback()
    {
        $this->load->model('checkout/order');
        $this->load->model('extension/payment/coingate');

        $order_id = $this->request->post['order_id'];
        $order_info = $this->model_checkout_order->getOrder($order_id);
        $ext_order = $this->model_extension_payment_coingate->getOrder($order_id);


        if (!empty($order_info) && !empty($ext_order) && strcmp($ext_order['token'], $this->request->get['cg_token']) === 0) {
            $this->setupCoingateClient();

            $cg_order = \CoinGate\Merchant\Order::find($ext_order['cg_invoice_id']);

            if ($cg_order) {
                switch ($cg_order->status) {
                    case 'paid':
                        $cg_order_status = 'payment_coingate_paid_status_id';
                        break;
                    case 'confirming':
                        $cg_order_status = 'payment_coingate_confirming_status_id';
                        break;
                    case 'invalid':
                        $cg_order_status = 'payment_coingate_invalid_status_id';
                        break;
                    case 'expired':
                        $cg_order_status = 'payment_coingate_expired_status_id';
                        break;
                    case 'canceled':
                        $cg_order_status = 'payment_coingate_canceled_status_id';
                        break;
                    case 'refunded':
                        $cg_order_status = 'payment_coingate_refunded_status_id';
                        break;
                    default:
                        $cg_order_status = NULL;
                }

                if (!is_null($cg_order_status)) {
                    $this->model_checkout_order->addOrderHistory($order_id, $this->config->get($cg_order_status));
                }
            }
        }

        $this->response->addHeader('HTTP/1.1 200 OK');
    }

    private function setupCoingateClient()
    {
        \CoinGate\CoinGate::config(array(
            'environment' => $this->config->get('payment_coingate_test_mode') == 1 ? 'sandbox' : 'live',
            'auth_token' => empty($this->config->get('payment_coingate_api_auth_token')) ? $this->config->get('payment_coingate_api_secret') : $this->config->get('payment_coingate_api_auth_token'),
            'user_agent' => 'CoinGate - OpenCart v' . VERSION . ' Extension v' . COINGATE_OPENCART_EXTENSION_VERSION
        ));
    }
}
