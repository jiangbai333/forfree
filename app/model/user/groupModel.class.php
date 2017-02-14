<?php

/**
 * @文件        groupModel.class.php   
 * @描述        子类文件
 * @功能        
 * @起始日期    2014-5-26 16:24:28    
 * @文件版本    cx_survey v0.1
 */
class groupModel extends model{
       /**
     *          从数据库取出分组数据
     * @param string $firmid 公司id
     * @return array|int 分组数据或操作码
     */
    public function group($firmid) {
        $firmid = md5($firmid);
        $data = $this->db->table('group')->field('groupid, name')->where("firmid='{$firmid}'")->select();
        if ( $this->db->numRows == 0 ) {
            return 1001; //没有分组
        }
        if ( array_foo($data) ) {
            $data = array(0=>$data);
        } 
        return $data;
    }
    
    /**
     * 
     * @param string $groupid 分组id
     * @return array|int 分组数据或操作码
     */
    public function groupMember($groupid) {
        $firmid = md5(ff::$lib['session']->get('firmid'));
        $sql = "SELECT cx_groupman.userid,cx_user.name FROM cx_user JOIN cx_groupman ON cx_user.userid=cx_groupman.userid WHERE cx_groupman.groupid='{$groupid}' AND cx_groupman.firmid='{$firmid}'";
        $data = $this->db->query($sql);
        if ( $this->db->numRows == 0 ) {
            return 1001; //没有人员
        }
        if ( array_foo($data) ) {
            $data = array(0=>$data);
        } 
        return $data;
    }    
    
    
    public function groupOtherMember($groupid) {
        $firmid = md5(ff::$lib['session']->get('firmid'));
        $sql = "SELECT cx_user.name,cx_user.userid FROM cx_group left JOIN cx_groupman on  cx_groupman.groupid='{$groupid}' right join cx_user ON cx_groupman.userid=cx_user.userid where (cx_groupman.groupid is null or  cx_groupman.groupid!='{$groupid}') AND cx_user.lv!='0'  AND cx_user.firmid='{$firmid}' group by cx_user.userid ";
        $data = $this->db->query($sql);
        if ( $this->db->numRows == 0 ) {
            return 1001; //没有人员
        }
        if ( array_foo($data) ) {
            $data = array(0=>$data);
        } 
        return $data;
    }
    
    /**
     *          删除分组
     * @param string $groupid 分组id
     */
    public function delGroup($groupid) {
        $firmid = md5(ff::$lib['session']->get('firmid'));
        $this->db->table('group')->where("groupid='{$groupid}'")->delete();
        $this->db->table('groupsurvey')->where("groupid='{$groupid}'")->delete();
        $this->db->table('groupman')->where("groupid='{$groupid}'")->delete();
    }

    /**
     *          删除分组中的人员
     * @param string $groupid 分组id 
     * @param string $userid 用户帐号
     */
    public function delGroupMember($groupid, $userid) {
        $firmid = md5(ff::$lib['session']->get('firmid'));
        $this->db->table('groupman')->where("groupid='{$groupid}' AND userid='{$userid}' AND firmid='{$firmid}'")->delete();
    }
    
    /**
     *          增加分组
     * @param string $firmid 公司id
     * @param string $groupName 分组名
     * @param string $groupid 分组id
     * @param array $userid 分组成员
     * @return boolean
     */
    public function addGroup($firmid, $groupName, $groupid, $userid) {
        $firmid = md5($firmid);
        $groupMember = array();
        $this->db->table('group')->where("firmid='{$firmid}' AND name='{$groupName}'")->select();
        if ( $this->db->numRows > 0) {
            return 1001; //分组已经存在
        } else {
            $this->db->table('group')->data(array('firmid'=>$firmid,'name'=>$groupName,'groupid'=>$groupid))->add();
            if ( sizeof($userid) ) {
                foreach ($userid as $v) {
                    $groupMember['userid'][] = $v;
                    $groupMember['groupid'][] = $groupid;
                    $groupMember['firmid'][] = $firmid;
                }
                $this->db->table('groupman')->data(array('userid'=>$groupMember['userid'],'groupid'=>$groupMember['groupid'],'firmid'=>$groupMember['firmid']))->add();
                return 1000; //建立成功,并添加了人员
            } else {
                return 1002; //创建成功,没添加人员 
            }
        }
    } 
    
    /**
     * 
     * @param type $partid
     * @param type $name
     * @return int 1001 分组已经存在
     */
    public function changeGroupName($groupid, $name) {
        $firmid = md5(ff::$lib['session']->get('firmid'));
        $this->db->table('part')->where("partid='{$groupid}' AND firmid='{$firmid}' AND name='{$name}'")->select();
        if ($this->db->numRows > 0) {
            return 1001;
        }
        $this->db->table('part')->data(array('name'=>$name))->where("partid='{$groupid}' AND firmid='{$firmid}'")->update();
        return 1000;
    }
    
    /**
     * 
     * @param type $partid
     * @return type
     */
    public function getGroupMemberNum($groupid) {
        $firmid = md5(ff::$lib['session']->get('firmid'));
        $this->db->table('groupman')->where("groupid='{$groupid}' AND firmid='{$firmid}'")->select();
        return $this->db->numRows;
    }
    
    public function addGroupMember($groupid, $userid) {
        $firmid = md5(ff::$lib['session']->get('firmid'));
        $data = array();
        foreach ($userid as $v) {
            $data['userid'][] = $v;
            $data['firmid'][] = $firmid;
            $data['groupid'][] = $groupid;
        }
        $this->db->table('groupman')->data(array('userid'=>$data['userid'],'groupid'=>$data['groupid'],'firmid'=>$data['firmid']))->add();
        return 1000;
    }
}
