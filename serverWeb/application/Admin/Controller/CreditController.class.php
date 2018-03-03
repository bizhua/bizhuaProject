<?php
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class CreditController extends AdminbaseController {
	protected $users_model;
	function _initialize() {
		parent::_initialize();
		$this->users_model =M("Users");
	}
	function index(){
		$admin_id = get_current_admin_id();

		if ($_SESSION['ADMIN_ID'] != 1) {
			$where_belong = ' and belong_admin = '.$_SESSION['ADMIN_ID'];
		}


		$where = "id >= 100000 and user_status = 1 and user_type = 2 $where_belong ";

		if(IS_POST){
			if (isset($_POST['keyword']) && !empty($_POST['keyword'])){
				$keyword = $_POST['keyword'];
				$_GET['keyword'] = $keyword;
				$where1 .= "  user_login like '%$keyword%'";
				$where1 .= " or  user_nicename like '%$keyword%'";
				$where1 .= " or  id like '%$keyword%'";
			}
		}else{
			if (isset($_GET['keyword']) && !empty($_GET['keyword'])){
				$keyword = $_GET['keyword'];
				$where1 .= "  user_login like '%$keyword%'";
				$where1 .= " or  user_nicename like '%$keyword%'";
				$where1 .= " or  id like '%$keyword%'";
			}
		}
		if (!empty($where1)) {
			$where = $where.'and ('.$where1.')';
		}

		$count=$this->users_model->where($where)->count();
		$page = $this->page($count, 20);
		$users=$this->users_model->where($where)->limit($page->firstRow . ',' . $page->listRows)->select();

		$this->assign("Page", $page->show('Admin'));
		$this->assign("formget",$_GET);
		$this->assign("users",$users);
		$status_name = array("已封禁","正常");
		$this->assign("status_name",$status_name);
		$this->display();
	}
	function edit(){
		$id= intval(I("get.id"));
		$method= I("get.method");
		$user=$this->users_model->where("id=$id")->find();
		$this->assign("user",$user);
		$this->assign("method",$method);
		$this->display();
	}
	function edit_post(){
		if (IS_POST) {
			$id =I("post.id");
			$user_login = I("post.user_login");
			$method = I("post.method");
			$money_num = I("post.money_num",0,'intval');
			$about = I("post.about");
			//$exchange = intval(C(SP_EXCHANGE));
			//if($exchange <= 0 || empty($exchange)){
			//	$exchange = 1;
			//}
			if($id=='')
				$this->error("ID不能为空！");
			// if($user_login=='')
			// 	$this->error("用户名不能为空！");
			if(($method!='add')&&($method!='minus'))
				$this->error("操作非法！");
			if($money_num=='')
				$this->error("金额不能为空！");
			if(!preg_match("/^[1-9][0-9]*$/", $money_num)){
				$this->error("金额数据非法！");
			}
			if($about=='')
			       $this->error("操作说明不能为空！");
			$user_info =$this->users_model->field('id,balance,belong_admin,belong_qudao')->where("id=$id")->find();
			if(empty($user_info['id']))
				$this->error("指定用户不存在！");
			else
				$balance_pre = $user_info['balance'];

			if ($_SESSION['ADMIN_ID'] != 1) {
				if ($_SESSION['ADMIN_ID'] != $user_info['belong_admin']) {
					//$this->error("操作非法！");
				}
			}

			$pay_amount = $money_num/100;
			if($method=='add'){
				 $order = array(
					'uid' => $id ,
					'pay_way' => "system",
					'pay_amount' => $pay_amount,
					'item_type' => "system",
					'item_id' => 1,
					'item_num' => 1,
					'extend'=>$money_num."个 ".get_balance_name(),
					'about'=>$about,
					'balance_pre' => $balance_pre,
					'belong_admin' => $user_info['belong_admin'],
					'belong_qudao' => $user_info['belong_qudao'],
					//'exchange'=>$exchange,
				 );
				 $order_id= sp_make_order($order);
				$result = $this->users_model->where("id=$id")->setInc('balance',$money_num);
				if($result){
					$user_info =$this->users_model->field('id','balance','api_token')->where("id=$id")->find();
					$balance_after = $user_info['balance'];
				 	 $order_info = array(
				 	 	"pay_time"=>date('Y-m-d H:i:s',time()),
				 	 	"order_status"=>"2",
				 	 	"deal_status"=>"1",
				 	 	"balance_after"=>$balance_after,
				 	 );
				 	 sp_update_order($order_id,$order_info);
				 	 insert_money_log(1,$id,$id,$balance_pre,$money_num,$balance_after,1,1,"后台加款".$order_id,1,0,$user_info['belong_admin'],$user_info['belong_qudao']);
					$this->success("操作成功！");
				}else{
					$this->success("操作失败");
				}
			}else{
				 $order = array(
					'uid' => $id ,
					'pay_way' => "system",
					'pay_amount' => -$pay_amount,
					'item_type' => "system_reduce",
					'item_id' => 1,
					'item_num' => 1,
					'extend'=>$money_num."个 ".get_balance_name(),
					'about'=>$about,
					'balance_pre' => $balance_pre,
					'belong_admin' => $user_info['belong_admin'],
					'belong_qudao' => $user_info['belong_qudao'],
					//'exchange'=>$exchange,
				 );
				 $order_id= sp_make_order($order);
				 $result = $this->users_model->where("id=$id")->setDec('balance',$money_num);
				 if($result){
					$user_info =$this->users_model->field('id','balance','api_token')->where("id=$id")->find();
					$balance_after = $user_info['balance'];
				 	 $order_info = array(
				 	 	"pay_time"=>date('Y-m-d H:i:s',time()),
				 	 	"order_status"=>"2",
				 	 	"deal_status"=>"1",
				 	 	"balance_after"=>$balance_after,
				 	 );
				 	 sp_update_order($order_id,$order_info);
				 	 insert_money_log(2,$id,$id,$balance_pre,$money_num,$balance_after,1,1,"后台扣款".$order_id,1,0,$user_info['belong_admin'],$user_info['belong_qudao']);
					 $this->success("操作成功！");
				 }else{
				 	 $this->error("操作失败！");
 				}
			}
		}
	}
}