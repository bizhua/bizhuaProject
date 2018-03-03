<?php

namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class DeliveryController extends AdminbaseController {

	public function index() {
		$admin_id = get_current_admin_id();
		if ($admin_id != 1) {
			$where_ands=array("a.status = 1 and a.play_result in (2,3) and a.belong_admin = $admin_id");
		}else{
			$where_ands=array("a.status = 1 and a.play_result in (2,3)");
		}

		$fields=array(
				'keyword'  => array("field"=>"a.uid","operator"=>"like"),
				'start_time'=> array("field"=>"a.play_time","operator"=>">="),
				'end_time'  => array("field"=>"a.play_time","operator"=>"<="),
				'belong_admin'  => array("field"=>"a.belong_admin","operator"=>"="),
				'is_fahuo'  => array("field"=>"a.play_result","operator"=>"="),
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

		$model = M("DevicePlayLog");
		$join = "".C("DB_PREFIX")."users b on b.id = a.uid";
		$join2 = "".C("DB_PREFIX")."device_doll c on c.id = a.doll_id";

		$count = $model->alias('a')->where($where)->count();
		$page = $this->page($count, 15);

		$field = "a.*,a.remark as remark2,b.user_nicename,b.avatar,b.balance,b.delivery_name,b.delivery_mobile,b.delivery_addr,c.name,c.img,c.remark";
		$data = $model->alias('a')->join($join)->join($join2)->where($where)->field($field)->limit($page->firstRow . ',' . $page->listRows)->order('a.order_number desc')->select();

		$belong_admin = M("Users")->where(array('user_type'=>1,'qudao_prefix'=>array('neq','')))->field('id,user_login')->select();

		$this->assign("Page", $page->show('Admin'));
		$this->assign("data",$data);
		$this->assign("formget",$_GET);
		$this->assign("belong_admin",$belong_admin);
		$this->display();
	}

	//同意或拒绝提现申请
	function action(){
		$admin_uid = get_current_admin_id();
		if(empty($admin_uid)){
			$this->error("请先登录");
		}
		$type = I('get.type','','htmlspecialchars');
		$id = I('get.id','','intval');
		$text = I('post.text','','htmlspecialchars');
		$deduct = I('post.deduct','','htmlspecialchars');
		if(empty($id) || $type != 'accept' && $type != 'refuse'){
			$this->error("参数错误");
		}

		if($type == 'accept'){
			$data['play_result'] = 3;
		}
		if($type == 'refuse'){
			$data['play_result'] = 1;
		}

		$model = M("DevicePlayLog");
		if($admin_uid == 1){
			$where = array('id'=>$id);
		}else{
			$where = array('id'=>$id,'belong_admin'=>$admin_uid);
		}
		$info = $model->where($where)->find();
		if($info['play_result'] != 2){
			$this->error("请勿重复提交");
		}
		$doll_info = M("DeviceDoll")->where(array('id'=>$info['doll_id']))->field('name')->find();
		$uinfo_balance = M("Users")->where(array('id'=>$info['uid']))->getField('balance');

		if($type == 'accept'){
			if($deduct == 'yes'){
				$text = $doll_info['name'].'提货扣除邮费600金币，'.$text;
			}else{
				$text = $doll_info['name'].'提货免邮，'.$text;
			}
		}elseif($uinfo_balance < 600){
			$text = '余额不足以扣除邮费600金币，'.$text;
		}
		$data['remark'] = $text;

		if($deduct == 'yes'){//准备扣除邮费
			if($uinfo_balance < 600){
				$this->error("余额不足以扣除邮费，禁止提货");
			}

			$order = array(
				'uid' => $info['uid'] ,
				'pay_way' => "system",
				'pay_amount' => -6,
				'item_type' => "system_reduce",
				'item_id' => 1,
				'item_num' => 1,
				'extend'=> "600个 ".get_balance_name(),
				'about'=> "单个娃娃提货扣款600".get_balance_name(),
				'balance_pre' => $uinfo_balance,
				'belong_admin' => $info['belong_admin'],
				'belong_qudao' => $info['belong_qudao'],
			);
			$order_id= sp_make_order($order);
			M("Users")->where(array('id'=>$info['uid']))->setDec('balance',600);
			$user_info = M("Users")->where(array('id'=>$info['uid']))->field('balance,belong_admin,belong_qudao')->find();
			$order_info = array(
				"pay_time"=>date('Y-m-d H:i:s',time()),
				"order_status"=>"2",
				"deal_status"=>"1",
				"balance_after"=>$user_info['balance'],
			);
			sp_update_order($order_id,$order_info);
			insert_money_log(2,$info['uid'],$info['uid'],$uinfo_balance,600,$user_info['balance'],$id,1,"娃娃提货扣邮费600金币".$order_id,1,0,$info['belong_admin'],$info['belong_qudao']);
		}
		if($model->where($where)->save($data) !== false){
			$data2 = array(
				'title' => '发货信息',
				'content' => $text,
				'send_type' => 1,
				'uid' => $info['uid'],
				'make_time' => date("Y-m-d H:i:s"),
				'belong_admin' => $info['belong_admin'],
			);
			M("Message")->add($data2);
			$this->success("操作成功");
		}else{
			$this->error("操作失败");
		}
	}

}

