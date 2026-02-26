<?php
// @author: C.A.D. BONDJE DOUE
// @file: PhoneBookConverterBase.php
// @date: 20250505 09:26:07
namespace IGK\Database\PhoneBooks;
/**
* 
* @package IGK\Database\PhoneBooks
* @author C.A.D. BONDJE DOUE
*/
abstract class PhoneBookConverterBase{

    /**
    * auto generate doc.
    * @param mixed $v
    */
    abstract function treat($v);
}