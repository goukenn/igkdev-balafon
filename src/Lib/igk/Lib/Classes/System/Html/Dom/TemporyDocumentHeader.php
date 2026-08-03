<?php
// @author: C.A.D. BONDJE DOUE
// @file: TemporyDocumentHeader.php
// @date: 20241016 15:50:05
namespace IGK\System\Html\Dom;

use IGKResourceUriResolver;

/**
* auto generate doc.
* @package IGK\System\Html\Dom
* @author C.A.D. BONDJE DOUE
*/
class TemporyDocumentHeader{
    /**
    * Property: sc.
    * @var mixed
    */
    private $sc = [];
    /**
    * Adds Temp Script.
    * @param string $src
    * @param mixed $type
    */
    public function addTempScript(string $src, $type='module'){
        if (!isset($sc[$src])){
            $sc[$src] = 1;
            $c = igk_create_node('script');
            $c['type']= $type;
            $c['src']= $src;
            return $c;
        }
        return null;
    }
    /**
     * 
     * @param mixed $src 
     * @return void 
     */
    public function addTempStyle(string $file){
       $aside = & AsideScripting::getInstance()->aside;
       $file = IGKResourceUriResolver::getInstance()->resolve($file, ['autolink'=>false]);
        $c = igk_create_node('link');
        $c['type']= 'text/css';
        $c['src']= $file;
        $aside[$file] = $c;
        return $c;
    }
}