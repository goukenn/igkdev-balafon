<?php
// @file: IGKDateTime.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
class IGKDateTime extends IGKObject{
    private $m_day, $m_hour, $m_min, $m_month, $m_sec, $m_year;
    private function __construct(){    }
    public function __toString(){
        return "IGKDateTime:[".$this->day."-".$this->month."-".$this->year."]";
    }
    public static function compareDate($date1, $date2){
        if(!$date1 || !$date2)
            return -2;
        $s1=$date1->getDate("Ymd");
        $s2=$date2->getDate("Ymd");
        return strcmp($s1, $s2);
    }
    public static function CreateFrom($format, $value){
        $tab=(object)date_parse_from_format($format, $value);
        if($tab->error_count == 0){
            $d=new IGKDateTime();
            $d->m_day=$tab->day;
            $d->m_month=$tab->month;
            $d->m_year=$tab->year;
            $d->m_min=$tab->minute;
            $d->m_sec=$tab->second;
            $d->m_hour=$tab->hour;
            return $d;
        }
        else{
            if(igk_environment()->isDebug())
                igk_show_prev($tab);
        }
        return null;
    }
    public static function GetAge($birthdate){
        return (new DateTime())->diff(new DateTime($birthdate))->y;
    }
    public function getDate($format){
        $s=$format;
        $s=str_replace("Y", $this->year, $s);
        $s=str_replace("m", $this->month, $s);
        $s=str_replace("d", $this->day, $s);
        $s=str_replace("H", $this->hour, $s);
        $s=str_replace("i", $this->min, $s);
        $s=str_replace("s", $this->sec, $s);
        return $s;
    }
    public function getday(){
        return $this->m_day;
    }
    public function gethour(){
        return $this->m_hour;
    }
    public function getmin(){
        return $this->m_min;
    }
    public function getmonth(){
        return $this->m_month;
    }
    public function getsec(){
        return $this->m_sec;
    }
    public function getyear(){
        return $this->m_year;
    }
    public static function isDateEqual($date1, $date2){
        return self::compareDate($date1, $date2) == 0;
    }
    public static function isDateMonthEqual($date1, $date2){
        return (self::IsDateYearEqual($date1, $date2) === true) && ($date1->month == $date2->month);
    }
    public static function isDateYearEqual($date1, $date2){
        if(!$date1 || !$date2)
            return -2;
        return $date1->year == $date2->year;
    }
}