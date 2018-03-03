<?php
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class OrderController extends AdminbaseController {
	protected $orders_model;
	function _initialize() {
		parent::_initialize();
		$this->orders_model =M("Orders");
	}
	function index(){
		$admin_id = get_current_admin_id();
		if ($admin_id != 1) {
			$where_ands=array("b.id >= 100000 and b.user_status = 1 and b.user_type = 2 and a.belong_admin = $admin_id");
		}else{
			$where_ands=array("b.id >= 100000 and b.user_status = 1 and b.user_type = 2");
		}

		$fields=array(
				'item_type'  => array("field"=>"a.item_type","operator"=>"="),
				'pay_way'  => array("field"=>"a.pay_way","operator"=>"="),
				'order_status'  => array("field"=>"a.order_status","operator"=>"="),
				'keyword'  => array("field"=>"a.uid","operator"=>"like"),
				'start_time'=> array("field"=>"a.order_time","operator"=>">="),
				'end_time'  => array("field"=>"a.order_time","operator"=>"<="),
		);
		if(IS_POST){
			foreach ($fields as $param =>$val){
				if (isset($_POST[$param]) && !empty($_POST[$param])) {
					$operator=$val['operator'];
					$field   =$val['field'];
					$get=$_POST[$param];
					$_GET[$param]=$get;
					if($operator=="like"){
						$get="%$get%";
					}
					array_push($where_ands, "$field $operator '$get'");
				}
			}
		}else{
			foreach ($fields as $param =>$val){
				if (isset($_GET[$param]) && !empty($_GET[$param])) {
					$operator=$val['operator'];
					$field   =$val['field'];
					$get=$_GET[$param];
					if($operator=="like"){
						$get="%$get%";
					}
					array_push($where_ands, "$field $operator '$get'");
				}
			}
		}

		$where= join(" and ", $where_ands);

		$count=$this->orders_model->alias('a')->join("".C("DB_PREFIX")."users b on b.id = a.uid")->where($where)->order('a.id desc')->count();
		$page = $this->page($count, 20);

		$orders=$this->orders_model->alias('a')->join("".C("DB_PREFIX")."users b on b.id = a.uid")->where($where)->field('a.*,b.user_login,b.user_nicename')->limit($page->firstRow . ',' . $page->listRows)->order('a.id desc')->select();

		$pay_way_name = array(
			"applepay"=>"苹果支付",
			"alipay"=>"支付宝",
			"alipay_app"=>"支付宝",
			"wxpay"=>"微信支付",
			"wxpay_app"=>"微信支付",
			"system"=>"线下支付",
			//"balance"=>get_balance_name(),
		);
		$item_type_name = array(
			"packet"=>get_balance_name()."套餐",
			"system"=>"后台加款",
			"system_reduce"=>"后台扣款",
		);
		$status_name = array("<font color='#FF0000'>已取消</font>","待付款","<font color='#3CB371'>成功</font>");
		$deal_name = array("未处理","<font color='#3CB371'>已处理</font>");
		$this->assign("Page", $page->show('Admin'));
		$this->assign("formget",$_GET);
		$this->assign("orders",$orders);
		$this->assign("pay_way_name",$pay_way_name);
		$this->assign("item_type_name",$item_type_name);
		$this->assign("status_name",$status_name);
		$this->assign("deal_name",$deal_name);
		$this->display();
	}
}