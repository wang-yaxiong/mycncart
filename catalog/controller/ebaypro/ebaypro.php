<?php
class ControllerEbayproEbaypro extends Controller {
	private $error = array();
	public function index() {
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
$this->nums();
//        echo '<pre>';
//        print_r($_GET);
//        echo '</pre>';die;
        $data['footer'] = $this->load->controller('common/footer');
        $this->document->addScript('catalog/view/javascript/jquery/jquery-1.9.1.min.js');
        $this->response->setOutput($this->load->view('ebaypro/ebaypro', $data));
    }

    //当前页的访问人数统计
    static public function nums()
    {
        if(!empty($_GET['product_id'])){
            $nums += 1;
        }
        // echo "<br><br><br><br><br><br><br><br><br><br><br>";
        // echo $nums;
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