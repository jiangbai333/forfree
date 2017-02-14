<?php
if (!defined('FORFREE')) {require_once '../conf/macro.php'; exit(__MSG__);}

/**
 * @文件        date.class.php   
 * @作者        b-jiang
 * @描述        类文件
 * @功能        日期/时间
 * @起始日期    2014-3-1  10:42:00    
 * @文件版本    1.2.5
 */
final class Date {
    public $stamp; //时间戳
    
    public $year; //年

    public $month; //月
    
    public $cMonth; //月的完整表示,如：January
    
    public $day; //日
    
    public $hour; //时
    
    public $minute; //分
    
    public $second; //秒
    
    public $weekNum; //星期的数字表示
    
    public $weekday; //星期的完整汉字文本表示，如：星期一
    
    public $cWeekday; //星期的完整文本表示
    
    public $yDay; //今天是一年中的第几天，0－365
    
    public $week = array("日", "一", "二", "三", "四", "五", "六"); //星期的输出
    
    /**
     *          构造方法
     */
    public function __construct() {
        $date = getdate(); //抓取时间信息
        $this->stamp = $date[0]; //时间戳
        $this->year = $date['year']; //年
        $this->month = $date['mon']; //月
        $this->cMonth = $date['month']; //月份的完整文本表示
        $this->day = $date['mday']; //日
        $this->hour = $date['hours']; //时
        $this->minute = $date['minutes']; //分
        $this->second = $date['seconds']; //秒
        $this->weekday = $date['wday']; //星期
        $this->weekday = '星期'. $this->week[$date['wday']]; //星期的完整汉字文本表示
        $this->cWeekday = $date['weekday']; //星期的完整文本表示
        $this->yDay = $date["yday"]; //一年中的第几天
    }
    
    /**
     *          判断是否为闰年 [V1.2.5] class Date[公共方法]
     * @param int $year 需要判断的年
     * @return bool 
     */
    public function isLeapYear($year) {
        if (empty($year)) 
            $year = $this->year;
        return ((($year % 4) == 0) && (($year % 100) != 0) || (($year % 400) == 0));
    }
    
    /**
     *          日期格式化 [V1.2.5] class Date[公共方法]
     * @param string $format 日期格式
     * @return string 按照$format格式排列好的日期
     * *
     * @用法 通过$format参数指定需要显示的格式
     */
    public function format($format = "%Y-%m-%d %H:%M:%S") {            
        return strftime($format, $this->stamp); //默认返回 1970-01-01 11:30:45 类型的格式格式
    }

    /**
     *          显示当前的日期 [V1.2.5] class Date[公共方法]
     * @return string 
     */
    public function showNowDate() {
        return date("Y-m-d", mktime(0, 0, 0, $this->month, $this->day, $this->year));
    }

    /**
     *          显示当前时间 [V1.2.5] class Date[公共方法]
     * @return string
     */
    public function showNowTime() {
        return date("H:i:s", mktime($this->hour, $this->minute, $this->second, 0, 0, 0));
    }

    /**
     *          显示当前日期和时间 [V1.2.5] class Date[公共方法]
     * @return string
     */
    public function showDateTime() {
        return date("Y-m-d H:i:s", mktime($this->hour, $this->minute, $this->second, $this->month, $this->day, $this->year));
    } 

    /**
     *          获得指定年月的天数 [V1.2.5] class Date[公共方法]
     * @param int $year 年 可选 若不指定 为当前年
     * @param int $month 月 可选 若不指定 为当前月
     * @return int 天数
     */
    public function getDays($year = '', $month = '') {
        $year = $year == '' ? $this->year : $year;
        $month = $month == '' ? $this->month : $month;
        return date("t", mktime(0, 0, 0, $month, 1, $year));
    }

    /**
     *          获得指定年月第一天是星期几 [V1.2.5] class Date[公共方法]
     * @param int $year 年 可选 若不指定 为当前年
     * @param int $month 月 可选 若不指定 认为当前月
     * @param bool $flag 切换显示方式 true : 数字 false : 汉字
     * @return string
     */
    public function getFirstDay($year = '', $month = '', $flag = false) {
        $year = $year == '' ? $this->year : $year;
        $month = $month == '' ? $this->month : $month;
        if (!$flag)
            return "星期" . $this->Week[date("w", mktime(0, 0, 0, $month, 1, $year))]; //数字表示，返回值为0-6（星期日——星期六）
        else
            return date("w", mktime(0, 0, 0, $month, 1, $year)); 
    }

    /**
     *          获得指定年月的日期字符串 [V1.2.5] class Date[公共方法]
     * @param int $year 年 可选 若不指定 为当前年
     * @param int $month 月 可选 若不指定 为当前月
     * @return array
     */
    public function getDateStr($year = '', $month = '') {
        $year = $year == '' ? $this->year : $year;
        $month = $month == '' ? $this->month : $month;
        $days = $this->getDays($year, $month); //当前指定年月的天数
        for ($i = 1; $i <= $days; $i++) {
            if ($i < 10) {
                $arr[$i] = $year . "-" . $month . "-0" . $i; //设置输出格式，当日期小于10时，在数字前面加0
            } else {
                $arr[$i] = $year . "-" . $month . "-" . $i;
            }
        }
        return $arr;
    }
    
}

//* End of the file date.class.php  
//* File path : ./sys/lib