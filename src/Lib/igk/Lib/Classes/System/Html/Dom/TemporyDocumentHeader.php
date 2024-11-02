<?php
// @author: C.A.D. BONDJE DOUE
// @file: TemporyDocumentHeader.php
// @date: 20241016 15:50:05
namespace IGK\System\Html\Dom;


///<summary></summary>
/**
* 
* @package IGK\System\Html\Dom
* @author C.A.D. BONDJE DOUE
*/
class TemporyDocumentHeader{
    private $sc = [];
    public function addTempScript(string $src, $type='module'){
        if (!isset($sc[$src])){
            $sc[$src] = 1;
            $c = igk_create_node('script');
            $c['type']= $type;
            return $c;
        }
        return null;
    }
}