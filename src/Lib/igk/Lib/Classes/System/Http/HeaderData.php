<?php
// @author: C.A.D. BONDJE DOUE
// @file: HeaderData.php
// @date: 20230128 14:03:46
namespace IGK\System\Http;


///<summary></summary>
/**
* 
* @package IGK\System\Http
*/
class HeaderData{
    private $m_heads;
    var $origin;
    public function __construct(array $data = [])
    {
        $cp = [];
        foreach($data as $k=>$v){
            $k = strtolower($k);
            $k = str_replace('-','_', $k);
            $cp[$k] = $v;
        }
        $this->m_heads = $cp;
        $this->origin = $this->__get('origin') ?? igk_io_baseuri();
    }
    public function __get($n){
        return igk_getv($this->m_heads, $n);
    }
}