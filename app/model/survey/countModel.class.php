<?php

/**
 * @文件        countModel.class.php   
 * @描述        子类文件
 * @功能        
 * @起始日期    2014-5-28 14:12:19    
 * @文件版本    cx_survey v0.1
 */
class countModel extends model{
    /**
     * @名称        用户显示结果界面
     * @功能        从session里取firmid;
     * @return     json数组surveyid,name,type;111表示没有统计完的问卷
     */
    public function showresult($firmid){
        $result= $this->db->table("survey")->field("surveyid,name,type")->where("firmid='{$firmid}' and ispublic=1 order by endtime")->select();
        if($this->db->numRows==0){
            return 111;
        }else{
            return $result;
        }
    }
    /**
     * @名称        用户显示特定问卷汇总结果
     * @功能        从session里取出firmid,从前端POST过来surveyid,type,从表intention,satisfy,
     *              type=0意向,type=1满意度;
     * @return     json数组{count":{"nowcount":"当前回答人数","allcount":"总人数","ratio":"比例"},"datum":"评分基准(满意度)","time":{"sendtime":"发布时间","endtime":"收集时间"},"ave":所有体平均分,
     *              "lowrank"(后三名):{"no1":{"quid":"问题id","score":"总分"},...},"highrank"(前三名):{"no1":{"quid":"问题id","score":"总分"},...},,"sum"(总分排名递增):{"no1":{"quid":"","score":""}}
     */
    public function suresult($firmid,$surveyid,$type){
        $data= array();
        if( $type==0 ){
            $sum = $this->db->table('intention')->field('nowcount,allcount')->where("surveyid='{$surveyid}'")->select();
            $data['count']['nowcount']=$sum['nowcount'];
            $data['count']['allcount']=$sum['allcount'];
            $data['count']['ratio']=$sum['nowcount'].'/'.$sum['allcount'];
            $colltime= $this->db->table('survey')->field('sendtime,endtime')->where("surveyid='{$surveyid}' and firmid='{$firmid}'")->select();
            $data['time']['sendtime']=$colltime['sendtime'];
            $data['time']['endtime']=$colltime['endtime'];
        }else if( $type==1 ){
            $info = $this->db->table('survey')->field('sendtime,endtime,datum')->where("surveyid='{$surveyid}' and firmid='{$firmid}'")->select();
            $sum = $this->db->table('satisfy')->field('nowcount,allcount,totalscore')->where("surveyid='{$surveyid}'")->select(); //人数
            $data['count']['nowcount']=$sum['nowcount'];
            $data['count']['allcount']=$sum['allcount'];
            $data['count']['ratio']=$sum['nowcount'].'/'.$sum['allcount'];
            $data['datum']=$info['datum'];
            $data['time']['sendtime']=$info['sendtime'];
            $data['time']['endtime']=$info['endtime'];

            //问卷的总问题数
            $quid= $this->db->table('question')->field('quid')->where("surveyid='{$surveyid}'")->select();
            $qunums= $this->db->numRows;
            $people= $sum['nowcount']; //现在回答完成的人数
            $data['ave']=$sum['totalscore']/($qunums*$people);
            $opid= $this->db->table('answer')->field('SUM(opid) as A,quid')->where("surveyid='{$surveyid}' GROUP BY quid ORDER BY A")->select();
            if ( $qunums <= 6 ) {
                if ( $qunums == 1 ) {
                    //一维数组
                    $data['no1']['quid']=$opid['quid'];
                    $data['no1']['A']=$opid['A'];
                } else {
                    //多维数组遍历
                    $z=0;
                    foreach($opid as $k4 => $v4){
                        $z++;
                        $data['sum']['no'.$z]['quid']=$v4['quid'];
                        $data['sum']['no'.$z]['score']=$v4['A'];
                    }
                }
            } else {
                $z=0;
                foreach($opid as $k4 => $v4){
                    $z++;
                    $data['sum']['no'.$z]['quid']=$v4['quid'];
                    $data['sum']['no'.$z]['score']=$v4['A'];
                }
                $arr[]=array();
                $arr= array_slice($data['sum'],0,3);
                $data['lowrank']=$arr;
                rsort($opid);
                $z=0;
                foreach($opid as $k4 => $v4){
                    $z++;
                    $data['sum']['no'.$z]['quid']=$v4['quid'];
                    $data['sum']['no'.$z]['score']=$v4['A'];
                }
                $str[]=array();
                $str= array_slice($data['sum'],0,3);
                $data['highrank']=$str;
            }
        }
        return $data;
    }
    /**
     * @名称        显示特定问卷详细信息
     * @功能        从session里取出firmid,从前端POST过来surveyid,type,lv,userid从表intention,satisfy,survey,answer
     *              type=0意向,type=1满意度;
     * @return 
     */
    public function particular($firmid,$surveyid,$type,$lv,$userid){
        $data= array();
        if( $type==0 ){//意向
            $quid= $this->db->table('question')->field('quid,opmod')->where("surveyid='{$surveyid}'")->select();
            if($this->db->numRows==1){
                $data['opmod']= $quid['opmod'];//单选多选
                $data['qu1']['quid']= $quid['quid'];
                $nowcount= $this->db->table('intention')->field('nowcount')->where("surveyid='{$surveyid}'")->select();
//                $options= $this->db->table('option')->field('opid')->where("quid='{$quid['quid']}'")->select();
                $options= "SELECT opid FROM cx_option WHERE quid='{$quid['quid']}'";
                $sql= $this->db->query($options);
                $x=0;
                foreach($sql as $k1=> $v1){
                    $x++;
                    $data['qu1']['opnum'.$x]['opid']=$v1['opid'];
                    $answer= $this->db->table('answer')->where("quid='{$quid['quid']}' and surveyid='{$surveyid}' and opid='{$v1['opid']}'")->select();
                    $annum= $this->db->numRows;
                    if($annum == 0){
                        $data['qu1']['opnum'.$x]['ratio']= 0;
                    }else{
                        if($data['opmod']==1){//单选
                            $data['qu1']['opnum'.$x]['ratio']= sprintf("%.2f",($annum/$nowcount['nowcount'])*100 ) ."%";
                        }else{//多选
                            $this->db->table('answer')->where("quid='{$quid['quid']}' and surveyid='{$surveyid}'")->select();
                            $all=$this->db->numRows;
                            $data['qu1']['opnum'.$x]['ratio']= sprintf("%.2f",($annum/$all)*100 ) ."%";
                        }
                    }
                }
                if($lv==1){//用户
                    if( $data['opmod']==1){
                        $user= $this->db->table('answer')->field('opid')->where("quid='{$quid['quid']}' and surveyid='{$surveyid}' and userid='{$userid}'")->select();
                        $data['qu1']['mychoose']['opid1']=$user['opid'];
                    }else{
                        $user= $this->db->table('answer')->field('opid')->where("quid='{$quid['quid']}' and surveyid='{$surveyid}' and userid='{$userid}'")->select();
                        $h=0;
                        foreach($user as $k4 =>$v4 ){
                            $h++;
                            $data['qu1']['mychoose']['opid'.$h]=$v4['opid'];
                        }
                    }
                }
            }else{
                $z=0;
                foreach($quid as $k=> $v){
                    $z++;
                    $data['qu'.$z]['opmod']= $v['opmod'];
                    $data['qu'.$z]['quid']= $v['quid'];
                    $nowcount= $this->db->table('intention')->field('nowcount')->where("surveyid='{$surveyid}'")->select();
                    $options= "SELECT opid FROM cx_option WHERE quid='{$v['quid']}' ORDER BY opid";
                    $sql= $this->db->query($options);
//                    $options= $this->db->table('option')->field('opid')->where("quid='{$v['quid']}'")->select();
                    $x=0;
                    foreach($sql as $k1=> $v1){
                        $x++;
                        $data['qu'.$z]['opnum'.$x]['opid']=$v1['opid'];
                        $answer= $this->db->table('answer')->where("quid='{$v['quid']}' and surveyid='{$surveyid}' and opid='{$v1['opid']}'")->select();
                        $annum= $this->db->numRows;
                        if($annum == 0){
                            $data['qu'.$z]['opnum'.$x]['ratio']= 0;
                        }else{
                            if($data['qu'.$z]['opmod']==1){//单选
                                $data['qu'.$z]['opnum'.$x]['ratio']= sprintf("%.2f",($annum/$nowcount['nowcount']) * 100 ) ."%";
                            }else{//多选
                                $this->db->table('answer')->where("quid='{$v['quid']}' and surveyid='{$surveyid}'")->select();
                                $all=$this->db->numRows;
                                $data['qu'.$z]['opnum'.$x]['ratio']= sprintf("%.2f",($annum/$all)*100 ) ."%";
                            }
                        }
                    }
                    if($lv==1){//用户
                        if( $data['qu'.$z]['opmod']==1){//单选
                            $user= $this->db->table('answer')->field('opid')->where("quid='{$v['quid']}' and surveyid='{$surveyid}' and userid='{$userid}'")->select();
                            $data['qu'.$z]['mychoose']['opid1']=$user['opid'];
                        }else{//多选
                            $user= $this->db->table('answer')->field('opid')->where("quid='{$v['quid']}' and surveyid='{$surveyid}' and userid='{$userid}'")->select();
                            $h=0;
                            foreach($user as $k4 =>$v4 ){
                                $h++;
                                $data['qu'.$z]['mychoose']['opid'.$h]=$v4['opid'];
                            }
                        }
                    }
                }
            }
        }else if($type==1){//满意度
            $datum= $this->db->table('survey')->field('datum')->where("surveyid='{$surveyid}'")->select();
            $i=$datum['datum'];//评分等级
            $quid= $this->db->table('question')->field('quid,opmod')->where("surveyid='{$surveyid}'")->select();
            if($this->db->numRows==1){
                $data['qu1']['quid']= $quid['quid'];
                $nowcount= $this->db->table('satisfy')->field('nowcount')->where("surveyid='{$surveyid}'")->select();
                for($j=1;$j <= $i;$j++){
                    $answer= $this->db->table('answer')->where("quid='{$quid['quid']}' and surveyid='{$surveyid}' and opid='{$j}'")->select();
                    $annum= $this->db->numRows;
                    $data['qu1']['opnum'.$j]['opid']= $j;
                    if($annum == 0){
                        $data['qu1']['opnum'.$j]['ratio']= 0;
                    }else{
                        $data['qu1']['opnum'.$j]['ratio']= sprintf("%.2f",($annum/$nowcount['nowcount'])*100 ) ."%";
                    } 
                } 
                if( $lv==1 ){//用户
                    $user= $this->db->table('answer')->field('opid')->where("quid='{$quid['quid']}' and surveyid='{$surveyid}' and userid='{$userid}'")->select();
                    $data['qu1']['mychoose']['opid1']=$user['opid'];
                }
            }else{
                $y=0;
                foreach($quid as $k2 => $v2){
                    $y++;
                    $data['qu'.$y]['quid']=$v2['quid'];
                    $nowcount= $this->db->table('satisfy')->field('nowcount')->where("surveyid='{$surveyid}'")->select();
                    for($j=1;$j <= $i;$j++){
                        $answer= $this->db->table('answer')->where("quid='{$v2['quid']}' and surveyid='{$surveyid}' and opid='{$j}'")->select();
                        $annum= $this->db->numRows;
                        $data['qu'.$y]['opnum'.$j]['opid']= $j;
                        if($annum == 0){
                            $data['qu'.$y]['opnum'.$j]['ratio']= 0;
                        }else{
                            $data['qu'.$y]['opnum'.$j]['ratio']= sprintf("%.2f",($annum/$nowcount['nowcount'])*100 ) ."%";
                        }
                    } 
                    if( $lv==1 ){//用户
                    $user= $this->db->table('answer')->field('opid')->where("quid='{$v2['quid']}' and surveyid='{$surveyid}' and userid='{$userid}'")->select();
                    $data['qu'.$y]['mychoose']['opid1']=$user['opid'];
                    }
                }
            }
        }
        return $data;
    }
     /**
     * @名称        显示特定问卷即时信息
     * @功能        从session里取出firmid,从前端POST过来surveyid,type,userid从表intention,satisfy,survey,ancache
     *              type=0意向,type=1满意度;
     * @return     
     */
    public function temporary($firmid,$surveyid,$type,$userid){
        $data= array();
        if( $type==0 ){//意向
            $quid= $this->db->table('question')->field('quid,opmod')->where("surveyid='{$surveyid}'")->select();
            if($this->db->numRows==1){
                $data['opmod']= $quid['opmod'];//单选多选
                $data['qu1']['quid']= $quid['quid'];
                $nowcount= $this->db->table('intention')->field('nowcount')->where("surveyid='{$surveyid}'")->select();
//                $options= $this->db->table('option')->field('opid')->where("quid='{$quid['quid']}'")->select();
                $options= "SELECT opid FROM cx_option WHERE quid='{$quid['quid']}'";
                $sql= $this->db->query($options);
                $x=0;
                foreach($sql as $k1=> $v1){
                    $x++;
                    $data['qu1']['opnum'.$x]['opid']=$v1['opid'];
                    $answer= $this->db->table('ancache')->where("quid='{$quid['quid']}' and surveyid='{$surveyid}' and opid='{$v1['opid']}'")->select();
                    $annum= $this->db->numRows;
                    if($annum == 0){
                        $data['qu1']['opnum'.$x]['ratio']= 0;
                    }else{
                        if($data['opmod']==1){//单选
                            $data['qu1']['opnum'.$x]['ratio']= sprintf("%.2f",($annum/$nowcount['nowcount'])*100 ) ."%";
                        }else{//多选
                            $this->db->table('ancache')->where("quid='{$quid['quid']}' and surveyid='{$surveyid}'")->select();
                            $all=$this->db->numRows;
                            $data['qu1']['opnum'.$x]['ratio']= sprintf("%.2f",($annum/$all)*100 ) ."%";
                        }
                    }
                }
            }else{
                $z=0;
                foreach($quid as $k=> $v){
                    $z++;
                    $data['qu'.$z]['opmod']= $v['opmod'];
                    $data['qu'.$z]['quid']= $v['quid'];
                    $nowcount= $this->db->table('intention')->field('nowcount')->where("surveyid='{$surveyid}'")->select();
                    $options= "SELECT opid FROM cx_option WHERE quid='{$v['quid']}' ORDER BY opid";
                    $sql= $this->db->query($options);
//                    $options= $this->db->table('option')->field('opid')->where("quid='{$v['quid']}'")->select();
                    $x=0;
                    foreach($sql as $k1=> $v1){
                        $x++;
                        $data['qu'.$z]['opnum'.$x]['opid']=$v1['opid'];
                        $answer= $this->db->table('ancache')->where("quid='{$v['quid']}' and surveyid='{$surveyid}' and opid='{$v1['opid']}'")->select();
                        $annum= $this->db->numRows;
                        if($annum == 0){
                            $data['qu'.$z]['opnum'.$x]['ratio']= 0;
                        }else{
                            if($data['qu'.$z]['opmod']==1){//单选
                                $data['qu'.$z]['opnum'.$x]['ratio']= sprintf("%.2f",($annum/$nowcount['nowcount']) * 100 ) ."%";
                            }else{//多选
                                $this->db->table('ancache')->where("quid='{$v['quid']}' and surveyid='{$surveyid}'")->select();
                                $all=$this->db->numRows;
                                $data['qu'.$z]['opnum'.$x]['ratio']= sprintf("%.2f",($annum/$all)*100 ) ."%";
                            }
                        }
                    }
                }
            }
        }else if($type==1){//满意度
            $datum= $this->db->table('survey')->field('datum')->where("surveyid='{$surveyid}'")->select();
            $i=$datum['datum'];//评分等级
            $quid= $this->db->table('question')->field('quid,opmod')->where("surveyid='{$surveyid}'")->select();
            if($this->db->numRows==1){
                $data['qu1']['quid']= $quid['quid'];
                $nowcount= $this->db->table('satisfy')->field('nowcount')->where("surveyid='{$surveyid}'")->select();
                for($j=1;$j <= $i;$j++){
                    $answer= $this->db->table('ancache')->where("quid='{$quid['quid']}' and surveyid='{$surveyid}' and opid='{$j}'")->select();
                    $annum= $this->db->numRows;
                    $data['qu1']['opnum'.$j]['opid']= $j;
                    if($annum == 0){
                        $data['qu1']['opnum'.$j]['ratio']= 0;
                    }else{
                        $data['qu1']['opnum'.$j]['ratio']= sprintf("%.2f",($annum/$nowcount['nowcount'])*100 ) ."%";
                    } 
                } 
            }else{
                $y=0;
                foreach($quid as $k2 => $v2){
                    $y++;
                    $data['qu'.$y]['quid']=$v2['quid'];
                    $nowcount= $this->db->table('satisfy')->field('nowcount')->where("surveyid='{$surveyid}'")->select();
                    for($j=1;$j <= $i;$j++){
                        $answer= $this->db->table('ancache')->where("quid='{$v2['quid']}' and surveyid='{$surveyid}' and opid='{$j}'")->select();
                        $annum= $this->db->numRows;
                        $data['qu'.$y]['opnum'.$j]['opid']= $j;
                        if($annum == 0){
                            $data['qu'.$y]['opnum'.$j]['ratio']= 0;
                        }else{
                            $data['qu'.$y]['opnum'.$j]['ratio']= sprintf("%.2f",($annum/$nowcount['nowcount'])*100 ) ."%";
                        }
                    } 
                }
            }
        }
        return $data;
    }
    
}


