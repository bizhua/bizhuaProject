<?php
namespace User\Controller;
use Common\Controller\AdminbaseController;
class FamilyController extends AdminbaseController {
    protected $family_model;
    function _initialize() {
        parent::_initialize();
        $this->family_model =M("Family");
    }
    function index(){
        $uid = get_current_admin_id();
        if($uid != 1){
            $family_creater = M("Users")->where(array('id'=>$uid))->getField('user_login');
            $where_ands = array("family_creater = $family_creater");
        }else{
            $where_ands = array();
        }

        $fields=array(
                'keyword'  => array("field"=>"name","operator"=>"like"),
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
        $where_ands[] = "type = 2";
        //dump($where_ands);
        $where= join(" and ", $where_ands);

    	$family_model=M("Family");
    	$count=$family_model->where($where)->count();
    	$page = $this->page($count, 15);
    	$lists = $family_model
    	->where($where)
    	->order("apply_time DESC")
    	->limit($page->firstRow . ',' . $page->listRows)
    	->select();

        //$user_from = array('','PC端','手机号','QQ','Wechat','SinaWeibo');

    	$this->assign('lists', $lists);
        //$this->assign('user_from', $user_from);
    	$this->assign("page", $page->show('Admin'));
        $this->assign("formget",$_GET);
    	$this->display();
    }

    //编辑
    public function edit(){
        $id = I("get.id");
        $result = $this->family_model->where(array("id"=>$id))->find();
        $this->assign("data",$result);

        $this->display();
    }

     //家族人员列表
    public function memberList(){
        $id = I("get.id");
        $where = array('b.user_status'=>1,'a.id'=>$id);
        $result = $this->family_model
            ->alias("a")
            ->join(C ( 'DB_PREFIX' )."users b ON a.id = b.family_id")
            ->where($where)
            ->select();
        $this->assign("lists",$result);
        $this->display("memberList");
    }

     //家族人员列表
    public function exit_family(){
        $id = I("get.id");
        $family_id = I("get.family_id");
        $userModel = M("Users");
        $where['id'] = $id;
        $info['family_id'] = 0;
        $result = $userModel->where($where)->save($info);
        if($result){
            $this->success("退出成功");
        }else{
            $this->error("操作失败");
        }
    }

     //家族人员列表
    public function family_income(){
        $where_ands=array();
        $fields=array(
                'trans_type'  => array("field"=>"a.trans_type","operator"=>"="),
                'start_time'=> array("field"=>"a.log_time","operator"=>">="),
                'end_time'  => array("field"=>"a.log_time","operator"=>"<="),
                'uid'  => array("field"=>"a.uid2","operator"=>"="),

                'id'  => array("field"=>"a.family_id","operator"=>"="),
        );
        if(IS_POST){
            if(empty($_POST['currency_type']))
            {
                $_POST['currency_type'] = 1;
            }
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
            if(empty($_GET['currency_type']))
            {
                $_GET['currency_type'] = 1;
            }
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
        $where = $where."and a.trans_type = 6";
        $money_log_model = M("money_log");
        $join = "left join ".C("DB_PREFIX")."users b on b.id = a.uid";
        $join2 = "left join ".C("DB_PREFIX")."users c on c.id = a.uid2";
        $count=$money_log_model->alias('a')->join($join)->join($join2)->where($where)->order('a.id desc')->count();
        $page = $this->page($count, 20);
        $logs=$money_log_model->alias('a')->join($join)->join($join2)->where($where)->field("a.*,b.user_login,b.user_nicename,c.user_login as user_login2,c.user_nicename as user_nicename2")->limit($page->firstRow . ',' . $page->listRows)->order('a.id desc')->select();
        $all_money = $money_log_model->alias('a')->join($join)->join($join2)->where($where)->sum("money");
        if (empty($all_money)) {
            $all_money = 0;
        }


        $item_type_name = array(
            "1"=>"扣款",
            "2"=>"充值",
            "3"=>"赠送普通礼物",
            "4"=>"提现",
            "5"=>"提现退回",
            "6"=>"收到普通礼物",
            "13"=>"收益兑换余额",
            "14"=>"家族长提成",
            "17"=>"直播间付费",
            "18"=>"付费直播间收入",
            "19"=>"分钟计费",
            "20"=>"分钟计费收入",
            "21"=>"余额兑换游戏币",
            "22"=>"赠送游戏礼物",
            "23"=>"收到游戏礼物",
            "24"=>"余额兑换游戏币",
            "25"=>"收益兑换余额",
            "26"=>"游戏币竞猜",
            "27"=>"游戏收益结算",
        );
        $this->assign("Page", $page->show('Admin'));
        $this->assign("all_money",$all_money);
        $this->assign("formget",$_GET);
        $this->assign("logs",$logs);
        $this->assign("item_type_name",$item_type_name);
        $this->display("income_detail");
    }

    //解散家族
    public function jiesan(){
        $id = I("get.id");
        $result = $this->family_model->where(array("id"=>$id))->find();
        if(empty($result)){
            $this->error("参数有误");
        }else{
            $family["type"] = 3;
            if($this->family_model->where(array("id"=>$id))->save($family)){
                $userModel = M("Users");
                $where['family_id'] = $id;
                $info['family_id'] = 0;
                $result = $userModel->where($where)->save($info);

                $this->success("解散家族成功");
            }else{
                $this->error("解散家族失败");
            }
        }
    }

    public function edit_post(){
        if(IS_POST && !empty($_POST["name"]) && !empty($_POST["family_img"]) && !empty($_POST["family_introduction"]) && !empty($_POST["family_creater_commission"]) && !empty($_POST["family_password"])){

            $family["name"] = I("post.name");
            $family["family_introduction"] = I("post.family_introduction");
            $family["family_img"] =  I("post.family_img");
            $family["family_creater_commission"] =  I("post.family_creater_commission");
            $family["family_password"] =  I("post.family_password");

            $id = I("get.id");
            if($this->family_model->where(array("id"=>$id))->save($family)){
                //$this->success("修改成功！", U("Portal/AdminGift/index"));
                $this->success("修改成功！");
            }else{
                $this->error("修改失败！");
            }
        }else{
            $this->error("信息未填写完整！");
        }
    }



}
