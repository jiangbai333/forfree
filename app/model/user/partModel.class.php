<?php

/**
 * @文件        partModel.class.php   
 * @描述        子类文件
 * @功能        
 * @起始日期    2014-5-22 12:39:17    
 * @文件版本    cx_survey v0.1
 */
class partModel extends model{
    /**
     *          检查部门是否存在
     * @param string $name 部门名
     * @return boolean 部门存在返回真，不存在返回假
     */
    public function touchPart($name) {
        $firmid = md5(ff::$lib['session']->get('firmid'));
        $this->db->table('part')->where("name='{$name}' AND firmid='{$firmid}'")->select();
        if ( $this->db->numRows > 0 ) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    /** 
     *          导入部门
     * @param array $data 
     */
    public function partImport($data) {
        $this->db->table('part')->data(array('partid'=>$data['partid'],'name'=>$data['name'],'firmid'=>$data['firmid']))->add();
    }
    
    /**
     *          从数据库取出部门数据
     * @param string $firmid 公司id
     * @return array|int 部门数据或操作码
     */
    public function part($firmid) {
        
        $firmid = md5($firmid);
        $data = $this->db->table('part')->field('partid, name')->where("firmid='{$firmid}'")->select();
        if ( $this->db->numRows == 0 ) {
            return 1001; //没有部门
        }
        if ( array_foo($data) ) {
            $data = array(0=>$data);
        } 
        return $data;
    }
    
    /**
     * 
     * @param string $partid 部门id
     * @return array|int 部门数据或操作码
     */
    public function partMember($partid) {
        $firmid = md5(ff::$lib['session']->get('firmid'));
        $sql = "SELECT cx_partman.userid,cx_user.name FROM cx_user JOIN cx_partman ON cx_user.userid=cx_partman.userid WHERE cx_partman.partid='{$partid}' AND cx_partman.firmid='{$firmid}'";
        $data = $this->db->query($sql);
        if ( $this->db->numRows == 0 ) {
            return 1001; //没有人员
        }
        if ( array_foo($data) ) {
            $data = array(0=>$data);
        } 
        return $data;
    }    
    
    
     public function partOtherMember($partid) {
        $firmid = md5(ff::$lib['session']->get('firmid'));
        $sql = "SELECT cx_user.name,cx_user.userid FROM cx_part left JOIN cx_partman on  cx_partman.groupid='{$partid}' right join cx_user ON cx_partman.userid=cx_user.userid where (cx_partman.groupid is null or  cx_partman.groupid!='{$partid}') AND cx_user.lv!='0'  AND cx_user.firmid='{$firmid}' group by cx_user.userid ";
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
     *          删除部门
     * @param string $partid 部门id
     */
    public function delPart($partid) {
        $firmid = md5(ff::$lib['session']->get('firmid'));
        $this->db->table('part')->where("partid='{$partid}'")->delete();
        $this->db->table('partsurvey')->where("partid='{$partid}'")->delete();
        $this->db->table('partman')->where("partid='{$partid}'")->delete();
    }

    /**
     *          删除部门中的人员
     * @param string $partid 部门id 
     * @param string $userid 用户帐号
     */
    public function delPartMember($partid, $userid) {
        $firmid = md5(ff::$lib['session']->get('firmid'));
        $this->db->table('partman')->where("partid='{$partid}' AND userid='{$userid}' AND firmid='{$firmid}'")->delete();
    }
    
    /**
     *          增加部门
     * @param string $firmid 公司id
     * @param string $partName 部门名
     * @param string $partid 部门id
     * @param array $userid 部门成员
     * @return int
     */
    public function addPart($firmid, $partName, $partid, $userid) {
        $firmid = md5($firmid);
        $partMember = array();
        $this->db->table('part')->where("firmid='{$firmid}' AND name='{$partName}'")->select();
        if ( $this->db->numRows > 0) {
            return 1001; //部门已经存在
        } else {
            $this->db->table('part')->data(array('firmid'=>$firmid,'name'=>$partName,'partid'=>$partid))->add();
            if ( sizeof($userid) ) {
                foreach ($userid as $v) {
                    $partMember['userid'][] = $v;
                    $partMember['partid'][] = $partid;
                    $partMember['firmid'][] = $firmid;
                }
                $this->db->table('partman')->data(array('userid'=>$partMember['userid'],'partid'=>$partMember['partid'],'firmid'=>$partMember['firmid']))->add();
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
     * @return int 1001 部门已经存在
     */
    public function changePartName($partid, $name) {
        $firmid = md5(ff::$lib['session']->get('firmid'));
        $this->db->table('part')->where("partid='{$partid}' AND firmid='{$firmid}' AND name='{$name}'")->select();
        if ($this->db->numRows > 0) {
            return 1001;
        }
        $this->db->table('part')->data(array('name'=>$name))->where("partid='{$partid}' AND firmid='{$firmid}'")->update();
        return 1000;
    }
    
    /**
     * 
     * @param type $partid
     * @return type
     */
    public function getPartMemberNum($partid) {
        $firmid = md5(ff::$lib['session']->get('firmid'));
        $this->db->table('partman')->where("partid='{$partid}' AND firmid='{$firmid}'")->select();
        return $this->db->numRows;
    }
    
    public function addPartMember($partid, $userid) {
        $firmid = md5(ff::$lib['session']->get('firmid'));
        $data = array();
        foreach ($userid as $v) {
            $data['userid'][] = $v;
            $data['firmid'][] = $firmid;
            $data['partid'][] = $partid;
        }
        $this->db->table('partman')->data(array('userid'=>$data['userid'],'partid'=>$data['partid'],'firmid'=>$data['firmid']))->add();
        return 1000;
    }
}
