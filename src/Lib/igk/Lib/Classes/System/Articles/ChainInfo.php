<?php
// @author: C.A.D. BONDJE DOUE
// @file: ChainInfo.php
// @date: 20230403 21:02:17
namespace IGK\System\Articles;


///<summary></summary>
/**
* 
* @package IGK\System\Articles
*/
class ChainInfo{
    var $n;
    var $data;

    public function __construct($n, $data)
    {
        $this->n = $n;
        $this->data = $data;
    }
}