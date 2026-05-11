<?php
// @author: C.A.D. BONDJE DOUE
// @file: ProjectHtmlField.php
// @date: 20260506 09:29:52
namespace IGK\System\Html;


/**
* 
* @package IGK\System\Html
* @author C.A.D. BONDJE DOUE
*/
/**
* auto generate doc.
* @package IGK\System\Html
*/
class ProtectHtmlField{
    /**
    * Property: engines.
    * @var mixed
    */
    private $engines;
    /**
    * Property: options.
    * @var mixed
    */
    private $options;
    /**
    * auto generate doc.
    */
    public function __construct(){
        $this->_initOptions();
        $this->engines=array();
        $this->_initengines();
    }
    /**
    * auto generate doc.
    * @param mixed $v
    */
    private function __output($v){
        return $v;
    }
    /**
    * auto generate doc.
    */
    private function _initengines(){
        $tab=& $this->engines;
        array_unshift($tab, (object)array(
            "name"=>"uncollapsestring",
            "mode"=>'*',
            "pattern"=>"/(\"|')/i",
            "callback"=>function(& $t, $start, & $offset, $m){
                    $lis=$start;
                    $ch=$t[$start];
                    $s="";
                    $multilinestart=($ch == "'");
                    $ln=& $m->options->lineNumber;
                    $tln=$m->options->totalLines;
                    $before=substr($t, 0, $start);
                    $x=substr($t, $start + 1);
                    $start=0;
                    $escaped=0;
                    while((($pos=strpos($x, $ch, $start)) === false) && ($ln < $tln) || ($escaped=(($pos > 0) && $x[$pos-1] == '\\'))){
                        if($escaped){
                            if($pos > 1){
                                if($x[$pos-2] == "\\"){
                                    break;
                                }
                            }
                            $start=$pos + 1;
                            $escaped=0;
                            continue;
                        }
                        $s .= substr($x, $start).$m->options->LF;
                        $x=$m->options->source[$ln];
                        $ln++;
                        $start=0;
                        $escaped=0;
                    }
                    if($pos !== false){
                        $t=substr($x, $pos + 1);
                        $offset=0;
                        $s .= substr($x, 0, $pos);
                        $s=$before.$ch.$s.$ch;
                        $offset=strlen($s);
                        $t=$s.$t;
                    }
                    else{ 
                        $offset = strlen($t);
                        return $t;
                    }
                    return $t;
                }
        ));
        array_unshift($tab, (object)array(
            "name"=>"scriptTagRemove",
            "mode"=>"*",
            "pattern"=>"/\<(\/)?(script|embed|audio|object|style|img|frame|iframe|link)/i",
            "callback"=>function(& $t, $start, & $offset, $m){
                    $r=substr($t, 0, $start);
                    if(!empty($r)){
                        igk_treat_append($m->options, $r, 0);
                    }
                    $t=substr($t, $start + strlen($m->data[0][0]));
                    $offset=0;
                    $m->options->mode=1;
                    return $t;
                }
        ));
        array_unshift($tab, (object)array(
            "name"=>"scriptTagRemove",
            "mode"=>1,
            "pattern"=>"/\>/i",
            "callback"=>function(& $t, $start, & $offset, $m){
                    $r=substr($t, 0, $start);
                    $t=substr($t, $start + strlen($m->data[0][0]));
                    $offset=0;
                    $m->options->mode=0;
                    return $t;
                }
        ));
    }
    /**
    * auto generate doc.
    */
    private function _initOptions(){
        $this->options=(object)array(
            "out"=>"",
            "lineNumber"=>0,
            "IgnoreEmptyLine"=>0,
            "output"=>"",
            "data"=>"",
            "mode"=>0,
            "offset"=>0,
            "endMarkerFlag"=>0,
            "DataLFFlag"=>0,
            "toread"=>null,
            "DataLF"=>"\n"
        );
    }
    /**
    * auto generate doc.
    * @param mixed $v
    */
    public function protect($v){
        $this->_initOptions();
        $options=& $this->options;
        $v=igk_treat_source($v, function(){
            return call_user_func_array(array($this, '__output'), func_get_args());
        }
        , $this->engines, $options);
        return $v;
    }
}