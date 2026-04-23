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
/**
* auto generate doc.
* @package IGK\System\Html\Metadatas
*/
class CoreDocMetadata extends MetadataGroupEntryBase{
    /**
    * Name of application name.
    * @var mixed
    */
    var $applicationName;
    /**
    * Property: generator.
    * @var mixed
    */
    var $generator;
    /**
    * Property: theme color.
    * @var mixed
    */
    var $themeColor;
    /**
    * Property: color scheme.
    * @var mixed
    */
    var $colorScheme;
    /**
    * Property: creator.
    * @var mixed
    */
    var $creator;
    /**
    * Property: publisher.
    * @var mixed
    */
    var $publisher;
    /**
    * Property: robots.
    * @var mixed
    */
    var $robots;
    /**
    * Property: alternates.
    * @var mixed
    */
    var $alternates;
    /**
    * Property: icons.
    * @var mixed
    */
    var $icons;
    /**
    * Property: manifest.
    * @var mixed
    */
    var $manifest;
    /**
    * Property: classification.
    * @var mixed
    */
    var $classification;
    /**
    * Property: category.
    * @var mixed
    */
    var $category;
    /**
    * Property: other.
    * @var mixed
    */
    var $other;
    /**
    * Property: archives.
    * @var mixed
    */
    var $archives;
    /**
    * Property: abstract.
    * @var mixed
    */
    var $abstract;
    /**
    * Property: itunes.
    * @var mixed
    */
    var $itunes;
    /**
    * Property: assets.
    * @var mixed
    */
    var $assets;
    /**
    * Property: bookmarks.
    * @var mixed
    */
    var $bookmarks;
    /**
    * Property: apple itunes app.
    * @var mixed
    */
    var $appleItunesApp;
    /**
    * auto generate doc.
    * @var null|array|IGK\System\Html\Metadatas\formatDetection
    */
    var $formatDetection;
    /**
    * Map.
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
    * Sets Property.
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
    * Handles archives.
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
    * Handles format Detection.
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
    * Binds Meta Def.
    * @param mixed $name
    * @param mixed $content
    */
    public function bindMetaDef($name, $content){
    }
}