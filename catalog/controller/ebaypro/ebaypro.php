<?php
class ControllerEbayproEbaypro extends Controller {
	private $error = array();
	public function index() {
        $data['header'] = $this->load->controller('common/top');
        $this->load->model('catalog/ebaypro');
        $data['c'] = $this->model_catalog_ebaypro->getProduct($this->request->get['product_id']);
        ($data['c'])['image'] = '/image/'.($data['c'])['image'];
        $ntime = time()+28800;
        $dtime = strtotime(($data['c'])['date_available']);
        $etime = strtotime(($data['c'])['end_available']);
        if($ntime <= $dtime){
            ($data['c'])['stock_status']='距开始';
        }

        if($dtime<=$ntime && $ntime<=$etime){
            ($data['c'])['stock_status']='距结束';
            ($data['c'])['crinfo'] = $this->customer_info();
            if(!isset($this->session->data['customer_id'])){
                echo ("<script> alert('您还没有登录,请登录并缴纳保证金后在进行拍卖!')</script>");
            }
            if(($this->customer_info())< ($data['c'])['price']){
                echo "<script> alert('保证金不足,请充值保证金!')</script>"; //缺跳转付款页面  拍品初始价 - 用户中保证金金额
                //  等于需要补交的保证金
            }
        }
        $data['c']['description'] = htmlspecialchars($data['c']['description']);
        $data['r'] = $this->duo_p()->r;
        $data['c']['outinfo'] = $this->readoutprice($data['c']['product_id']);
        $data['c']['outcount'] = count($data['c']['outinfo']);
        foreach($data['c']['outinfo'] as $k=>$v){
            $num = max($num,intval($v->nowprice));
        }
        ($data['c'])['maxnum'] = $num;
//        echo '<pre><br><br><br><br><br><br><br><br><br><br>';
//        print_r($data['c']);
//        echo '</pre>';die;

        $data['footer'] = $this->load->controller('common/footer');
        $this->document->addScript('catalog/view/javascript/jquery/jquery-1.9.1.min.js');
        $this->response->setOutput($this->load->view('ebaypro/ebaypro', $data));
    }

    //读取个人信息中的保证金
    private function customer_info()
    {

	    $this->load->model('account/customer');
	    $crres = $this->model_account_customer->getCustomer($this->session->data['customer_id']);
        $userarr['customer_id'] = $crres['customer_id'];
        $userarr['bzprice'] = $crres['bzprice'];
        $userarr['fullname'] = $crres['fullname'];
        $userarr['telephone'] = $crres['telephone'];
	    $userarr['ip'] = $crres['ip'];
	    return $userarr;
    }
    //收集信息写入出价历史表
    public function maxn($id)
    {
        $arr = [];
        $arr = $this->readoutprice($id);
        $this->load->model('catalog/ebaypro');
        $p = $this->model_catalog_ebaypro->getProduct($id);
        $arr['shop_id'] = $p->shop_id;
        $arr['product_id'] = $id;

    }
    //读取出价记录
    public function readoutprice($product_id)
    {
        if(!file_exists($product_id.'.txt')){
            return false;
        }
        $str = file_get_contents($product_id.'.txt',true);
        $nowarr = explode('|$|',$str);
        foreach($nowarr as $k=>$v){
            if(empty($v)){
                unset($nowarr[$k]);
            }
            $nowarr[$k] = json_decode($v);
        }
        return $nowarr;
    }
    //出价记录
    public function outprice()
    {
        $product_id = $this->request->get['product_id'];
        $arr['product_id'] = $product_id;
        $arr['uid'] = $this->customer_info()['customer_id'];
        $arr['shop_id'] = $this->request->get['shop_id'];
        $arr['name'] = $this->customer_info()['fullname'];
        $arr['nowprice'] = $this->request->get['nowprice'];
        $a = ($this->request->get)['nowtime']+28800;
        $arr['outtime'] =Date("Y-m-d H:i:s",$a);
        $arr['ip'] = $this->customer_info()['ip'];
        $test = json_encode($arr);
        $json_str = file_get_contents($product_id.".txt",true);
        $json_str .='|$|'. $test;
        file_put_contents($product_id.".txt",$json_str);// 追加 file_put_contents($product_id.".txt",$json_str.PHP_EOL, FILE_APPEND);
        //获取出价最大值
        $maxarr = $this->readoutprice($this->request->get['product_id']);
        foreach($maxarr as $k=>$v){
            $num = max($num,intval($v->nowprice));
        }
        return $num;
    }

    //当前页的访问人数统计
    public function nums()
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
//
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
//            $this->model_catalog_ebaypro->insertoutprice($this->readoutprice($pid));
            return true;
        }else{
            return false;
        }
    }
}