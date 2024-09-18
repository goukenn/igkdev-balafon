<?php
// @author: C.A.D. BONDJE DOUE
// @file: FormFieldHelper.php
// @date: 20240910 07:47:01
namespace IGK\System\Html\Forms\Helper;

use Exception;
use IGKException;
use IGKValidator;

///<summary></summary>
/**
 * 
 * @package IGK\System\Html\Forms\Helper
 * @author C.A.D. BONDJE DOUE
 */
abstract class FormFieldHelper
{
    const FORM_FIELD = 'forms';
    /**
     * handle session request args
     * @param mixed $obj 
     * @return mixed 
     * @throws Exception 
     */
    public static function HandleSessionRequestArgs($obj=null){
        $sess_form= igk_app()->session->{self::FORM_FIELD};
        if (!$sess_form)
            return null;

        $request = $_REQUEST;
        $obj = self::HandleFormRequest($sess_form, $request, $obj);
        return $obj;
    }
    /**
     * clear session from 
     * @return void 
     */
    public static function ClearFormSession(){
        igk_app()->session->{self::FORM_FIELD} = igk_createobj(); 
    }

    /**
     * single treat 
     * @param object $sess_form_form array<guid, array<string, index,name>
     * @param array|null $request_data 
     * @param null|object $obj 
     * @return mixed 
     * @throws Exception 
     */
    public static function HandleFormRequest($sess_form_form, array $request_data = null, $obj = null)
    {
        if (!is_object($sess_form_form)){
            throw new IGKException("must be and object");
        }
        $keys = array_keys((array)$sess_form_form);
        $request_data = $request_data ?? $_REQUEST;
        $found = false; 
 
        foreach ($keys as $uid) {
            if (IGKValidator::IsGUID($uid) && (1 == igk_getv($request_data, $uid))) {

                $info = igk_getv($sess_form_form, $uid);
                if (is_null($obj)) {
                    $obj = igk_createobj();
                } 

                foreach ($info as $k => $t) {
                    if (is_string($t)){
                        $t = json_decode($t);
                    }
                    list(, $name) = $t;
                    $obj->{$name} = igk_getv($request_data, $k); 
                    
                }
                $found = true;
                unset($sess_form_form->$uid);
                break;
            }
        }
        return $found ? $obj : null;
    }

    /**
     * randomize form fields
     * @param array $formFields 
     * @return mixed|array 
     * @throws IGKException 
     */
    public static function FormRandFieldName(array $formFields)
    {

        $session = igk_app()->getSession();
        if (!($sess_form = $session->forms)) {
            $sess_form = igk_createobj();
            $session->forms = $sess_form;
        }
        $form_guid = igk_create_guid();
        $ls = array_keys($formFields);
        $count = 1;
        $sess_form_def = [];

        foreach ($ls as $k) {
            $kn = $k;
            if (is_numeric($k)) {
                $kn = $formFields[$k];
            }
            $nkey = igk_get_unique_identifier(3, $list) . str_pad($count, 3, "0", STR_PAD_LEFT);
            $v_tf = $formFields[$k];
            if (is_object($v_tf)&& empty($v_tf->label_text)) {
                $v_tf->label_text = __($kn);
            }
            $nfields[$nkey] = $v_tf; // formFields[$k];
            $sess_form_def[$nkey] = [$k, $kn]; // + | index_or_key | name
            $count++;
        }

        $nfields[$form_guid] = ['type' => 'hidden', 'value' => '1'];
        if (isset($sess_form->{$form_guid})) {
            $sess_form->{$form_guid} = array_merge($sess_form->{$form_guid}, $sess_form_def);
        } else
            $sess_form->{$form_guid} = $sess_form_def;
        $session->forms = $sess_form;

        return $nfields;
    }
}
