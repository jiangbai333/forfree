<?php

/**
 * @文件        indexController.class.php   
 * @作者        b-jiang
 * @描述        类文件
 * @功能        (使用您组织的语言替换此处,用以描述该文件基本功能)
 * @起始日期    2014-2-6  15:56:13    
 * @文件版本    1.2.2   
 */
class indexController extends controller{
    public function help() {
        $this->space = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
        $this->title = array(
            'head'  =>  'forFree1.2.4帮助文档',
            'body'  =>  'forFree ',
            'add'   =>  '新增功能:',
            'optimization'  =>  '优化内容:',
            'imprint'   =>  '版本说明:',
        );
        $this->version = '版本号:1.2.4';
        $this->author = '作者:b-jiang';
        $this->coreCT = '内核修改时间：2014-1-28  12:30:50';
        $this->coreOT = '核心完成时间：';
        
        //新增功能
        $this->add = array(
            1   =>  '1) 添加工具函数：'. $this->changeColor('mark()', 'a', "href='#mark'"). '，'. $this->changeColor('redirect()', 'a', "href='#redirect'"). '。',
            2   =>  '2) 添加日志类和错误类(该版本未完成)：'. $this->changeColor('log'). '，'. $this->changeColor('error'). '。',
            3   =>  '3) 核心控制器增加'. $this->changeColor('ajaxReturn()', 'a', "href='#ajaxReturn'"). '方法，优化ajax返回数据的类型。该方法可被子类继承使用。',
            4   =>  '4) 应用内添加单一入口协议。',
            5   =>  '5) 搭载jmind1.0.1原生框架。目前该框架处于开发中，功能尚不完善，但是应用此框架，可实现表现层与动作层分离。'
        );
        
        //优化项
        $this->optimization = array(
            1   =>  '1) 优化模板变量赋值，可直接通过'. $this->changeColor('$this->param = string|array|bool|object'). '进行赋值。',
            2   =>  '2) 优化数据库操作，可通过配置文件中'. $this->changeColor('autocon'). '属性设置是否自动连接数据库，从而避免在不使用数据库时浪费资源。',
            3   =>  '3) 修正核心驱动类中'. $this->changeColor('setAutoLibs()'). '方法多次调用无效bug。',
            4   =>  '4) 删除核心驱动类中赘余方法'. $this->changeColor('newLib()'). '。',
            5   =>  '5) 修改控制器方法'. $this->changeColor('display()'). ',当视图未指定时,默认渲染与当前动作名称和分组相同的视图。'
        );
        
        //版本说明
        $this->imprint = 'V1.2.4版本相对于V1.2原始版本有较大改动，虽然主体功能不变，但是由于修改了类文件加载方法，因此，在一定意义<br>
            上会造成一些兼容性问题。主要原因是由于文件命名，V1.2.3之前的版本，类文件名以大写字母开头，V1.2.4版本中全部改为<br>
            自然命名法，无需特意将首字母大写。<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            针对控制器，模型的引用，与之前版本相同。可以仿照'. $this->changeColor('项目实例', 'a', "href='#examples1'", 'blue'). '进行操作。<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            对于ajax的处理，与普通开发有所不同，全部应用都是针对入口文件建立的，请仿照'. $this->changeColor('ajax实例', 'a', "href='#examples2'", 'blue'). '进行操作。<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            对于日志类[log]开发者可根据自己需要，向需要纪录的位置添加'. $this->changeColor('writeLog()'). '方法，通过调整配置文件以及方法参数，可实<br>
                现自定义生成日志文件，公共库'. $this->changeColor('common'). '内'. $this->changeColor('global.php'). '文件内部可自定义日志等级，方便分类管理。';
        
        //实例1
        $this->examples_first = array(
            'title' =>  '实例1：构建完整项目',
            
            '1' =>  array(
                'title' =>  '1.在view/下建立show.html:',
                'source'  =>  '&lt;!DOCTYPE html&gt;
&lt;html&gt;
    &lt;head&gt;
        &lt;meta http-equiv="Content-Type" content="text/html; charset=UTF-8"&quot;&gt;
        &lt;title&gt;{$title}&lt;/title&gt;
    &lt;/head&gt;
    &lt;body&gt;
        &lt;div&gt;{$str}&lt;/div&gt;
    &lt;/body&gt;
&lt;/html&gt;'
            ),
            
            '2' =>  array(
                'title' =>  '2.在app/controller/下建立showController.class.php(视图show的控制器):',
                'source'  =>  'class showController extends controller{
    public function firstApp() {
        $this->title = \'我是标题\'; //模板变量直接赋值
        $this->str = \'第一个forfree应用\'; //模板变量直接赋值
        $this->display(\'show.html\'); //指定需要渲染的视图
    }
}',
            ),
            
            '3' =>  array(
                'title' =>  '3.修改配置文件(可选):',
                'source'  =>  '$config[\'system\'][\'router\'] = array(
    \'default_c\'	=>	\'show\', //默认controller 指定为show后 会直接运行showontroller控制器
    \'default_a\'	=>	\'firstApp\', //默认action 指定为firstApp后 会直接运行firstApp方法
    \'default_g\'     =>      \'\'  //默认group
    \'urlType\'	=>	\'\',
);',
            ),
            
            '4' =>  array(
                'title' =>  '4.运行项目',
                'str1'  =>  '若用户进行了第3步操作，则在浏览器中输入 http://localhost/项目名/index.php 进行访问',
                'str2'  =>  '若用户没有进行第3步操作，则在浏览器中输入 http://localhost/项目名/index.php?a=firstApp&c=show 进行访问',
            ),
        );
        
