<?php
namespace Common\Controller;
use Common\Controller\AppframeController;
class H5Controller extends AppframeController {

	public function __construct() {
		$this->set_action_success_error_tpl();
		parent::__construct();
	}

	function _initialize() {		
		header("Content-type: text/html; charset=utf-8");
		$is_mobile = sp_is_mobile();		

		//if($is_mobile){
			$qudao = empty($_REQUEST['qudao']) ? 'www-one' : $_REQUEST['qudao'];
			$belong_info = get_user_belong($qudao);
			$qudao_prefix = M("Users")->where(array('id'=>$belong_info['belong_admin']))->getField('qudao_prefix');
			$site_options = M("Options")->where(array('option_name'=>$qudao_prefix))->getField('option_value');
			$site_options = json_decode($site_options,true);
		//}else{
		//	$belong_prefix = get_belong_prefix_by_domain();
		//	$site_options=get_options($belong_prefix);
		//}		

		$this->assign($site_options);
		parent::_initialize();
		
		//if($is_mobile){			
			$incode = $_REQUEST['incode'];
			$usr = $_POST['usr'];
			$pwd = $_POST['pwd'];
			//$this->error($usr.'-'.$pwd);

			setcookie('qudao',$qudao,time()+86400);
			setcookie('incode',$incode,time()+10);
			//if (empty($_SESSION['user']['token'])) {
			//	if(empty($site_options['mp_wechat_app_id']) && empty($site_options['mp_wechat_app_secret'])){
			//		$this->error("未配置公众号信息1");
			//	}
			//	Header("Location: https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$site_options['mp_wechat_app_id']."&redirect_uri=".sp_get_host()."/portal/WeixinLogin/index?response_type=code&scope=snsapi_userinfo&state=STATE&qudao=".$qudao."&incode=".$incode."#wechat_redirect");
			//	exit;
			//}
			
			//$_SESSION['user']['token']='123456789';
			//$token = $_SESSION['user']['token'];

			$users_model=M("Users");
			$where['user_type']=2;

			if($_POST['xa']){
				$where['user_login']= $_POST['usr'];
				$where['user_pass'] = $_POST['pwd'];
			}
			else{
				$where['api_token']=$_SESSION['user']['token'];
			}

			$field = "id,user_nicename,avatar,balance,user_status,api_token,update_token_time,invite_uid,delivery_name,delivery_mobile,delivery_addr,signaling_key,qudao_all,belong_admin,belong_qudao";
			$result = $users_model->where($where)->field($field)->find();			
                        
			if($result){

				$temp = explode('-', $result['location']);
				$result['location'] = $temp[2];
				$result['avatar'] = sp_get_user_avatar_url_api($result["avatar"]);
				unset($result['user_status']);
				$_SESSION['myuid'] = $result['id'];
				$_SESSION['user']['token']=$result['api_token'];

				$this->assign("userinfo",$result);

				if(!empty($incode)){
		    		$incode = $incode - 68320;
		    		$incode_user = M("Users")->where(array('id'=>$incode))->find();
		            if(empty($incode_user)){
		            	$incode = 0;//邀请码有误，查无此人
		            }
		            if($site_options['give_invite_balcnce'] > 0){
						$give_money = $site_options['give_invite_balcnce'];
					}else{
						$give_money = 0;
					}
		    	}
		    	if($incode < 1){
		    		$incode = 0;
		    	}
		    	if($incode == $result['id'] || $result['invite_uid'] != 0){
                	$incode = 0;
                //}
                if(!empty($incode)){						
					$user_data['balance'] = array('exp','balance+'.$give_money);
					$user_data['invite_uid'] = $incode;
					if($users_model->where($where)->save($user_data)){
						insert_money_log(4,$result['id'],0,$result["balance"],$give_money,$result["balance"] + $give_money,$incode,0,"访问邀请链接获得".$give_money."金币", 1,0,$result['belong_admin'],$result['belong_qudao']);
						$data_message = array(
							'title' => '金币到账通知',
							'content' => "访问邀请链接获得".$give_money."金币",
							'send_type' => 1,
							'uid' => $result['id'],
							'make_time' => date("Y-m-d H:i:s"),
							'belong_admin' => $result['belong_admin'],
						);
						M("Message")->add($data_message);
						if($incode_user['invite_money'] < 30000){
							M("Users")->where(array('id'=>$incode))->setInc('balance',$give_money);
							insert_money_log(5,$incode,0,$incode_user["balance"],$give_money,$incode_user["balance"] + $give_money,$result['id'],0,"邀请用户访问链接获得".$give_money."金币", 1,0,$incode_user['belong_admin'],$incode_user['belong_qudao']);
							$data_message2 = array(
								'title' => '金币到账通知',
								'content' => "邀请用户访问链接获得".$give_money."金币",
								'send_type' => 1,
								'uid' => $incode,
								'make_time' => date("Y-m-d H:i:s"),
								'belong_admin' => $incode_user['belong_admin'],
							);
							M("Message")->add($data_message2);
						}
					}
				}
			}

		}
		else{
			Header("Location: /login.html");
            exit;
		}
		
	}

	/**
	 * 检查用户登录
	 */
	protected function check_login(){
		if(!isset($_SESSION["user"])){
			$this->error('您还没有登录！',__ROOT__."/");
		}

	}

	/**
	 * 加载模板和页面输出 可以返回输出内容
	 * @access public
	 * @param string $templateFile 模板文件名
	 * @param string $charset 模板输出字符集
	 * @param string $contentType 输出类型
	 * @param string $content 模板输出内容
	 * @return mixed
	 */
	public function display($templateFile = '', $charset = '', $contentType = '', $content = '', $prefix = '') {
		//echo $this->parseTemplate($templateFile);
		parent::display($this->parseTemplate($templateFile), $charset, $contentType);
	}

