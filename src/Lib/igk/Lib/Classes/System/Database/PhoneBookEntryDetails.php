<?php
declare(strict_types=1);
// @author: C.A.D. BONDJE DOUE
// @file: PhoneBookEntyDetails.php
// @date: 20251219 08:09:01
namespace IGK\System\Database;

use IGK\Helper\JSon;
use IGK\Helper\JSonEncodeOption;
use IGK\System\IToJSon;

/**
 * 
 * @package IGK\System\Database
 * @author C.A.D. BONDJE DOUE
 */
class PhoneBookEntryDetails implements IToJSon
{

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
    var $title;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $gsm;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $tel;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $picture;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $site;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $email;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $address;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $photo;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $vat;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $rrn;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $company;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $website;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $birthdate;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $creditcard;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $bankaccount;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $twitter;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $instagram;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $youtube;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $soundclound;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $tiktok;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $snapchat;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $twitch;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $discord;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $alias;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $asbl;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $vcard;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $notes;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $organization;

    /**
     * 
     * @param mixed $option 
     * @param int $flag 
     * @return false|string 
     */

    public function to_json($option = null, int $flag = 0)
    {
        $c = $option ?? JSonEncodeOption::IgnoreEmpty();
        return JSon::Encode((array)$this, $c, $flag); 
    }

    /**
    * auto generate doc.
    * @param string $name
    * @return string
    */
    public static function GetPropertyName(string $name): string
    {
        return $name;
    }

    /**
    * auto generate doc.
    * @return string
    */
    public function display(): string
    {
        return $this->title ?? implode(" ", array_filter([$this->firstname, $this->lastname]));
    }
}
