<?php
class ControllerAccountPaimai extends Controller {
	private $error = array();
	private $id = '';

	public function index() {

	    $data['header'] = $this->load->controller('common/top');
        $this->document->addScript('catalog/view/javascript/jquery/jquery-1.4.2.min.js');
        $this->load->model('catalog/ebaypro');
        $data['r'] = $this->model_catalog_ebaypro->getProducts();
        $data['footer'] = $this->load->controller('common/footer');
//        echo '<pre>';
//        print_r($_SESSION);
//        echo '</pre>';die;
		$this->response->setOutput($this->load->view('account/paimai', $data));
	}

	public function move_product()
    {
        //过滤ajax传入的值
        if(!empty($_POST['pid'])){
            $pid = intval($_POST['pid']);
        }else{
            return false;
        }
        $this->load->model('catalog/ebaypro');
        $res = $this->model_catalog_ebaypro->getProduct($pid);
        $strtime = strtotime($res['date_available']);
        $nowtime = time();
        if($strtime<=$nowtime){
            $this->model_catalog_ebaypro->forProduct($pid);
            return true;
        }else{
            return false;
        }
    }

}