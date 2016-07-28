<?php

use CoinGate\CoinGate;

require_once DIR_SYSTEM.'library/vendor/coingate/init.php';
require_once DIR_SYSTEM.'library/vendor/coingate/version.php';

class ControllerPaymentCoingate extends Controller
{
  private $error = array();
  private $oc_version = null;

  public function __construct($registry)
  {
    parent::__construct($registry);

    $this->load->language('payment/coingate');
    $this->load->model('setting/setting');
    $this->load->model('localisation/order_status');

    CoinGate::config(array(
      'app_id' => $this->config->get('coingate_app_id'),
      'api_key' => $this->config->get('coingate_api_key'),
      'api_secret' => $this->config->get('coingate_api_secret'),
      'environment' => $this->config->get('coingate_test') == 1 ? 'sandbox' : 'live',
      'user_agent' => 'CoinGate - OpenCart '.$this->oc_version.' Extension v'.COINGATE_OPENCART_EXTENSION_VERSION,
    ));

    $this->log = new Log('coingate.log');
    $this->oc_version = substr(VERSION, 0, 1);
  }

  private function get_order_status_name($order_status_id)
  {
    if ($order_status = $this->model_localisation_order_status->getOrderStatus($order_status_id)) {
      return $order_status['name'];
    } else {
      return '-';
    }
  }

  public function download_log()
  {
    if ($this->oc_version == '1') {
      $this->load->model('setting/extension');
      $model = $this->model_setting_extension;
    } else {
      $this->load->model('extension/extension');
      $model = $this->model_extension_extension;
    }

    $this->load->model('localisation/currency');

    echo '<pre>';

    echo "# OpenCart Data:\n";
    echo 'PHP Version: '.phpversion()."\n";
    echo 'cURL Version: '.json_encode(curl_version())."\n";
    echo 'OpenCart Version: '.VERSION."\n";
    echo 'CoinGate Payment Plugin Version '.COINGATE_OPENCART_EXTENSION_VERSION."\n";
    echo 'Installed Modules: '.implode(', ', $model->getInstalled('module'))."\n";
    echo 'Installed Payments Methods: '.implode(', ', $model->getInstalled('payment'))."\n";
    echo 'Installed Shipping Methods: '.implode(', ', $model->getInstalled('shipping'))."\n";
    echo 'CoinGate APP ID: '.$this->config->get('coingate_app_id')."\n";
    echo 'OpenCart CoinGate Plugin Environment: '.($this->config->get('coingate_test') == 1 ? 'Sandbox' : 'Live')."\n";
    echo 'Connection with CoinGate: '.(CoinGate::testConnection() === true ? 'Success' : 'Error')."\n";
    echo 'User Currencies: '.json_encode($this->model_localisation_currency->getCurrencies())."\n";
    echo 'Receive Currency: '.$this->config->get('coingate_receive_currency')."\n";
    echo 'New Order Status: '.$this->get_order_status_name($this->config->get('coingate_new_order_status_id'))."\n";
    echo 'Cancelled Order Status: '.$this->get_order_status_name($this->config->get('coingate_cancelled_order_status_id'))."\n";
    echo 'Expired Order Status: '.$this->get_order_status_name($this->config->get('coingate_expired_order_status_id'))."\n";
    echo 'Failed Order Status: '.$this->get_order_status_name($this->config->get('coingate_failed_order_status_id'))."\n";
    echo 'Completed Order Status: '.$this->get_order_status_name($this->config->get('coingate_completed_order_status_id'))."\n";

    echo "\n# Error logs:\n";
    readfile(DIR_LOGS.'coingate.log');

    echo '</pre>';
  }