        //实例2
        $this->examples_second = array(
            'title' =>  '实例2：ajax实例',
            
            '1' =>  array(
                'title' =>  '1.在view/下建立ajax.html:',
                'source'  =>  '&lt;!DOCTYPE html&gt;
&lt;html&gt;
    &lt;head&gt;
        &lt;meta http-equiv="Content-Type" content="text/html; charset=UTF-8"&quot;&gt;
        &lt;title&gt;{$title}&lt;/title&gt;
        &lt;script src =\'./app/_jScript/index.js\' rp="false"&gt;&lt;/script&gt;
    &lt;/head&gt;
    &lt;body&gt;
        &lt;div id=\'test\' onclick=\'test();\'&gt;{$str}&lt;/div&gt;
    &lt;/body&gt;
&lt;/html&gt;'
            ),
            
            '2' =>  array(
                'title' =>  '2.在app/controller/下建立ajaxController.class.php(视图show的控制器):',
                'source'  =>  'class ajaxController extends controller{
    /**
     * 渲染首页
     */
    public function ajaxApp() {
        $this->title = \'ajax测试文件\'; //模板变量直接赋值
        $this->str = \'第一个ajax应用\'; //模板变量直接赋值
        $this->display(\'ajax.html\'); //指定需要渲染的视图
    }
    
    /**
     * ajax动作
     */
    public function ajaxReturn() {
        echo \'success\';
    }
}',
            ),
            
            '3' =>  array(
                'title' =>  '3.在app/_jScript/下建立index.js:',
                'source'  =>  '
var ajax;
function test(){
    ajax=GetXmlHttpObject();    
    if (ajax === null){alert ("Browser does not support HTTP Request");return;} 
    var url="./index.php?c=ajax&a=ajaxReturn"; //给出POST位置 指向ajaxController的ajaxApp动作
    ajax.onreadystatechange=ajaxReturn; 
    ajax.open("POST",url,true); //POST方法，启用异步通讯
    ajax.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    ajax.send(); //发送数据
    return;
}

function ajaxReturn(){
    if (ajax.readyState===4 || ajax.readyState==="complete"){ //判断服务端处理状态
        document.getElementById(\'test\').innerHTML = ajax.responseText; //ajax返回
        return;
    }
}

function GetXmlHttpObject(){
    var ajax=null;
    // Firefox, Opera 8.0+, Safari
    try{ajax=new XMLHttpRequest();}
    // Internet Explorer
    catch (e){
        try{ajax=new ActiveXObject("Msxml2.XMLHTTP");}
        catch (e){ajax=new ActiveXObject("Microsoft.XMLHTTP");}
    }
    return ajax;
}',
            ),
                        
            '4' =>  array(
                'title' =>  '4.修改配置文件(可选):',
                'source'  =>  '$config[\'system\'][\'router\'] = array(
    \'default_c\'	=>	\'ajax\', //默认controller 指定为show后 会直接运行showontroller控制器
    \'default_a\'	=>	\'ajaxApp\', //默认action 指定为firstApp后 会直接运行firstApp方法
    \'default_g\'     =>      \'\'  //默认group
    \'urlType\'	=>	\'\',
);',
            ),
            
            '5' =>  array(
                'title' =>  '5.运行项目',
                'str1'  =>  '若用户进行了第3步操作，则在浏览器中输入 http://localhost/项目名/index.php 进行访问',
                'str2'  =>  '若用户没有进行第3步操作，则在浏览器中输入 http://localhost/项目名/index.php?a=ajaxApp&c=ajax 进行访问',
            ),
        );
        
