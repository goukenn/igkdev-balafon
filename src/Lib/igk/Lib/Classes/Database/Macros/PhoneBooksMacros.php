<?php
// @author: C.A.D. BONDJE DOUE
// @filename: PhoneBooksMacros.php
// @date: 20251219 08:07:38
// @desc: macros function 

declare(strict_types=1);
namespace IGK\Database\Macros;
use IGK\Database\DbQueryCondition;
use IGK\Database\IDbQueryResult;
use IGK\Database\PhoneBookUtility;
use IGK\Helper\Activator;
use IGK\Models\PhoneBookEntries;
use IGK\Models\PhoneBooks;
use IGK\Models\PhoneBookTypes;
use IGK\Models\PhoneBookUserAssociations;
use IGK\Models\Users;
use IGK\System\Constants\PhonebookTypeNames;
use IGK\System\Database\IPhoneBookDetailVisitor;
use IGK\System\Database\PhoneBookEntryDetails;
use IGK\System\IO\VCF\VCard;
use IGK\System\IToJSon;
/**
* auto generate doc.
* @package IGK\Database\Macros
*/
/**
* auto generate doc.
* @package IGK\Database\Macros
*/
class PhoneBooksMacros
{
    /**
    * Constant: phone default tel.
    * @var mixed
    */
    const PHONE_DEFAULT_TEL = 'gsm|tel|phone';
    /**
    * auto generate doc.
    * @param null|string $search
    * @return bool|null|IDbQueryResult|IToJSon
    */
    public static function userPhoneEntries(PhoneBooks $model, Users $user, ?string $type = PhoneBooksMacros::PHONE_DEFAULT_TEL, ?string $search = null)
    {
        $T1 = get_class($model);
        $cond = [];
        if ($type != '@@') {
            foreach (explode("|", $type) as $t) {
                if ($r = PhoneBookTypes::GetCache(PhoneBookTypes::FD_NAME, $t)) {
                    $bt_id = $r->Id;
                    $cond[] = [$T1::FD_TYPE, $bt_id];
                }
            }
        }
        $bc = [
            PhoneBookEntries::FN_USER_GUID() => $user->clGuid,
            $cond ? DbQueryCondition::Create($cond, DbQueryCondition::OP_OR) : null
        ];
        if ($search) {
            $bc['@@' . PhoneBooks::FD_VALUE] = '%' . $search . '%';
        }
        $q = PhoneBookEntries::prepare()
            ->with(PhoneBooks::table())
            ->join(
                [
                    PhoneBooks::table() => [
                        sprintf('%s=%s', PhoneBookEntries::FD_GUID, PhoneBooks::FD_ENTRY_GUID),
                        'type' => 'left',
                    ]
                ]
            )
            ->where(array_filter($bc));
        $r = $q->execute();
        return $r;
    }
    /**
    * auto generate doc.
    * @param PhoneBooks $model
    * @param ?Users $user
    * @param ?string $search
    * @param string $type
    * @return mixed
    */
    public static function userSearchPhoneEntries(PhoneBooks $model, ?Users $user, ?string $search, ?string $type = PhoneBooksMacros::PHONE_DEFAULT_TEL)
    {
        return $model::userPhoneEntries($user, $type ?? '@@', $search);
    }
    /**
     * macros funtion 
     * @param PhoneBooks $model 
     * @param Users $user 
     * @param mixed $value 
     * @param string $type 
     * @return mixed 
     */
    public static function addPhoneBookEntry(PhoneBooks $model, Users $user, $value, $type = PhonebookTypeNames::PHT_PHONE)
    {
        return $user->addPhoneBookEntry($type, $value);
    }
    /**
     * macros function 
     * @param PhoneBooks $model 
     * @param Users $user 
     * @return mixed 
     */
    public static function getPhoneBookEntry(PhoneBooks $model, Users $user)
    {
        return $user->getPhoneBookEntry();
    }
    /**
    * retrieve entries for a phonebook
    * @param PhoneBooks $model
    * @param ?string $entry
    * @return void
    */
    public static function getEntries(PhoneBooks $model, ?string $entry = null)
    {
        if ($entry) {
            return array_map(
                function ($a): array {
                    return ["type" => $a->type, "value" => $a->value];
                },
                PhoneBookUserAssociations::prepare()
                    ->join_left($model::table(), PhoneBooks::FD_ENTRY_GUID . '=' . PhoneBookUserAssociations::FD_PHONE_BOOK_ENTRY_GUID)
                    ->join_left(PhoneBookTypes::table(), PhoneBookTypes::FD_ID . '=' . $model::FD_TYPE)
                    ->columns([
                        PhoneBookTypes::FD_NAME => "type",
                        $model::FD_VALUE => "value",
                    ])
                    ->execute() ?? []
            );
        }
    }
    /**
    * Searches For Entry.
    * @param PhoneBooks $phone
    * @param string $search
    */
    public static function searchForEntry(PhoneBooks $phone, string $search)
    {
        return PhoneBooks::select_all([
            '@@' . $phone::FD_VALUE => '%' . $search . '%s'
        ]);
    }
    /**
    * auto generate doc.
    * @param PhoneBooks $phone
    * @return bool
    */
    public static function deleteEntry(PhoneBooks $phone)
    {
        $key = $phone->EntryGuid;
        return PhoneBooks::delete([
            PhoneBooks::FD_ENTRY_GUID => $key
        ]) &&
            PhoneBookEntries::delete([
                PhoneBookEntries::FD_GUID => $key
            ]);
    }
    /**
    * auto generate doc.
    * @param PhoneBooks $phone
    * @param ?IPhoneBookDetailVisitor $visitor
    * @return mixed
    */
    public static function getPhoneDetails(PhoneBooks $phone, ?IPhoneBookDetailVisitor $visitor = null)
    {
        $phone->is_mock() && igk_die('require non mocking instance object');
        $rh = PhoneBooks::select_all([
            PhoneBooks::FD_ENTRY_GUID => $phone->EntryGuid
        ]);
        $inf = Activator::CreateNewInstance(PhoneBookEntryDetails::class, []);
        foreach (
            $rh
            as $srow
        ) {
            $type = PhoneBookTypes::GetCache(PhoneBookTypes::FD_ID, $srow->Type);
            $n = PhoneBookEntryDetails::GetPropertyName(strtolower($type->Name));
            $v = $srow->Value;
            if ($visitor) {
                $v = $visitor->visit($n, $v, igk_getv($inf, $n), $type->Cardinality);
            } else {
                if (isset($inf->{$n})) {
                    $g = $inf->{$n};
                    if (!is_array($g)) {
                        $g = [$g];
                    }
                    $g[] = $v;
                    if ($type->Cardinality > 0) {
                        if (count($g) > $type->Cardinality) {
                            igk_die('detail exceeds');
                        }
                    }
                    $v = $g;
                }
            }
            $inf->{$n} = $v;
        }
        return $inf;
    }
    /**
     * resolve and return the first phonebooks entries if exists
     * @param PhoneBooks $phone 
     * @param mixed $search 
     * @return mixed 
     */
    public static function resolve(PhoneBooks $phone, $search)
    {
        if (is_numeric($search)) {
            $cl = PhoneBooks::FD_ID;
        } else if (is_string($search)) {
            $cl = PhoneBooks::FD_ENTRY_GUID;
        }
        $r = PhoneBooks::select_all([$cl => $search]);
        return $r ? igk_getv($r, 0) : null;
    }
    /**
    * auto generate doc.
    * @param PhoneBooks $phone
    * @param ?Users $user
    * @param mixed $search
    * @return array<\IGK\Models\PhoneBooks
    */
    public static function vcard(PhoneBooks $phone, ?Users $user, $search)
    {
        /**
         * @var PhoneBooks $row
         */
        $r = self::userSearchPhoneEntries($phone, $user, $search, null);
        $ids = [];
        foreach ($r->to_array() as $row) {
            /**
            * auto generate doc.
            * @var PhoneBooks
            */
            $_id = $row->{PhoneBooks::FD_ENTRY_GUID};
            if (!isset($ids[$_id])) {
                if ($trow = PhoneBooks::select_first([PhoneBooks::FD_ENTRY_GUID => $_id])) {
                    $ids[$_id] = self::getPhoneDetails($trow);
                }
            }
        }
        return $ids;
    }
    /**
    * load vcard to user
    * @param PhoneBooks $model
    * @param string $file
    * @param Users $user
    * @return void
    */
    public static function loadVCardToUser(PhoneBooks $model, string $file, Users $user){
        $v_cards = VCard::OpenFile($file) ?? igk_die(__('missing or incorrect vcard file'));
        $user  || igk_die('required user');
        $count = 0;
        $result = PhoneBookUtility::ImportVCards($v_cards, $user, $count);
        return compact('count');
    }
}