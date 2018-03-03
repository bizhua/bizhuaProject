<?php
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class LiveMonitorController extends AdminbaseController {

    public function index() {
        $channel_term_relationships_model = M("ChannelTermRelationships");
        $where = "a.status=1 and b.channel_status = 2 and c.user_status = 1 and c.is_host = 1 and c.user_type > 1";
        //$where = "a.status=1 and b.channel_status = 2 and c.user_status = 1 and c.is_host = 1";
        $count=$channel_term_relationships_model
        ->alias("a")
        ->join(C ( 'DB_PREFIX' )."channels b ON a.object_id = b.id")
        ->join(C('DB_PREFIX')."users c on c.id = b.channel_creater")
        ->where($where)
        ->count();
        $page = $this->page($count, 16);

        $field = "b.id,b.channel_creater,b.channel_title,b.channel_source,b.stream_type,b.third_stream,c.user_login,c.user_nicename";

        $channels=$channel_term_relationships_model
        ->alias("a")
        ->join(C ( 'DB_PREFIX' )."channels b ON a.object_id = b.id")
        ->join(C('DB_PREFIX')."users c on c.id = b.channel_creater")
        ->where($where)
        ->field($field)
        ->limit($page->firstRow . ',' . $page->listRows)
        ->order("a.listorder ASC,b.id DESC")->select();

        $this->assign("Page", $page->show('Admin'));
        $this->assign("current_page",$page->GetCurrentPage());
        $this->assign("channels",$channels);

    	$this->display();
    }
    
}