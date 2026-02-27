<?php
// @author: C.A.D. BONDJE DOUE
// @file: DbSchemaUtility.php
// @date: 20240910 19:42:43
namespace IGK\System\Console\Commands\Database;
use Exception;
use IGK\System\Html\HtmlReader;
use IGK\System\Html\HtmlReaderDocument;
use IGKException;
/**
* 
* @package IGK\System\Console\Commands\Database
* @author C.A.D. BONDJE DOUE
*/

/**
* auto generate doc.
* @package IGK\System\Console\Commands\Database
*/
class DbSchemaUtility{

    /**
    * Property: file.
    * @var mixed
    */
    var $file;

    /**
    * .ctr
    * @param mixed $controller
    */
    public function __construct($controller)
    {
        $this->file = $controller->getDataSchemaFile();    
    }

    /**
    * auto generate doc.
    * @return HtmlReaderDocument|null
    */

    public function load(){
        return HtmlReader::LoadFile($this->file);
    }

    /**
    * Store.
    * @param mixed $node
    */
    public function store($node){
        $option = (object)["Indent"=>true];
        igk_io_w2file($this->file, $node->render($option));
    } 
}