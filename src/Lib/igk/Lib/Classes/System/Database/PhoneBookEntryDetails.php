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
    var $firstname;
    var $lastname;
    var $title;
    var $gsm;
    var $tel;
    var $picture;
    var $site;
    var $email;
    var $address;
    var $photo;
    var $vat;
    var $rrn;
    var $company;
    var $website;
    var $birthdate;
    var $creditcard;
    var $bankaccount;
    var $twitter;
    var $instagram;
    var $youtube;
    var $soundclound;
    var $tiktok;
    var $snapchat;
    var $twitch;
    var $discord;
    var $alias;
    var $asbl;
    var $vcard;
    var $notes;
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


    public static function GetPropertyName(string $name): string
    {
        return $name;
    }
    public function display(): string
    {
        return $this->title ?? implode(" ", array_filter([$this->firstname, $this->lastname]));
    }
}
