<?php

/**
 * @文件        editModel.class.php   
 * @描述        子类文件
 * @功能        
 * @起始日期    2014-5-28 14:11:33    
 * @文件版本    cx_survey v0.1
 */
class editModel extends model{
    /**
     *          增加问卷
     * @param array $data 问卷信息
     * @param array $member 问卷成员
     */
    public function add($data, $member) {
        $memData = array();
        $pageid = mark(2);
        $this->db->table('survey')->data($data)->add();
        $this->db->table('page')->data(array('surveyid'=>$data['surveyid'],'pageid'=>$pageid,'name'=>'新页面','num'=>'1'))->add();
        if ( sizeof($member) >= 1 ) {
            foreach ($member as $v) {
                $memData['surveyid'][] = $data['surveyid'];
                $memData['userid'][] = $v;
                $memData['firmid'][] = $data['firmid'];
            }
            $this->db->table('member')->data(array('surveyid'=>$memData['surveyid'],'userid'=>$memData['userid'],'firmid'=>$memData['firmid']))->add();
        }
        if ( $data['type'] == 1 ) {
            $this->db->table('satisfy')->data(array('surveyid'=>$data['surveyid'],'allcount'=>sizeof($member)))->add();
        } else if ( $data['type'] == 0 ) {
            $this->db->table('intention')->data(array('surveyid'=>$data['surveyid'],'allcount'=>sizeof($member)))->add();
        }
        $this->changeSurveyEditTime($data['surveyid']);
    }
    
    public function delSurvey($surveyid) {
        $question = $this->db->table('question')->where("surveyid='{$surveyid}'")->select();
        if ( $this->db->numRows == 0 ) {
            
        } else if ( $this->db->numRows == 1 ) {
            $this->db->table('option')->where("quid='{$question['quid']}'")->delete();
        } else {
            foreach ($question as $v) {
                $this->db->table('option')->where("quid='{$v['quid']}'")->delete();
            }
        }
        $this->db->table('page')->where("surveyid='{$surveyid}'")->delete();
        $this->db->table('survey')->where("surveyid='{$surveyid}'")->delete();
        $this->db->table('member')->where("surveyid='{$surveyid}'")->delete();
        $this->db->table('answer')->where("surveyid='{$surveyid}'")->delete();
        $this->db->table('ancache')->where("surveyid='{$surveyid}'")->delete();
        $this->db->table('satisfy')->where("surveyid='{$surveyid}'")->delete();
        $this->db->table('question')->where("surveyid='{$surveyid}'")->delete();
        $this->db->table('intention')->where("surveyid='{$surveyid}'")->delete();
        $this->db->table('partsurvey')->where("surveyid='{$surveyid}'")->delete();
        $this->db->table('groupsurvey')->where("surveyid='{$surveyid}'")->delete();
    }
    
    
    public function cancelSurvey($surveyid) {
        $dateTime = ff::$lib['date']->showDateTime();
        $this->db->table('survey')->data(array('canceltime'=>$dateTime))->where("surveyid='{$surveyid}'")->update();
    }
    
    public function restoreSurvey($surveyid) {
        $this->db->table('survey')->data(array('canceltime'=>'0000-00-00 00:00:00'))->where("surveyid='{$surveyid}'")->update();
    }
    
    public function completeSurvey($surveyid) {
        $this->db->table('survey')->data(array('isend'=>'1'))->where("surveyid='{$surveyid}'")->update();
        $surveyType = $this->db->table('survey')->where("surveyid='{$surveyid}'")->select()['type'];
        if ( $surveyType == 1 ) {
            $score = $this->db->table('answer')->field("SUM(opid) AS A")->where("surveyid='{$surveyid}'")->select()['A'];
            $this->db->table('satisfy')->data(array('totalscore'=>$score))->where("surveyid='{$surveyid}'")->update();
        }
    }
    
    public function publicSurvey($surveyid) {
        $this->db->table('survey')->data(array('ispublic'=>'1'))->where("surveyid='{$surveyid}'")->update();
    }
    
    public function unpublicSurvey($surveyid) {
        $this->db->table('survey')->data(array('ispublic'=>'0'))->where("surveyid='{$surveyid}'")->update();
    }
    
