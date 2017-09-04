<?php

require_once(DIR_SYSTEM . 'library/coingate/coingate-php/init.php');

class ControllerExtensionPaymentCoingate extends Controller {
  private $error = array();

  public function index() {
    $this->load->language('extension/payment/coingate');
    $this->document->setTitle($this->language->get('heading_title'));

    $this->load->model('setting/setting');
    $this->load->model('localisation/order_status');
    $this->load->model('localisation/geo_zone');

    if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('payment_coingate', $this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true));
		}

    $data['action']             = $this->url->link('extension/payment/coingate', 'user_token=' . $this->session->data['user_token'], true);
		$data['cancel']             = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true);
    $data['order_statuses']     = $this->model_localisation_order_status->getOrderStatuses();
    $data['geo_zones']          = $this->model_localisation_geo_zone->getGeoZones();
    $data['receive_currencies'] = array('BTC', 'EUR', 'USD');

    if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

    $data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true)
		);
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/payment/coingate', 'user_token=' . $this->session->data['user_token'], true)
		);

    $fields = array('payment_coingate_status', 'payment_coingate_app_id', 'payment_coingate_api_key', 'payment_coingate_api_secret',
      'payment_coingate_order_status_id', 'payment_coingate_pending_status_id', 'payment_coingate_confirming_status_id', 'payment_coingate_paid_status_id',
      'payment_coingate_invalid_status_id', 'payment_coingate_expired_status_id', 'payment_coingate_canceled_status_id', 'payment_coingate_refunded_status_id',
      'payment_coingate_total', 'payment_coingate_geo_zone_id', 'payment_coingate_receive_currency', 'payment_coingate_test_mode');


    foreach ($fields as $field) {
      if (isset($this->request->post[$field])) {
  			$data[$field] = $this->request->post[$field];
  		} else {
  			$data[$field] = $this->config->get($field);
  		}
    }

    $data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

    $this->response->setOutput($this->load->view('extension/payment/coingate', $data));
  }

  protected function validate() {
    if (!$this->user->hasPermission('modify', 'extension/payment/coingate')) {
      $this->error['warning'] = $this->language->get('error_permission');
    }

    if (!class_exists('CoinGate\CoinGate')) {
      $this->error['warning'] = $this->language->get('error_composer');
    }

    if (!$this->error) {
      $testConnection = \CoinGate\CoinGate::testConnection(array(
        'environment'   => $this->request->post['payment_coingate_test_mode'] == 1 ? 'sandbox' : 'live',
        'app_id'        => $this->request->post['payment_coingate_app_id'],
        'api_key'       => $this->request->post['payment_coingate_api_key'],
        'api_secret'    => $this->request->post['payment_coingate_api_secret']));

      if ($testConnection !== true) {
        $this->error['warning'] = $testConnection;
      }
    }

    return !$this->error;
  }


	public function install() {
		$this->load->model('extension/payment/coingate');

		$this->model_extension_payment_coingate->install();
	}

	public function uninstall() {
		$this->load->model('extension/payment/coingate');

		$this->model_extension_payment_coingate->uninstall();
	}
}
