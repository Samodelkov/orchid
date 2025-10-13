<?php

if (!class_exists('Equotix')) {
	class Equotix extends Controller {
		public function generateOutput($file, $data = array()) {

			$folder = 'extension/module';
      $data['equotix_token'] = '&user_token=' . $this->session->data['user_token'];
			$data['folder'] = isset($this->folder) ? $this->folder : $folder;
			$data['code'] = $this->code;
			$data['purchase_url'] = $this->purchase_url;
			$data['extension'] = $this->extension;
			$data['version'] = $this->version;

			$data['about'] = '';
			$data['tab'] = '';

			if (!empty($this->request->server['HTTPS'])) {
				$base = str_replace('http://', 'https://', (defined('HTTPS_CATALOG') ? HTTPS_CATALOG : HTTP_CATALOG) . 'system/library/equotix/' . $this->code . '/');
			} else {
				$base = str_replace('https://', 'http://', (defined('HTTPS_CATALOG') ? HTTPS_CATALOG : HTTP_CATALOG) . 'system/library/equotix/' . $this->code . '/');
			}

			$search = array();

			$replace = array();

      $this->response->setOutput(str_replace($search, $replace, $this->load->view($file, $data)));

		}

		private function saveSetting($group, $data) {
			$this->db->query("DELETE FROM " . DB_PREFIX . "setting WHERE store_id = '0' AND `group` = '" . $this->db->escape($group) . "'");

				foreach ($data as $key => $value) {
					if (!is_array($value)) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '0', `group` = '" . $this->db->escape($group) . "', `key` = '" . $this->db->escape($key) . "', `value` = '" . $this->db->escape($value) . "'");
					} else {
						$this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '0', `group` = '" . $this->db->escape($group) . "', `key` = '" . $this->db->escape($key) . "', `value` = '" . $this->db->escape(serialize($value)) . "', serialized = '1'");
					}
				}

		}

		protected function validated() {
			return true;
		}
	}
}