    /**
     *          添加页
     * @param type $surveyid
     * @param type $pageNum
     */
    public function addPage($surveyid, $pageNum) {
        $firmid = md5(ff::$lib['session']->get('firmid'));
        $pageid = mark(2);
        $sql = "UPDATE `cx_page` SET num=num+1 WHERE surveyid='{$surveyid}' AND num>={$pageNum}";
        $this->db->execute($sql);
        $this->db->table('page')->data(array('surveyid'=>$surveyid,'pageid'=>$pageid,'name'=>'新页面','num'=>$pageNum))->add();
        $sql = "UPDATE `cx_survey` SET pagenum=pagenum+1 WHERE surveyid='{$surveyid}'";
        $this->db->execute($sql);
        $this->changeSurveyEditTime($surveyid);
    }
    
    public function editPageName($surveyid, $pageid, $name) {
        $this->db->table('page')->data(array('name'=>$name))->where("surveyid='{$surveyid}' AND pageid='{$pageid}'")->update();
        $this->changeSurveyEditTime($surveyid);
    }
    
    /**
     *          删除页 同时删除页内的问题和选项
     * @param type $surveyid
     * @param type $pageid
     */
    public function delPage($surveyid, $pageid) {
        $this->db->table('page')->where("surveyid='{$surveyid}'")->select();
        if ( $this->db->numRows == 1 ) {
            return 1001;
        }        
        $pageNum = $this->db->table('page')->where("surveyid='{$surveyid}' AND pageid='{$pageid}'")->select()['num'];
        $this->db->table('page')->where("surveyid='{$surveyid}' AND pageid='{$pageid}'")->delete();
        $sql = "UPDATE `cx_page` SET num=num-1 WHERE surveyid='{$surveyid}' AND num>{$pageNum}";
        $this->db->execute($sql);
        $sql = "UPDATE `cx_survey` SET pagenum=pagenum-1 WHERE surveyid='{$surveyid}'";
        $this->db->execute($sql);
        $qu = $this->db->table('question')->where("surveyid='{$surveyid}' AND pageid='{$pageid}'")->select();
        if ( $this->db->numRows == 0 ) {
            
        } else if ( $this->db->numRows == 1 ) {
            $quid = $qu['quid'];
            $this->db->table('question')->where("surveyid='{$surveyid}' AND quid='{$quid}'")->delete();
            $this->db->table('option')->where("quid='{$quid}'")->delete();
        } else {
            foreach ($qu as $v) {
                $quid = $v['quid'];
                $this->db->table('question')->where("surveyid='{$surveyid}' AND quid='{$quid}'")->delete();
                $this->db->table('option')->where("quid='{$quid}'")->delete();
            }
        }
        $this->changeSurveyEditTime($surveyid);
        return 1000;
    }
    
    /**
     *          添加一个问题
     * @param type $qudata
     * @param type $opdata
     */
    public function addQuestion($qudata, $opdata = '') {
        if ( $opdata == '' ) {
            $this->db->table('question')->data($qudata)->add();
        } else {
            $this->db->table('question')->data($qudata)->add();
            $this->db->table('option')->data(array('name'=>$opdata['name'],'quid'=>$opdata['quid'],'opid'=>$opdata['opid']))->add();
        }
        $this->changeSurveyEditTime($qudata['surveyid']);
    }
    
    
    public function editQuestionName($surveyid, $quid, $name) {
        $this->db->table('question')->data(array('name'=>$name))->where("surveyid='{$surveyid}' AND quid='{$quid}'")->update();
        $this->changeSurveyEditTime($surveyid);
    }
    
    /**
     *          删除一个问题
     * @param type $surveyid
     * @param type $quid
     */
    public function delQuestion($surveyid, $quid) {
        $this->db->table('question')->where("surveyid='{$surveyid}' AND quid='{$quid}'")->delete();
        $this->db->table('option')->where("quid='{$quid}'")->delete();
        $this->changeSurveyEditTime($surveyid);
    }
    
    /**
     *          添加一个答案
     * @param type $quid
     * @param type $option
     */
    public function addOption($quid, $option) {
        $surveyid = $this->db->table('question')->where("quid='{$quid}'")->select()['surveyid'];
        $sql = "UPDATE `cx_option` SET opid=opid+1 WHERE quid='{$quid}'";
        $this->db->execute($sql);
        $this->db->table('option')->data(array('name'=>$option,'opid'=>'1','quid'=>$quid))->add();
        $this->changeSurveyEditTime($surveyid);
    }
    
