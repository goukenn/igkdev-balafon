<?php
// @author: C.A.D. BONDJE DOUE
// @file: DocumentParserExportViewController.php
// @date: 20221209 08:24:28
namespace IGK\DocumentParser\Controllers;

use IGK\Controllers\BaseController;
use IGK\System\IO\Path;

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
        return Path::Combine($this->m_outdir, "/Views/");
    }
    public function getDataDir()
    {
        return Path::Combine($this->m_outdir, "/Data/");
    }
    public function getStylesDir()
    {        
        return Path::Combine($this->m_outdir, "/Styles/");
    }
    public function getDeclaredDir(): string
    {
        return $this->m_outdir;
    }
    public function getAssetsDir(){
        return Path::Combine($this->getDataDir(), "/assets");
    }
}