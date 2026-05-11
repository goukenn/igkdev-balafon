<?php
// @author: C.A.D. BONDJE DOUE
// @file: PhoneBookUtility.php
// @date: 20250505 09:23:04
namespace IGK\Database;
use Exception;
use IGK\Database\Constants\PhoneBookTypeNames;
use IGK\Models\PhoneBookEntries;
use IGK\Models\PhoneBooks;
use IGK\Models\PhoneBookTypes;
use IGK\Models\Users;
use IGK\System\Console\Logger;
use IGK\System\IO\VCF\VCard;
use IGKException;

/**
 * 
 * @package IGK\Database
 * @author C.A.D. BONDJE DOUE
 */
/**
* auto generate doc.
* @package IGK\Database
*/
class PhoneBookUtility
{
    /**
     * search for pattern 
     * @param string $pattern 
     * @return mixed 
     */
    public static function Search(string $pattern){
        $ids = [];
        foreach([
            PhoneBookTypeNames::PHT_NAME,
            PhoneBookTypeNames::PHT_TEL,
            PhoneBookTypeNames::PHT_PHONE,
            PhoneBookTypeNames::PHT_GSM,
            PhoneBookTypeNames::PHT_FIRSTNAME,
            PhoneBookTypeNames::PHT_LASTNAME,
            PhoneBookTypeNames::PHT_ALIAS,
        ] as $k){
            $ids[] = PhoneBookTypes::GetCache(PhoneBookTypes::FD_NAME, $k)->{PhoneBookTypes::FD_ID};
        }
        array_unique($ids);        
        $tpatter = '';
        $conditions = ['!!'.PhoneBooks::FD_TYPE=>$ids];
        if (false !== strpos($pattern, ' ')){
            $tpatter = array_map(function($a){return igk_str_surround($a, '%'); }, explode(' ', $pattern));
            array_unshift($tpatter, igk_str_surround($pattern, '%'));
            $tc = [];
            foreach($tpatter as $v){
                $tc[] = ['@@'.PhoneBooks::FD_VALUE, $v];
            }
            $qcond = (object)['type'=>'merge', 'list'=>$tc, 'operand'=>'OR'];            
            $conditions[] = $qcond;
        } else{
            $tpatter = igk_str_surround($pattern, '%');
            $conditions['@@'.PhoneBooks::FD_VALUE]=$tpatter;
        }
        return PhoneBooks::select_all($conditions, ['Distinct'=>true, 'Columns'=>[PhoneBooks::FD_ENTRY_GUID]]);
    }
    /**
    * auto generate doc.
    * @param string $type
    * @return null|string
    */
    public static function ResolveNameToVCardProperty(string $type): ?string{
        return igk_getv(['email' => 'EMAIL', 
        'address'=>'ADR',
        'firstname' => 'N',
        'lastname'=>'FN',
        'tel' => 'TEL', 
        'organization'=>'ORG',
        'gsm'=>'TEL', 'phone'=>'TEL'], strtolower($type));
    }
    /**
    * auto generate doc.
    * @param null|Users $user
    * @return array
    */
    public static function ExportVCards(?Users $user = null)
    {
        $v_tab = [];
        $tab = self::GetPhoneEntries($user);
        while (count($tab) > 0) {
            if (($q = array_shift($tab)) instanceof PhoneBookEntries) {
                $g =  PhoneBooks::prepare()->with(PhoneBookTypes::table())
                    ->join_left(PhoneBookTypes::table(), sprintf('%s=%s', PhoneBookTypes::FN_ID(), PhoneBooks::FN_TYPE()))
                    ->where([
                        PhoneBooks::FD_ENTRY_GUID => $q->Guid
                    ])->execute();
                $v_card = null;
                foreach ($g->getRows() as $row) {
                    $type = $row->{PhoneBookTypes::FD_NAME};
                    $v = $row->{PhoneBooks::FD_VALUE};
                    $ctype = self::ResolveNameToVCardProperty($type);
                    if(!property_exists(VCard::class, $ctype)){
                        continue;
                    }
                    if (is_null($v_card))
                    $v_card = new VCard;
                    if (isset($v_card->{$ctype})) {
                        if (!is_array($v_card->{$ctype}))
                            $v_card->{$ctype} = [$v_card->{$ctype}];
                        $v_card->{$ctype}[] = $v;
                    } else
                        $v_card->{$ctype} = $v;
                }
                if ($v_card)
                    $v_tab[$q->Guid] = $v_card;
            }
        }
        return $v_tab;
    }
    /**
    * impoortd vcard
    * @param array $cards array of vcard
    * @param null|Users $user
    * @param mixed & $count
    * @throws Exception
    * @throws IGKException
    * @return void
    */
    public static function ImportVCards($cards, ?Users $user = null, & $count = 0)
    {
        foreach ($cards as $c) {
            $firstname = null;
            $lastname = null;
            list($fullname, $name, $tel, $email,$birthdate, $organization, $url) = 
            igk_extract($c, 'FN|N|TEL|EMAIL|BDAY|ORG|URL');
            $v_tpnames = explode(';', $name ?? '');
            list($firstname, $lastname) = igk_extract($v_tpnames,'0|1');
            $data = [];
            foreach (['firstname', 'lastname', 'tel', 'email', 'birthdate','organization', 'url'] as $r) {
                if ($$r) {
                    $data[$r] = $$r;
                }
            }
            if ($data && PhoneBookUtility::LoadEntryData($data, $user)){
                $count++;
            }
        }
    }
    /**
    * get phone book entries
    * @param ?Users $user
    * @param null|Users $users
    * @return mixed
    */
    public static function GetPhoneEntries(?Users $user, $limit=null)
    {
        $conditions = [];
        if ($user) {
            $conditions[PhoneBookEntries::FD_USER_GUID] = $user->clGuid;
        }
        if (is_null($limit)){
            return PhoneBookEntries::select_all($conditions);
        }
        $t1 = PhoneBookTypes::table();
        $t2 = PhoneBooks::table();
        $conditions[PhoneBookTypes::FN_NAME()]= PhoneBookTypeNames::PHT_NAME;
        $options = [];
        if ($limit){
            $options['Limit'] = $limit;
        }
        $options['OrderBy'] = [PhoneBookTypes::FN_NAME().'|Asc'];
        $options['Columns'] = PhoneBookEntries::queryColumns(); 
        $tab = PhoneBookEntries::prepare()
        ->with($t1 = PhoneBookTypes::table(), 'type')
        ->with($t2 = PhoneBooks::table(), 'books')
        ->join_left($t2, sprintf('%s=%s', PhoneBookEntries::FN_GUID(), PhoneBooks::FN_ENTRY_GUID()))
        ->join_left($t1, sprintf('%s=%s', PhoneBookTypes::FN_ID(), PhoneBooks::FN_TYPE()))
        ->columns(array_merge(PhoneBookEntries::queryColumns(),PhoneBooks::queryColumns()))
        ->where($conditions) 
        ->orderBy([ PhoneBooks::FN_VALUE().'|Asc', ])
        ->execute(true, $options); 
        return $tab;
    }
    /**
    * auto generate doc.
    * @param null|Users $user
    * @return array
    */
    public static function DeleteAllBookEntry(?Users $user=null)
    {
        $conditions = [];
        $conditions[PhoneBookEntries::FD_USER_GUID] = ($user) ? $user->clGuid : null;    
        $tab = PhoneBookEntries::select_all($conditions);
        $delete = [];
        while (count($tab) > 0) {
            $r = array_shift($tab);
            if (PhoneBooks::delete([PhoneBooks::FD_ENTRY_GUID => $r->{PhoneBookEntries::FD_GUID}])) {
                $delete[] = $r;
            }
        }
        if (!$conditions) {
            $conditions[PhoneBookEntries::FD_USER_GUID] = null;
        }
        PhoneBookEntries::delete($conditions);
        return $delete;
    }
    /**
    * auto generate doc.
    * @param string $type
    * @return PhoneBookConverterBase|object|null
    */
    public static function GetPhoneBookConverter(string $type)
    {
        $cl = __NAMESPACE__ . "\\PhoneBooks\\" . ucfirst($type) . "Converter";
        if (class_exists($cl)) {
            return new $cl();
        }
        return null;
    }
    /**
    * auto generate doc.
    * @param mixed $data
    * @param null|Users $user
    * @return bool
    */
    public static function LoadEntryData($data, ?Users $user = null)
    {
        $row = PhoneBookEntries::createEmptyRow();
        if ($user){
            $row->{PhoneBookEntries::FD_USER_GUID} = $user->clGuid;
        }
        if ($l = PhoneBookEntries::insert($row)) {
            $p_guid = $l->{PhoneBookEntries::FD_GUID};
            foreach ($data as $k => $v) {
                $kt = PhoneBookTypes::GetCache(PhoneBookTypes::FD_NAME, $k);
                if (!$kt) {
                    igk_ilog('missing book entry type: ' . $k);
                    continue;
                }
                if (!is_array($v)) {
                    $v = [$v];
                }
                $vconc = PhoneBookUtility::GetPhoneBookConverter($k);
                while (count($v) > 0) {
                    $q = array_shift($v);
                    if ($vconc) {
                        $q = $vconc->treat($q);
                    } else {
                        $q = trim($q);
                    }
                    if (is_null($q))continue;
                    $l = PhoneBooks::insert([
                        PhoneBooks::FD_ENTRY_GUID => $p_guid,  
                        PhoneBooks::FD_TYPE => $kt,
                        PhoneBooks::FD_VALUE => $q
                    ]);
                }
            }
            return true;
        }
        return false;
    }
    /**
    * Phone detail list.
    * @param mixed $entries
    */
    public static function PhoneDetailList($entries){
    }
}