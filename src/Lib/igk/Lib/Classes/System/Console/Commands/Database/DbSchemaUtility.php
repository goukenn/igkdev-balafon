<?php
// @author: C.A.D. BONDJE DOUE
// @file: DbSchemaUtility.php
// @date: 20240910 19:42:43
namespace IGK\System\Console\Commands\Database;

use Exception;
use IGK\System\Html\HtmlReader;
use IGK\System\Html\HtmlReaderDocument;
use IGKException;

///<summary></summary>
/**
* 
* @package IGK\System\Console\Commands\Database
* @author C.A.D. BONDJE DOUE
*/
class DbSchemaUtility{
    var $file;
    public function __construct($controller)
    {
        $this->file = $controller->getDataSchemaFile();    
    }
    /**
     * 
     * @return HtmlReaderDocument|null 
     * @throws IGKException 
     * @throws Exception 
     */
    public function load(){
        return HtmlReader::LoadFile($this->file);
    }
    public function store($node){
        $option = (object)["Indent"=>true];
        igk_io_w2file($this->file, $node->render($option));
    } 
}