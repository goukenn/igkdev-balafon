<?php
namespace IGK\Database\Macros;

use IGK\Models\PhoneBooks;
use IGK\Models\PhoneBookTypes;
use IGK\Models\PhoneBookUserAssociations;

/**
 * 
 * @package IGK\Database\Macros
 */
class PhoneBookUserAssociationsMacros{
    public static function GetPhoneBookDetailsFromUserGuid(string $guid){
        $result = PhoneBookUserAssociations::prepare()
        ->join_left(PhoneBooks::table(),PhoneBooks::FD_RCPHB_ENTRY_GUID.'='.PhoneBookUserAssociations::FD_USRPHB_PHONE_BOOK_ENTRY_GUID )
        ->join_left(PhoneBookTypes::table(),PhoneBookTypes::FD_RCPHBT_ID.'='.PhoneBooks::FD_RCPHB_TYPE )
        ->columns([
            PhoneBooks::FD_RCPHB_ID=>"id",
            PhoneBookTypes::FD_RCPHBT_NAME => "type",
            PhoneBooks::FD_RCPHB_VALUE => "value",
        ])
        ->where([PhoneBookUserAssociations::FD_USRPHB_USER_GUID=>$guid])
        ->execute();

        return array_map(
            function($a){
                return ["type"=>$a->type, "value"=>$a->value];
            },
            ($result ? $result->to_array() : null) ?? []
        );
    }
    /**
     * macros to get phone entries details
     * @return void 
     */
    public static function getEntries(PhoneBookUserAssociations $entry){
        return self::GetPhoneBookDetailsFromUserGuid($entry->usrphb_UserGuid);
    }    
}