<?php
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class WithdrawController extends AdminbaseController{
	
	function _initialize() {
	}
	
	function index(){
		$where_ands=array("a.status = 1 and b.is_host = 1");
		
		$fields=array(
				'start_time'=> array("field"=>"a.apply_time","operator"=>">"),
				'end_time'  => array("field"=>"a.apply_time","operator"=>"<"),
				'keyword'  => array("field"=>"b.id","operator"=>"like"),
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
		$withdraw_model = M("Withdraw");
		$join = C("DB_PREFIX")."users b on b.id = a.uid";
		$count = $withdraw_model->alias("a")->join($join)->where($where)->count();

		$page = $this->page($count, 15);
		$field = "a.*,b.user_login,b.user_nicename,b.true_name,b.id_no,b.mobile";

		$data = $withdraw_model
		->alias("a")
		->join($join)
		->where($where)
		->field($field)
		->limit($page->firstRow . ',' . $page->listRows)
		->order("a.id ASC")->select();

		$type = array('','银行卡','支付宝','微信');

		$this->assign("type",$type);
		$this->assign("data",$data);
		$this->assign("Page", $page->show('Admin'));
		$this->assign("current_page",$page->GetCurrentPage());
		unset($_GET[C('VAR_URL_PARAMS')]);
		$this->assign("formget",$_GET);
		$this->display();
	}
	
	function record(){
		$where_ands=array("a.status != 1");
		
		$fields=array(
				'start_time'=> array("field"=>"a.apply_time","operator"=>">"),
				'end_time'  => array("field"=>"a.apply_time","operator"=>"<"),
				'keyword'  => array("field"=>"b.id","operator"=>"like"),
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
		$withdraw_model = M("Withdraw");
		$join = C("DB_PREFIX")."users b on b.id = a.uid";
		$count = $withdraw_model->alias("a")->join($join)->where($where)->count();

		$page = $this->page($count, 15);
		$field = "a.*,b.user_login,b.user_nicename,b.true_name,b.id_no,b.mobile";

		$data = $withdraw_model
		->alias("a")
		->join($join)
		->where($where)
		->field($field)
		->limit($page->firstRow . ',' . $page->listRows)
		->order("a.deal_time DESC")->select();

		$type = array('','银行卡','支付宝','微信');
		$status = array('拒绝','待审批','同意');
		
		$this->assign("status",$status);
		$this->assign("type",$type);
		$this->assign("data",$data);
		$this->assign("Page", $page->show('Admin'));
		$this->assign("current_page",$page->GetCurrentPage());
		unset($_GET[C('VAR_URL_PARAMS')]);
		$this->assign("formget",$_GET);
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
		if(empty($id) || $type != 'accept' && $type != 'refuse'){
			$this->error("参数错误");
		}
		$data = array(
			'deal_time' => date("Y-m-d H:i:s"),
			'deal_uid' => $admin_uid,
			'deal_comment' => $text,
		);
		if($type == 'accept'){
			$data['status'] = 2;
		}
		if($type == 'refuse'){
			$data['status'] = 0;
		}
		$withdraw_model = M("Withdraw");
		//提现操作加入锁定判断
		$status = $withdraw_model->where(array('id'=>$id))->getField("status");
		if($status != 1){
			$this->error("请勿重复提交");
		}
		if($withdraw_model->where(array('id'=>$id))->save($data) !== false){
			//如果拒绝提现，申请金额按虚拟币返回到用户余额，并写入money_log提现退回
			if($type == 'refuse'){
				$result = $withdraw_model->where(array('id'=>$id))->field('uid,total')->find();
				$uid = $result['uid'];
				$total = $result['total'];
				$balance = M("Users")->where(array('id'=>$uid))->getField('sidou');
				M("Users")->where(array('id'=>$uid))->setInc('sidou',$total);

				//写费用变更记录(类型：5提现退回，uid， ，退还前思豆，退还金额，退还后思豆，提现记录id，沙发数量，详情)
				insert_money_log(5,$uid,0,$balance,$total,$balance+$total,$id,1,"拒绝提现",2);
				\Think\Log::write('insert_money_log','INFO');
			}
			

			$this->success("操作成功");
		}else{
			$this->error("操作失败");
		}		
	}
		
}