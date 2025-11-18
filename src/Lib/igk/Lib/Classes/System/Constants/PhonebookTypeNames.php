<?php
// @author: C.A.D. BONDJE DOUE
// @file: PhonebookTypeNames.php
// @date: 20230205 06:53:24
namespace IGK\System\Constants;
use IGK\System\Traits\EnumeratesConstants;
/**
* phone books type system constants
* @package IGK\System\Constants
*/
abstract class PhonebookTypeNames{
    use EnumeratesConstants;
    const PHT_LOCALE = 'locale';
    const PHT_NAME = 'name';
    const PHT_LASTNAME = 'lastname';
    const PHT_FIRSTNAME = 'firstname';
    const PHT_SURNAME = 'surname';
    const PHT_COMPANY = "company";
    const PHT_ORGANISATION = "organisation";
    const PHT_PROFESSION = "profession";
    const PHT_PHONE = "phone";
    const PHT_TEL = "tel";
    const PHT_GSM = "gsm";
    const PHT_EMAIL = "email";
    const PHT_URL = "url";
    const PHT_WEBSITE = "website";
    const PHT_BIRTHDATE = "birthdate";
    const PHT_RELATEDNAME = "relatedname";
    const PHT_SOCIAL_PROFILE = "social profile";
    const PHT_INSTANT_MESSAGE = "instant message";
    const PHT_VAT = "vat";
    const PHT_RRN = "rrn";
    const PHT_NOTES = "notes";
    const PHT_VCARD = "vcard";
    const PHT_PHOTO = "photo"; 
    const PHT_BANKACCOUNT = "bankaccount";
    const PHT_CREDIT_CARD = "creditcard";
    const PHT_SOCIAL_FACEBOOK = "facebook";
    const PHT_SOCIAL_TWITTER = "twitter";
    const PHT_SOCIAL_INSTAGRAM = "instagram";
    const PHT_SOCIAL_YOUTUBE = "youtube";
    const PHT_SOCIAL_SOUNDCLOUND = "soundclound";
    const PHT_SOCIAL_TIKTOK = "tiktok";
    const PHT_SOCIAL_SNAPCHAT = "snapchat"; 
    const PHT_SOCIAL_LINKEDIN = "linkedin"; 
    const PHT_SOCIAL_TWITCH = "twitch"; 
    const PHT_SOCIAL_DISCORD = "discord"; 
    const PHT_SOCIAL_REDDIT = "reddit"; 
    const PHT_SOCIAL_PINTEREST = "pinterest"; 
    const PHT_TITLE = 'title'; 
    const PHT_ALIAS = 'alias'; 
    const PHT_ADR = 'address';
    const PHT_NICKNAME = 'nickname';
    const PHT_ASBL = 'asbl';
    /**
     * societe that this entry belong took
     */
    const PHT_SA = 'sa';
    /**
     * thumbnail picture 
     */
    const PHT_THUMBNAIL = 'thumbnail';
    /**
     * check for cardinality
     * @param string $s 
     * @return bool 
     */
    public static function IsSingle(string $n):bool{
        return in_array($n, explode('|', 'name|lastname|firstname|locale'));
    }
} 