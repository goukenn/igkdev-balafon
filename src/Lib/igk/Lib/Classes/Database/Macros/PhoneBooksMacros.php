<?php
// @author: C.A.D. BONDJE DOUE
// @filename: PhoneBooksMacros.php
// @date: 20251219 08:07:38
// @desc: macros function 

declare(strict_types=1);

namespace IGK\Database\Macros;

use IGK\Database\DbQueryCondition;
use IGK\Database\IDbQueryResult;
use IGK\Helper\Activator;
use IGK\Models\PhoneBookEntries;
use IGK\Models\PhoneBooks;
use IGK\Models\PhoneBookTypes;
use IGK\Models\PhoneBookUserAssociations;
use IGK\Models\Users;
use IGK\System\Constants\PhonebookTypeNames;
use IGK\System\Database\IPhoneBookDetailVisitor;
use IGK\System\Database\PhoneBookEntryDetails;
use IGK\System\IToJSon;

/**
 * 
 * @package IGK\Database\Macros
 */
class PhoneBooksMacros
{
    const PHONE_DEFAULT_TEL = 'gsm|tel|phone';

    /**
     * 
     * @param PhoneBooks $model 
     * @param Users $user 
     * @param string $type 
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
     * 
     * @param PhoneBooks $model 
     * @param Users $user 
     * @param null|string $search 
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
                    ->join_left($model::table(), PhoneBooks::FD_ENTRY_GUID . '=' . PhoneBookUserAssociations::FD_USRPHB_PHONE_BOOK_ENTRY_GUID)
                    ->join_left(PhoneBookTypes::table(), PhoneBookTypes::FD_ID . '=' . $model::FD_TYPE)
                    ->columns([
                        PhoneBookTypes::FD_NAME => "type",
                        $model::FD_VALUE => "value",
                    ])
                    ->execute() ?? []
            );
        }
    }


    public static function searchForEntry(PhoneBooks $phone, string $search)
    {
        return PhoneBooks::select_all([
            '@@' . $phone::FD_VALUE => '%' . $search . '%s'
        ]);
    }
    /**
     * 
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
     * 
     * @param PhoneBooks $phone 
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
                    // check for cardinality 
                    // Logger::info('cardinality ...');

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
     * 
     * @param PhoneBooks $phone 
     * @param Users $user 
     * @param mixed $search 
     * @return array<\IGK\Models\PhoneBooks, mixed> 
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
}