  public function index()
  {
    $this->document->setTitle($this->language->get('heading_title'));

    if ($this->request->server['REQUEST_METHOD'] == 'POST') {
      if ($this->validate()) {
        $this->load->model('setting/setting');
        $this->model_setting_setting->editSetting('coingate', $this->request->post);
        $this->session->data['success'] = $this->language->get('success_text');
        $this->response->redirect($this->url->link('extension/payment', 'token='.$this->session->data['token'], $this->config->get('config_secure')));
      }
    }

    if ($this->oc_version == '1') {
      $this->load->model('setting/extension');
      $model = $this->model_setting_extension;
    } else {
      $this->load->model('extension/extension');
      $model = $this->model_extension_extension;
    }

    $data['iamtesting'] = implode(', ', $model->getInstalled('shipping'));

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
    $data['entry_total'] = $this->language->get('entry_total');
    $data['help_total'] = $this->language->get('help_total');
    $data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
    $data['text_all_zones'] = $this->language->get('text_all_zones');
    $data['error_warning'] = isset($this->error['warning']) ? $this->error['warning'] : '';
    $data['app_id_error'] = isset($this->error['coingate_app_id']) ? $this->error['coingate_app_id'] : '';
    $data['api_key_error'] = isset($this->error['coingate_api_key']) ? $this->error['coingate_api_key'] : '';
    $data['api_secret_error'] = isset($this->error['coingate_api_secret']) ? $this->error['coingate_api_secret'] : '';
    $data['receive_currency_error'] = isset($this->error['coingate_receive_currency']) ? $this->error['coingate_receive_currency'] : '';
    $data['new_order_status_label'] = $this->language->get('new_order_status_label');
    $data['cancelled_order_status_label'] = $this->language->get('cancelled_order_status_label');
    $data['expired_order_status_label'] = $this->language->get('expired_order_status_label');
    $data['failed_order_status_label'] = $this->language->get('failed_order_status_label');
    $data['completed_order_status_label'] = $this->language->get('completed_order_status_label');
    $data['refunded_order_status_label'] = $this->language->get('refunded_order_status_label');
    $data['sort_order_label'] = $this->language->get('sort_order_label');
    $data['log_download_url'] = $this->url->link('payment/coingate/download_log', 'token='.$this->session->data['token'], $this->config->get('config_secure'));

    $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

    $data['breadcrumbs'] = array();

    $data['breadcrumbs'][] = array(
      'text' => $this->language->get('home_text'),
      'href' => $this->url->link('common/dashboard', 'token='.$this->session->data['token'], $this->config->get('config_secure')),
    );

    $data['breadcrumbs'][] = array(
      'text' => $this->language->get('payment_text'),
      'href' => $this->url->link('extension/payment', 'token='.$this->session->data['token'], $this->config->get('config_secure')),
    );

    $data['breadcrumbs'][] = array(
      'text' => $this->language->get('heading_title'),
      'href' => $this->url->link('payment/coingate', 'token='.$this->session->data['token'], $this->config->get('config_secure')),
    );

    $data['action'] = $this->url->link('payment/coingate', 'token='.$this->session->data['token'], $this->config->get('config_secure'));
    $data['cancel'] = $this->url->link('extension/payment', 'token='.$this->session->data['token'], $this->config->get('config_secure'));

    $statuses = array('new', 'cancelled', 'expired', 'failed', 'completed', 'refunded');

    foreach($data['order_statuses'] as $order_status) {
      foreach ($statuses as $status) {
        $cg_order_status = ($status == 'new' ? 'Pending' : ucfirst($status));
        $cg_order_status = ($cg_order_status == 'Completed' ? 'Complete' : $cg_order_status);
        $cg_order_status = ($cg_order_status == 'Cancelled' ? 'Canceled' : $cg_order_status);

        if ($cg_order_status == $order_status['name']) {
          $data["coingate_{$status}_order_status_id"] = (isset($this->request->post["coingate_{$status}_order_status_id"]) ? $this->request->post["coingate_{$status}_order_status_id"] : ($this->config->get("coingate_{$status}_order_status_id") != '' ? $this->config->get("coingate_{$status}_order_status_id") : $order_status['order_status_id']));
        }
      }
    }

    $fields = array(
      'coingate_status', 'coingate_app_id', 'coingate_api_key', 'coingate_api_secret', 'coingate_test',
      'coingate_receive_currency', 'coingate_sort_order', 'coingate_total', 'coingate_geo_zone_id'
    );

    foreach ($fields as $field_name) {
      if (isset($this->request->post[$field_name])) {
        $data[$field_name] = $this->request->post[$field_name];
      } else {
        $data[$field_name] = $this->config->get($field_name);
      }
    }

    $this->load->model('localisation/geo_zone');
    $data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

    $this->load->model('localisation/currency');
    $currencies = $this->model_localisation_currency->getCurrencies();
    $data['currencies'] = array();

    foreach ($currencies as $key => $value) {
      $data['currencies'][] = strtolower($key);
    }

    if ($this->oc_version == '1') {
      $this->template = 'payment/coingate_v1.tpl';

      $this->data = $data;

      $this->children = array(
        'common/header',
        'common/footer',
      );

      $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
    } else {
      $this->template = 'payment/coingate_v2.tpl';

      $data['header'] = $this->load->controller('common/header');
      $data['column_left'] = $this->load->controller('common/column_left');
      $data['footer'] = $this->load->controller('common/footer');

      $this->response->setOutput($this->load->view($this->template, $data));
    }
  }

  private function validate()
  {
    if (!$this->user->hasPermission('modify', 'payment/coingate')) {
      $this->error['warning'] = $this->language->get('error_permission');
    }

    if (!$this->request->post['coingate_app_id']) {
      $this->error['coingate_app_id'] = $this->language->get('app_id_error');
    }

    if (!$this->request->post['coingate_api_key']) {
      $this->error['coingate_api_key'] = $this->language->get('api_key_error');
    }

    if (!$this->request->post['coingate_api_secret']) {
      $this->error['coingate_api_secret'] = $this->language->get('api_secret_error');
    }

    if (!in_array($this->request->post['coingate_receive_currency'], array('eur', 'usd', 'btc'))) {
      $this->error['coingate_receive_currency'] = $this->language->get('receive_currency_error');
    }

    if (!$this->error) {
      $authentication = array(
        'app_id' => $this->request->post['coingate_app_id'],
        'api_key' => $this->request->post['coingate_api_key'],
        'api_secret' => $this->request->post['coingate_api_secret'],
        'environment' => $this->request->post['coingate_test'] == 1 ? 'sandbox' : 'live',
        'user_agent' => 'CoinGate - OpenCart Extension v'.COINGATE_OPENCART_EXTENSION_VERSION,
      );

      if (($test_connection = CoinGate::testConnection($authentication)) !== true) {
        $this->error['warning'] = $this->language->get('coingate_connection_error');

        $this->log->write(
        '[Admin] Testing connection error'
        .' - App ID: '.$this->request->post['coingate_app_id']
        .'; Exception: '.$test_connection
        ."\n");
      }
    }

    return !$this->error;
  }
}
