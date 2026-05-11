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
    * Constant: pht locale.
    * @var mixed
    */
    const PHT_LOCALE = 'locale';
    /**
    * Constant: pht name.
    * @var mixed
    */
    const PHT_NAME = 'name';
    /**
    * Constant: pht lastname.
    * @var mixed
    */
    const PHT_LASTNAME = 'lastname';
    /**
    * Constant: pht firstname.
    * @var mixed
    */
    const PHT_FIRSTNAME = 'firstname';
    /**
    * Constant: pht surname.
    * @var mixed
    */
    const PHT_SURNAME = 'surname';
    /**
    * Constant: pht company.
    * @var mixed
    */
    const PHT_COMPANY = "company";
    /**
    * Constant: pht organization.
    * @var mixed
    */
    const PHT_ORGANIZATION = "organization";
    /**
    * Constant: pht profession.
    * @var mixed
    */
    const PHT_PROFESSION = "profession";
    /**
    * Constant: pht phone.
    * @var mixed
    */
    const PHT_PHONE = "phone";
    /**
    * Constant: pht tel.
    * @var mixed
    */
    const PHT_TEL = "tel";
    /**
    * Constant: pht gsm.
    * @var mixed
    */
    const PHT_GSM = "gsm";
    /**
    * Constant: pht email.
    * @var mixed
    */
    const PHT_EMAIL = "email";
    /**
    * Constant: pht url.
    * @var mixed
    */
    const PHT_URL = "url";
    /**
    * Constant: pht website.
    * @var mixed
    */
    const PHT_WEBSITE = "website";
    /**
    * Constant: pht birthdate.
    * @var mixed
    */
    const PHT_BIRTHDATE = "birthdate";
    /**
    * Constant: pht relatedname.
    * @var mixed
    */
    const PHT_RELATEDNAME = "relatedname";
    /**
    * Constant: pht social profile.
    * @var mixed
    */
    const PHT_SOCIAL_PROFILE = "social profile";
    /**
    * Constant: pht instant message.
    * @var mixed
    */
    const PHT_INSTANT_MESSAGE = "instant message";
    /**
    * Constant: pht vat.
    * @var mixed
    */
    const PHT_VAT = "vat";
    /**
    * Constant: pht rrn.
    * @var mixed
    */
    const PHT_RRN = "rrn";
    /**
    * Constant: pht notes.
    * @var mixed
    */
    const PHT_NOTES = "notes";
    /**
    * Constant: pht vcard.
    * @var mixed
    */
    const PHT_VCARD = "vcard";
    /**
    * Constant: pht photo.
    * @var mixed
    */
    const PHT_PHOTO = "photo";
    /**
    * Constant: pht bankaccount.
    * @var mixed
    */
    const PHT_BANKACCOUNT = "bankaccount";
    /**
    * Constant: pht credit card.
    * @var mixed
    */
    const PHT_CREDIT_CARD = "creditcard";
    /**
    * Constant: pht social facebook.
    * @var mixed
    */
    const PHT_SOCIAL_FACEBOOK = "facebook";
    /**
    * Constant: pht social twitter.
    * @var mixed
    */
    const PHT_SOCIAL_TWITTER = "twitter";
    /**
    * Constant: pht social instagram.
    * @var mixed
    */
    const PHT_SOCIAL_INSTAGRAM = "instagram";
    /**
    * Constant: pht social youtube.
    * @var mixed
    */
    const PHT_SOCIAL_YOUTUBE = "youtube";
    /**
    * Constant: pht social soundclound.
    * @var mixed
    */
    const PHT_SOCIAL_SOUNDCLOUND = "soundclound";
    /**
    * Constant: pht social tiktok.
    * @var mixed
    */
    const PHT_SOCIAL_TIKTOK = "tiktok";
    /**
    * Constant: pht social snapchat.
    * @var mixed
    */
    const PHT_SOCIAL_SNAPCHAT = "snapchat";
    /**
    * Constant: pht social linkedin.
    * @var mixed
    */
    const PHT_SOCIAL_LINKEDIN = "linkedin";
    /**
    * Constant: pht social twitch.
    * @var mixed
    */
    const PHT_SOCIAL_TWITCH = "twitch";
    /**
    * Constant: pht social discord.
    * @var mixed
    */
    const PHT_SOCIAL_DISCORD = "discord";
    /**
    * Constant: pht social reddit.
    * @var mixed
    */
    const PHT_SOCIAL_REDDIT = "reddit";
    /**
    * Constant: pht social pinterest.
    * @var mixed
    */
    const PHT_SOCIAL_PINTEREST = "pinterest";
    /**
    * Constant: pht title.
    * @var mixed
    */
    const PHT_TITLE = 'title';
    /**
    * Constant: pht alias.
    * @var mixed
    */
    const PHT_ALIAS = 'alias';
    /**
    * Constant: pht adr.
    * @var mixed
    */
    const PHT_ADR = 'address';
    /**
    * Constant: pht nickname.
    * @var mixed
    */
    const PHT_NICKNAME = 'nickname';
    /**
    * Constant: pht asbl.
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
    * @param string $n
    * @return bool
    */
    public static function IsSingle(string $n):bool{
        return in_array($n, explode('|', 'name|lastname|firstname|locale'));
    }
} 