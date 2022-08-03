<?php
// @author: C.A.D. BONDJE DOUE
// @filename: Pagination.php
// @date: 20220803 13:48:55
// @desc: 


namespace IGK\System\WinUI;

use Exception;
use IGK\Helper\UriHelper; 
use IGKException;

use function igk_resources_gets as __;

class Pagination{
    /**
     * selected pan
     */
    var $total; 
    /**
     * count data
     * @var mixed
     */
    var $count;
    /**
     * current page
     * @var mixed
     */
    var $page; 
    /**
     * shits selction 
     * @var int
     */
    var $shift;
    /**
     * pagination type
     * @var mixed
     */
    var $type; 

    /**
     * default css class 
     * @var string
     */
    var $className = "igk-pagination";

    var $pageQuery;

    /**
     * 
     * @param mixed $itemPerPage number per page
     * @param mixed $total total item 
     * @param string $p request page field
     * @return void 
     */
    public function __construct(int $itemPerPage, int $total, $p="p", $default_page=1, $shift=1)
    {
        if (!$default_page){
            $default_page = 1;
        }
        if ($shift<1){
            throw new Exception("shift must be greather or equal to one");
        }
        $page = igk_getr($p, $default_page);
        if ($page <= 0){
            throw new Exception("page no allowed");
        }
        $this->total = $total;
        $this->count = $itemPerPage;
        $this->shift = $shift;
        $this->pageQuery = $p;
        $this->page = $page;
    }
    /**
     * generate list 
     * @return mixed|object
     * @throws IGKException 
     */
    public function list($ajx=0, $request_uri = null){ 
        
        $total = floor($this->total/$this->count)+1;
        $n = igk_create_node("ul");
        $n["class"] = $this->className;
        $min = max($this->page - $this->shift, 1);
        $max = min($this->page + $this->shift + 1, $total);
       // igk_wln_e(compact( "max", "min"));
        $l = ($this->shift * 2) - ($max - $min);
        if ($l>0){
            $max = min($max+$l,  $total);
            $min = max($min-$l, 1);
        }
        // igk_wln_e("leve l ", compact("l", "max", "min"));
        $request_uri = $request_uri ?? igk_io_request_uri();
        $query = UriHelper::GetQueryTab($request_uri);
        $request_uri = explode("?", $request_uri)[0];
        
        if (empty($query)){
            $q = "?".$this->pageQuery."=";
        }else{
            unset($query[$this->pageQuery]);
            $q = "?";
            if(!empty($s = http_build_query($query))){
                $q = $s."&";
            }
            $q .= $this->pageQuery."=";
        }
        $query[$this->pageQuery] = 0;
        $u = $request_uri.$q;
        $this->_prefix($n , $u,   $total );
        for($i = $min; $i<= $max; $i++){
            $li = $n->li();
            $bu = $u.$i;
            $a = null;
            $a = $ajx ? $li->ajxa($bu) : $li->a($bu);
            $a->setContent($i);
            if ($i==$this->page){
                $li->setClass("+igk-active");
            }
            
        }
        $this->_postfix($n, $u,   $total );
        return $n;
    }
    private function _prefix($n, $u,   $total ){
        $link = "#";
        switch($this->type){
            default:        
            $li = $n->li();
            $li->a($u."1")->Content = __("First");
            // if ($this->page>1){
                $li = $n->li();
                $li->a( $u. ($this->page-1))->setClass([
                    "disable"=>$this->page<=1
                ])->Content = __("Prev");
            //}
            break;
        }
    }
    private function _postfix($n, $u, $total){
        $next = min($this->page+1, $total);
        switch($this->type){
            default:        
            $li = $n->li();
            $li->a($u.$next)->Content = __("Next");
            $li = $n->li();
            $li->a($u.$total)->Content = __("Last");
            break;
        }
    }
    public function getLimit(){     
        $p = $this->page - 1;   
        return implode(",", [($p * $this->count),  
            $this->count
            ]);
    }
}