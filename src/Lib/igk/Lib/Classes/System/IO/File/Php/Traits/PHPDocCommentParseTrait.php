<?php
// @author: C.A.D. BONDJE DOUE
// @file: PHPDocCommentParseTrait.php
// @date: 20230731 10:21:35
namespace IGK\System\IO\File\Php\Traits;

use IGK\System\IO\StringBuilder;

/**
* 
* @package IGK\System\IO\File\Php\Traits
*/

/**
* auto generate doc.
* @package IGK\System\IO\File\Php\Traits
*/
trait PHPDocCommentParseTrait{
 /**
     * parse php doc comment
     * @param string $cm 
     * @param ?PhpDocBlocReader $reader
     * @param ?array $filter array of property to filter
     * @param null|callable $filterCallback
     * @param null|callable(string $name, mixed $definition, $parser):bool $handler handle extra properties
     * @return PHPDocCommentParser 
     */
    public static function ParsePhpDocComment(string $cm,  $reader=null, ?array $filter=null, $filterCallback=null, $handlerCallback=null){
        $c = trim(igk_str_rm_start($cm, "/**"));
        $c = rtrim(igk_str_rm_last($c, "*/"));
        $g = new self;
        $g->setPropertyFilterListener($filterCallback);
        $g->setPropertyHandlerListener($handlerCallback);
        $g->summary = '';
        /// TODO: Remove filter property 
        // $g->m_reader = $reader;
        // $g->m_filter = $filter;
        $summary = false;
        $content = "";
        $name = "";
        array_map(function($c)use($g, & $summary, & $content, & $name){
            $k =  ltrim(trim($c), " *");            
            $offset = 1;
            if (!$summary){
                if (strlen($k)>0){
                    if ($k[0] ==='@'){
                        $summary=true;
                        $name = $g->_readName($k, $offset);
                    }                    
                }
                if (!$summary){
                    $g->summary.= $k;
                    return;
                }       
                $content .= $g->_TreatContent(substr($k, $offset));
            } else {
                if (strlen($k)>0){
                    if ($k[0] ==='@'){
                        $g->$name($content);
                        $content = "";
                        $offset = 1;
                        $name = $g->_readName($k, $offset);
                        $s = trim(substr($k, $offset));                          
                        $content .= $g->_TreatContent($s); 
                        if ($name=='api'){
                            $g->api = true;
                        }
                    }else{
                        $content .= $k;
                    }
                }
            }
        }, explode("\n", $c));
        if (!empty($content)){
            $g->$name($content);
        }
        return $g;
    }

    /**
    * auto generate doc.
    * @return string
    */
    public function render(): string{
        $sb = new StringBuilder;
        $p = [];
        if ($sum = $this->summary){
            $p[] = $sum;
        }
        $ref = igk_sys_reflect_class(static::class);
        $props = get_object_vars($this);
        foreach($props as $k=>$v){
            if (is_null($v) || ($k=='summary') || !$ref->getProperty($k)->isPublic()) continue;
            if (!is_string($v)){
                $v = implode(' ', (array)$v);
            }
            $p[] = ' @'.$k.' '.trim($v);
        }
        if ($extra = $this->getExtraProperties()){
            foreach($extra as $k=>$v){
                $p[] = ' @'.$k.' '.trim($v);
            }
        }
        $sb->appendLine('/**');
        $sb->appendLine('* '.implode("\n*", $p));
        $sb->append('*/');
        return ''.$sb;
    }
}