        //function mark()
        $this->Fmark = array(
            'title' =>  'function mark()',
            'explain'   =>  'mark()函数是自V1.2.4版本起系统提供的工具函数，用户可在C或M中调用此函数，返回一个20位的唯一数字ID',
            'test'  =>  'mark();',
            'oput'  =>  mark()
        );
        
        $this->Fredirect = array(
            'title' =>  'function redirect($url, $time = 0)',
            'explain'   =>  'redirect()函数是自V1.2.4版本起系统提供的工具函数，用户可在C或M中调用此函数，实现重定向。$time参数指定等待时间。',
            'test1'  =>  'redirect();',
            'oput1'  =>  '重定向到首页',
            'test2'  =>  'redirect(\'目标url\');',
            'oput2'  =>  '重定向到url指向的页面',
        );    
        
        $this->FajaxReturn = array(
            'title' =>  'function ajaxReturn($data, $is_end = true, $type = \'json\')',
            'explain'   =>  'ajaxReturn()函数是控制器私有方法，该方法由核心控制器制定，子类可继承使用但无法覆盖。该方法形参$data必须给出，其数据类型为 string|array|int ,默认返回json数据类型。',
            'test1'  =>  '$this->ajaxReturn(string|array|int);',
            'oput1'  =>  '将指定的数据按照json格式返回给js脚本，js脚本可通过 eval("(" + ajax.responseText + ")") 将返回的数据当作对象处理' ,
            'test2'  =>  '$this->ajaxReturn(string|array|int， false);',
            'oput2'  =>  '若将$is_end参数指定为false 则此次返回非最后一次返回，后续的数据将继续返回给js脚本',
        );     
        $this->display();
    }

    /**
     * 改变输出样式
     * @param string $str 需要显示的字符串 [文本]
     * @param type $tag 标签名 [默认为span]
     * @param type $property 属性 [id|className|.....]
     * @param string $color 显示的颜色
     * @实例 $this->makeTag('mark()', 'a', "href='#mark'")；
     * @return tag
     */
    private function changeColor($str, $tag = 'span', $property = '', $color = 'tomato') {
        $str = preg_replace('/</', '&lt;', $str);
        $str = preg_replace('/>/', '&gt;', $str);
        return "<$tag $property style='color: $color' title='点击察看详细'>$str</$tag>";
    }
}

/* **文件 indexController.class.php 结束 */
/* 文件位置: *********/