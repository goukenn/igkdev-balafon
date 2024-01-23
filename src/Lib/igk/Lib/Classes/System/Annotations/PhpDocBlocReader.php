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
    public function readDoc(string $docblock, array $uses)
    {
        $this->m_docblock = $docblock;
        //+ | bind uses
        AnnotationDocBlockReader::Uses($uses);
        $tp = AnnotationDocBlockReader::ParsePhpDocComment($docblock);
        //+ | unbind uses
        AnnotationDocBlockReader::Uses(null);
        return $tp;
    }
    private function getDocBlock(){
        return $this->m_docblock;
    }
}
