<?php
// @author: C.A.D. BONDJE DOUE
// @file: ReadTokenMergeOption.php
// @date: 20221024 10:31:39
namespace IGK\System\Runtime\Compiler;
/**
* 
* @package IGK\System\Runtime\Compiler
*/
class ReadTokenMergeOption implements IReadTokenMergeOption{

    /**
    * auto generate doc.
    * @var mixed
    */
    var $noComment;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $depth;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $mergeVariable;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $namespace;        
}