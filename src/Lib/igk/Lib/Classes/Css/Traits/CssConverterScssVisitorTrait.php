<?php
// @author: C.A.D. BONDJE DOUE
// @file: CssConverterScssVisitorTrait.php
// @date: 20230125 17:55:13
namespace IGK\Css\Traits;


///<summary></summary>
/**
* 
* @package IGK\Css\Traits
*/
trait CssConverterScssVisitorTrait{
    protected function _visit_return(){

    }
    protected static function _ReadBlock($converter, & $tab, $options){
        $src = $converter->src;
        $offset = & $options->offset; 
        $v_1 = self::_ReadSelector($src, $offset, $converter->length, null);
        $body = '';
        $depth = 0;
        while($converter->_read($options)){
            $ch = $options->ch;
            if ($ch=='}'){
                $depth--;
                if ($depth==0){
                    break;
                }
            } else if ($ch=='{'){
                $depth++;
                if ($depth==1){
                    continue;
                }
            }
            $body.= $ch;
        }
        $tab[$v_1] = $body;      
    }
    
    protected function _visit_keyframes($options){
        $this->_ReadBlock($this, $this->keyframes, $options);          
    }
    protected function _visit_webkit_keyframes($options){
        $tab = [];
        $this->_ReadBlock($this, $tab, $options);  
        if ($tab)
        $this->keyframes['@-moz-webkit'][array_keys($tab)[0]] = array_values($tab)[0];        
    }
    protected function _visit_moz_keyframes($options){
        $tab = [];
        $this->_ReadBlock($this, $tab, $options);          
        if ($tab)
        $this->keyframes['@-moz-keyframes'][array_keys($tab)[0]] = array_values($tab)[0];
    }
    protected function _visit_function($options){
        
        $src = $this->src;
        $offset = & $options->offset;
        // + | get function name and parameter : scass support '$' as parement like php that's perfect.
        $v_1 = self::_ReadSelector($src, $offset, $this->length, null);
        $body = '';
        $depth = 0;
        while($this->_read($options)){
            $ch = $options->ch;
            if ($ch=='}'){
                $depth--;
                if ($depth==0){
                    break;
                }
            } else if ($ch=='{'){
                $depth++;
                if ($depth==1){
                    continue;
                }
            }
            $body.= $ch;
        }
        $this->functions[$v_1] = $body;        
    }
    protected function _visit_debug($options){
        igk_die(__METHOD__.' not implement ');
    }
}