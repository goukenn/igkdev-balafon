<?php
// @file: igk_treat.php
// @author: C.A.D. BONDJE DOUE
// @copyright: igkdev © 2019
// @license: Microsoft MIT License. For more informartion read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com

use IGK\System\Html\ProjectHtmlField as IGKProtectHtmlField;

defined("IGK_FRAMEWORK") || die("REQUIRE FRAMEWORK - No direct access allowed");
/**
* protect request information
* @param mixed & $tab
* @return mixed
*/
function igk_protect_request(& $tab){
    static $protecResolver=null;
    if($protecResolver === null){
        $protecResolver=new IGKProtectHtmlField();
    }
    $q=array();
    foreach($tab as $k=>$v){
        if(!is_numeric($v)){
            $v=$protecResolver->protect($v);
        }
        $tab[$k]=$v;
    }
}
/**
* represent igk_treat_source function
* @param string|array<string> $source string to treat
* @param ?closure $callback callback to call when done
* @param mixed $tab tab information for algorightm
* @param mixed & $options
* @return mixed
*/
function igk_treat_source($source, $callback, $tab=null, & $options=null){
    if(is_string($source)){
        $source=explode("\n", $source);
    }
    if(!function_exists("igk_treat_append")){
        /**
        * Igk treat append.
        * @param mixed $options
        * @param mixed $t
        * @param mixed $indent
        * @return mixed
        */
function igk_treat_append($options, $t, $indent=0){
            if(isset($options->writeListener)){
                $fc=$options->writeListener;
                call_user_func_array($fc, func_get_args());
                return;
            }
            if($options->mode != 0)
                return;
            $options->output .= $t;
        }
    }
    if(!function_exists("igk_treat_create_options")){
        /**
        * Igk treat create options.
        * @return mixed
        */
function igk_treat_create_options(){
            $options=(object)array();
            return $options;
        }
    }
    if(!function_exists("igk_treat_source_expression")){
        /**
        * Igk treat source expression.
        * @param mixed $options
        * @return mixed
        */
function igk_treat_source_expression($options){
            $tab=array();
            return $tab;
        }
    }
    $options=$options ?? igk_treat_create_options();
    $tab=$tab ?? igk_treat_source_expression($options);
    $out=& $options->output;
    $offset=& $options->offset;
    $sline=& $options->lineNumber;
    $tline=igk_count($source);
    $options->totalLines=$tline;
    $options->source=$source;
    $options->{"@automatcher_flag"}=array();
    $flag=0;
    $autoreset_flag=& $options->{"@automatcher_flag"};
    while($sline < $tline){
        $t=$source[$sline];
        $sline++;
        if($options->IgnoreEmptyLine && (strlen(trim($t)) == 0)){
            continue;
        }
        if($flag){
            if($options->DataLFFlag && ($options->conditionDepth<=0)){
                $options->DataLFFlag=0;
                igk_treat_append($options, $options->LF, 0);
            }
            else{
                if(is_object($options->toread) && ($options->toread->mode == 0)){
                    $options->DataLFFlag=0;
                    igk_treat_append($options, " ", 0);
                }
            }
        }
        $flag=1;
        $tq=array(rtrim($t));
        $offset=0;
        $auto_reset_list=isset($options->autoResetList) ? $options->autoResetList: array("operatorFlag", "mustPasLineFlag");
        while($t=array_pop($tq)){
            $matches=null;
            $mlist=null;
            foreach($tab as  $v){
                if(((is_callable($gf=$v->mode) && $gf($options)) || ($v->mode === "*") || ($v->mode === $options->mode)) && preg_match($v->pattern, $t, $matches, PREG_OFFSET_CAPTURE, $offset)){
                    $start=$matches[0][1];
                    if(!$mlist || ($mlist->start > $start)){
                        if(!$mlist)
                            $mlist=(object)array();
                        $mlist->start=$start;
                        $mlist->matcher=$v;
                        $mlist->data=$matches;
                        $mlist->options=$options;
                    }
                }
            }
            if($mlist){
                foreach($auto_reset_list as $re){
                    if(isset($options->$re)){
                        if(isset($autoreset_flag[$re])){
                            $options->$re=0;
                            unset($autoreset_flag[$re]);
                        }
                        else
                            $autoreset_flag[$re]=1;
                    }
                }
                if($options->endMarkerFlag && isset($options->definitions->lastTreat)){
                    if(isset($autoreset_flag["endMarkerFlag"])){
                        $options->endMarkerFlag=0;
                        unset($autoreset_flag["endMarkerFlag"]);
                    }
                    else
                        $autoreset_flag["endMarkerFlag"]=1;
                }
                igk_debug_wln("matcher: ".$mlist->matcher->name);
                $fc=$mlist->matcher->callback;
                $t=$fc($t, $mlist->start, $offset, $mlist);
                if(!empty($t)){
                    array_push($tq, $t);
                    continue;
                }
            }
            break;
        }
        $s=trim($t);
        if((strlen($s) == 0) && $options->IgnoreEmptyLine){
            $flag=0;
        }
        else{
            igk_treat_append($options, ltrim($t), 0);
        }
    }
    unset($options->{"@automatcher_flag"});
    if($callback){
        return $callback($out, $options);
    }
    return $out;
}
 