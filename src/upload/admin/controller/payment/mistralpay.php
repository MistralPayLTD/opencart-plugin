<?php
/**
 * MistralPay Payment Controller
*/

class ControllerPaymentMistralpay extends Controller {
	
	private $error = array();

	public function index() {
		$this->load->language('payment/mistralpay');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('mistralpay', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['button_confirm'] = $this->language->get('button_confirm');

		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_all_zones'] = $this->language->get('text_all_zones');

		$data['entry_order_status'] = $this->language->get('entry_order_status');
		$data['entry_total'] = $this->language->get('entry_total');
		$data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
		$data['entry_successful_order_status'] = $this->language->get('entry_successful_order_status');
		$data['entry_blocked_order_status'] = $this->language->get('entry_blocked_order_status');
		$data['entry_doubt_order_status'] = $this->language->get('entry_doubt_order_status');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');
		$data['entry_account'] = $this->language->get('entry_account');
		$data['entry_secret'] = $this->language->get('entry_secret');
		$data['entry_mode'] = $this->language->get('entry_mode');
		$data['entry_mode_option'] = array(
			"test" => $this->language->get('entry_mode_test'),
			'production' => $this->language->get('entry_mode_production')
		);

		$data['help_total'] = $this->language->get('help_total');
		$data['help_account'] = $this->language->get('help_account');
		$data['help_secret'] = $this->language->get('help_secret');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_payment'),
			'href' => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('payment/mistralpay', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['action'] = $this->url->link('payment/mistralpay', 'token=' . $this->session->data['token'], 'SSL');

		$data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');

		$data["mistralpay_modes"] = array(
			"test",
			"production"
		);
		if (isset($this->request->post['mistralpay_mode'])) {
			$data['mistralpay_mode'] = $this->request->post['mistralpay_mode'];
		} else {
			$data['mistralpay_mode'] = $this->config->get('mistralpay_mode') ? $this->config->get('mistralpay_mode') : "test";
		}

		if (isset($this->request->post['mistralpay_total'])) {
			$data['mistralpay_total'] = $this->request->post['mistralpay_total'];
		} else {
			$data['mistralpay_total'] = $this->config->get('mistralpay_total');
		}

		if (isset($this->request->post['mistralpay_successful_order_status_id'])) {
			$data['mistralpay_successful_order_status_id'] = $this->request->post['mistralpay_successful_order_status_id'];
		} else {
			$data['mistralpay_successful_order_status_id'] = $this->config->get('mistralpay_successful_order_status_id');
		}

		if (isset($this->request->post['mistralpay_blocked_order_status_id'])) {
			$data['mistralpay_blocked_order_status_id'] = $this->request->post['mistralpay_blocked_order_status_id'];
		} else {
			$data['mistralpay_blocked_order_status_id'] = $this->config->get('mistralpay_blocked_order_status_id');
		}

		if (isset($this->request->post['mistralpay_doubt_order_status_id'])) {
			$data['mistralpay_doubt_order_status_id'] = $this->request->post['mistralpay_doubt_order_status_id'];
		} else {
			$data['mistralpay_doubt_order_status_id'] = $this->config->get('mistralpay_doubt_order_status_id');
		}

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		if (isset($this->request->post['mistralpay_geo_zone_id'])) {
			$data['mistralpay_geo_zone_id'] = $this->request->post['mistralpay_geo_zone_id'];
		} else {
			$data['mistralpay_geo_zone_id'] = $this->config->get('mistralpay_geo_zone_id');
		}

		$this->load->model('localisation/geo_zone');

		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		if (isset($this->request->post['mistralpay_status'])) {
			$data['mistralpay_status'] = $this->request->post['mistralpay_status'];
		} else {
			$data['mistralpay_status'] = $this->config->get('mistralpay_status');
		}

		if (isset($this->request->post['mistralpay_sort_order'])) {
			$data['mistralpay_sort_order'] = $this->request->post['mistralpay_sort_order'];
		} else {
			$data['mistralpay_sort_order'] = $this->config->get('mistralpay_sort_order');
		}

		if (isset($this->request->post['mistralpay_account'])) {
			$data['mistralpay_account'] = $this->request->post['mistralpay_account'];
		} else {
			$data['mistralpay_account'] = $this->config->get('mistralpay_account');
		}

		if (isset($this->request->post['mistralpay_secret'])) {
			$data['mistralpay_secret'] = $this->request->post['mistralpay_secret'];
		} else {
			$data['mistralpay_secret'] = crypt($this->config->get('mistralpay_secret'), "mistralpay");
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('payment/mistralpay.tpl', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'payment/mistralpay')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}