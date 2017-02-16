<?php

/**
 * @文件:       common.php
 * @作者:       b-jiang
 * @版本:       1.2.6
 * @创建时间:   2014-7-9 12:41:54
 */

/**
 *          实例化模型 [V1.2.0] 工具函数 [模型]
 * @param string $modelPrefix 模型名前缀
 * @return object 模型操作句柄
 */
function N($modelPrefix) {
    $model = $modelPrefix.'Model'; //模型类名
    return class_exists($model) ? new $model : false; //模型类是否存在，若存在则返回该模型操作对象
}

/**
 *          抓取对应模型的操作句柄 [V1.2.0] 工具函数 [模型]
 * @param string $modelPrefix 模型名前缀
 * @return object 数据库操作句柄
 */
function M($modelPrefix) {
    return is_object(N($modelPrefix)) ? N($modelPrefix)-> db : false; //模型类是否被实例化为对象，若为真则返回该模型的数据库操作句柄db
}

/**
 *          从给定的数组中剪切出一个元素 [V1.2.5] 工具函数 [数组] 
 * @param array $arr 目标数组
 * @param int|string $key 将要被剪切位置的键名
 * @return string|int|array|object|bool $arr[$key]
 */
function array_cut(&$arr, $key = 0) {
    $a = $arr[$key]; //取出当前键位的值
    unset($arr[$key]); //释放当前键位
    return $a; //返回被剪切的值
}

/**
 *          判断数组是否为一维数组 [V1.2.2] 工具函数 [数组]
 * @param array $arr 需要检查的数组
 * @return boolean true $arr为一维数组 false $arr为多位数组
 */
function array_foo($arr) {
    return count($arr)==count($arr, 1);
}

/**
 *          获得数组最大维度 [V1.2.2] 工具函数 [数组]
 * @param array $data 需要检查的数组
 * @return int 数组维度
 */
function array_maxDim($data)
{
    if(!is_array($data)) return 0; //非数组返回0
    else {
        $max1 = 0; //计数器清零
        foreach($data as $item1) { //遍历要检查的数组
            $t1 = $this->arrayMaxDim($item1); //递归
            if( $t1 > $max1) $max1 = $t1; //计数器内部值交换
        }
        return $max1 + 1; //返回计数值
    }
}

/**
 *          字符串大小写转换 [V1.2.3] 工具函数 [字符串]
 * @param string $str 需要转换的字符串
 * @param TO_LOWER|TO_UPPER $flag 转换标记
 * @return string
 */
function str_ul_change($str, $flag = 'TO_LOWER') {
    if($flag === 'TO_LOWER') return strtolower($str); //返回小写
    else if($flag === 'TO_UPPER') return strtoupper ($str); //返回大写
    else return $str; //标记不合法，返回原字符串
}


/**
 *          从字符串中读取指定位置的字母 [V1.2.3] 工具函数 [字符串]
 * @摘要 <b>字符串起始位置为0</b>
 * @param string $str 规定的字符串
 * @param int $start 读取的起始位置[默认为0，即字符串首字母]
 * @param int|string $length 读取的字母数目[默认为1，返回单个字母][若此参数大于1，则返回一数组，包含$length个元素，第一个元素为$start位置的字母][若此参数为字符串，会自动检查需要匹配的字母个数]
 * @return string|array|bool
 */
function str_get_letter($str, $start = 0, $length = 1) {
    if(is_numeric($length)) { //$length为数字
        if($length === 1) return substr($str, $start, $length); //默认长度返回单个字母
        else return str_split(substr($str, $start, $length), 1); //长度大于1，返回数组
    } else if(is_string($length)){ //$length为字母
        return strripos($str, $length) - $start == 1 ? //$length所在位置与起始位置$start的距离
                substr($str, $start, strripos($str, $length)) : //距离为1 返回单个字母
                str_split(substr($str, $start, strripos($str, $length)), 1);//距离大于1 返回数组
    } else return false; //非法形参
}

/**
 *          生成唯一ID [V1.2.4] 工具函数 [标识]
 * @摘要 <b>20位数字ID [2014]-年 [02]-月 [06]-日 [11]-时 [12]-分 [59]-秒 [2323]-微秒 [12]-随机值</b>
 * @param int $flag 生成方式 模式0 20位数字ID 模式1 20位混合ID 模式2 10位混合ID
 * @return string 唯一ID
 */
function mark($flag = 0) {
    $seed = 'abcdefghijkmnopqrstuvwxyz1234567890ABCDEFGHJKLMNPQRSTUVWXYZ'; //种子
    list($usec, $sec) = explode(" ", microtime());
    $usec = substr(str_replace('0.', '', $usec), 0 ,4);
    $str = rand(10,99);
    $id = date("YmdHis").$usec.$str;
    if ($flag === 0) {
        return $id;
    } else if ($flag === 1) {
        $arr = str_split($id);
        $str = '';
        foreach ($arr as $key => $value) {
            $str .= str_get_letter($seed, rand(0, rand(0,5). $value));
        }
        return $str;
    } else if ($flag === 2) {
        $arr = str_split($id, 2);
        $str = '';
        foreach ($arr as $key => $value) {
            $str .= str_get_letter($seed, rand(0, $value%59));
        }
        return $str;
    } else {
        return $id;
    }
}


/**
 * url重定向
 * @param mixed $url 定向位置
 * @param int $time 延迟时间
 */
function redirect($url = './index.php', $time = 0) {
    if (!headers_sent()) {
            if ($time === 0) header("Location: ".$url);
            header("refresh:" . $time . ";url=" .$url. "");
    } else {
            exit("<meta http-equiv='Refresh' content='" . $time . ";URL=" .$url. "'>");
    }
}

function micro() {
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

//End of file common.php