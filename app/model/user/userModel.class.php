<?php

/**
 * @文件        userModelController.class.php   
 * @描述        类文件
 * @功能        添加字段
 * @起始日期    14:16:18    
 * @文件版本    cx_survey v0.1
 */
class userModel extends model{
    public function touchUser($userid) {
        $firmid = md5(ff::$lib['session']->get('firmid'));
        $this->db->table('user')->where("userid='{$userid}' AND firmid='{$firmid}'")->select();
        if ( $this->db->numRows > 0 ) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    public function userImport($data, $lines, $firmid) {
        $this->db->table('ucache')->data(array('userid'=>$data['userid'],'name'=>$data['name'],'email'=>$data['email'],'password'=>$data['password'],'firmid'=>$data['firmid']))->add();
        if ( $this->db->numRows === $lines ) {
            $sql = "INSERT INTO `cx_user`(`userid`, `name`, `email`, `password`, `firmid`) select `userid`, `name`, `email`, `password`, `firmid` from `cx_ucache` where `firmid`='5a105e8b9d40e1329780d62ea2265d8a'";
            $this->db->execute($sql);
            $this->db->table('ucache')->where("firmid='{$firmid}'")->delete();
            $this->db->table("loginfo")->data(array('userid'=>$data['userid'],'firmid'=>$data['firmid']))->add();
            echo "导入成功";
        } else {
            echo "导入失败";
        } 
    }
}

