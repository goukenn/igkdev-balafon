<?php
// @author: C.A.D. BONDJE DOUE
// @file: PhoneConverter.php
// @date: 20250505 09:25:00
namespace IGK\Database\PhoneBooks;

/**
* auto generate doc.
* @package IGK\Database\PhoneBooks
* @author C.A.D. BONDJE DOUE
*/
class PhoneConverter extends PhoneBookConverterBase{
    /**
    * Treat.
    * @param mixed $v
    */
    public function treat($v) { 
        $v = str_replace(' ', '', trim($v));
        $v = preg_replace('/^00/', '+', $v);
        $v = preg_replace('/^04/', '+324', $v);
        $v = preg_replace('/^0([1-9])/', '+32\\1', $v);
        return $v;
    }
}