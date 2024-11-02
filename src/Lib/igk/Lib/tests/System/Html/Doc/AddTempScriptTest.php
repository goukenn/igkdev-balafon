<?php
// @author: C.A.D. BONDJE DOUE
// @file: AddTempScriptTest.php
// @date: 20241017 16:24:28
namespace IGK\Tests\System\Html\Doc;

use IGK\Helper\IO;
use IGK\Tests\BaseTestCase;
use IGKHtmlDoc;

///<summary></summary>
/**
* 
* @package IGK\Tests\System\Html\Doc
* @author C.A.D. BONDJE DOUE
*/
class AddTempScriptTest extends BaseTestCase{
    public function test_htmldoc_addtemps_script(){
        $doc = IGKHtmlDoc::CreateDocument(-1);
        $p = null;
        $file = IO::CreateTempFile(IO::CreateTempDir('uri-test'), '.js');
       
        $cn = $doc->addTempScript($file."?p=new_app");
        $n = $cn->render(); 
        IO::IsAbsolutePath($file);
        $this->assertEquals('<script language="javascript" src="./?p=new_app" type="text/javascript"></script>', $n);
    
        unlink($file);
    }
}