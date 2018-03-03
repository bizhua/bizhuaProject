<?php
namespace User\Controller;
use Common\Controller\AdminbaseController;
class IndexadminController extends AdminbaseController {
    function index(){
        $admin_id = get_current_admin_id();
        if($admin_id == 1){
            $where_ands = array("user_type = 2");
        }else{
            $where_ands = array("user_type = 2 and belong_admin = $admin_id");
        }        

        $fields=array(
                'keyword'  => array("field"=>"user_login","operator"=>"like"),
                'id' => array("field"=>"id","operator"=>"="),
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

    	$users_model=M("Users");
    	$count=$users_model->where($where)->count();
    	$page = $this->page($count, 15);
    	$lists = $users_model
    	->where($where)
    	->order("create_time DESC")
    	->limit($page->firstRow . ',' . $page->listRows)
    	->select();

        $user_from = array('','PC端','手机号','QQ','Wechat','SinaWeibo');

    	$this->assign('lists', $lists);
        $this->assign('user_from', $user_from);
    	$this->assign("page", $page->show('Admin'));
        $this->assign("formget",$_GET);    	
    	$this->display(":index");
    }
    
    function ban(){
    	$id=intval($_GET['id']);
        $admin_id = sp_get_current_admin_id();
    	if ($id) {
    		$rst = M("Users")->where(array("id"=>$id,"user_type"=>2,'belong_admin'=>$admin_id))->setField(array('user_status'=>0,'api_token'=>sp_random_string(32)));
    		if ($rst !== false) {
    			//$this->success("会员拉黑成功！", U("indexadmin/index"));
				$this->success("会员拉黑成功！");
    		} else {
    			$this->error('会员拉黑失败！');
    		}
    	} else {
    		$this->error('数据传入失败！');
    	}
    }
    
    function cancelban(){
    	$id=intval($_GET['id']);
        $admin_id = sp_get_current_admin_id();
    	if ($id) {
    		$rst = M("Users")->where(array("id"=>$id,"user_type"=>2,'belong_admin'=>$admin_id))->setField('user_status','1');
    		if ($rst !== false) {
    			//$this->success("会员启用成功！", U("indexadmin/index"));
				$this->success("会员启用成功！");
    		} else {
    			$this->error('会员启用失败！');
    		}
    	} else {
    		$this->error('数据传入失败！');
    	}
    }

    //查看用户详情
    function info(){
        $id=intval($_GET['id']);
        if ($id) {
            $data = M("Users")->where(array("id"=>$id,"user_type"=>2))->field("id,user_login,user_nicename,true_name,user_email,mobile,location,avatar,sex,birthday,signature,last_login_ip,last_login_time,create_time,user_status,balance,total_earn,total_spend,vip_deadline,is_host,attention_num,fans_num")->find();
            if (!empty($data)) {
                if($data['is_host'] == 1){
                    $channel = M("Channels")->where(array('channel_creater'=>$id))->field("id as channel_id,times,last_begin_time,duration")->find();
                    $data = array_merge($data,$channel);
                }
                $this->assign('data', $data);
                $this->display(":info");
            } else {
                $this->error('没有查询到数据');
            }
        } else {
            $this->error('数据传入失败！');
        }
    }

    //查看用户详情
    function info1(){
        $id=intval($_GET['id']);
        if ($id) {
            $data = M("Users")->where(array("id"=>$id,"user_type"=>2))->field("id,user_login,user_nicename,true_name,user_email,mobile,location,avatar,sex,birthday,signature,last_login_ip,last_login_time,create_time,user_status,balance,total_earn,total_spend,vip_deadline,is_host,attention_num,fans_num")->find();
            if (!empty($data)) {
                if($data['is_host'] == 1){
                    $channel = M("Channels")->where(array('channel_creater'=>$id))->field("id as channel_id,times,last_begin_time,duration")->find();
                    $data = array_merge($data,$channel);
                }
                $this->assign('data', $data);
                $this->display(":info1");
            } else {
                $this->error('没有查询到数据');
            }
        } else {
            $this->error('数据传入失败！');
        }
    }

    //管理员重置用户密码
    function reset_password(){
        $id = intval(I('post.id'));
        $pass = I('post.pass','','htmlspecialchars');
        if(empty($id) || empty($pass)){
            $this->error('数据传入失败！');
        }
        $user = M("Users")->where(array("id"=>$id,"user_status"=>1))->find();
        if(empty($user)){
            $this->error('用户不存在！');
        }
        $status = M("Users")->where(array("id"=>$id,"user_status"=>1))->setField('user_pass', sp_password($pass));
        if($status !== false){
            $this->success("重置成功！");
        }else{
            $this->error('重置失败！');
        }
    }
    
    function advanced(){
        if(isset($_POST['ids']) && $_GET["advanced"]){
            $data["advanced_administrator"]=1;

            $ids=join(",", $_POST['ids']);
            if ( M("Users")->where("id in ($ids)")->save($data)!==false) {
                $this->success("设为超管成功！");
            } else {
                $this->error("设为超管失败！");
            }
        }
        if(isset($_POST['ids']) && $_GET["unadvanced"]){

            $data["advanced_administrator"]=0;
            
            $ids=join(",", $_POST['ids']);
            if ( M("Users")->where("id in ($ids)")->save($data)) {
                $this->success("取消超管成功！");
            } else {
                $this->error("取消超管失败！");
            }
        }
    }

    //change_info
    function change_info(){
        $id = intval(I('post.id'));
        $name = I('post.name','','htmlspecialchars');
        $text = I('post.text','','htmlspecialchars');
        if(empty($id) || empty($name) || empty($text)){
            $this->error('数据传入失败！');
        }
        $user = M("Users")->where(array("id"=>$id,"user_status"=>1))->find();
        if(empty($user)){
            $this->error('用户不存在！');
        }
        $status = M("Users")->where(array("id"=>$id,"user_status"=>1))->setField($name, $text);
        if($status !== false){
            $this->success("修改成功！");
        }else{
            $this->error('修改失败！');
        }
    }
}
