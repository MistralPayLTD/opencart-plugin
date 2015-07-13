<?php
/**
 * MistralPay Payment Model
 */

class ModelPaymentMistralpay extends Model{
	public function getMethod($address, $total) {
		$this->load->language('payment/mistralpay');

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('firstdata_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

		if ($this->config->get('mistralpay_total') > 0 && $this->config->get('mistralpay_total') > $total) {
			$status = false;
		} elseif (!$this->config->get('mistralpay_geo_zone_id')) {
			$status = true;
		} elseif ($query->num_rows) {
			$status = true;
		} else {
			$status = false;
		}

		$method_data = array();

		if ($status) {
			$method_data = array(
				'code'       => 'mistralpay',
				'title'      => $this->language->get('text_title'),
				'terms'      => '',
				'sort_order' => $this->config->get('mistralpay_sort_order')
			);
		}

		return $method_data;
	}

	function getAction($mode) {
		if ($mode == "test"){
			return "https://stg.secure.mistralpay.com/api/payment/form/create/";
		} elseif ($mode == "production"){
			return "https://secure.mistralpay.com/api/payment/form/create/";
		}
	}
}
