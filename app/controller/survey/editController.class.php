<?php

/**
 * @文件        editController.class.php   
 * @描述        子类文件
 * @功能        
 * @起始日期    2014-5-28 12:36:59    
 * @文件版本    cx_survey v0.1
 */
class editController extends controller{
    
    /**
     *          新建问卷
     * 需要前端传入以下数据
     * name：问卷名
     * type：问卷类型，意向问卷 0 满意度问卷 1
     * datum：当type为1时，需要传这个参数，表示满意度评分基准
     * member：指定当前问卷的回答成员，格式为userid1@#userid2@#......@#useridn,如果不指定问卷成员，这个参数不需要传入
     */
    public function add() {
        $firmid = md5(ff::$lib['session']->get('firmid'));
        $buildTime = ff::$lib['date']->showDateTime();
        $surveyid = mark(1);
        $userid = ff::$lib['session']->get('userid');
        $surveyName = $_POST['name'];
        $surveyType = $_POST['type'];
        if ( isset($_POST['member']) && !empty($_POST['member']) ) {
            $member = explode("@#", $_POST['member']);
        } else {
            $member = array();
        }
        if ( $surveyType == 1 ) {
            $surveyDatum = $_POST['datum'];
            $data = array('firmid'=>$firmid,'buildtime'=>$buildTime,'surveyid'=>$surveyid,'userid'=>$userid,'name'=>$surveyName,'type'=>$surveyType,'datum'=>$surveyDatum);
        } else if ( $surveyType == 0 ) {
            $data = array('firmid'=>$firmid,'buildtime'=>$buildTime,'surveyid'=>$surveyid,'userid'=>$userid,'name'=>$surveyName,'type'=>$surveyType);
        }
        N('edit')->add($data, $member);
    }
    
    /**
     *          删除问卷
     * 传入要删除问卷的问卷id：surveyid
     */
    public function delSurvey() {
        $surveyid = $_POST['surveyid'];
        N('edit')->delSurvey($surveyid);
    }
    
    /**
     *          取消问卷
     * 问卷id：surveyid
     */
    public function cancelSurvey() {
        $surveyid = $_POST['surveyid'];
        N('edit')->cancelSurvey($surveyid);
    }
    
    /**
     *          恢复问卷
     * 问卷id：surveyid
     */
    public function restoreSurvey() {
        $surveyid = $_POST['surveyid'];
        N('edit')->restoreSurvey($surveyid);
    }
    
    /**
     *          完成问卷
     * 问卷id：surveyid
     */
    public function completeSurvey() {
        $surveyid = $_POST['surveyid'];
        N('edit')->completeSurvey($surveyid);
    }
        
    /**
     *          公开问卷
     * 问卷id：surveyid
     */
    public function publicSurvey() {
        $surveyid = $_POST['surveyid'];
        N('edit')->publicSurvey($surveyid);
    }
    
    /**
     *          不公开问卷
     * 问卷id：surveyid
     */
    public function unpublicSurvey() {
        $surveyid = $_POST['surveyid'];
        N('edit')->unpublicSurvey($surveyid);
    }
    
    
    /**
     *          为问卷添加一个可编辑问题的页
     * 需要传入
     * surveyid：问卷id
     * pageNum：页码
     */
    public function addPage() {
        $surveyid = $_POST['surveyid'];
        $pageNum = $_POST['pagenum'];
        N('edit')->addPage($surveyid, $pageNum);
    }
   
    /**
     *          编辑页面名
     * surveyid：问卷id
     * pageid：页id
     * name：新页名
     */
    public function editPageName() {
        $surveyid = $_POST['surveyid'];
        $pageid = $_POST['pageid'];
        $name = $_POST['name'];
        N('edit')->editPageName($surveyid, $pageid, $name);
    }
    
    /**
     *          删除一个页
     * 需要传入
     * surveyid：问卷id
     * pageid：被删除的页id
     * 删除成功返回操作码 1000 若只剩下唯一页面则不允许删除,返回操作码 1001
     */
    public function delPage() {
        $surveyid = $_POST['surveyid'];
        $pageid = $_POST['pageid'];
        $this->ajaxReturn(N('edit')->delPage($surveyid, $pageid));
    }
    
