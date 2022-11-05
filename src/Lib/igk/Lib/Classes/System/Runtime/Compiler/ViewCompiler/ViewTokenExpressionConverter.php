<?php
// @author: C.A.D. BONDJE DOUE
// @file: ViewTokenExpressionConverter.php
// @date: 20221021 08:35:29
namespace IGK\System\Runtime\Compiler\ViewCompiler;


///<summary></summary>
/**
* 
* @package IGK\System\Runtime\Compiler\ViewCompiler
*/
class ViewTokenExpressionConverter{

    /**
     * leave comment that start with 
     * @var string
     */
    var $skipComment = " + |";

    /**
     * remove line comment 
     * @var true
     */
    var $stripComment = true;

    /**
     * tab stop
     * @var string
     */
    var $tabstop = "";

    public function convert(string $source): ?string{
        $v_ = new ViewTokenizeCompiler;
        $v_->converter = $this;
        return $v_->compileSource($source);
    }
}