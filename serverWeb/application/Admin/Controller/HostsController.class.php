<?php
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class HostsController extends AdminbaseController {
    protected $users_model;
    function _initialize() {
        parent::_initialize();
        $this->users_model =M("Users");
    }
    function index(){
        if(IS_POST){
            if (isset($_POST['keyword']) && !empty($_POST['keyword'])){
                $keyword = $_POST['keyword'];
                $where = " user_login like '%$keyword%' and";
            }
            // if (isset($_POST['host_status'])&& ($_POST['host_status']!='')){
            //  $host_status = $_POST['host_status'];
            //  $where .= " is_host = $host_status";
            // }
        }

        $where .= " is_host = 1";
        $count=$this->users_model->where($where)->count();
        $page = $this->page($count, 20);
        $users=$this->users_model->where($where)->limit($page->firstRow . ',' . $page->listRows)->select();
        $this->assign("Page", $page->show('Admin'));
        $this->assign("formget",$_POST);
        $this->assign("users",$users);
        $status_name = array("已封禁","正常");
        $this->assign("status_name",$status_name);
        $host_status = array("否","<font color ='red'>是</font>");
        $this->assign("host_status",$host_status);
        $this->display();
    }

    function set($id){
        $id= intval($id);
        $user=$this->users_model->where("id=$id")->find();
        if($user){
            if($user['is_host']==0){
                $data['is_host']=1;
                if($this->users_model->where("id=$id")->save($data))
                {
                    $channels_model = M("channels");
                    $data['channel_creater'] = $id;
					$result = $channels_model->where($data)->getField("id");
					if(empty($result)){
						$data['created_date'] = date('Y-m-d H:i:s',time());
	                    $data['channel_title'] = $user['user_login'].'的直播间';
						$data['channel_description'] = $user['user_login'].'的直播间';
	                    $stream_key = sp_random_string(32);
	                    $strem_id = strtolower(sp_random_string(16));

	                    if(!$stream_key || !$strem_id )
	                    {
	                        $this->error("创建失败,流媒体参数不完整！");
	                    }
	                    $channel_source =$strem_id;
	                    $channel_source_hls =$strem_id;
	                    $channel_stream =$strem_id;
	                    $data['stream_key'] = $stream_key;
	                    $data['channel_source'] = $channel_source;
	                    $data['channel_source_hls'] = $channel_source_hls;
	                    $data['channel_stream'] = $channel_stream;
	                    $data['channel_status'] = 1;
	                    $data['channel_type'] = 1;
	                    $channel_id = $channels_model->add($data);

                        //为直播间创建LeanCloud 聊天室ID
                        $temp = create_leancloud_room_id($channel_id);
                        if($temp['code'] == 1){
                            $channels_model->where(array('id'=>$channel_id))->setField('leancloud_room',$temp['objectId']);
                        }

	                    $channel_term_relationships_model=M("channel_term_relationships");
	                    $item['term_id'] = 1;
	                    $item['status'] = 1;
	                    $item['object_id'] = $channel_id;
	                    $tid = $channel_term_relationships_model->add($item);
					}

                    $this->success('主播审核成功');
                }else
                    $this->error('设置主播失败');
            }else{
                    $this->error('该用户已经是主播了');
            }
        }else{
            $this->error('指定的用户不存在');
        }
    }

    function cancel(){
        $id= intval(I("get.id"));
        $user=$this->users_model->where("id=$id")->find();
        if($user){
            if($user['is_host']==1){
                $data['is_host']=0;
                if($this->users_model->where("id=$id")->save($data))
                {
                    $channel_term_relationships_model = M("channel_term_relationships");
                    $channels_model = M("channels");
                    $ids = $channels_model->field("id")->where("channel_creater=$id")->select();
                    if(!empty($ids))
                    {
                        $n = 0;
                        foreach($ids as $key=>$value)
                        {
                            $ids[$n] = $value['id'];
                            $n++;
                        }
                        $map['object_id'] = array('in',$ids);
                        $data['status'] = 0;
                        $channel_term_relationships_model->where($map)->save($data);
                    }
                    $this->success('取消成功');
                }else
                    $this->error('取消失败');
            }else{
                    $this->error('该用户还不是主播哦');
            }
        }else{
            $this->error('指定的用户不存在');
        }
    }



    private function set_truename($id){
        $id= intval($id);
        $user=$this->users_model->where("id=$id")->find();
        if($user){
            if($user['is_truename']==1){
                $this->set($id);
            }else{
                $data['is_truename']=1;
                if($this->users_model->where("id=$id")->save($data))
                {
                    $this->set($id);
                }else{
                    $this->error('服务器繁忙，请稍后再试');
                }

            }
        }else{
            $this->error('指定的用户不存在');
        }
    }

    function cancel_host($id){
        $id= intval($id);
        $user=$this->users_model->where("id=$id")->find();
        if($user){
            $data['user_status'] = 0;
            if($user['is_host']==1){
                $data['is_host']=0;
                if($this->users_model->where("id=$id")->save($data))
                {
                    $this->success('取消主播身份成功');
                }else
                    $this->error('取消主播身份操作失败');
            }else{
                    $this->error('该主播已经取消过了');
            }
        }else{
            $this->error('指定的主播不存在');
        }
    }

    //推荐
    function set_recommend($id){
        $id= intval($id);
        $user=$this->users_model->where("id=$id")->find();
        if($user){
            if($user['is_recommend']==0){
                $data['is_recommend']=1;
                $data['recommend_time']=date('Y-m-d H:i:s',time());
                if($this->users_model->where("id=$id")->save($data))
                {
                    $this->success('推荐主播成功');
                }else
                    $this->error('推荐主播失败');
            }else{
                    $this->error('该用户已经是推荐过了');
            }
        }else{
            $this->error('指定的用户不存在');
        }
    }
    //取消推荐
    function cancel_recommend(){
        $id= intval(I("get.id"));
        $user=$this->users_model->where("id=$id")->find();
        if($user){
            if($user['is_recommend']==1){
                $data['is_recommend']=0;
                if($this->users_model->where("id=$id")->save($data))
                {
                    $this->success('取消推荐成功');
                }else
                    $this->error('取消推荐失败');
            }else{
                    $this->error('该用户还没有被推荐哦');
            }
        }else{
            $this->error('指定的用户不存在');
        }
    }


    function applylist(){
        if(IS_POST){
            if (isset($_POST['keyword']) && !empty($_POST['keyword'])){
                $keyword = $_POST['keyword'];
                $where = " user_login like '%$keyword%'";
            }
            if (isset($_POST['host_status'])&& ($_POST['host_status']!='')){
                $host_status = $_POST['host_status'];
                if($host_status == 0){
                    $where2 = " name_status= 1 and status=3";
                    $selected_index  = 1;
                }
                if($host_status == 1){
                    $where2 = " name_status<> 3 and status=3";
                    $selected_index  = 2;
                }
            }
            if (isset($_POST['host_status'])&& ($_POST['host_status']=='')){
                $where2 = "name_status<> 3 and status=3";
            }
        }else{
            $where2 = " name_status = 1 and status=3";
            $selected_index  = 1;
        }
        $apply_host_model = M("apply_truename");
        if(empty($where))
        {
            $count = $apply_host_model -> where($where2)->order('id desc')->count();
            $page = $this->page($count, 20);
            $records=$apply_host_model -> where($where2)->order('id desc')->limit($page->firstRow . ',' . $page->listRows)->select();
            $this->assign("Page", $page->show('Admin'));
        }else{
            $ids = $this->users_model->field('id')->where($where)->select();
            if(!empty($ids))
            {
                $n = 0;
                foreach($ids as $key=>$value)
                {
                    $ids[$n] = $value['id'];
                    $n++;
                }
                $map['uid'] = array('in',$ids);
                $count = $apply_host_model -> where($where2) ->where($map)->order('id desc') ->count();
                $page = $this->page($count, 20);
                $records= $apply_host_model -> where($where2)  ->where($map) ->order('id desc') ->limit($page->firstRow . ',' . $page->listRows)->select();
                $this->assign("Page", $page->show('Admin'));
            }
        }
        //var_dump($records);
        $this->assign("formget",$_POST);
        $this->assign("records",$records);
        $this->assign("selected_index",$selected_index);
        $status_name = array("<font color='red'>审批拒绝</font>","等待审批","<font color='green'>审批通过</font>");
        $this->assign("status_name",$status_name);
        $users_obj=M("Users");
        $users_data=$users_obj->field("id,user_login")->where("user_status=1")->select();
        $users=array();
        foreach ($users_data as $u){
            $users[$u['id']]=$u;
        }
        $this->assign("users",$users);
        $this->display();
    }

	function approvelist(){
		$where_ands = array();
		$fields=array(
				'host_status'=> array("field"=>"a.status","operator"=>"="),
				'keyword'  => array("field"=>"b.user_login","operator"=>"like"),
		);
        if(IS_POST){
			foreach($fields as $param => $val){
				if(isset($_POST[$param]) && !empty($_POST[$param])){
					$operator=$val['operator'];
					$field   =$val['field'];
					$get=$_POST[$param];
					$_GET[$param]=$get;
					if($param == 'host_status' && $get == 3){
						$field = 3;
					}
					if($operator=="like"){
						$get="%$get%";
					}
					array_push($where_ands, "$field $operator '$get'");
				}
			}
        }else{
            foreach($fields as $param => $val){
				if(isset($_GET[$param]) && !empty($_GET[$param])){
					$operator=$val['operator'];
					$field   =$val['field'];
					$get=$_GET[$param];
					$_GET[$param]=$get;
					if($param == 'host_status' && $get == 3){
						$field = 3;
					}
					if($operator=="like"){
						$get="%$get%";
					}
					array_push($where_ands, "$field $operator '$get'");
				}
			}
        }
		if(empty($where_ands)){
			$where_ands = array("a.status = 1");
            $_GET['host_status']  = 1;
		}
		$where = join(" and ", $where_ands);
        $apply_host_model = M("apply_host");

		$count = $apply_host_model->alias("a")->join(C("DB_PREFIX")."users b on b.id = a.uid")->where($where)->count();
		$page = $this->page($count, 15);
        $field = "a.id as apply_id,a.reason,a.status,a.apply_time,a.apply_type,b.*";
		$records = $apply_host_model->alias("a")->join(C("DB_PREFIX")."users b on b.id = a.uid")->where($where)->field($field)->limit($page->firstRow . ',' . $page->listRows)->order("a.id ASC")->select();

        //dump($records);
        $this->assign("formget",$_GET);
        $this->assign("records",$records);
        $status_name = array("<font color='red'>审批拒绝</font>","等待审批","<font color='green'>审批通过</font>");
        $this->assign("status_name",$status_name);
		$this->assign("Page", $page->show('Admin'));
        $this->display();
    }

    function decline(){
        $id= intval(I("get.id"));
        $data['about'] = I("post.about");
        $apply_host_model = M("apply_host");
        $data['status'] = 0;
        $data['check_time'] = date('Y-m-d H:i:s',time());
        if($apply_host_model->where("id=$id")->save($data))
            $this->success('拒绝请求成功');
        else
            $this->error('拒绝请求失败');

    }

    function accept(){
        $id= intval(I("get.id"));
        $data['about'] = I("post.about");
        $apply_host_model = M("apply_host");
        $data['status'] =2;
        $data['check_time'] = date('Y-m-d H:i:s',time());
        if($apply_host_model->where("id=$id")->save($data))
        {
            $info=$apply_host_model ->where("id=$id")->field("uid")->find();
            if($info)
                $this->set($info['uid']);
            else
                $this->error('设置主播失败：未找到该用户信息');
        }else{
            $this->error('请求通过失败');
        }
    }

}