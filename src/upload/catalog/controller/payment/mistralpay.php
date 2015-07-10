<?php
class ControllerPaymentMistralpay extends Controller {
	public function index() {
		$this->load->language('payment/mistralpay');

		$this->load->model('checkout/order');

		$this->load->model('payment/mistralpay');

		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
		//$this->load->model("shipping/" . $order_info["shipping_method_code"]);
		//$shipping_method = $this->registry->get("model_shipping_" . $order_info["shipping_method_code"]);
		//echo $shipping_method;
		if($this->cart->hasShipping()){
			$shipping = array(
					"total"=>0,
					"fist_name"=>$order_info["shipping_firstname"],
					"last_name"=>$order_info["shipping_lastname"],
					"company"=>in_array("company", $order_info) ? $order_info["company"] : "",
					"address"=>array(
						"street"=>$order_info["shipping_address_1"],
						"details"=>$order_info["shipping_address_2"],
						"zip_code"=>$order_info["shipping_postcode"],
						"city"=>$order_info["shipping_city"],
						"county"=>$order_info["shipping_zone"],
						"country"=>$order_info["shipping_country"]
					),
				);
		}else{
			$shipping = array();
		};
		$load = array(
			"amount"=>0,
			"currency"=>$order_info['currency_code'],
			"reference"=>$this->session->data['order_id'],
			'timestamp'=>date('U'),
			"data"=>array(
				"items"=>array(),
				"customer"=>array(
					"first_name"=>$order_info["firstname"],
					"last_name"=>$order_info["lastname"],
					"email"=>$order_info["email"]
				),
				"shipping"=> $shipping,
				"discounts"=>array(
				),
				"ETALIA"=>0,
			)
		);
		$load["amount"] = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
		$total = 0;
		foreach ($this->cart->getProducts() as $product) {
			$cost = $this->currency->format($this->tax->calculate(
				$product['price'], $product['tax_class_id'], $this->config->get('config_tax')
				), $order_info['currency_code'], $order_info['currency_value'], false
			);
			array_push($load["data"]["items"], array(
				"name"=> $product['name'],
				"price"=>$cost,
				"quantity"=>$product['quantity']
			));
			$total += $cost*$product['quantity'];
		}
		$load["data"]["ETALIA"]=$load["amount"]-$total;

		$encoded_load = json_encode($load);
		$ch_action = $this->model_payment_mistralpay->getAction($this->config->get('mistralpay_mode'));
		$sign = hash_hmac(
			"sha256",
			$ch_action . $encoded_load,
			$this->config->get("mistralpay_secret")
		);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_URL, $ch_action);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'X-Signature: ' . $sign,
			"X-Account: " . $this->config->get('mistralpay_account'),
			"X-Method: " . "hmac_sha256"
		));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $encoded_load);
		$server_response = curl_exec($ch);
		$data = array(
			"server_response" => json_decode($server_response),
			"button_confirm" => $this->language->get('button_confirm'),
			"continue" => $this->url->link('checkout/success')
		);
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/mistralpay.tpl')) {
			return $this->load->view($this->config->get('config_template') . '/template/payment/mistralpay.tpl', $data);
		} else {
			return $this->load->view('default/template/payment/mistralpay.tpl', $data);
		}
	}

	public function confirm() {
		$this->session->data['info'] = $this->request->post['success_reason'];
		$this->response->redirect($this->url->link('checkout/checkout', '', 'SSL'));
	}

	public function notify() {
		$this->load->language('payment/mistralpay');
		$body = file_get_contents('php://input');
		try{
			$load = json_decode($body);
		} catch (Exception $e) {
			$this->response->redirect($this->url->link('checkout/checkout'));
		}

		$this->load->model('checkout/order');

		// we check the signing
		if(!function_exists('hash_equals')) {
			// thanks asphp at dsgml dot com - http://php.net/manual/en/function.hash-equals.php#115635
			function hash_equals($str1, $str2) {
				if(strlen($str1) != strlen($str2)) {
					return false;
				} else {
					$res = $str1 ^ $str2;
					$ret = 0;
					for($i = strlen($res) - 1; $i >= 0; $i--) $ret |= ord($res[$i]);
					return !$ret;
				}
			}
		}
		if (!function_exists('getallheaders')) {
			function getallheaders(){
				$headers = '';
				foreach ($_SERVER as $name => $value) {
				if (substr($name, 0, 5) == 'HTTP_') {
					$headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
				}
			}
			return $headers;
			}
		}
		function curPageURL() {
			$pageURL = 'http';
			if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"]) $pageURL .= "s";
			$pageURL .= "://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
			return $pageURL;
		}
		$uri = curPageURL();
		$headers = getallheaders();		
		$sign_is_valid = hash_equals($headers["X-Signature"], hash_hmac(
			"sha256",
			$uri . $body,
			$this->config->get("mistralpay_secret")
		));
		
		// we query the database before checking the sign result to mitigate time based attacks
		$order_info = $this->model_checkout_order->getOrder($load->{'order'});
		if (!$order_info) {
			header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
			die;
		}
		if (!$sign_is_valid){
			header($_SERVER['SERVER_PROTOCOL'] . ' 403 Forbidden');
			die;
		}

		$order_status = $order_info["order_status_id"];
		$blocked = $this->config->get('mistralpay_blocked_order_status_id');
		$doubt = $this->config->get('mistralpay_doubt_order_status_id');
		$successful = $this->config->get('mistralpay_successful_order_status_id');
		if ($order_status == $blocked || $order_status == $successful){
			header($_SERVER["SERVER_PROTOCOL"] . ' 403 Forbidden');
			die;
		}
		$total = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
		if ($total != $load->{"amount"}){
			header($_SERVER['SERVER_PROTOCOL'] . ' 202 Accepted');
			$this->model_checkout_order->addOrderHistory(
				$load->{'order'},
				$doubt
			);
			die;
		}
		$this->model_checkout_order->addOrderHistory(
			$load->{'order'},
			$successful
		);
		header($_SERVER['SERVER_PROTOCOL'] . ' 200 OK');
		die;
	}

	public function fail() {
		$this->load->language('payment/mistralpay');

		if (isset($this->request->get['fail_reason']) && !empty($this->request->get['fail_reason'])) {
			$this->session->data['error'] = $this->request->post['fail_reason'];
		} else {
			$this->session->data['error'] = $this->language->get('error_failed');
		}

		$this->response->redirect($this->url->link('checkout/checkout', '', 'SSL'));
	}
}