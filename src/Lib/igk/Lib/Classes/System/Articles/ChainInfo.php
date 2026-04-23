<?php
// @author: C.A.D. BONDJE DOUE
// @file: ChainInfo.php
// @date: 20230403 21:02:17
namespace IGK\System\Articles;

/**
* auto generate doc.
* @package IGK\System\Articles
*/
class ChainInfo{
    /**
    * Property: n.
    * @var mixed
    */
    var $n;
    /**
    * Property: data.
    * @var mixed
    */
    var $data;
    /**
    * .ctr
    * @param mixed $n
    * @param mixed $data
    */
    public function __construct($n, $data)
    {
        $this->n = $n;
        $this->data = $data;
    }
}