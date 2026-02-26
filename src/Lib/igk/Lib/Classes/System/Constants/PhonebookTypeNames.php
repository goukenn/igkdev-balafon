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

    /**
    * auto generate doc.
    * @var mixed
    */
    const PHT_LOCALE = 'locale';

    /**
    * auto generate doc.
    * @var mixed
    */
    const PHT_NAME = 'name';

    /**
    * auto generate doc.
    * @var mixed
    */
    const PHT_LASTNAME = 'lastname';

    /**
    * auto generate doc.
    * @var mixed
    */
    const PHT_FIRSTNAME = 'firstname';

    /**
    * auto generate doc.
    * @var mixed
    */
    const PHT_SURNAME = 'surname';

    /**
    * auto generate doc.
    * @var mixed
    */
    const PHT_COMPANY = "company";

    /**
    * auto generate doc.
    * @var mixed
    */
    const PHT_ORGANIZATION = "organization";

    /**
    * auto generate doc.
    * @var mixed
    */
    const PHT_PROFESSION = "profession";

    /**
    * auto generate doc.
    * @var mixed
    */
    const PHT_PHONE = "phone";

    /**
    * auto generate doc.
    * @var mixed
    */
    const PHT_TEL = "tel";

    /**
    * auto generate doc.
    * @var mixed
    */
    const PHT_GSM = "gsm";

    /**
    * auto generate doc.
    * @var mixed
    */
    const PHT_EMAIL = "email";

    /**
    * auto generate doc.
    * @var mixed
    */
    const PHT_URL = "url";

    /**
    * auto generate doc.
    * @var mixed
    */
    const PHT_WEBSITE = "website";

    /**
    * auto generate doc.
    * @var mixed
    */
    const PHT_BIRTHDATE = "birthdate";

    /**
    * auto generate doc.
    * @var mixed
    */
    const PHT_RELATEDNAME = "relatedname";

    /**
    * auto generate doc.
    * @var mixed
    */
    const PHT_SOCIAL_PROFILE = "social profile";

    /**
    * auto generate doc.
    * @var mixed
    */
    const PHT_INSTANT_MESSAGE = "instant message";

    /**
    * auto generate doc.
    * @var mixed
    */
    const PHT_VAT = "vat";

    /**
    * auto generate doc.
    * @var mixed
    */
    const PHT_RRN = "rrn";

    /**
    * auto generate doc.
    * @var mixed
    */
    const PHT_NOTES = "notes";

    /**
    * auto generate doc.
    * @var mixed
    */
    const PHT_VCARD = "vcard";

    /**
    * auto generate doc.
    * @var mixed
    */
    const PHT_PHOTO = "photo";

    /**
    * auto generate doc.
    * @var mixed
    */
    const PHT_BANKACCOUNT = "bankaccount";

    /**
    * auto generate doc.
    * @var mixed
    */
    const PHT_CREDIT_CARD = "creditcard";

    /**
    * auto generate doc.
    * @var mixed
    */
    const PHT_SOCIAL_FACEBOOK = "facebook";

    /**
    * auto generate doc.
    * @var mixed
    */
    const PHT_SOCIAL_TWITTER = "twitter";

    /**
    * auto generate doc.
    * @var mixed
    */
    const PHT_SOCIAL_INSTAGRAM = "instagram";

    /**
    * auto generate doc.
    * @var mixed
    */
    const PHT_SOCIAL_YOUTUBE = "youtube";

    /**
    * auto generate doc.
    * @var mixed
    */
    const PHT_SOCIAL_SOUNDCLOUND = "soundclound";

    /**
    * auto generate doc.
    * @var mixed
    */
    const PHT_SOCIAL_TIKTOK = "tiktok";

    /**
    * auto generate doc.
    * @var mixed
    */
    const PHT_SOCIAL_SNAPCHAT = "snapchat";

    /**
    * auto generate doc.
    * @var mixed
    */
    const PHT_SOCIAL_LINKEDIN = "linkedin";

    /**
    * auto generate doc.
    * @var mixed
    */
    const PHT_SOCIAL_TWITCH = "twitch";

    /**
    * auto generate doc.
    * @var mixed
    */
    const PHT_SOCIAL_DISCORD = "discord";

    /**
    * auto generate doc.
    * @var mixed
    */
    const PHT_SOCIAL_REDDIT = "reddit";

    /**
    * auto generate doc.
    * @var mixed
    */
    const PHT_SOCIAL_PINTEREST = "pinterest";

    /**
    * auto generate doc.
    * @var mixed
    */
    const PHT_TITLE = 'title';

    /**
    * auto generate doc.
    * @var mixed
    */
    const PHT_ALIAS = 'alias';

    /**
    * auto generate doc.
    * @var mixed
    */
    const PHT_ADR = 'address';

    /**
    * auto generate doc.
    * @var mixed
    */
    const PHT_NICKNAME = 'nickname';

    /**
    * auto generate doc.
    * @var mixed
    */
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