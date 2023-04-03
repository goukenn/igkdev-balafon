<?php
// @author: C.A.D. BONDJE DOUE
// @file: CompilerTokenMergeSourceTrait.php
// @date: 20221024 00:26:03
namespace IGK\System\Runtime\Compiler\Traits;

use IGK\System\IO\StringBuilder;
use IGK\System\Runtime\Compiler\ReadTokenMergeOption;
use IGK\System\Runtime\Compiler\ReadTokenUtility;

///<summary></summary>
/**
* 
* @package IGK\System\Runtime\Compiler
*/
trait CompilerTokenMergeSourceTrait{
   /**
     * merge source code
     * @return null|string 
     */
    public function mergeSourceCode($header=false): ?string
    {
        /** 
         * var ReadTokenOptions $v_options 
         */

        $v_options = $this->m_read_options; 
        if (is_null($v_options)){
            return null;
            // igk_die("read options is null ", $this);
        }


        $mp = new ReadTokenMergeOption;
        $mp->mergeVariable = $this->mergeVariable;
        $mp->noComment = $this->noComment;
        $mp->namespace = $v_options->namespace;

        $sb = new StringBuilder;
        $sb->appendLine("<?php");

        if ($v_options->describeComments) {
            $sb->appendLine(implode("\n", $v_options->describeComments));
            $sb->appendLine();
        }

        if ($v_options->namespace) {
            $sb->appendLine("");
            $sb->appendLine("namespace " . $v_options->namespace . ";");
        }

        if ($uses = $v_options->uses) {
            ReadTokenUtility::GenerateUses($uses, $sb);            
        }

        if ($structs = $v_options->structs) {
            $sb->appendLine();
            $s =  ReadTokenUtility::GenerateStruct($structs, $header, $mp);
            $sb->appendLine($s);
        }

        if ($v = trim(ReadTokenUtility::GenerateVariables($v_options->variables, $this->mergeVariable)))
            $sb->appendLine($v);
        if (!empty($b = trim($v_options->buffer))) {
            $sb->appendLine($b);
        }
        return rtrim('' . $sb);
    }

}