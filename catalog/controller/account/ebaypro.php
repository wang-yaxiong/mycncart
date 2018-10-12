<?php
class ControllerAccountEbaypro extends Controller {
	private $error = array();
	public function index() {
        $this->document->addStyle('catalog/view/theme/default/paim/css/pmxq.css');
        $data['header'] = $this->load->controller('common/top');
        $this->load->model('catalog/ebaypro');
        $data['c'] = $this->model_catalog_ebaypro->getProduct($this->request->get['product_id']);
        ($data['c'])['image'] = '/image/'.($data['c'])['image'];
        $ntime = time();
        $dtime = strtotime(($data['c'])['date_available']);
        $etime = strtotime(($data['c'])['end_available']);
        if($ntime <= $dtime){
            ($data['c'])['time_available'] = ($data['c'])['date_available'];
            ($data['c'])['stock_status']='距开始';
        }
        if($dtime<=$ntime && $ntime<=$etime){
            ($data['c'])['time_available'] = ($data['c'])['end_available'];
            ($data['c'])['stock_status']='距结束';

        }
        $data['r'] = $this->duo_p()->r;


        $data['footer'] = $this->load->controller('common/footer');
        $this->document->addScript('catalog/view/javascript/jquery/jquery-1.9.1.min.js');

        $this->response->setOutput($this->load->view('account/ebaypro', $data));
    }

    //更多作品
    public function duo_p()
    {
        $this->load->model('catalog/ebaypro');
        //以后可以根据作者的ID查询出几个新作品
        return $this->model_catalog_ebaypro->duoProducts();

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