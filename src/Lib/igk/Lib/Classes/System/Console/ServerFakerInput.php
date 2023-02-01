<?php
// @author: C.A.D. BONDJE DOUE
// @file: ServerFakerInput.php
// @date: 20230107 13:32:22
namespace IGK\System\Console;

use IGK\System\IO\FakeInput;

///<summary></summary>
/**
* 
* @package IGK\System\Console
*/
class ServerFakerInput extends FakeInput{
    private $jsondata;
    public function __construct(?string $jsondata=null)
    {
        $this->jsondata = $jsondata;
    }
    public function getRaw() { 
        if ($r = $this->jsondata){
            $this->jsondata = null;
        }
        return $r;
    }

}