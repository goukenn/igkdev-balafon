<?php
// @author: C.A.D. BONDJE DOUE
// @file: Entry.php
// @date: 20250520 18:26:49
namespace IGK\PhoneBook;
use IGK\Helper\JSon;
use IGK\Helper\JSonEncodeOption;
/**
* 
* @package IGK\PhoneBook
* @author C.A.D. BONDJE DOUE
*/
class PhoneEntry{
    var $id;
    var $type;
    var $tel;
    var $firstname;
    var $lastname;
    var $email;
    var $gsm;
    var $phone;
    var $organisation;
    public function to_json(){
        return JSon::Encode($this, JSonEncodeOption::IgnoreEmpty(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}