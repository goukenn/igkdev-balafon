<?php
namespace IGK\Database\Macros;

use IGK\Models\PhoneBooks;
use IGK\Models\PhoneBookTypes;
use IGK\Models\PhoneBookUserAssociations;
use IGK\Models\Users;
use IGK\System\Constants\PhonebookTypeNames;

class PhoneBooksMacros{
    /**
     * macros funtion 
     * @param PhoneBooks $model 
     * @param Users $user 
     * @param mixed $value 
     * @param string $type 
     * @return mixed 
     */
    public static function addPhoneBookEntry(PhoneBooks $model, Users $user, $value, $type=PhonebookTypeNames::PHT_PHONE){
        return $user->addPhoneBookEntry($type, $value);
    }
    /**
     * macros function 
     * @param PhoneBooks $model 
     * @param Users $user 
     * @return mixed 
     */
    public static function getPhoneBookEntry(PhoneBooks $model, Users $user){
        return $user->getPhoneBookEntry();
    }
    /**
     * retreive entries for a phone book
     * @param PhoneBooks $model 
     * @return void 
     */
    public static function GetEntries(PhoneBooks $model, ?string $entry=null){
        if ($entry){
            return array_map(
                function($a){
                    return ["type"=>$a->type, "value"=>$a->value];
                },
                PhoneBookUserAssociations::prepare()
                ->join_left(PhoneBooks::table(),PhoneBooks::FD_RCPHB_ENTRY_GUID.'='.PhoneBookUserAssociations::FD_USRPHB_PHONE_BOOK_ENTRY_GUID )
                ->join_left(PhoneBookTypes::table(),PhoneBookTypes::FD_RCPHBT_ID.'='.PhoneBooks::FD_RCPHB_TYPE )
                ->columns([
                    PhoneBookTypes::FD_RCPHBT_NAME => "type",
                    PhoneBooks::FD_RCPHB_VALUE => "value",
                ])
                ->execute() ?? []
            );
        }
    }
}