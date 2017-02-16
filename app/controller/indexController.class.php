<?php

/**
 * @文件        indexController.class.php   
 * @作者        b-jiang
 * @描述        类文件
 * @功能        (使用您组织的语言替换此处,用以描述该文件基本功能)
 * @起始日期    2014-1-7  17:07:28    
 * @文件版本    1.2.2   
 */
class indexController extends controller{
    public function index() {
        $img = imagecreatetruecolor(500, 500);
        $blue = imagecolorallocate($img, 0, 0, 255);
        for ($i = 0, $j = 0; $i <= 500; $i++, $j++) {
            imagesetpixel($img, $i, $j, $blue);
        }
        imagefill($img,1,0,65535); //区域填充
        header('Content-type:image/jpeg');
        imagejpeg($img);
        imagedestroy($img);
//        $this->str = 'test';
//        $this->title = array(
//            'head'  =>  'forfree1.2.5',
//            'body'  =>  'ForFree',
//        );
////        echo ff::$lib['my']->a;
////        ff::$lib['my']->work();
////        mysql_query("CREATE TABLE IF NOT EXISTS `table7` (
////  `a` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'bbb',
////  `b` int(11) NOT NULL AUTO_INCREMENT,
////  PRIMARY KEY (`b`)
////) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ");
////        mysql_query('DROP TABLE table1');
////        $a = mysql_query(" SHOW TABLES FROM " . ff::$conDb['database']);
////        $c = array();
////        while($b = mysql_fetch_row($a)){
////            $c[] = $b;
////        }
////            print_r($c);
////        echo "☑✔✓";
////        var_dump(explode(" ",M('index')->table('table1')->select()['a']));
////        echo md5('test3');
//        $this->display();
    }

    public function show() {
        //echo $this->lib['log']->catchLogFile();
        //redirect('./index.php', 3);
        //$this->ajaxReturn(mark());
//        $keys = array();
//        for($i = 0; $i < 100; $i++) {
//            $keys[$i] = mark(2);
//        }
//        print_r($keys);
//        M('index')->table('test')->data(array('key'=>$keys))->add();
//        $data = M('index')->table('info')->field('email')->where("userid=1028 or userid=1029 or userid=1030 or userid=1027")->select();
//        foreach ($data as $key => $value) {
//            echo $value['email'].'<br>';
//            ff::$lib['mail']->smtp($value['email'], '565197391@qq.com', '测试邮件', '这是一封测试邮件');
//        }
        
    }
    
    public function send() {
//        $data = M('index')->table('info')->field('email')->where("userid=1028 or userid=1029 or userid=1030 or userid=1027")->select();
        $data = M('index')->table('test')->where('id=1')->select();
        $a = $data['key'];
//        foreach ($data as $key => $value) {
//            echo $value['email'].'<br>';
//            ff::$lib['mail']->smtp($value['email'], '565197391@qq.com', '测试邮件', '这是一封测试邮件');
//        }
        for($i=0;$i<10;$i++)
        ff::$lib['mail']->smtp('565197391@qq.com', '1129087617@qq.com', '测试邮件', "http://localhost/forfree1.2.5/index.php?a=test&p0={$a}");
    }
    
    public function test() {
//        $sql = "SELECT uname FROM user where uid=2";
//        $a = M('index')->query($sql);
//        echo $this->micro()-M('index')->queryStartTime."<br>";
//        $sql = "SELECT user.uname, art.title FROM user JOIN art ON user.uid = art.uid WHERE user.uid>1 AND art.tid=2";
//        $a = M('index')->query($sql);
//        echo $this->micro()-M('index')->queryStartTime."<br>";
//        if(!array_foo($a)){
//        echo '<table>';
//        foreach ($a as $key => $value) {
//            echo "<tr>";
//            echo "<td>{$value['uname']}</td><td>{$value['title']}</td>";
//            echo "<tr>";
//            
//        }
//        
//        echo "</table>";
//        }else {print_r($a);}
        
        $a = M('index')->table('art')->order('CONVERT(title USING gbk)')->select();
        foreach ($a as $key => $value) {
            echo "<br>";
            print_r($value);
            echo "<br>";
        }
        /**
         * 打开csv
         */
//        $file =fopen($_FILES["file"]['name'],"r"); 
////        $file = fopen("a.csv","r");
//        while (!feof($file)){
//        print_r(fgetcsv($file));
//        echo "<br>";
//        
//        }
//        echo "Upload: " . $_FILES["file"]["name"] . "<br />";
//		echo "Type: " . $_FILES["file"]["type"] . "<br />";
//		echo "Size: " . ($_FILES["file"]["size"] / 1024) . " Kb<br />";
//		echo "Temp file: " . $_FILES["file"]["tmp_name"] . "<br />";
//        print_r($file);
//        fclose($file);
        
//        var_dump($_FILES);
    }
}

?>
