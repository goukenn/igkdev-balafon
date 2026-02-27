<?php
// @author: C.A.D. BONDJE DOUE
// @file: CompilerTokenStateBufferTrait.php
// @date: 20221021 09:50:56
namespace IGK\System\Runtime\Compiler\Traits;

/**
* auto generate doc.
* @package IGK\System\Runtime\Compiler\Traits
*/
trait CompilerTokenStateBufferTrait{

    /**
    * Pushes Buffer.
    * @param mixed $options
    * @param mixed & $buffer
    * @param string $id
    */
    protected function pushBuffer($options, &$buffer, string $id='')
    {
        $bckBuffer =  &$options->buffer;
        // change buffer
        array_push($options->buffers,['id'=>$id, 'buffer'=>& $bckBuffer]);
        $options->buffer = &$buffer;
    }

    /**
    * Pops Buffer.
    * @param mixed $options
    * @param string $id
    */
    protected function popBuffer($options, string $id='')
    {
        if (count($options->buffers) != 0) {        
            $buffer = array_pop($options->buffers);
            if (!is_null($buffer))
            {
                // if ($id != $buffer['id']){
                    // error_log(" -- resolv id not match -- ".$id.' vs '.$buffer['id']);
                // }
                $options->buffer = & $buffer['buffer'];
            }
            else {
                unset($options->buffer);
                $options->buffer = "";
            }
        } 
    }
}