    /**
     *          删除一个答案
     * quid：问题id
     * opid：要删除的答案的id
     */
    public function delOption($quid, $opid) {
        $surveyid = $this->db->table('question')->where("quid='{$quid}'")->select()['surveyid'];
        $this->db->table('option')->where("quid='{$quid}' AND opid='{$opid}'")->delete();
        $sql = "UPDATE `cx_option` SET opid=opid-1 WHERE quid='{$quid}' AND opid>{$opid}";
        $this->db->execute($sql);
        $this->changeSurveyEditTime($surveyid);
    }
    
    /**
     *          编辑答案
     * quid：问题id
     * opid：答案id
     * name：新答案
     */
    public function editOptionName($quid, $opid, $name) {
        $surveyid = $this->db->table('question')->where("quid='{$quid}'")->select()['surveyid'];        
        $this->db->table('option')->data(array('name'=>$name))->where("quid='{$quid}' AND opid='{$opid}'")->update();
        $this->changeSurveyEditTime($surveyid);
    }
    
    /**
     *          添加n个问卷回答成员
     * @param type $data
     * @param type $size
     */
    public function addMember($data, $size) {
        $this->db->table('member')->data(array('surveyid'=>$data['surveyid'],'userid'=>$data['userid'],'firmid'=>$data['firmid']))->add();
        $type = $this->db->table('survey')->where("surveyid='{$data['surveyid'][0]}'")->select()['type'];
        if ( $type == 1 ) {
            $sql = "UPDATE `cx_satisfy` SET allcount=allcount+{$size} WHERE surveyid='{$data['surveyid'][0]}'";
            $this->db->execute($sql);
        } else if ( $type == 0 ) {
            $sql = "UPDATE `cx_intention` SET allcount=allcount+{$size} WHERE surveyid='{$data['surveyid'][0]}'";
            $this->db->execute($sql);
        }
        $this->changeSurveyEditTime($data['surveyid'][0]);
    }
    
    /**
     *          删除问卷回答成员
     * @param type $surveyid
     * @param type $member
     * @param type $size
     */
    public function delMember($surveyid, $member, $size) {
        foreach ($member as $v) {
            $this->db->table('member')->where("surveyid='{$surveyid}' AND userid='{$v}'")->delete();
        }
        $type = $this->db->table('survey')->where("surveyid='{$surveyid}'")->select()['type'];
        if ( $type == 1 ) {
            $sql = "UPDATE `cx_satisfy` SET allcount=allcount-{$size} WHERE surveyid='{$surveyid}'";
            $this->db->execute($sql);
        } else if ( $type == 0 ) {
            $sql = "UPDATE `cx_intention` SET allcount=allcount-{$size} WHERE surveyid='{$surveyid}'";
            $this->db->execute($sql);
        }
        $this->changeSurveyEditTime($surveyid);
    }
    
    
    public function sendSurvey($surveyid, $endTime, $sendTime) {
        $this->db->table('survey')->data(array('endtime'=>$endTime, 'sendTime'=>$sendTime))->where("surveyid='{$surveyid}'")->update();
        $this->changeSurveyEditTime($surveyid);
    }
    
    public function changeEndTime($surveyid, $endTime) {
        $sendTime = $this->db->table('survey')->where("surveyid='{$surveyid}'")->select()['sendtime'];
        if ( strtotime($endTime) < strtotime($sendTime) ) {
            return 1001;
        }
        $this->db->table('survey')->data(array('endtime'=>$endTime))->where("surveyid='{$surveyid}'")->update();
        $this->changeSurveyEditTime($surveyid);
        return 1000;
    }
    
    /**
     *          更新问卷最后修改时间
     * @param string $surveyid 需要更新修改时间的问卷id
     */
    private function changeSurveyEditTime($surveyid) {
        $dateTime = ff::$lib['date']->showDateTime();
        $sql = "UPDATE `cx_survey` SET changetime='{$dateTime}' WHERE surveyid='{$surveyid}'";
        $this->db->execute($sql);
    }
}
