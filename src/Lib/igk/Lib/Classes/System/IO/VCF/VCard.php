<?php
// @author: C.A.D. BONDJE DOUE
// @file: VCard.php
// @date: 20250503 12:25:54
namespace IGK\System\IO\VCF;

use IGK\Helper\Activator;
use IGK\System\IO\StringBuilder;
use IGK\System\Text\RegexMatcherContainer;

///<summary></summary>
/**
* 
* @package IGK\System\IO\VCF
* @author C.A.D. BONDJE DOUE
* @remark specification : https://www.rfc-editor.org/rfc/rfc6350.html
*/
class VCard
{
    var $ADR;
    var $BDAY;
    var $EMAIL;
    var $FN;
    var $N;
    var $NICKNAME;
    var $NOTE;
    var $ORG;
    var $PHOTO;
    var $PRODID;
    var $TEL;
    var $TITLE;
    var $VERSION;
    var $X_SOCIALPROFILE;
    var $SOURCE;
    /**
     * 
     * @var ?string|'individual'|'group'|'org'|'location'
     */
    var $KIND;
    var $XML;
    var $ANNIVERSARY;
    var $GENDER;
    var $LANG;
    /**
     * time zone
     * @var mixed
     */
    var $TZ;

    /**
     * geo location
     * @var mixed
     */
    var $GEO;

    var $ROLE;
    var $LOGO;
    var $RELATED;
    var $REV;
    var $SOUND;
    var $PID;
    var $CLIENTPIDMAP;
    var $KEY;
    var $FBURL;
    var $CALADRURI;

    public function __construct()
    {
        $this->VERSION = '3.0';
    }

    /**
     * 
     * @param string $type 
     * @return null 
     */
    public function getPreferred(string $type){
        return null;
    }
    /**
     * 
     * @param mixed $file 
     * @param array $vcards 
     * @param array|IVCardSaveOptions $save_options 
     * @return void 
     */
    public static function Save($file, array $vcards,  $save_options = null)
    {
        $v_sb = new StringBuilder;
        $save_options = Activator::CreateNewInstance(VCardSaveOptions::class, $save_options ?? []);
        $rv = array_keys(get_class_vars(static::class));
        foreach ($vcards as $value) {
            if ($value instanceof static) {
                $v_sb->appendLine("BEGIN:VCARD");

                foreach($rv as $k){

                    if (!empty($value->{$k})){
                        $n = str_replace('_','-', $k);
                        $v = $value->{$k};
                        if (is_object($v)){
                            $v = $v->getValue();
                        }
                        if (!is_array($v)){
                            $v = [$v];
                        }
                        while(count($v)>0){
                            $tv = array_shift($v);
                            $v_sb->appendLine(sprintf('%s:%s', $n, $tv));
                        }
                    }
                }

                $v_sb->appendLine("END:VCARD");
            }
        }
        return igk_io_w2file($file, rtrim($v_sb.''));
    }
    /**
     * 
     * @param string $file 
     * @return mixed 
     */
    public static function OpenFile(string $file)
    {
        $t = []; //entries;
        $src = file_get_contents($file);
        $regex = new RegexMatcherContainer;
        $block = $regex->begin('BEGIN:VCARD', 'END:VCARD', 'block')->last();
        $block->patterns = [
            [
                'match' => '^[A-Z\-]+(?=;|:)',
                'tokenID' => 'property'
            ],
            [
                'match' => '(?<=;|:).+$',
                'tokenID' => 'value'
            ]
        ];
        $pos = 0;
        $properties = [];
        $key = $value = null;
        $def = [];
        $lastpos = 0;
        $fc_loaddef = function (& $def, $key, $value){ 
            if (isset($def[$key])){
                if (!is_array($def[$key])){
                    $def[$key] = [$def[$key]];
                } 
                $def[$key][] = $value;
            } else 
                $def[$key] = $value;
        };


        while ($g = $regex->detect($src, $pos)) {
            if ($e = $regex->end($g, $src, $pos)) {
                switch ($e->tokenID) {
                    case 'block':
                        if (!empty($key)) {
                            $fc_loaddef($def, $key, $value);
                        }
                        $cl = Activator::CreateNewInstance(static::class, $def);
                        $t[] = $cl;
                        $key = $value = null;
                        $def = [];
                        break;
                    case 'property':
                        // DEBUG - get properties
                        if (!isset($properties[$e->value])) {
                            $properties[$e->value] = 1;
                        }
                        $properties[$e->value]++;
                        if (!empty($key)) {
                            $fc_loaddef($def, $key, $value); 
                        }
                        $key = $value = null;
                        $key = str_replace('-', '_', $e->value);
                        break;
                    case 'value':
                        if (preg_match('/ENCODING=b/', $e->value)) {
                            // read value until next coding data ;
                            $ln  = 0;
                            $sb = substr($e->value, strpos($e->value, ':') + 1);
                            $pos = $pos + $ln;
                            do {
                                $ln = strpos($src, "\n", $pos + 1);
                                $line = substr($src, $pos + 1, $ln - $pos - 1);
                                if (($ln === false) || ((strlen($line) > 0) && ord($line[0]) != 32)) {
                                    break;
                                }
                                $pos = $ln;
                                $sb .= substr($line, 1);
                            } while ($pos < strlen($src));
                            $data = base64_decode($sb);
                            $value = new VCardEncodingBinaryData($data);
                            //igk_io_w2file('pics.png', $data);
                        } else {
                            $tv = $value = $e->value;
                            if (($rpos = strpos($value, ':')) !== false){
                                $tv = substr($value, $rpos + 1);
                            }
                               
                            if ($key == 'TEL') {
                                //just treat 
                                $v = str_replace(' ', '', $tv);
                                $v = preg_replace("/^00/", "+", $v);
                                $v = preg_replace("/^04/", "+32", $v);
                                $value = $v;
                            }
                            else {
                                $value = $tv;
                            }
                        }
                        break;
                }
            }

            if ($pos == $lastpos) {
                igk_wln_e("cusort not move", $lastpos);
                break;
            }
            $lastpos = $pos;
        }

        if (!empty($key) && $value){
            $def[$key] = $value;
        }
        // extract key sorct 
        // ksort($properties);
        // foreach(array_keys($properties) as $v){
        //     echo 'var $'.$v.';', PHP_EOL;
        // }
        return $t;
    }
}