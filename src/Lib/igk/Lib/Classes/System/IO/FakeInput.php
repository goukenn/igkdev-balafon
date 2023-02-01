<?php
// @author: C.A.D. BONDJE DOUE
// @file: FakeInput.php
// @date: 20230107 13:26:11
namespace IGK\System\IO;


///<summary></summary>
/**
* use to fake php://input reading
* @package IGK\System\IO
*/
abstract class FakeInput{
    public abstract function getRaw();
}