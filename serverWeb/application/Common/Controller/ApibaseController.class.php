<?php
namespace Common\Controller;
use Think\Controller;

class ApibaseController extends AppframeController {
	private $access_secret = "HZ1lERfDhUqNuUQ42PfX5lALvKlaTQxT";

	function _initialize() {
		header("Content-type: text/html; charset=utf-8"); 
		//G('begin0');
/*
		$wap_action = array('game_coin_dh_zs','family_settle','change_rate','change_gonggao','get_game_list','userUploadWorks','change_userinfo','appwap_upload','apply_truename','apply_create_family','apply_join_family','apply_join_member','apply_deney_member','apply_quit_family','apply_quit_member','apply_deney_quit_member','sidou_dh_zs','sidou_tixian_old','sidou_tixian','test');
		if(!in_array(strtolower(ACTION_NAME), json_decode(strtolower(json_encode($wap_action)), true))){

			$timestamp = I('post.timestamp','','intval');

			//验证时间戳（10分钟内有效）
			$now = time();
			if($timestamp < $now-600 || $timestamp > $now+600){
				$ret = array('code'=>511, 'descrp'=>'时间戳无效');
				$this->do_aes(json_encode($ret));
			}

			$signature = I('post.sign','','htmlspecialchars');
			$sign = md5($this->access_secret.$timestamp);
			if($sign != $signature){
				$ret = array('code'=>510, 'descrp'=>'签名有误');
				$this->do_aes(json_encode($ret));
			}
		}
*/
		//G('end0');echo 'end0: '.G('begin0','end0').'s | ';echo 'end0: '.G('begin0','end0','m').'kb <br>';
	}

	//检测Token，成功时返回用户信息
	protected function checkToken($token){
		if(empty($token)){
			$ret = array('code'=>503,'descrp'=>'登录信息未填写');
			$this->do_aes(json_encode($ret));
		}
		$users_model=M("Users");
		$where['user_type']=2;
		//$where['user_status']=1;
		$where['api_token']=$token;
		//$field = "id,user_nicename,is_truename,user_email,mobile,mobile_status,avatar,sex,birthday,signature,SUBSTRING_INDEX(location,'-',-1) as location,longitude,latitude,balance,sidou,total_earn,total_spend,vip_deadline,api_token,is_host,update_token_time,family_id,superior,path,subordinate_count,advanced_administrator";
		$field = "id,user_nicename,is_truename,user_email,mobile,mobile_status,avatar,sex,birthday,signature,location,longitude,latitude,balance,sidou,game_coin,total_earn,total_spend,vip_deadline,user_status,api_token,is_host,update_token_time,family_id,superior,path,subordinate_count,advanced_administrator,minute_charge_timestamp,delivery_name,delivery_mobile,delivery_addr,qudao_all,belong_admin,belong_qudao";
		$result = $users_model->where($where)->field($field)->find();
		if($result){
			$temp = explode('-', $result['location']);
			$result['location'] = $temp[2];
			$result['avatar'] = sp_get_user_avatar_url_api($result["avatar"]);
			unset($result['user_status']);
			return $result;

			// $last_time =strtotime($result['update_token_time']);
			// $now_time = time();
			// $diff = $now_time - $last_time;
			// if($diff<86400*3){//86400*3
			// 	$temp = explode('-', $result['location']);
			// 	$result['location'] = $temp[2];
			// 	$result['avatar'] = sp_get_user_avatar_url_api($result["avatar"]);
			// 	unset($result['user_status']);
			// 	return $result;
			// }else{
			// 	$ret = array('code'=>505,'descrp'=>'登录已过期,请重新登录');
			// 	die(json_encode($ret));
			// }
		}elseif ($result['user_status'] != 1) {
			$ret = array('code'=>504,'descrp'=>'用户信息异常');
			$this->do_aes(json_encode($ret));
		}else{
			$ret = array('code'=>504,'descrp'=>'登录信息错误');
			$this->do_aes(json_encode($ret));
		}
	}

	//检测Token，成功时返回用户信息
	protected function checkToken1($token){
		if(empty($token)){
			$ret = array('code'=>503,'descrp'=>'登录信息未填写');
			$this->do_aes(json_encode($ret));
		}
		return sp_redis_hash_checkToken($token);
	}

	protected function do_aes($str){
		die($str);
		vendor("CryptAES.CryptAES");
		$aes = new \CryptAES();
		$aes->set_key($this->access_secret);
		$aes->require_pkcs5();
		$ciphertext = $aes->encrypt($str);
		die($ciphertext);
	}

	protected function undo_aes($str){
		die($str);
		vendor("CryptAES.CryptAES");
		$aes = new \CryptAES();
		$aes->set_key($this->access_secret);
		$aes->require_pkcs5();
		$ciphertext = $aes->decrypt($str);
		die($ciphertext);
	}
}