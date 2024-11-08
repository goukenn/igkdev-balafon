<?php

// @author: C.A.D. BONDJE DOUE
// @filename: FormHelper.php
// @date: 20220531 11:45:52
// @desc: 
namespace IGK\System\Html\Forms;

use Closure;
use IGKException;

/**
 * helper to get basic hml form
 * @package 
 */
class FormHelper{

    public static function ConvertToInputDateTimelocal(string $datevalue) {
        $i =  strtotime($datevalue);
        return date('Y-m-d', $i).'T'.date('H:i', $i);
    }

    /**
     * get submit callable
     * @return Closure 
     */
    public static function submit(){
        return Closure::fromCallable("igk_html_submit");
    }
    public static function FormActionHost($form, $callback){
        return function($a)use($callback, $form){
            return $callback($a, $form);
        };
    }
    public static function __callStatic($name, $args){
        return null;
    }
    /**
     * 
     * @param mixed $data 
     * @param string $key key used for display
     * @param string $name key used for display 
     * @param null|array $options 
     * @return void 
     * @throws IGKException 
     */
    public static function SelectOptions($data, string $key, string  $name,?array $options=null){
        if ($options && !key_exists('no_sort_text', $options))
            $options['no_sort_text'] = 1;
 
       $data = FormUtils::SelectData($data, $key, $name, $options); 
    
        return implode("", array_map(self::_InitOption($options), $data));
    }
    /**
     * 
     * @param mixed $options 
     * @return Closure 
     */
    public static function  _InitOption($options){
        //
        
        return function ($d)use($options){
            $k_data = "";
            $bas = isset($options["selected"]) ? $options["selected"] : null;
            if (isset($options["data"]) && is_string($m_data = $options["data"])) {
                $k_data = " data=\"" . $m_data . "\"";
            }
            if ($bas && ($bas == $d['i'])){
                
                // igk_wln_e("selected ".$bas, $d['i']);
                $k_data.= ' selected="selected"';
            }
            return '<option value="'.$d['i'].'"'.$k_data.'>'.$d['t'].'</option>';
        };
    }

    /**
     * helper : create a month selection
     * @param null|string $year 
     * @return array 
     */
    public static function YearMounthSelection(?string $year = null){
        $d = [];
        if (is_null($year)){
            $year = date('Y');
        }
        foreach(range(1, 12)as $k){
            $k = str_pad($k, 1,"0", STR_PAD_LEFT);
            $pb= date_create_from_format("Ym", $year.$k);
            $d[] = ['t'=>$pb->format("F"), 'i'=>$k];
        }
        return $d;
    }
}
