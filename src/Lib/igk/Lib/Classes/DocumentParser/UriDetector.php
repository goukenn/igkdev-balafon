<?php
// @author: C.A.D. BONDJE DOUE
// @file: UriDetector.php
// @date: 20221122 19:53:46
namespace IGK\DocumentParser;


///<summary></summary>
/**
* 
* @package IGK\DocumentParser
*/
class UriDetector{
    private $m_regex;
    public function __construct()
    {
        $this->_initRegex();
    }
    protected function _initRegex(){
        $domain = '(?P<domain>\w+(\.[\w0-9]+)+)';
        $path = '(\/(?P<path>[^\#\?\'\"\),\s]+)?)?';//'(?P<path>[^#\s\\\'\"]+)';
        $query = '(\?(?P<query>[^\#\?\'\"\,\s)]+)?)?';
        $hash = '(\?(?P<hash>[^\#\?\'\"\),\s]+)?)?';
        $this->m_regex = '#(?P<uri>((?P<scheme>http(s)?):)?\/\/)'.$domain.$path.$query.$hash.'?#im';
    }
    public function match($src){      
        if ($g = preg_match_all($this->m_regex, $src, $out)){
            $tab = [];
            for($i = 0 ; $i< $g; $i++ ){
                $match = new UriDectectorMatch;
                foreach($match as $k=>$v){
                    if ($k =='uri'){
                        continue;
                    }
                    $r = igk_getv($out, $k );
                    $match->$k = $r ? igk_getv($r, $i) : null;
                }
                $match->uri = $out[0][$i];
                $tab[] = $match;
            }            
            return $tab;
        }        
        return false;
    }

    public function cssUrl($src){
        $tab =  null;
        if ($g = preg_match_all('/url\s*\((?<path>[^\)\#\?\,\ ]+)\)/', $src, $out)){
            for($i = 0 ; $i< $g; $i++ ){
                $match = new UriDectectorMatch;
                foreach($match as $k=>$v){
                    if ($k =='uri'){
                        continue;
                    }
                    $r = igk_getv($out, $k );
                    $match->$k = $r ? igk_getv($r, $i) : null;
                }
                $match->uri = $out[0][$i];
                $tab[] = $match;
            }       
        }
        return $tab;
    }
}
