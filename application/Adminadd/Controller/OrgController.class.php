<?php
// | Create: 2016/3/29 
// +----------------------------------------------------------------------
// | Author: 海枯 <haiku888@foxmail.com> 
// +----------------------------------------------------------------------
// | Description:  
// +----------------------------------------------------------------------
namespace Adminadd\Controller;
use Common\Controller\AdminbaseController;
class OrgController extends AdminbaseController
{
    protected $UserModel;
    function _initialize()
    {
        parent::_initialize(); // TODO: Change the autogenerated stub
        $this->UserModel=M('Users');
    }

    public function org_verify(){
        $where_ands=array("user_status=3 and user_type=1");
        $fields=array(
            'start_time'=> array("field"=>"create_time","operator"=>">"),
            'end_time'  => array("field"=>"create_time","operator"=>"<"),
            'keyword'  => array("field"=>"manager_name","operator"=>"like"),
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

        $count=$this->UserModel
            ->alias("a")
            ->join(C('DB_PREFIX')."organization as b ON a.id=b.users_id")
            ->where($where)->count();
        $page = $this->page($count, 10);

        $posts=$this->UserModel
            ->alias("a")
            ->join(C('DB_PREFIX')."organization as b ON a.id=b.users_id")
            ->where($where)->limit($page->firstRow . ',' . $page->listRows)->select();

        $this->assign("Page", $page->show('Admin'));
        $this->assign("formget",$_GET);
        $this->assign("posts",$posts);

        $this->display(":org_verify");
    }
    public function refuse(){
        if(isset($_POST['ids'])){
            $ids = implode(",", $_POST['ids']);
            $data=array("user_status"=>"4");
            if ($this->UserModel->where("id in ($ids)")->save($data)) {
                $this->success("拒绝成功！");
            } else {
                $this->error("拒绝失败！");
            }
        }else{
            if(isset($_GET['id'])){
                $id = intval(I("get.id"));
                $data=array("id"=>$id,"user_status"=>"4");
                if ($this->UserModel->save($data)) {
                    $this->success("拒绝成功！");
                } else {
                    $this->error("拒绝失败！");
                }
            }
        }
    }
    public  function agree(){
        if(isset($_POST['ids'])){
            $ids = implode(",", $_POST['ids']);
            $data=array("user_status"=>"2");
            if ($this->UserModel->where("id in ($ids)")->save($data)) {
                $this->success("同意成功！");
            } else {
                $this->error("同意失败！");
            }
        }else{
            if(isset($_GET['id'])){
                $id = intval(I("get.id"));
                $data=array("id"=>$id,"user_status"=>"2");
                if ($this->UserModel->save($data)) {
                    $this->success("同意成功！");
                } else {
                    $this->error("同意失败！");
                }
            }
        }
    }
}