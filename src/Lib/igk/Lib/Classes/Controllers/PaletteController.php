<?php
// @file: IGKPalettesController.php
// @author: C.A.D. BONDJE DOUE
// @description:
// @copyright: igkdev © 2020
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\Controllers;
use IGK\Helper\IO;
/**
*
*represent a Palette controller Model
*
*/
final class PaletteController extends NonVisibleControllerBase {
    /**
    * Property: palettes.
    * @var mixed
    */
    private $m_palettes;
    /**
    * auto generate doc.
    */
    public function __construct(){
        parent::__construct();
        $this->m_palettes=array();
    }
    /**
    * auto generate doc.
    */
    public function getName(): string{
        return IGK_PALETTE_CTRL;
    }
    /**
    * auto generate doc.
    */
    public function getPaletteDir(){
        return $this->getConfigs()->Location;
    }
    /**
    * auto generate doc.
    */
    public function getPalettes(){
        if ($this->m_palettes === null){
            $this->loadPalette();
        }
        return $this->m_palettes;
    }
    /**
    * auto generate doc.
    */
    protected function initComplete($context=null){
       parent::initComplete(); 
    }
    /**
    * auto generate doc.
    * @param mixed $fname
    */
    public function loadFile($fname){
        if(!igk_io_file_exists($fname))
            return;
        $v_name=igk_io_basenamewithoutext($fname);
        $v_t=null;
        if(isset($this->m_palettes[$v_name])){
            $v_t=$this->m_palettes[$v_name];
        }
        else
            $v_t=array();
        $e=igk_create_node("pal");
        try {
            $e->Load(IO::ReadAllText($fname));
            $e=igk_getv($e->getElementsByTagName("palette"), 0);
            if($e){
                foreach($e->Childs as $k){
                    if(strtolower($k->TagName) == "item"){
                        $v=$k["color"];
                        $n=$k["name"];
                        $v_t[$n]=$v;
                    }
                }
                $this->m_palettes[$v_name]=$v_t;
            }
        }
        catch(\Exception $ex){}
    }
    /**
    * auto generate doc.
    */
    public function loadPalette(){
        $dir=$this->getPaletteDir();
        if($dir && is_dir($dir)){
            $v_tfiles=IO::GetFiles($dir, "/\.gkpal$/i", false);
            foreach($v_tfiles as $f){
                igk_wln_e("load file : ".$f);
                $this->loadFile($f);
            }
        }
    }
    /**
    * auto generate doc.
    * @param mixed $id
    */
    public function RemovePalette($id){
        $s=$this->getPaletteDir()."/".$id.".gkpal";
        if(igk_io_file_exists($s)){
            unlink($s);
            $this->m_palettes=array();
            $this->loadPalette();
        }
    }
}