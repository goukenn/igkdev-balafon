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

    /**
    * auto generate doc.
    * @var mixed
    */
    var $id;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $type;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $tel;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $firstname;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $lastname;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $email;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $gsm;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $phone;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $organisation;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $birthdate;

    /**
    * auto generate doc.
    */
    public function to_json(){
        return JSon::Encode($this, JSonEncodeOption::IgnoreEmpty(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}