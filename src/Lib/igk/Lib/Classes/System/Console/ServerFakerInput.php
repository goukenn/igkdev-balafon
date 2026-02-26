<?php
// @author: C.A.D. BONDJE DOUE
// @file: ServerFakerInput.php
// @date: 20230107 13:32:22
namespace IGK\System\Console;
use IGK\System\IO\FakeInput;
/**
* 
* @package IGK\System\Console
*/
class ServerFakerInput extends FakeInput{

    /**
    * auto generate doc.
    * @var mixed
    */
    private $jsondata;

    /**
    * .ctr
    * @param null|string $jsondata
    */
    public function __construct(?string $jsondata=null)
    {
        $this->jsondata = $jsondata;
    }

    /**
    * auto generate doc.
    */
    public function getRaw() { 
        if ($r = $this->jsondata){
            $this->jsondata = null;
        }
        return $r;
    }
}