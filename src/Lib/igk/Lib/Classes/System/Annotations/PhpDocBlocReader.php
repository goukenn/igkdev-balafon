<?php
// @author: C.A.D. BONDJE DOUE
// @file: PhpDocBlocReader.php
// @date: 20230731 12:52:03
namespace IGK\System\Annotations;


///<summary></summary>
/**
* dock block reader in use.
* @package IGK\System\Annotations
*/
class PhpDocBlocReader
{
    private $m_docblock;
    /**
     * 
     * @param string $docblock 
     * @param array $uses {<string fulltype_name, string alias_name>|string fullname}
     * @param ?array $filter list of class annotation to filter 
     * @return IGK\System\IO\File\Php\Traits\PHPDocCommentParser 
     */
    public function readDoc(string $docblock, array $uses, ?array $filter=null)
    {
        $this->m_docblock = $docblock;
        //+ | bind uses
        AnnotationDocBlockReader::Uses($uses);
        $tp = AnnotationDocBlockReader::ParsePhpDocComment($docblock, $this, $filter);
        //+ | unbind uses
        AnnotationDocBlockReader::Uses(null);
        return $tp;
    }
    /**
     * retrieve source document block
     * @return mixed 
     */
    function getDocBlock(){
        return $this->m_docblock;
    }
}