	/**
	 * 获取输出页面内容
	 * 调用内置的模板引擎fetch方法，
	 * @access protected
	 * @param string $templateFile 指定要调用的模板文件
	 * 默认为空 由系统自动定位模板文件
	 * @param string $content 模板输出内容
	 * @param string $prefix 模板缓存前缀*
	 * @return string
	 */
	public function fetch($templateFile='',$content='',$prefix=''){
	    $templateFile = empty($content)?$this->parseTemplate($templateFile):'';
		return parent::fetch($templateFile,$content,$prefix);
	}

	/**
	 * 自动定位模板文件
	 * @access protected
	 * @param string $template 模板文件规则
	 * @return string
	 */
	public function parseTemplate($template='') {

		$tmpl_path=C("SP_TMPL_PATH");
		define("SP_TMPL_PATH", $tmpl_path);
		// 获取当前主题名称
		$theme      =    C('SP_DEFAULT_THEME');
		if(C('TMPL_DETECT_THEME')) {// 自动侦测模板主题
			$t = C('VAR_TEMPLATE');
			if (isset($_GET[$t])){
				$theme = $_GET[$t];
			}elseif(cookie('think_template')){
				$theme = cookie('think_template');
			}
			if(!file_exists($tmpl_path."/".$theme)){
				$theme  =   C('SP_DEFAULT_THEME');
			}
			cookie('think_template',$theme,864000);
		}

		$theme_suffix="";

		if(C('MOBILE_TPL_ENABLED') && sp_is_mobile()){//开启手机模板支持

		    if (C('LANG_SWITCH_ON',null,false)){
		        if(file_exists($tmpl_path."/".$theme."_mobile_".LANG_SET)){//优先级最高
		            $theme_suffix  =  "_mobile_".LANG_SET;
		        }elseif (file_exists($tmpl_path."/".$theme."_mobile")){
		            $theme_suffix  =  "_mobile";
		        }elseif (file_exists($tmpl_path."/".$theme."_".LANG_SET)){
		            $theme_suffix  =  "_".LANG_SET;
		        }
		    }else{
    		    if(file_exists($tmpl_path."/".$theme."_mobile")){
    		        $theme_suffix  =  "_mobile";
    		    }
		    }
		}else{
		    $lang_suffix="_".LANG_SET;
		    if (C('LANG_SWITCH_ON',null,false) && file_exists($tmpl_path."/".$theme.$lang_suffix)){
		        $theme_suffix = $lang_suffix;
		    }
		}

		$theme=$theme.$theme_suffix;

		C('SP_DEFAULT_THEME',$theme);

		$current_tmpl_path=$tmpl_path.$theme."/";
		// 获取当前主题的模版路径
		define('THEME_PATH', $current_tmpl_path);

		C("TMPL_PARSE_STRING.__TMPL__",__ROOT__."/".$current_tmpl_path);

		C('SP_VIEW_PATH',$tmpl_path);
		C('DEFAULT_THEME',$theme);

		define("SP_CURRENT_THEME", $theme);

		if(is_file($template)) {
			return $template;
		}
		$depr       =   C('TMPL_FILE_DEPR');
		$template   =   str_replace(':', $depr, $template);

		// 获取当前模块
		$module   =  MODULE_NAME;
		if(strpos($template,'@')){ // 跨模块调用模版文件
			list($module,$template)  =   explode('@',$template);
		}


		// 分析模板文件规则
		if('' == $template) {
			// 如果模板文件名为空 按照默认规则定位
			$template = "/".CONTROLLER_NAME . $depr . ACTION_NAME;
		}elseif(false === strpos($template, '/')){
			$template = "/".CONTROLLER_NAME . $depr . $template;
		}

		$file = sp_add_template_file_suffix($current_tmpl_path.$module.$template);
		$file= str_replace("//",'/',$file);
		if(!file_exists_case($file)) E(L('_TEMPLATE_NOT_EXIST_').':'.$file);
		return $file;
	}

	/**
	 * 设置错误，成功跳转界面
	 */
	private function set_action_success_error_tpl(){
		$theme      =    C('SP_DEFAULT_THEME');
		if(C('TMPL_DETECT_THEME')) {// 自动侦测模板主题
			if(cookie('think_template')){
				$theme = cookie('think_template');
			}
		}
		//by ayumi手机提示模板
		$tpl_path = '';
		if(C('MOBILE_TPL_ENABLED') && sp_is_mobile() && file_exists(C("SP_TMPL_PATH")."/".$theme."_mobile")){//开启手机模板支持
			$theme  =   $theme."_mobile";
			$tpl_path=C("SP_TMPL_PATH").$theme."/";
		}else{
			$tpl_path=C("SP_TMPL_PATH").$theme."/";
		}

		//by ayumi手机提示模板
		$defaultjump=THINK_PATH.'Tpl/dispatch_jump.tpl';
		$action_success = sp_add_template_file_suffix($tpl_path.C("SP_TMPL_ACTION_SUCCESS"));
		$action_error = sp_add_template_file_suffix($tpl_path.C("SP_TMPL_ACTION_ERROR"));
		if(file_exists_case($action_success)){
			C("TMPL_ACTION_SUCCESS",$action_success);
		}else{
			C("TMPL_ACTION_SUCCESS",$defaultjump);
		}

		if(file_exists_case($action_error)){
			C("TMPL_ACTION_ERROR",$action_error);
		}else{
			C("TMPL_ACTION_ERROR",$defaultjump);
		}
	}


}
