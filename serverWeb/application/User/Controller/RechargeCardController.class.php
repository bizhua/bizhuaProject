<?php
namespace User\Controller;
use Common\Controller\HomebaseController;
class RechargeCardController extends HomeBaseController {

	function _initialize() {
		$app_e = get_options("app_e");
		if($app_e['14'] != 1){
			$this->error("充值卡充值异常");
		}
	}

	function pay(){
		$order_id = I('get.order_id');
		if(empty($order_id)){
			$this->error("订单信息有误");
		}
		$model = M('Orders');
		$order = $model->where(array('order_id'=>$order_id,'status'=>1))->find();
		if(empty($order)){
			$this->error("订单信息有误");
		}
		if($order['order_status'] == 2 && $order['deal_status'] == 1){
			$this->success("充值成功",'/');
			exit();
		}
		$paytype = M("Paysetting")->where(array('class_name'=>'rechargecard','status'=>1))->find();

		$this->assign('order',$order);
		$this->assign('paytype',$paytype);
		$this->display();
	}

	function pay_post(){		
		$order_id = I('post.order_id');
		$card_num = I('post.card_num');
		$card_pass = I('post.card_pass','','intval');

		if(empty($order_id) || empty($card_num) || empty($card_pass)){
			$this->error("信息不全");
		}
		if( strlen($card_num) != 20){
			$this->error("信息有误");
		}
		if( strlen($card_pass) != 6){
			$this->error("信息有误");
		}

		$model = M('Orders');
		$order = $model->where(array('order_id'=>$order_id,'status'=>1))->find();
		if(empty($order)){
			$this->error("订单信息有误");
		}
		if($order['order_status'] != 1){
			$this->error("订单信息有误");
		}
		if($order['deal_status'] != 0){
			$this->error("订单信息有误");
		}
		if($order['order_status'] == 2 && $order['deal_status'] == 1){
			$this->success("充值成功",'/');
		}

		$card = M("RechargeCard")->where(array('card_num'=>$card_num,'card_pass'=>$card_pass))->find();
		if(empty($card)){
			$this->error("请输入正确的卡号和密码");
		}
		if($card['status'] == 3){
			$this->error("充值卡被禁用");
		}
		if($card['status'] == 2){
			$this->error("充值卡已使用");
		}
		if($card['status'] == 1){
			$card_data = array(
				'status' => 2,
				'sell_time' => date("Y-m-d H:i:s"),
				'buy_uid' => $order['uid'],
			);
			if(M("RechargeCard")->where(array('id'=>$card['id']))->save($card_data) !== false){
				$balance = M("Users")->where(array('id'=>$order['uid']))->getField("balance");
				$order_info = array(
					'pay_amount' => $card['money_num'],
					'item_id' => $card_num,
					'extend' => $card['diamond_num']."个".get_balance_name(),
					'balance_pre' => $balance,
					'order_status' => 2,
					'pay_time' => date("Y-m-d H:i:s",time()),
					'about' => '支付成功',
				);
				sp_update_order($order_id, $order_info);
				$ret = M("Users")->where(array('id'=>$order['uid']))->setInc('balance',$card['diamond_num']);
				if($ret){
					$info['balance_after'] = intval($balance + $card['diamond_num']);                          		
		            $info['deal_status'] = 1;
		            sp_update_order($order_id, $info);
		            insert_money_log(2,$order['uid'],$order['uid'],$balance,$card['diamond_num'],intval($balance + $card['diamond_num']),$card_num,1,"充值卡充值".$order_id);
		            $this->success("充值成功",'/');
				}else{
					$this->error("充值异常");
				}
			}else{
				$this->error("充值卡充值异常，请重试");
			}
		}else{
			$this->error("充值卡充值异常");
		}
	}
}