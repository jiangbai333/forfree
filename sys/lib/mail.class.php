<?php
if (!defined('FORFREE')) {require_once '../conf/macro.php'; exit(__MSG__);}

/**
 * @文件        mail.class.php   
 * @作者        b-jiang
 * @描述        类文件
 * @功能        邮件操作类
 * @起始日期    2014-3-17  12:37:31    
 * @文件版本    1.2.5   
 */
final class Mail { 
    private $socket; //套接字资源集
    
    private $host; //邮件服务器域名
    
    private $user; //邮件服务器用户名
    
    private $pwd; //邮件服务器密码
    
    private $port; //邮件服务器端口号
    
    private $nickName; //发件人昵称
    
    private $homePage; //发件人主页
              
    /**
     *          构造方法
     */
    function __construct(){
        $this->autoSmtpInit(ff::$conMail);
    }
    
    /**
     *          自动smtp连接初始化 [V1.2.5] class Mail [私有]
     * 通过指定邮箱发送邮件
     * @param array $config 邮箱配置
     */
    private function autoSmtpInit($config) {
        $this->host = $config['host']; //邮件服务器域名
        $this->user = $config['user']; //邮件服务器用户名
        $this->pwd  = $config['pwd']; //邮件服务器密码
        $this->port = $config['port']; //邮件服务器端口号
        $this->nickName = $config['nickName']; //发件人昵称
        $this->homePage = $config['homePage']; //发件人主页
    }
    
    /**
     *          自定义smtp连接初始化 [V1.2.5] class Mail [公共]
     * 通过使用者自己的邮箱发送邮件
     * @param array $config 邮箱配置
     */
    public function smtpInit($config) {
        $this->host = $config['host']; //邮件服务器域名
        $this->user = $config['user']; //邮件服务器用户名
        $this->pwd  = $config['pwd']; //邮件服务器密码
        $this->port = $config['port']; //邮件服务器端口号
        $this->nickName = $config['nikcname']; //发件人昵称
        $this->homePage = $config['homePage']; //发件人主页
    }    
    
    /**
     *          执行smtp发送请求 [V1.2.5] class mail [公共]
     * @param type $to  收信人
     * @param type $from    送信人
     * @param type $subject 邮件主题
     * @param type $body 邮件内容
     * @return type
     */
    public function smtp($to, $from, $subject, $body){
        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP); //创建套接字
        $conn = socket_connect($this->socket, $this->host, $this->port); //启动套接字连接
        if( $conn ){ // 连接成功            
            $msg  = "succeed connect to ".$this->host.":".$this->port."\n";
            $msg .= $this->_r();
            // 开始认证过程
            $this->_w("HELO ".$this->homePage."\r\n"); //尝试与服务器握手
            $msg .= $this->_r();
            $this->_w("AUTH LOGIN ".base64_encode($this->user)."\r\n"); //发送smtp账号
            $msg .= $this->_r();
            $this->_w(base64_encode($this->pwd)."\r\n"); //发送smtp密码
            $msg .= $this->_r();
            if( stripos($msg, '235 Authentication successful')!==FALSE ){ //用户验证成功				
                $this->_w("MAIL FROM:<{$from}>\r\n"); //发信人地址送入socket流
                $msg .= $this->_r();
                $this->_w("RCPT TO:<{$to}>\r\n"); //收信人地址送入socket流
                $msg .= $this->_r();                          
                $this->_w("DATA\r\n"); //传输开始
                $msg .= $this->_r();
                $data = $this->_genHeader($to, $from, $subject). "\r\n". $this->_genBody($body); //连接邮件头部与邮件体
                $this->_w($data); //邮件送入socket流
                $this->_w("\r\n.\r\n"); //邮件文本结束标识
                $msg .= $this->_r();
                $this->_w("QUIT\r\n"); //结束socket传输
                $msg .= $this->_r();
                socket_close($this->socket);
            }
        }
        return $msg;
    }
              
    /**
     *          生成邮件头部信息 [V1.2.5] class mail[私有]
     * @param string $to 收信人
     * @param string $from 发信人
     * @param string $subject 主题
     * @return string 完整的头部信息
     */
    private function _genHeader($to, $from, $subject){
        $header  = "MIME-Version:1.0\r\n"; //遵从的MIME1.0规范
        $header .= "Content-Type: text/plain; charset=\"utf-8\"\r\n"; //标准化表示纯文本信息
        $header .= "Subject: =?utf-8?B?".base64_encode($subject)."?=\r\n"; //邮件主题
        $header .= "From: ".$this->nickName." <".$from.">\r\n"; //发信人
        $header .= "To: ".$to."\r\n"; //收信人
        $header .= "Date: ".date("r")."\r\n"; //日期与时间
        list($msec, $sec) = explode(" ", microtime());
        $header .= "Message-ID: <DCC_".date("YmdHis").".".($msec*1000000).".".$from.">\r\n"; //邮件标识
        return $header;
    }
              
    /**
     *          生成邮件文本信息 [V1.2.5] class mail[私有]
     * @param string $body 文本字符串
     * @return type
     */
    private function _genBody($body){
        $body = preg_replace("/(^|(\r\n))(\.)/", "\1.\3", $body);
        return $body;
    }
              
    /**          写入套接字资源 [V1.2.5] class mail [私有]
     * @param string $s 套接字资源符
     */
    private function _w($s){
        socket_write($this->socket, $s);
    }
    
    /**
     *          读取套接字资源 [V1.2.5] class mail [私有]
     * @return type 连接是否成功
     */
    private function _r(){
        return socket_read($this->socket, 1024); //读取套接字资源
    }
}

//* End of the file mail.class.php  
//* File path : ./sys/lib/