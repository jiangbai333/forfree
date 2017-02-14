<?php

/**
 * @文件        groupController.class.php   
 * @描述        子类文件
 * @功能        
 * @起始日期    2014-5-26 16:24:02    
 * @文件版本    cx_survey v0.1
 */
class groupController extends controller{
        
    /**
     *          APIs 返回所有当前公司的分组
     * 若firmid公司有分组则返回分组信息,格式如:[{0:{groupid: "组id",name: "组名"},1:{groupid: "组id",name: "组名"}}......]
     * 否则返回操作码:1001
     * 若非管理员权限,则返回操作码:4001
     */
    public function showGroup() {
        if ( !$this->checkLevel() ) $this->ajaxReturn (4001);
        $firmid = ff::$lib['session']->get('firmid');
        $this->ajaxReturn(N('group')->group($firmid));
    }
    
    /**
     *          APIs 返回所有当前分组的所有成员
     * 若firmid公司有分组则返回分组信息,格式如:[{0:{userid: "用户id",name: "用户名"},1:{userid: "用户id",name: "用户名"}}......]
     * 否则返回操作码 1001
     * 若非管理员权限,则返回操作码:4001
     */
    public function showGroupMember() {
        if ( !$this->checkLevel() ) $this->ajaxReturn (4001);
        $groupid = $_POST['groupid'];
        $this->ajaxReturn(N('group')->groupMember($groupid));
    }
    
    
    public function showGroupOtherMember() {
        if ( !$this->checkLevel() ) $this->ajaxReturn (4001);
        $groupid = $_POST['groupid'];
        $this->ajaxReturn(N('group')->groupOtherMember($groupid));
    }
    
    /**
     *          删除分组接口
     * 需要前端post分组id"groupid",删除数据库中所有与groupid有关的项
     * 若非管理员权限,则返回操作码:4001
     */
    public function deleteGroup() {
        if ( !$this->checkLevel() ) $this->ajaxReturn (4001);
        $groupid = $_POST['groupid'];
        N('group')->delGroup($groupid);
    }
    
    /**
     *          删除分组中人员接口
     * 需要前段post分组id"groupid"和用户id"userid"
     * 若非管理员权限,则返回操作码:4001
     */
    public function deleteGroupMember() {
        if ( !$this->checkLevel() ) $this->ajaxReturn (4001);
        $groupid = $_POST['groupid'];
        $userid = $_POST['userid'];
        N('group')->delGroupMember($groupid, $userid);
    }
    
    /**
     *          新增分组接口
     * 前端需要传入分组名name以及分组成员groupMember,groupMember以如下格式：
     * userid1@#userid2@#userid2@#userid4@#userid5@#userid6@#....@#useridn
     * 若非管理员权限,则返回操作码:4001
     * 返回如下操作码:
     * 1001 分组已经存在
     * 1000 新建成功,添加成员成功
     * 1002 新建成功,没添加成员
     */
    public function addGroup() {
        if ( !$this->checkLevel() ) $this->ajaxReturn (4001);
        $firmid = ff::$lib['session']->get('firmid');
        $groupName = $_POST['name'];
        if ( isset($_POST['groupMember']) ) {
            $userid = explode("@#",$_POST['groupMember']);
        } else {
            $userid = array();
        }    
        $groupid = mark();
        $this->ajaxReturn(N('group')->addGroup($firmid, $groupName, $groupid, $userid));
    }
    
    /**
     *          为分组添加成员
     * 分组id:groupid
     * 分组成员:groupMember 格式如下
     * userid1@#userid2@#userid2@#userid4@#userid5@#userid6@#....@#useridn
     * 若非管理员权限,则返回操作码:4001
     * 返回如下操作码:
     * 1001 没传入用户帐号
     * 1000 添加成员成功
     */
    public function addGroupMember() {
        if ( !$this->checkLevel() ) $this->ajaxReturn (4001);
        $groupid = $_POST['groupid'];
        if ( empty($_POST['groupMember']) ) {
            $this->ajaxReturn(1001);
        }
        $userid = explode("@#",$_POST['groupMember']);
        $this->ajaxReturn(N('group')->addGroupMember($groupid, $userid));
    }
 
    /**
     *          修改分组名
     * 分组id:groupid
     * 新分组名:name
     * 返回如下操作码:
     * 1001 将修改的分组名,已经有其它分组在使用
     * 1000 修改成功
     */
    public function changeGroupName() {
        $groupid = $_POST['groupid'];
        $name = $_POST['name'];
        $this->ajaxReturn(N('part')->changeGroupName($groupid, $name));
    }
    
    /**
     *          获得分组内人数
     * 分组id:groupid
     * 返回分组中人数
     */
    public function getGroupMemberNum() {
        $groupid = $_POST['groupid'];
        $this->ajaxReturn(N('group')->getGroupMemberNum($groupid));
    }
}
