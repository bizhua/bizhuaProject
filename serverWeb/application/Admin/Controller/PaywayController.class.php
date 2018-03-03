<?php
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class PaywayController extends AdminbaseController {
	
	//设置
	function index(){
		$this->display();
	}
	
	//设置
	function index_post(){
		if($_POST){
			$alipay_account=$_POST['alipay_account'];
			$alipay_key=$_POST['alipay_key'];
			$wxpay_appid=$_POST['wxpay_appid'];
			$wxpay_appsecret=$_POST['wxpay_appsecret'];
			$wxpay_account=$_POST['wxpay_account'];
			$wxpay_key=$_POST['wxpay_key'];
			$yeepay_account=$_POST['yeepay_account'];
			$yeepay_key=$_POST['yeepay_key'];
			
			$data = array(
					'PAY_ALIPAY' => array(
							'ACCOUNT'    => $alipay_account,
							'KEY' => $alipay_key,
							'HOST'=> sp_get_host().__ROOT__,
					),
					'PAY_WXPAY' => array(
							'APPID'    => $wxpay_appid,
							'APPSECRET' => $wxpay_appsecret,
							'ACCOUNT'    => $wxpay_account,
							'KEY' => $wxpay_key,
							'HOST'=> sp_get_host().__ROOT__,
					),
					'PAY_YEEPAY' => array(
							'ACCOUNT'    => $yeepay_account,
							'KEY' => $yeepay_key,
							'HOST'=> sp_get_host().__ROOT__,
					),
			);

			$result=sp_set_dynamic_config($data);
			
			if($result){
				$this->success("更新成功！");
			}else{
				$this->error("更新失败！");
			}
		}
	}
}