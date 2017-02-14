<?php

/**
 * @文件        showModel.class.php   
 * @描述        子类文件
 * @功能        
 * @起始日期    2014-5-28 14:11:58    
 * @文件版本    cx_survey v0.1
 */
class showModel extends model{
    /**
     * @名称        显示未发布问卷
     * @功能        从session里取出firmid和 userid，sendtime=0未发布
     * @return     json数组surveyid,type,name ;111代表没有未发布问卷;
     */
    public function unissue($firmid,$userid){
        $data=array();
        $unissue= $this->db->table("survey")->field("name,surveyid,type")->where("firmid='{$firmid}' and userid='{$userid}' and sendtime='0000-00-00 00:00:00' and isend=0")->select();             
        if($this->db->numRows==0){
            return 111;
        }elseif($this->db->numRows==1){
            $data['survey1']['name']=$unissue['name'];
            $data['survey1']['surveyid']=$unissue['surveyid'];
            $data['survey1']['type']=$unissue['type'];
            return $data;
        }else{
            $x=0;
            foreach ($unissue as $k => $v) {
                $x++;
            $data['survey'.$x]['name']=$v['name'];
            $data['survey'.$x]['surveyid']=$v['surveyid'];
            $data['survey'.$x]['type']=$v['type'];                
            }
            return $data;
        } 
    }   
    /**
     * @名称        显示发布问卷
     * @功能        从session里取出firmid和 userid，sendtime!=0已发布;type=0意向,type=1满意度;到表survey找isend,endtime比较 
     * @return     json数组surveyid,type,name,endtime,progress,status;111代表没有已发布问卷;
     */
    public function issue($firmid,$userid){
        $data=array();
        $issue= $this->db->table("survey")->field("name,surveyid,type,endtime,canceltime")->where("firmid='{$firmid}' and userid='{$userid}' and sendtime!='0000-00-00 00:00:00' and isend=0")->select(); 
        if($this->db->numRows==0){
            return 111;
        }elseif($this->db->numRows==1){
            $data['survey1']['name']=$issue['name'];
            $data['survey1']['surveyid']=$issue['surveyid'];
            $data['survey1']['type']=$issue['type'];
            $data['survey1']['endtime']=$issue['endtime'];
            $canceltime=$issue['canceltime'];
            $date= getdate(date("U"));       
            $str= "$date[year]-$date[mon]-$date[mday] $date[hours]:$date[minutes]:$date[seconds]";         
            $str= strtotime($str);
            $endtime= strtotime($data['survey1']['endtime']);
            if($canceltime!="0000-00-00 00:00:00"){
                $data['survey1']['status']="已取消";
            }else{
                if($data['survey1']['type']==0){
                    $progress= $this->db->table("intention")->field("nowcount,allcount")->where("surveyid='{$data['survey1']['surveyid']}'")->select();
                }elseif($data['survey1']['type']==1){
                    $progress= $this->db->table("satisfy")->field("nowcount,allcount")->where("surveyid='{$data['survey1']['surveyid']}'")->select();
                }
                $count=$progress['nowcount']."/".$progress['allcount'];
                if($str<=$endtime){
                    $data['survey1']['status']="投票中";
                    $data['survey1']['progress']=$count;
                }else{
                    $data['survey1']['status']="已结束";
                    $data['survey1']['progress']=$count;
                }
            }    
            return $data;
        }else{
            $x=0;
            foreach ($issue as $k => $v) {
                $x++;
                $data['survey'.$x]['surveyid']=$v['surveyid'];
                $data['survey'.$x]['name']=$v['name'];
                $data['survey'.$x]['type']=$v['type'];
                $data['survey'.$x]['endtime']=$v['endtime'];
                $canceltime=$v['canceltime'];
                $date= getdate(date("U"));       
                $str= "$date[year]-$date[mon]-$date[mday] $date[hours]:$date[minutes]:$date[seconds]";            
                $str= strtotime($str);
                $endtime= strtotime($v['endtime']);
                if($canceltime!="0000-00-00 00:00:00"){
                    $data['survey'.$x]['status']="已取消";                    
                }else{
                    if($data['survey'.$x]['type']==0){
                        $progress= $this->db->table("intention")->field("nowcount,allcount")->where("surveyid='{$data['survey'.$x]['surveyid']}'")->select();
                    }elseif($data['survey'.$x]['type']==1){
                        $progress= $this->db->table("satisfy")->field("nowcount,allcount")->where("surveyid='{$data['survey'.$x]['surveyid']}'")->select();
                    }
                    $count=$progress['nowcount']."/".$progress['allcount'];
                    if($str<=$endtime){
                        $data['survey'.$x]['status']="投票中";
                        $data['survey'.$x]['progress']=$count;
                    }else{
                        $data['survey'.$x]['status']="已结束";
                        $data['survey'.$x]['progress']=$count;
                    }
                }       
            }
            return $data;
        }
    }    
    /**
     * @名称        显示投票人
     * @功能        从前端POST来surveyid,从session里取出管理员的firmid;到表member取出员工的userid
     *              若非管理员权限,则返回操作码:4001
     * @return     json数组userid;111没有投票人
     */
    public function showmember($firmid,$surveyid){
        $data= $this->db->table("member")->field("userid")->where("firmid='{$firmid}' and surveyid='{$surveyid}'")->select();
        if($this->db->numRows==0){
            return 111;
        }else{
            return $data;
        }
    }
    /**
     * @名称        显示所有人员
     * @功能        从前端POST来surveyid,从session里取出管理员的firmid;到表user,member取出未参加投票员工的name,userid(排序未完)
     *              若非管理员权限,则返回操作码:4001
     * @return     json数组name,userid;111代表公司没有员工
     */
    public function allmember($firmid,$surveyid){     
        $some=array();
        $data=array();
        $pq= "SELECT userid,name FROM cx_user WHERE firmid='{$firmid}' and lv=1 ORDER BY name asc";
        $have=$this->db->query($pq);
        if($this->db->numRows==0){
            return 111;
        }
        elseif($this->db->numRows==1){
            $some[0]=$have;
        }else{
            $some=$have;
        }
        foreach ($some as $v){
            $sql="SELECT * FROM cx_member WHERE firmid='{$firmid}' AND userid='{$v['userid']}' AND surveyid='{$surveyid}'";
            $nothave = $this->db->query($sql);            
            if($this->db->numRows==0){
                $data[]=$v;
            }
        }
            return $data;
    }              
    /**
     * @名称        修改问卷显示界面
     * @功能        前端POST来surveyid,从session里取firmid;到表survey里取type,name,pagenum;
     *              用surveyid到表page里取pageid,name,num ;
     *              用surveyid,pageid到表question取quid,name,opmod;
     *              用quid到表option取opid,name;
     * @return     json数组type,name,pagenum;pageid,name,num;quid,name,opmod;opid,name;
     */
    public function showsurvey($firmid,$surveyid){
        $data = array();
        $data= $this->db->table("survey")->field("type,name,pagenum")->where("firmid='{$firmid}' and surveyid='{$surveyid}'")->select();
        $page = $this->db->table("page")->field("pageid,name,num")->where("surveyid='{$surveyid}' order by num")->select();
        if ( $this->db->numRows == 1 ) {
            $data['page1']['pageid'] = $page['pageid'];
            $data['page1']['name'] = $page['name'];
            $data['page1']['num'] = $page['num'];
            $question= $this->db->table("question")->field("quid,name,opmod")->where("surveyid='{$surveyid}' and pageid='{$page['pageid']}' order by quid")->select();
            if ( $this->db->numRows != 0 ) {   
                if ( $data['type'] == 0 ) {
                    if ( $this->db->numRows == 1 ) {
                        $data['page1']['question1']['quid'] = $question['quid'];
                        $data['page1']['question1']['name'] = $question['name'];
                        $data['page1']['question1']['opmod'] = $question['opmod'];
                        $option= $this->db->table("option")->field("opid,name")->where("quid='{$question['quid']}' order by opid")->select();
                        $x = 0;
                        foreach ($option as $k => $v) {
                            $x++;
                            $data['page1']['question1']['op'.$x]['opid'] = $v['opid'];
                            $data['page1']['question1']['op'.$x]['name'] = $v['name'];
                        }
                    } else {
                        $x = 0;
                        foreach ($question as $k => $v) {
                            $x++;
                            $data['page1']['question'. $x]['quid'] = $v['quid'];
                            $data['page1']['question'. $x]['name'] = $v['name'];
                            $data['page1']['question'. $x]['opmod'] = $v['opmod'];
                            $option= $this->db->table("option")->field("opid,name")->where("quid='{$v['quid']}' order by opid")->select();
                            $y=0;
                            foreach ($option as $k1 => $v1) {
                                $y++;
                                $data['page1']['question'.$x]['op'.$y]['opid'] = $v1['opid'];
                                $data['page1']['question'.$x]['op'.$y]['name'] = $v1['name'];
                            }
                        }
                    }
                }  else if ( $data['type'] == 1 ) {
                    if ( $this->db->numRows == 1 ) {
                        $data['page1']['question1']['quid'] = $question['quid'];
                        $data['page1']['question1']['name'] = $question['name'];
                        $data['page1']['question1']['opmod'] = $question['opmod'];
                    } else {
                        $x = 0;
                        foreach ($question as $k => $v) {
                            $x++;
                            $data['page1']['question'. $x]['quid'] = $v['quid'];
                            $data['page1']['question'. $x]['name'] = $v['name'];
                            $data['page1']['question'. $x]['opmod'] = $v['opmod'];
                        }
                    }
                    $option= $this->db->table("survey")->field("datum")->where("surveyid='{$surveyid}'")->select();
                    $data['page1']['datum']=$option['datum'];                   
                }
            }
        }else {
            $z = 0;
            foreach ($page as $k2 => $v2) {
                $z++;
                $data['page'. $z]['pageid'] = $v2['pageid'];
                $data['page'. $z]['name'] = $v2['name'];
                $data['page'. $z]['num'] = $v2['num'];
                $question= $this->db->table("question")->field("quid,name,opmod")->where("surveyid='{$surveyid}' and pageid='{$v2['pageid']}' order by quid")->select();
                if ( $this->db->numRows != 0 ) {   
                    if ( $data['type'] == 0 ) {
                        if ( $this->db->numRows == 1 ) {
                            $data['page'. $z]['question1']['quid'] = $question['quid'];
                            $data['page'. $z]['question1']['name'] = $question['name'];
                            $data['page'. $z]['question1']['opmod'] = $question['opmod'];
                            $option= $this->db->table("option")->field("opid,name")->where("quid='{$question['quid']}' order by opid")->select();
                            $x = 0;
                            foreach ($option as $k => $v) {
                                $x++;
                                $data['page'. $z]['question1']['op'.$x]['opid'] = $v['opid'];
                                $data['page'. $z]['question1']['op'.$x]['name'] = $v['name'];
                            }
                        } else {
                            $x = 0;
                            foreach ($question as $k => $v) {
                                $x++;
                                $data['page'. $z]['question'. $x]['quid'] = $v['quid'];
                                $data['page'. $z]['question'. $x]['name'] = $v['name'];
                                $data['page'. $z]['question'. $x]['opmod'] = $v['opmod'];
                                $option= $this->db->table("option")->field("opid,name")->where("quid='{$v['quid']}' order by opid")->select();
                                $y=0;
                                foreach ($option as $k1 => $v1) {
                                    $y++;
                                    $data['page'. $z]['question'.$x]['op'.$y]['opid'] = $v1['opid'];
                                    $data['page'. $z]['question'.$x]['op'.$y]['name'] = $v1['name'];
                                }
                            }
                        }
                    } else if ( $data['type'] == 1 ) {                    
                        if ( $this->db->numRows == 1 ) {
                            $data['page'. $z]['question1']['quid'] = $question['quid'];
                            $data['page'. $z]['question1']['name'] = $question['name'];
                            $data['page'. $z]['question1']['opmod'] = $question['opmod'];
                        } else {
                            $x = 0;
                            foreach ($question as $k => $v) {
                                $x++;
                                $data['page'. $z]['question'. $x]['quid'] = $v['quid'];
                                $data['page'. $z]['question'. $x]['name'] = $v['name'];
                                $data['page'. $z]['question'. $x]['opmod'] = $v['opmod'];
                            }
                        }
                        $option= $this->db->table("survey")->field("datum")->where("surveyid='{$surveyid}'")->select();
                        $data['page1']['datum']=$option['datum'];                        
                    }
                }
            }
        }
        return $data;
//        foreach ($data as $k1 => $v1) {
//            if (is_array($v1)) {
//                echo '____'. $k1. '<br>';
//                foreach ($v1 as $k2 => $v2) {
//                    if (is_array($v2)) {
//                        echo '_______'. $k2. '<br>';
//                        foreach ($v2 as $k3 => $v3) {
//                            if(is_array($v3)){
//                                echo '___________'. $k3. '<br>';
//                                foreach ($v3 as $k4 => $v4) {
//                                    echo '_______________'. $k4. '=>'. $v4. '<br>';
//                                }
//                            } else {
//                                echo '___________'. $k3. '=>'. $v3. '<br>';
//                            }
//                        }
//                    } else {
//                        echo '_______'. $k2. '=>'. $v2. '<br>';
//                    }
//                }
//            } else {
//                echo $k1. '=>'. $v1. '<br>';
//            }
//        }
    }  
/**
     * @名称        用户显示问卷界面
     * @功能        从session里取firmid,userid;到表survey取surveyid,name,type
     * @return     json数组{surveyid:"问卷id",name:"问卷名",type:"类型"}
     */
    public function usersurvey($firmid,$userid){
        $data=array();
        $date= getdate(date("U"));       
        $str= "$date[year]-$date[mon]-$date[mday] $date[hours]:$date[minutes]:$date[seconds]";         
        $str= strtotime($str);
        $survey= $this->db->table("survey")->field("surveyid,name,type,endtime")->where("firmid='{$firmid}' and sendtime!='0000-00-00 00:00:00' and isend=0 order by endtime")->select();   
        if( $this->db->numRows == 0){
            return 111;
        }elseif( $this->db->numRows == 1){
            $endtime= strtotime($survey['endtime']);
            $answer=  $this->db->table("answer")->where("userid='{$userid}' and surveyid='{$survey['surveyid']}'")->select();
            if($str<$endtime&&(!$answer)){
            $data['survey1']['surveyid']=$survey['surveyid'];
            $data['survey1']['name']=$survey['name'];
            $data['survey1']['type']=$survey['type'];
            }
            return $data;
        } else {
            $x=0;
            foreach($survey as $k => $v){
                $endtime= strtotime($v['endtime']);
                $answer=  $this->db->table("answer")->where("userid='{$userid}' and surveyid='{$v['surveyid']}'")->select();
                if($str<$endtime&&(!$answer)){
                    $x++;
                    $data['survey'.$x]['surveyid']=$v['surveyid'];
                    $data['survey'.$x]['name']=$v['name'];
                    $data['survey'.$x]['type']=$v['type'];
                }
            }
            return $data;
        }
    }
    /**
     * @名称        用户答题界面
     * @功能        从前端POST来surveyid,type,name ;到表survey,page,question,option,ancache
     * @return     返回 json数组
     */
    public function answerface($firmid,$userid,$surveyid){
        $data=array();
        $data= $this->db->table("survey")->field("name,type,pagenum")->where("firmid='{$firmid}' and surveyid='{$surveyid}'")->select();
        if($data['type']==0){//type=0意向
            $page= $this->db->table("page")->field("pageid")->where("surveyid='{$surveyid}' order by num")->select();
            if($this->db->numRows==1){
                $data['page1']['pageid']=$page['pageid'];
                $question= $this->db->table("question")->field("quid,name,opmod")->where("pageid='{$page['pageid']}' and surveyid='{$surveyid}'")->select();
                if($this->db->numRows==0){

                }
                else if($this->db->numRows==1){
                    $data['page1']['question1']['quid']=$question['quid'];
                    $data['page1']['question1']['name']=$question['name'];
                    $data['page1']['question1']['opmod']=$question['opmod'];
                    $option= $this->db->table("option")->field("opid,name")->where("quid='{$question['quid']}' order by opid")->select();
                    $i=0;
                    foreach ($option as $k1 => $v1) {
                        $i++;
                        $data['page1']['question1']['option'.$i]['opid']=$v1['opid'];
                        $data['page1']['question1']['option'.$i]['name']=$v1['name'];
                        }
                    $ancache= $this->db->table("ancache")->field("opid")->where("quid='{$question['quid']}' and userid='{$userid}' and surveyid='{$surveyid}'")->select();
                    if($ancache){
                        if($this->db->numRows==1){
                        $data['page1']['question1']['ancache'][$ancache['opid']]=$ancache['opid'];
                        }elseif($this->db->numRows>1){
//                            $x=0;
                            foreach ($ancache as $k2 => $v2) {
//                                $x++;
                                $data['page1']['question1']['ancache'][$v2['opid']]=$v2['opid'];
                            }
                        }   
                    }
                    
                }else{
                    $y=0;
                    foreach ($question as $k => $v) {
                        $y++;  
                        $data['page1']['question'.$y]['quid']=$v['quid'];
                        $data['page1']['question'.$y]['name']=$v['name'];
                        $data['page1']['question'.$y]['opmod']=$v['opmod'];//区别单选和多选
                        $option= $this->db->table("option")->field("opid,name")->where("quid='{$v['quid']}' order by opid")->select();
                        $i=0;
                        foreach ($option as $k1 => $v1) {
                            $i++;
                            $data['page1']['question'.$y]['option'.$i]['opid']=$v1['opid'];
                            $data['page1']['question'.$y]['option'.$i]['name']=$v1['name'];
                            }
                        $ancache= $this->db->table("ancache")->field("opid")->where("quid='{$v['quid']}' and userid='{$userid}' and surveyid='{$surveyid}'")->select();
                        if($ancache){
                            if($this->db->numRows==1){
                            $data['page1']['question'.$y]['ancache'][$ancache['opid']]=$ancache['opid'];
                            }elseif($this->db->numRows>1){
//                                $x=0;
                                foreach ($ancache as $k2 => $v2) {
//                                    $x++;
                                    $data['page1']['question'.$y]['ancache'][$v2['opid']]=$v2['opid'];
                                }
                            }
                        }
                    }
                }
            }else{
                $z=0;
                foreach ($page as $k => $v) {
                    $z++;
                    $data['page'.$z]['pageid']=$v['pageid'];
                    $question= $this->db->table("question")->field("quid,name,opmod")->where("pageid='{$v['pageid']}' and surveyid='{$surveyid}'")->select();
                    if($this->db->numRows==0){
                        
                    }
                    else if($this->db->numRows==1){
                        $data['page'.$z]['question1']['quid']= $question['quid'];
                        $data['page'.$z]['question1']['name']= $question['name'];
                        $data['page'.$z]['question1']['opmod']= $question['opmod'];
                        $option= $this->db->table("option")->field("opid,name")->where("quid='{$question['quid']}' order by opid")->select();
                        $i=0;
                        foreach ($option as $k6 => $v6) {
                            $i++;
                            $data['page'.$z]['question1']['option'.$i]['opid']=$v6['opid'];
                            $data['page'.$z]['question1']['option'.$i]['name']=$v6['name'];
                        }
                        $ancache= $this->db->table("ancache")->field("opid")->where("quid='{$question['quid']}' and userid='{$userid}' and surveyid='{$surveyid}'")->select();
                        if($ancache){
                            if($this->db->numRows==1){
                            $data['page'.$z]['question1']['ancache'][$ancache['opid']]=$ancache['opid'];
                            }elseif($this->db->numRows>1){
//                                $x=0;
                                foreach ($ancache as $k7 => $v7) {
//                                    $x++;
                                    $data['page'.$z]['question1']['ancache'][$v7['opid']]=$v7['opid'];
                                }
                            }
                        }
                    }else{
                        $y=0;
                        foreach ($question as $k4 => $v4) {
                            $y++;  
                            $data['page'.$z]['question'.$y]['quid']=$v4['quid'];
                            $data['page'.$z]['question'.$y]['name']=$v4['name'];
                            $data['page'.$z]['question'.$y]['opmod']=$v4['opmod'];//区别单选和多选
                            $option= $this->db->table("option")->field("opid,name")->where("quid='{$v4['quid']}' order by opid")->select();
                            $i=0;
                            foreach ($option as $k2 => $v2) {
                                $i++;
                                $data['page'.$z]['question'.$y]['option'.$i]['opid']=$v2['opid'];
                                $data['page'.$z]['question'.$y]['option'.$i]['name']=$v2['name'];
                            }
                            $ancache= $this->db->table("ancache")->field("opid")->where("quid='{$v4['quid']}' and userid='{$userid}' and surveyid='{$surveyid}'")->select();
                            if($ancache){
                                if($this->db->numRows==1){
                                $data['page'.$z]['question'.$y]['ancache'][$ancache['opid']]=$ancache['opid'];
                                }elseif($this->db->numRows>1){
//                                    $x=0;
                                    foreach ($ancache as $k5 => $v5) {
//                                        $x++;
                                        $data['page'.$z]['question'.$y]['ancache'][$v5['opid']]=$v5['opid'];
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }elseif ($data['type']==1) {//满意度
            $datum= $this->db->table("survey")->field("datum")->where("firmid='{$firmid}' and surveyid='{$surveyid}'")->select();
            $data['datum']=$datum['datum'];
            $page= $this->db->table("page")->field("pageid")->where("surveyid='{$surveyid}' order by num")->select();
            if($this->db->numRows==1){
                $data['page1']['pageid']=$page['pageid'];
                $question= $this->db->table("question")->field("quid,name")->where("pageid='{$page['pageid']}' and surveyid='{$surveyid}'")->select();
                //
                if($this->db->numRows==0){
                    
                }elseif($this->db->numRows==1){
                    $data['page1']['question1']['quid']=$question['quid'];
                    $data['page1']['question1']['name']=$question['name'];
                    $ancache= $this->db->table("ancache")->field("opid")->where("quid='{$v['quid']}' and userid='{$userid}' and surveyid='{$surveyid}'")->select();
                    if($ancache){
                        $data['page1']['question1']['ancache']['opid']=$ancache['opid'];
                    }
                }else{
                    $y=0;
                    foreach ($question as $k => $v) {
                        $y++;  
                        $data['page1']['question'.$y]['quid']=$v['quid'];
                        $data['page1']['question'.$y]['name']=$v['name'];
                        $ancache= $this->db->table("ancache")->field("opid")->where("quid='{$v['quid']}' and userid='{$userid}' and surveyid='{$surveyid}'")->select();
                        if($ancache){
                            $data['page1']['question'.$y]['ancache']['opid']=$ancache['opid'];
                        }
                    }
                }
            }else{
                $z=0;
                foreach ($page as $k2 => $v2) {
                    $z++;
                    $data['page'.$z]['pageid']=$v2['pageid'];
                    $question= $this->db->table("question")->field("quid,name")->where("pageid='{$v2['pageid']}' and surveyid='{$surveyid}'")->select();
                    if($this->db->numRows==0){
                        
                    }
                    else if($this->db->numRows==1){
                        $data['page'.$z]['question1']['quid']=$question['quid'];
                        $data['page'.$z]['question1']['name']=$question['name'];
                        $ancache= $this->db->table("ancache")->field("opid")->where("quid='{$question['quid']}' and userid='{$userid}' and surveyid='{$surveyid}'")->select();
                        if($ancache){
                            $data['page'.$z]['question1']['ancache']['opid']=$ancache['opid'];
                        }
                    }else{
                        $y=0;
                        foreach ($question as $k1 => $v1) {
                            $y++;  
                            $data['page'.$z]['question'.$y]['quid']=$v1['quid'];
                            $data['page'.$z]['question'.$y]['name']=$v1['name'];
                            $ancache= $this->db->table("ancache")->field("opid")->where("quid='{$v1['quid']}' and userid='{$userid}' and surveyid='{$surveyid}'")->select();
                            if($ancache){
                                $data['page'.$z]['question'.$y]['ancache']['opid']=$ancache['opid'];
                            }
                        }
                    }
                }
            }
        }  
        return $data;
    }
    
}
   
