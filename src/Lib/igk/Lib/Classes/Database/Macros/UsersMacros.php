<?php

namespace IGK\Database\Macros;

use GrahamCampbell\ResultType\Success;
use IGK\Models\PhoneBookEntries;
use IGK\Models\PhoneBooks;
use IGK\Models\PhoneBookTypes;
use IGK\Models\PhoneBookUserAssociations;
use IGK\Models\Users;
use IGK\PhoneBook\PhoneBookEntry;
use IGK\System\Constants\PhonebookTypeNames;
use IGKException;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use ReflectionException;


/**
 * used for macros injection 
 * @package IGK\Database\Macros
 */
abstract class UsersMacros
{

    /**
     * activate user
     * @param Users $user 
     * @param string $newPassword 
     * @return bool 
     */
    public static function activate(Users $user)
    {
        $user->clStatus = 1;
        unset($user->clPwd);
        return $user->save();
    }
    /**
     * set user password
     * @param Users $user 
     * @param string $newPassword 
     * @return null|IGK\Models\IIGKQueryResult 
     */
    public static function changePassword(Users $user, string $newPassword)
    {
        if ($user->is_mock()){ igk_die(__METHOD__.": mock is not allowed");}
        return  \IGK\Models\Users::update([
            'clPwd' => $newPassword,
        ], ['clGuid' => $user->clGuid]);
    }
    // + | -----------------------------------------------------------    
    // + | phone book macros
    // + |
    public static function addPhoneBookEntry(Users $model, $type, $value)
    {
        $r = static::getPhoneBookEntry($model);

        $guid = ($r ? $r->usrphb_PhoneBookEntryGuid : null) ?? PhoneBookEntries::create()->rcphbe_Guid;

        if (($r && !$r->usrphb_PhoneBookEntryGuid) && ($guid)) {
            $r->usrphb_PhoneBookEntryGuid = $guid;
            $r->save();
        }

        $t = PhoneBookTypes::GetCache(PhoneBookTypes::FD_RCPHBT_NAME, $type);
        if (!$t) {
            return false;
        }
        $success = false;
        if (empty($value)) {
            PhoneBooks::delete([
                PhoneBooks::FD_RCPHB_ENTRY_GUID => $guid,
                PhoneBooks::FD_RCPHB_TYPE => $t->rcphbt_Id,
            ]);
        } else {
            PhoneBooks::beginTransaction();
            if ($g = PhoneBooks::createIfNotExists([
                PhoneBooks::FD_RCPHB_ENTRY_GUID => $guid,
                PhoneBooks::FD_RCPHB_TYPE => $t->rcphbt_Id,
                PhoneBooks::FD_RCPHB_VALUE => $value,
            ])) {
                if (!$r) {
                    $success  = $g && PhoneBookUserAssociations::create([
                        PhoneBookUserAssociations::FD_USRPHB_USER_GUID => $model->clGuid,
                        PhoneBookUserAssociations::FD_USRPHB_PHONE_BOOK_ENTRY_GUID => $guid,
                    ]);
                } else {
                    if ($g->isNew()) {
                        $success = true;
                    }
                }
            }
            if (!$success) {
                PhoneBooks::rollback();
            } else {
                PhoneBooks::commit();
            }
        }
    }
    /**
     * get phone book entries
     * @param Users $model 
     * @return mixed 
     */
    public static function getPhoneBookEntries(Users $model)
    {
        return PhoneBookUserAssociations::select_all([
            PhoneBookUserAssociations::FD_USRPHB_USER_GUID => $model->clGuid
        ]);
    }
    /**
     * get user phonebook entry
     * @param Users $model 
     * @return null|PhoneBookUserAssociations 
     */
    public static function getPhoneBookEntry(Users $model)
    {
        return PhoneBookUserAssociations::select_row([
            PhoneBookUserAssociations::FD_USRPHB_USER_GUID => $model->clGuid
        ]);
        //     return $model->prepare()
        //     ->with(PhoneBooks::table())
        //     ->with(PhoneBookUserAssociations::table())
        //     //->join(PhoneBookUserAssociations::table())
        //     ->join_left_on(PhoneBooks::table(), 'rcphb_EntryGuid','usrphb_PhoneBookEntryGuid')
        //    ->conditions([
        //         PhoneBookUserAssociations::FD_USRPHB_USER_GUID=>$model->clGuid
        //     ])->execute(); 
        // $r = PhoneBookUserAssociations::prepare()
        // ->with(PhoneBookTypes::table())
        // ->with($model->table())
        // // ->join_left_on($model->table(), 'clGuid','usrphb_UserGuid')
        // ->join_left_on(PhoneBooks::table(), 'rcphb_EntryGuid','usrphb_PhoneBookEntryGuid')
        // ->conditions([
        //     PhoneBookUserAssociations::FD_USRPHB_USER_GUID=>$model->clGuid
        // ])->execute(); 
        // return $r;
    }
    /**
     * get phone entry by type
     * @param Users $model 
     * @param null|string $type 
     * @return ?array<PhoneBookEntry>
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public static function getPhoneBookEntryByType(Users $model, ?string $type = PhonebookTypeNames::PHT_PHONE)
    {
        if ($g = PhoneBookUserAssociations::select_row([
            PhoneBookUserAssociations::FD_USRPHB_USER_GUID => $model->clGuid
        ])) {
            $response = [];
            PhoneBooks::prepare()
                ->join_left(
                    PhoneBookTypes::table(),
                    PhoneBooks::FD_RCPHB_TYPE . '=' . PhoneBookTypes::FD_RCPHBT_ID
                )
                ->where([
                    PhoneBooks::FD_RCPHB_ENTRY_GUID => $g->usrphb_PhoneBookEntryGuid,
                    PhoneBookTypes::FD_RCPHBT_NAME => $type
                ])
                ->execute(true, [
                    "@callback" => function ($a) use (&$response) {
                        $phone = new PhoneBookEntry();
                        $phone->value = $a->rcphb_Value;
                        $phone->type = $a->rcphbt_Name;
                        $phone->id = $a->rcphb_Id;
                        $response[] = $phone;
                        return true;
                    }
                ]);
            return $response;
        }
        return null;
    } 
}