    /**
     *          为指定的问卷添加一个问题和答案!
     * 需要以下参数
     * surveyid：问卷id
     * pageid：页id
     * name：问题
     * option：答案，可选，对于意向问卷传递这个参数，满意度不传递。结构：option1@#option2@#.....@#optionN
     * opmod:选项模式 1 单选 0 多选 ，可选，意向问卷传递这个参数，满意度不传递
     */
    public function addQuestion() {
        $surveyid = $_POST['surveyid'];
        $pageid = $_POST['pageid'];
        $name = $_POST['name'];
        $quid = mark(1);
        if ( isset($_POST['option']) ) {
           $opmod = $_POST['opmod'];
           $num = 0;
           $opdata = array();
           $qudata = array('surveyid'=>$surveyid,'name'=>$name,'pageid'=>$pageid,'quid'=>$quid,'opmod'=>$opmod);
           $option = explode("@#",$_POST['option']);
           foreach(  $option as $v ) {
               $num++;
               $opdata['name'][] = $v;
               $opdata['quid'][] = $quid;
               $opdata['opid'][] = $num;         
           }
           N('edit')->addQuestion($qudata, $opdata);
        } else {
            $qudata = array('surveyid'=>$surveyid,'name'=>$name,'pageid'=>$pageid,'quid'=>$quid);
           N('edit')->addQuestion($qudata);
        }
    }
    
    /**
     *          编辑问题名
     * surveyid：问卷id
     * quid：问题id
     * name：新问题名
     */
    public function editQuestionName() {
        $surveyid = $_POST['surveyid'];
        $quid = $_POST['quid'];
        $name = $_POST['name'];
        N('edit')->editQuestionName($surveyid, $quid, $name);
    }
        
    /**
     *          删除一个问题
     * 需要前端传入参数
     * surveyid：问卷id
     * quid：被删除的问题id
     */
    public function delQuestion() {
        $surveyid = $_POST['surveyid'];
        $quid = $_POST['quid'];
        N('edit')->delQuestion($surveyid, $quid);
    }
    
    /**
     *          添加一个答案
     * quid：问题id
     * option：要添加的答案
     */
     
    public function addOption() {
        $quid = $_POST['quid'];
        $option = $_POST['option'];
        N('edit')->addOption($quid, $option);
    }
    
    /**
     *          删除一个答案
     * quid：问题id
     * opid：要删除的答案的id
     */
    public function delOption() {
        $quid = $_POST['quid'];
        $opid = $_POST['opid'];
        N('edit')->delOption($quid, $opid);
    }
    
    /**
     *          编辑答案
     * quid：问题id
     * opid：答案id
     * name：新答案
     */
    public function editOptionName() {
        $quid = $_POST['quid'];
        $opid = $_POST['opid'];
        $name = $_POST['name'];
        N('edit')->editOptionName($quid, $opid, $name);
    }
    
    /**
     *          添加问卷回答成员
     * 需要前端传入参数
     * 问卷id：surveyid
     * 添加到问卷的成员：member(格式：userid1@#userid2@#.....@#useridn)
     */
    public function addSurveyMember() {
        $firmid = md5(ff::$lib['session']->get('firmid'));
        $surveyid = $_POST['surveyid'];
        $member = $_POST['member'];
        $member = explode("@#", $_POST['member']);
        $size = sizeof($member);
        $data = array();
        foreach ($member as $v) {
            $data['surveyid'][] = $surveyid;
            $data['userid'][] = $v;
            $data['firmid'][] = $firmid;
        }
        N('edit')->addMember($data, $size);
    }
    
    /**
     *          删除问卷回答成员
     * 需要前端传入参数
     * 问卷id：surveyid
     * 要删除的问卷成员：member(格式：userid1@#userid2@#.....@#useridn)
     */
    public function delSurveyMember() {
        $surveyid = $_POST['surveyid'];
        $member = $_POST['member'];
        $member = explode("@#", $_POST['member']);
        $size = sizeof($member);
        N('edit')->delMember($surveyid, $member, $size);        
    }
    
    /**
     *          发送问卷
     * 执行后就不能再次编辑问卷了
     * 需要前端传入参数
     * 问卷id：surveyid
     * 问卷能被搜索到的最晚时间：endtime 格式：yyyy-mm-dd hh:mm:ss
     * 问卷能被搜索到的最早时间：sendtime 格式：yyyy-mm-dd hh:mm:ss 非必需 若不传递这个参数，则以现在的时间为准
     */
    public function sendSurvey() {
        $surveyid = $_POST['surveyid'];
        $endTime = $_POST['endtime'];
        if ( isset($_POST['sendtime']) ){
            $sendTime = $_POST['sendtime'];
        } else {
            $sendTime = ff::$lib['date']->showDateTime();
        }
        N('edit')->sendSurvey($surveyid, $endTime, $sendTime);
    }
    
    /**
     *          修改问卷的结束时间!
     * 需要前端传入参数
     * 问卷id：surveyid
     * 问卷能被搜索到的最晚时间：endtime 格式：yyyy-mm-dd hh:mm:ss
     * 返回操作码：
     * 1001　设置的结束时间小于问卷开始时间　拒绝修改
     * 1000  修改成功
     */
    public function changeEndTime() {
        $surveyid = $_POST['surveyid'];
        $endTime = $_POST['endtime'];
        $this->ajaxReturn(N('edit')->changeEndTime($surveyid, $endTime));
    }

}
