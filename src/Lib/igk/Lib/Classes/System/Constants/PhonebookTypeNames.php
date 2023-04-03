<?php
// @author: C.A.D. BONDJE DOUE
// @file: PhonebookTypeNames.php
// @date: 20230205 06:53:24
namespace IGK\System\Constants;

use IGK\System\Traits\EnumeratesConstants;

///<summary></summary>
/**
* 
* @package IGK\System\Constants
*/
abstract class PhonebookTypeNames{
    use EnumeratesConstants;
    const PHT_COMPANY = "company";
    const PHT_PHONE = "phone";
    const PHT_EMAIL = "email";
    const PHT_URL = "url";
    const PHT_WEBSITE = "website";
    const PHT_BIRTHDATE = "birthdate";
    const PHT_RELATEDNAME = "relatedname";
    const PHT_SOCIAL_PROFILE = "social profile";
    const PHT_INSTANT_MESSAGE = "instant message";
    const PHT_NOTES = "notes";
    const PHT_VCARD = "vcard";
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
} 