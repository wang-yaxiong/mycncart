<?php
class ControllerCommonLogout extends Controller {
	public function index() {
		$this->user->logout();

		unset($this->session->data['user_tokens']);

		$this->response->redirect($this->url->link('common/login'));
	}
}