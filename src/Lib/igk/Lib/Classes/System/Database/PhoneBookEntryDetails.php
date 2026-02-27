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

/**
* auto generate doc.
* @package IGK\System\Database
*/
class PhoneBookEntryDetails implements IToJSon
{

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
    * Property: title.
    * @var mixed
    */
    var $title;

    /**
    * Property: gsm.
    * @var mixed
    */
    var $gsm;

    /**
    * Property: tel.
    * @var mixed
    */
    var $tel;

    /**
    * Property: picture.
    * @var mixed
    */
    var $picture;

    /**
    * Property: site.
    * @var mixed
    */
    var $site;

    /**
    * Property: email.
    * @var mixed
    */
    var $email;

    /**
    * Property: address.
    * @var mixed
    */
    var $address;

    /**
    * Property: photo.
    * @var mixed
    */
    var $photo;

    /**
    * Property: vat.
    * @var mixed
    */
    var $vat;

    /**
    * Property: rrn.
    * @var mixed
    */
    var $rrn;

    /**
    * Property: company.
    * @var mixed
    */
    var $company;

    /**
    * Property: website.
    * @var mixed
    */
    var $website;

    /**
    * Property: birthdate.
    * @var mixed
    */
    var $birthdate;

    /**
    * Property: creditcard.
    * @var mixed
    */
    var $creditcard;

    /**
    * Count: bankaccount.
    * @var mixed
    */
    var $bankaccount;

    /**
    * Property: twitter.
    * @var mixed
    */
    var $twitter;

    /**
    * Property: instagram.
    * @var mixed
    */
    var $instagram;

    /**
    * Property: youtube.
    * @var mixed
    */
    var $youtube;

    /**
    * Property: soundclound.
    * @var mixed
    */
    var $soundclound;

    /**
    * Property: tiktok.
    * @var mixed
    */
    var $tiktok;

    /**
    * Property: snapchat.
    * @var mixed
    */
    var $snapchat;

    /**
    * Property: twitch.
    * @var mixed
    */
    var $twitch;

    /**
    * Property: discord.
    * @var mixed
    */
    var $discord;

    /**
    * Property: alias.
    * @var mixed
    */
    var $alias;

    /**
    * Property: asbl.
    * @var mixed
    */
    var $asbl;

    /**
    * Property: vcard.
    * @var mixed
    */
    var $vcard;

    /**
    * Property: notes.
    * @var mixed
    */
    var $notes;

    /**
    * Property: organization.
    * @var mixed
    */
    var $organization;

    /**
    * auto generate doc.
    * @param int $flag
    * @return false|string
    */

    public function to_json($option = null, int $flag = 0)
    {
        $c = $option ?? JSonEncodeOption::IgnoreEmpty();
        return JSon::Encode((array)$this, $c, $flag); 
    }

    /**
    * Returns Property Name.
    * @param string $name
    * @return string
    */
    public static function GetPropertyName(string $name): string
    {
        return $name;
    }

    /**
    * Displays.
    * @return string
    */
    public function display(): string
    {
        return $this->title ?? implode(" ", array_filter([$this->firstname, $this->lastname]));
    }
}
