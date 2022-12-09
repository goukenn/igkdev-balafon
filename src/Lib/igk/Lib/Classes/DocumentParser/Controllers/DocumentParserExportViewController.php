<?php
// @author: C.A.D. BONDJE DOUE
// @file: DocumentParserExportViewController.php
// @date: 20221209 08:24:28
namespace IGK\DocumentParser\Controllers;

use IGK\Controllers\BaseController;

///<summary></summary>
/**
* 
* @package IGK\DocumentParser\Controllers
*/
class DocumentParserExportViewController extends BaseController{
    /**
     * the output directory 
     * @var string
     */
    private $m_outdir;
    public static function IsRegistrable(){
        return false;
    }
    public function __construct(string $outputdir)
    {        
        $this->m_outdir = $outputdir;
    }
    public function getViewDir()
    {
        return $this->m_outdir;
    }
    public function getDataDir()
    {
        return $this->m_outdir;
    }
    public function getStylesDir()
    {        
        return $this->m_outdir;
    }
}