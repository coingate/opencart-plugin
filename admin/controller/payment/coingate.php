<?php

require_once(DIR_SYSTEM . 'library/vendor/coingate/coingate_merchant.class.php');

class ControllerPaymentCoingate extends Controller
{
    private $error = [];

    public function __construct($registry)
    {
        parent::__construct($registry);

        $this->load->language('payment/coingate');
        $this->load->model('setting/setting');
    }

    public function index()
    {
        $this->document->setTitle($this->language->get('heading_title'));

        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            if ($this->validate()) {
                $this->load->model('setting/setting');
                $this->model_setting_setting->editSetting('coingate', $this->request->post);
                $this->session->data['success'] = $this->language->get('success_text');
                $this->response->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], $this->config->get('config_secure')));
            }
        }

        $data['heading_title'] = $this->language->get('heading_title');
        $data['status_off'] = $this->language->get('status_off');
        $data['status_on'] = $this->language->get('status_on');
        $data['test_off'] = $this->language->get('test_off');
        $data['test_on'] = $this->language->get('test_on');
        $data['receive_currency_label'] = $this->language->get('receive_currency_label');
        $data['currencies_label']['eur'] = $this->language->get('eur_label');
        $data['currencies_label']['usd'] = $this->language->get('usd_label');
        $data['currencies_label']['btc'] = $this->language->get('btc_label');
        $data['app_id_label'] = $this->language->get('app_id_label');
        $data['status_label'] = $this->language->get('status_label');
        $data['api_key_label'] = $this->language->get('api_key_label');
        $data['api_secret_label'] = $this->language->get('api_secret_label');
        $data['test_label'] = $this->language->get('test_label');
        $data['edit_text'] = $this->language->get('edit_text');
        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');
        $data['error_warning'] = isset($this->error['warning']) ? $this->error['warning'] : '';
        $data['app_id_error'] = isset($this->error['coingate_app_id']) ? $this->error['coingate_app_id'] : '';
        $data['api_key_error'] = isset($this->error['coingate_api_key']) ? $this->error['coingate_api_key'] : '';
        $data['api_secret_error'] = isset($this->error['coingate_api_secret']) ? $this->error['coigate_api_secret'] : '';
        $data['receive_currency_error'] = isset($this->error['coingate_receive_currency']) ? $this->error['coingate_receive_currency'] : '';
        $data['new_order_status_label'] = $this->language->get('new_order_status_label');
        $data['cancelled_order_status_label'] = $this->language->get('cancelled_order_status_label');
        $data['expired_order_status_label'] = $this->language->get('expired_order_status_label');
        $data['failed_order_status_label'] = $this->language->get('failed_order_status_label');
        $data['completed_order_status_label'] = $this->language->get('completed_order_status_label');
        $data['sort_order_label'] = $this->language->get('sort_order_label');

        $this->load->model('localisation/order_status');
        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        $data['breadcrumbs'] = [];

        $data['breadcrumbs'][] = [
            'text' => $this->language->get('home_text'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], $this->config->get('config_secure'))
        ];

        $data['breadcrumbs'][] = [
            'text' => $this->language->get('payment_text'),
            'href' => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], $this->config->get('config_secure'))
        ];

        $data['breadcrumbs'][] = [
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('payment/coingate', 'token=' . $this->session->data['token'], $this->config->get('config_secure'))
        ];

        $data['action'] = $this->url->link('payment/coingate', 'token=' . $this->session->data['token'], $this->config->get('config_secure'));
        $data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], $this->config->get('config_secure'));


        if (isset($this->request->post['coingate_status']))
            $data['coingate_status'] = $this->request->post['coingate_status'];
        else
            $data['coingate_status'] = $this->config->get('coingate_status');

        if (isset($this->request->post['coingate_app_id']))
            $data['coingate_app_id'] = $this->request->post['coingate_app_id'];
        else
            $data['coingate_app_id'] = $this->config->get('coingate_app_id');

        if (isset($this->request->post['coingate_api_key']))
            $data['coingate_api_key'] = $this->request->post['coingate_api_key'];
        else
            $data['coingate_api_key'] = $this->config->get('coingate_api_key');

        if (isset($this->request->post['coingate_api_secret']))
            $data['coingate_api_secret'] = $this->request->post['coingate_api_secret'];
        else
            $data['coingate_api_secret'] = $this->config->get('coingate_api_secret');

        if (isset($this->request->post['coingate_test']))
            $data['coingate_test'] = $this->request->post['coingate_test'];
        else
            $data['coingate_test'] = $this->config->get('coingate_test');

        if (isset($this->request->post['coingate_receive_currency']))
            $data['coingate_receive_currency'] = $this->request->post['coingate_receive_currency'];
        else
            $data['coingate_receive_currency'] = $this->config->get('coingate_receive_currency');

        if (isset($this->request->post['coingate_new_order_status_id']))
            $data['coingate_new_order_status_id'] = $this->request->post['coingate_new_order_status_id'];
        else
            $data['coingate_new_order_status_id'] = $this->config->get('coingate_new_order_status_id');

        if (isset($this->request->post['coingate_cancelled_order_status_id']))
            $data['coingate_cancelled_order_status_id'] = $this->request->post['coingate_cancelled_order_status_id'];
        else
            $data['coingate_cancelled_order_status_id'] = $this->config->get('coingate_cancelled_order_status_id');

        if (isset($this->request->post['coingate_expired_order_status_id']))
            $data['coingate_expired_order_status_id'] = $this->request->post['coingate_expired_order_status_id'];
        else
            $data['coingate_expired_order_status_id'] = $this->config->get('coingate_expired_order_status_id');

        if (isset($this->request->post['coingate_failed_order_status_id']))
            $data['coingate_failed_order_status_id'] = $this->request->post['coingate_failed_order_status_id'];
        else
            $data['coingate_failed_order_status_id'] = $this->config->get('coingate_failed_order_status_id');

        if (isset($this->request->post['coingate_completed_order_status_id']))
            $data['coingate_completed_order_status_id'] = $this->request->post['coingate_completed_order_status_id'];
        else
            $data['coingate_completed_order_status_id'] = $this->config->get('coingate_completed_order_status_id');

        if (isset($this->request->post['coingate_sort_order']))
            $data['coingate_sort_order'] = $this->request->post['coingate_sort_order'];
        else
            $data['coingate_sort_order'] = $this->config->get('coingate_sort_order');

        $this->template = 'payment/coingate.tpl';
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view($this->template, $data));
    }

    private function validate()
    {
        if (!$this->user->hasPermission('modify', 'payment/coingate'))
            $this->error['warning'] = $this->language->get('error_permission');

        if (!$this->request->post['coingate_app_id'])
            $this->error['coingate_app_id'] = $this->language->get('app_id_error');

        if (!$this->request->post['coingate_api_key'])
            $this->error['coingate_api_key'] = $this->language->get('api_key_error');

        if (!$this->request->post['coingate_api_secret'])
            $this->error['coingate_api_secret'] = $this->language->get('api_secret_error');

        if (!in_array($this->request->post['coingate_receive_currency'], ['eur', 'usd', 'btc']))
            $this->error['coingate_receive_currency'] = $this->language->get('receive_currency_error');

        return !$this->error;
    }
}

