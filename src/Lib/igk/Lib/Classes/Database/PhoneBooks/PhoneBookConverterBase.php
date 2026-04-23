<?php
// @author: C.A.D. BONDJE DOUE
// @file: PhoneBookConverterBase.php
// @date: 20250505 09:26:07
namespace IGK\Database\PhoneBooks;

/**
* auto generate doc.
* @package IGK\Database\PhoneBooks
* @author C.A.D. BONDJE DOUE
*/
abstract class PhoneBookConverterBase{
    /**
    * Treat.
    * @param mixed $v
    */
    abstract function treat($v);
}