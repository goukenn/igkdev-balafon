<?php
// @author: C.A.D. BONDJE DOUE
// @file: Entry.php
// @date: 20250520 18:26:49
namespace IGK\PhoneBook;
use IGK\Helper\JSon;
use IGK\Helper\JSonEncodeOption;

/**
* auto generate doc.
* @package IGK\PhoneBook
* @author C.A.D. BONDJE DOUE
*/
class PhoneEntry{
    /**
    * Identifier: id.
    * @var mixed
    */
    var $id;
    /**
    * Type of type.
    * @var mixed
    */
    var $type;
    /**
    * Property: tel.
    * @var mixed
    */
    var $tel;
    /**
    * Name of firstname.
    * @var mixed
    */
    var $firstname;
    /**
    * Name of lastname.
    * @var mixed
    */
    var $lastname;
    /**
    * Property: email.
    * @var mixed
    */
    var $email;
    /**
    * Property: gsm.
    * @var mixed
    */
    var $gsm;
    /**
    * Property: phone.
    * @var mixed
    */
    var $phone;
    /**
    * Property: organisation.
    * @var mixed
    */
    var $organisation;
    /**
    * Property: birthdate.
    * @var mixed
    */
    var $birthdate;
    /**
    * To json.
    */
    public function to_json(){
        return JSon::Encode($this, JSonEncodeOption::IgnoreEmpty(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}