<?php
class ControllerExtensionAnalyticsBaidu extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/analytics/baidu');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('analytics_baidu', $this->request->post, $this->request->get['store_id']);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_tokens=' . $this->session->data['user_tokens'] . '&type=analytics'));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['code'])) {
			$data['error_code'] = $this->error['code'];
		} else {
			$data['error_code'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_tokens=' . $this->session->data['user_tokens'])
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_tokens=' . $this->session->data['user_tokens'] . '&type=analytics')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/analytics/baidu', 'user_tokens=' . $this->session->data['user_tokens'] . '&store_id=' . $this->request->get['store_id'])
		);

		$data['action'] = $this->url->link('extension/analytics/baidu', 'user_tokens=' . $this->session->data['user_tokens'] . '&store_id=' . $this->request->get['store_id']);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_tokens=' . $this->session->data['user_tokens'] . '&type=analytics');

		$data['user_tokens'] = $this->session->data['user_tokens'];

		if (isset($this->request->post['analytics_baidu_code'])) {
			$data['analytics_baidu_code'] = $this->request->post['analytics_baidu_code'];
		} else {
			$data['analytics_baidu_code'] = $this->model_setting_setting->getSettingValue('analytics_baidu_code', $this->request->get['store_id']);
		}

		if (isset($this->request->post['analytics_baidu_status'])) {
			$data['analytics_baidu_status'] = $this->request->post['analytics_baidu_status'];
		} else {
			$data['analytics_baidu_status'] = $this->model_setting_setting->getSettingValue('analytics_baidu_status', $this->request->get['store_id']);
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/analytics/baidu', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/analytics/baidu')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['analytics_baidu_code']) {
			$this->error['code'] = $this->language->get('error_code');
		}

		return !$this->error;
	}
}
