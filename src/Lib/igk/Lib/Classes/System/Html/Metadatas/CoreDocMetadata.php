<?php
// @author: C.A.D. BONDJE DOUE
// @file: CoreDocMetadata.php
// @date: 20231221 15:20:59
namespace IGK\System\Html\Metadatas;
use IGK\Helper\Activator;
use IGK\Helper\StringUtility;
use ReflectionProperty;
/**
* 
* @package IGK\System\Html\Metadatas
*/
class CoreDocMetadata extends MetadataGroupEntryBase{

    /**
    * auto generate doc.
    * @var mixed
    */
    var $applicationName;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $generator;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $themeColor;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $colorScheme;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $creator;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $publisher;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $robots;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $alternates;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $icons;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $manifest;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $classification;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $category;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $other;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $archives;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $abstract;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $itunes;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $assets;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $bookmarks;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $appleItunesApp;
    /**
     * 
     * @var null|array|IGK\System\Html\Metadatas\formatDetection
     */
    var $formatDetection;

    /**
    * auto generate doc.
    * @return array
    */
    public function map(): array {
        static $mapped = null;
        if (is_null($mapped)){
            $mem =  igk_reflection_get_private_member(static::class, ReflectionProperty::IS_PUBLIC);
            $c = array_fill_keys($mem, $mem);
            array_map(function($i, $v)use(& $c){
                $c[$v] = strtolower(StringUtility::GetSnakeKebab($v, true));
            }, $c, $mem);
            $mapped =  $c;
        }
        return $mapped; 
    }

    /**
    * auto generate doc.
    * @param string $n
    * @param mixed $v
    */
    public function setProperty(string $n, $v)
    {
        switch($n){
            case 'manifest':
                $notag = igk_create_notagnode();
                $link = $notag->link();  
                $link['rel'] = 'manifest';
                $link['href'] = $v;
                $this->m_def[$n]= $notag;
                $this->icons = $v;
                return;
            case 'icons':
                $notag = igk_create_notagnode();
                if (is_array($v)){ 
                    foreach($v as $t){
                        $m = Activator::CreateNewInstance(CoreIconDescriptorMetaData::class, $t);
                       if ($m instanceof CoreIconDescriptorMetaData){
                            $link = $notag->link();
                            $link['rel'] = $m->rel; 
                            $link['href'] = $m->href;; 
                        } 
                    }
                } else if ( is_object($v) &&  ( $v = (Activator::CreateNewInstance(CoreIconMetaData::class, $v)))){
                    if ($v->icon){
                        $link = $notag->link();
                        $link['rel'] ="icon"; 
                        $link['href'] = $v->icon;
                    }
                    if ($v->apple){
                        $link = $notag->link();
                        $link['rel'] ="apple-touch-icon"; 
                        $link['href'] = $v->apple;
                    }  
                }
                else {
                    $link = $notag->link();  
                    $link['rel'] = 'icon';
                    $link['href'] = $v; 
                }  
                $this->m_def[$n]= $notag;
                $this->icons = $v;
                return;
            default:
            if (method_exists($this, $fc = 'handle_'.$n)){
                $this->$n = $this->$fc($v, $n);
                return;
            }
            break;
        }
        parent::setProperty($n, $v);
    }

    /**
    * auto generate doc.
    * @param mixed $v
    * @param mixed $n
    */
    public function handle_archives($v, $n){
        $notag = igk_create_notagnode();
        $link = $notag->link();
        $this->m_def[$n] = $notag;
        $link['rel'] = $n; 
        $link['href'] = $v;
        return $v;
    }

    /**
    * auto generate doc.
    * @param mixed $v
    * @param mixed $n
    */
    public function handle_formatDetection($v, $n){
        $s = '';
        if(is_array($v)){
            $c = '';
            foreach($v as $k=>$v){
                $s.= $c.$k.'='.($v? 'yes':'no');
                $c = ',';
            } 
            parent::setProperty($n, $s);
            return $s;
        }
    }

    /**
    * auto generate doc.
    * @param mixed $name
    * @param mixed $content
    */
    public function bindMetaDef($name, $content){
    }
}