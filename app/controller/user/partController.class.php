<?php

/**
 * @文件        partController.class.php   
 * @描述        子类文件
 * @功能        
 * @起始日期    2014-5-22 10:34:57    
 * @文件版本    cx_survey v0.1
 */
class partController extends controller{
    /**
     *          导入部门
     * 表单action指向这个动作
     */
    public function partImport() {
        if(empty($_FILES['file']['name'])){
            exit('<script>alert(\'请选择文件\')</script>');
        }
        $data = file($_FILES["file"]['tmp_name']);
        $errorFlag = FALSE;
        $parts = explode(",",$data[0]);
        
        if ( array_unique($parts) != $parts ) {
            $errorFlag = TRUE;
            echo $_FILES["file"]['name']. '文件内部门名';
            foreach (array_diff_assoc($parts, array_unique($parts)) as $k => $v) {
                echo ' <span style=\'color:blue\'>'. $v. '</span> ';
            }
            echo '重复,请检查并改正后重试<br>';
        }
        foreach($parts as $k => $v) {
            if (!preg_match(_CHAR_REG_, $v)) {
                $errorFlag = TRUE;
                echo $_FILES["file"]['name']. '文件内 <span style=\'color:blue;\'>'.$v . '</span> 编辑错误<br>';
            } else {
                if ( N('part')->touchPart($v) ) {
                    $errorFlag = TRUE;
                    echo $_FILES["file"]['name']. '文件内 <span style=\'color:blue;\'>'.$v . '</span> 部门已经存在<br>';
                }
            }
        }
        if ( !$errorFlag ) {
            $data = array();
            $firmid = md5(ff::$lib['session']->get('firmid'));
            foreach($parts as $k => $v) {
                $data['partid'][] = mark();
                $data['firmid'][] = $firmid;
                $data['name'][] = $v;
            }
            N('part')->partImport($data);
        }
    }
    
    /**
     *          APIs 返回所有当前公司的部门
     * 若firmid公司有部门则返回部门信息,格式如:[{0:{partid: "部门id",name: "部门名"},1:{partid: "部门id",name: "部门名"}}......]
     * 否则返回操作码:1001
     * 若非管理员权限,则返回操作码:4001
     */
    public function showPart() {
        if ( !$this->checkLevel() ) $this->ajaxReturn (4001);
        $firmid = ff::$lib['session']->get('firmid');
        $this->ajaxReturn(N('part')->part($firmid));
    }
    
    /**
     *          APIs 返回所有当前部门的所有成员
     * 若firmid公司有部门则返回部门信息,格式如:[{0:{userid: "用户id",name: "用户名"},1:{userid: "用户id",name: "用户名"}}......]
     * 否则返回操作码 1001
     * 若非管理员权限,则返回操作码:4001
     */
    public function showPartMember() {
        if ( !$this->checkLevel() ) $this->ajaxReturn (4001);
        $partid = $_POST['partid'];
        $this->ajaxReturn(N('part')->partMember($partid));
    }
    
    public function showPartOtherMember() {
        if ( !$this->checkLevel() ) $this->ajaxReturn (4001);
        $partid = $_POST['partid'];
        $this->ajaxReturn(N('part')->partOtherMember($partid));
    }
    
    /**
     *          删除部门接口
     * 需要前端post部门id"partid",删除数据库中所有与partid有关的项
     * 若非管理员权限,则返回操作码:4001
     */
    public function deletePart() {
        if ( !$this->checkLevel() ) $this->ajaxReturn (4001);
        $partid = $_POST['partid'];
        N('part')->delPart($partid);
    }
    
    /**
     *          删除部门中人员接口
     * 需要前段post部门id"partid"和用户id"userid"
     * 若非管理员权限,则返回操作码:4001
     */
    public function deletePartMember() {
        if ( !$this->checkLevel() ) $this->ajaxReturn (4001);
        $partid = $_POST['partid'];
        $userid = $_POST['userid'];
        N('part')->delPartMember($partid, $userid);
    }
    
    /**
     *          新增部门接口
     * 前端需要传入部门名name以及部门成员partMember,partMember以如下格式：
     * userid1@#userid2@#userid2@#userid4@#userid5@#userid6@#....@#useridn
     * 若非管理员权限,则返回操作码:4001
     * 返回如下操作码:
     * 1001 部门已经存在
     * 1000 新建成功,添加成员成功
     * 1002 新建成功,没添加成员
     */
    public function addPart() {
        if ( !$this->checkLevel() ) $this->ajaxReturn (4001);
        $firmid = ff::$lib['session']->get('firmid');
        $partName = $_POST['name'];
        if ( isset($_POST['partMember']) ) {
            $userid = explode("@#",$_POST['partMember']);
        } else {
            $userid = array();
        }        
        $partid = mark();
        $this->ajaxReturn(N('part')->addPart($firmid, $partName, $partid, $userid));
    }
    
    /**
     *          为部门添加成员
     * 部门id:partid
     * 部门成员:partMember 格式如下
     * userid1@#userid2@#userid2@#userid4@#userid5@#userid6@#....@#useridn
     * 若非管理员权限,则返回操作码:4001
     * 返回如下操作码:
     * 1001 没传入用户帐号
     * 1000 添加成员成功
     */
    public function addPartMember() {
        if ( !$this->checkLevel() ) $this->ajaxReturn (4001);
        $partid = $_POST['partid'];
        if ( empty($_POST['partMember']) ) {
            $this->ajaxReturn(1001);
        }
        $userid = explode("@#",$_POST['partMember']);
        $this->ajaxReturn(N('part')->addPartMember($partid, $userid));
    }

    /**
     *          修改部门
     * 部门id:partid
     * 新部门名:name
     * 返回如下操作码:
     * 1001 将修改的部门名,已经有其它部门在使用
     * 1000 修改成功
     */
    public function changePartName() {
        $partid = $_POST['partid'];
        $name = $_POST['name'];
        $this->ajaxReturn(N('part')->changePartName($partid, $name));
    }
    
    /**
     *          获得部门内人数
     * 部门id:partid
     * 返回部门中人数
     */
    public function getPartMemberNum() {
        $partid = $_POST['partid'];
        $this->ajaxReturn(N('part')->getPartMemberNum($partid));
    }
}