<?php
class ControllerAccountPaimai extends Controller {
	private $error = array();

	public function index() {
		 
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/top');
		
		$this->response->setOutput($this->load->view('account/paimai', $data));
	}
 

}