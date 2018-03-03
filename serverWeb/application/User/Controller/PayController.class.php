<?php
namespace User\Controller;
use Common\Controller\HomebaseController;
class PayController extends HomeBaseController {
	
    //回调处理
    function callback($payway = '') {
    		$pay_way = strtolower($payway);
        	$get = array();
        	if ($pay_way == '') 
        		die();
        	if ($pay_way == 'alipay') {
            		$get['out_trade_no'] = $_REQUEST['out_trade_no'];
            		$get['trade_no'] = $_REQUEST['trade_no'];
            		$get['trade_status'] = $_REQUEST['trade_status'];
        	}
        	if ($pay_way == 'alipaybank') {
            		$get['out_trade_no'] = $_REQUEST['out_trade_no'];
            		$get['trade_no'] = $_REQUEST['trade_no'];
            		$get['trade_status'] = $_REQUEST['trade_status'];
        	}
        	if ($pay_way == 'alipay_app') {
            		$get['out_trade_no'] = $_REQUEST['out_trade_no'];
            		$get['trade_no'] = $_REQUEST['trade_no'];
            		$get['trade_status'] = $_REQUEST['trade_status'];
        	}        	        	
        	vendor("Pay.{$pay_way}");
        	$callback = new $pay_way();
       	 if (!method_exists($callback, 'CallBack')) {
            		$this->error("CallBack操作方法不存在");
       	 }
        	$callback->CallBack($get);
    }

	//F-MP  发起公众号支付
	public function begin_mppay(){
    $token = I("post.token"); 
		$item_name = intval(I("post.amount")); 

		$users_model = M("users");
		$user_info = $users_model->where(array('api_token'=>$token))->find();
		if ($user_info) {
			$user_balance = $user_info['balance'];			
		}else{
			$ret = array('code'=>582,'descrp'=>'用户信息查询失败，请重试');
			die(json_encode($ret));
		}

		$uid = $user_info['id'];
		$belong_admin = $user_info['belong_admin'];

        $max_day_charge = 2000;
        $day_now = date("Ymd");
        $day_charge = $user_info['charge_day'];
        $amount_charge = $user_info['charge_amount'];

        if(!$day_charge || $day_now != $day_charge){
            $amount_charge = 0;
        }

        if($amount_charge + $item_name > $max_day_charge){
            $ret = array('code'=>582,'descrp'=>'超过每天充值上限，每天最多充值'.$max_day_charge.'金币,您今天已经充值'.$amount_charge.'金币');
            die(json_encode($ret));    
        }

        $users_model = M("users");
        $where['id'] = $uid;
        $ret = $users_model->where($where)->setInc('balance',$item_name);

        if ($ret) {
            $where1['id'] = $uid;
            $new_user_data = array(
                    'charge_day' => $day_now,
                    'charge_amount' => $amount_charge + $item_name
            );
            $r = $users_model->where($where1)->setField($new_user_data);
            $ret = array('code'=>200,'descrp'=>'成功充值'.$item_name.'金币');
            die(json_encode($ret));
        }else{
            $ret = array('code'=>583,'descrp'=>'充值失败');
            die(json_encode($ret));
        }


	}
}