<?php
// @author: C.A.D. BONDJE DOUE
// @file: PHPDocCommentParseTrait.php
// @date: 20230731 10:21:35
namespace IGK\System\IO\File\Php\Traits;

use IGK\System\IO\StringBuilder;

/**
* auto generate doc.
* @package IGK\System\IO\File\Php\Traits
*/

/**
 * auto generate doc.
 * @package IGK\System\IO\File\Php\Traits
 */
trait PHPDocCommentParseTrait
{
    /**
     * use on loading definition 
     * @var mixed
     */
    protected static $sm_loading;

    protected function _supportMutiple(string $k): bool
    {
        return in_array($k, ['property', 'param', 'method', 'throws', 'var', 'property_read', 'property_write', 'mixin', 'uses', 'see', 
        'author', 'example', 'link', 'todo','deprecated','requires', 'template','extends', 'implements', 'use']);
    }
    /**
     * parse php doc comment
     * @param string $cm 
     * @param ?PhpDocBlocReader $reader
     * @param ?array $filter array of property to filter
     * @param null|callable $filterCallback
     * @param null|callable(string $name, mixed $definition, $parser):bool $handler handle extra properties
     * @return PHPDocCommentParser 
     */
    public static function ParsePhpDocComment(string $cm,  $reader = null, ?array $filter = null, $filterCallback = null, $handlerCallback = null)
    {
        $c = trim(igk_str_rm_start($cm, "/**"));
        $c = rtrim(igk_str_rm_last($c, "*/"));
        $g = new self;
        $g->setPropertyFilterListener($filterCallback);
        $g->setPropertyHandlerListener($handlerCallback);
        $g->summary = '';
        /// TODO: Remove filter property 
        // $g->m_reader = $reader;
        // $g->m_filter = $filter;
        $summary = false;
        $content = "";
        $name = "";
        $fc_load = function($g, $name, $content){
            self::$sm_loading = true;
            $g->$name($content);
            self::$sm_loading = false;
        };
        array_map(function ($c) use ($g, &$summary, &$content, &$name, $fc_load) {
            $k =  ltrim(trim($c), " *");
            $offset = 1;
            if (!$summary) {
                if (strlen($k) > 0) {
                    if ($k[0] === '@') {
                        $summary = true;
                        $name = $g->_readName($k, $offset);
                    }
                }
                if (!$summary) {
                    $g->summary .= $k;
                    return;
                }
                $content .= $g->_TreatContent(substr($k, $offset));
            } else {
                if (strlen($k) > 0) {
                    if ($k[0] === '@') {
                        $fc_load($g, $name, $content);
                        $content = "";
                        $offset = 1;
                        $name = $g->_readName($k, $offset);
                        $s = trim(substr($k, $offset));
                        $content .= $g->_TreatContent($s);
                        if ($name == 'api') {
                            $g->api = true;
                        }
                    } else {
                        $content .= $k;
                    }
                }
            }
        }, explode("\n", $c));
        if (!empty($content)) {
            self::$sm_loading = true;
            $g->$name($content);
            self::$sm_loading = false;
        }
        return $g;
    }

    /**
     * auto generate doc.
     * @return string
     */
    public function render(): string
    {
        $update_key = function (& $p, $k, $v) {
            $tv = $v;
            if (is_string($v)) {
                $tv = [$v];
            }
            while (count($tv) > 0) {
                $v = array_shift($tv);
                if (is_array($v)){
                    if (empty($v)){
                        continue;
                    }
                    igk_dev_wln_e(__FILE__.":".__LINE__ , "is array ", $v);
                }
                $p[] = '@' . $k . ' ' . trim($v);
            }
        };
        $sb = new StringBuilder;
        $p = [];
        if ($sum = $this->summary) {
            $p[] = $sum;
        }
        $ref = igk_sys_reflect_class(static::class);
        $props = get_object_vars($this);
        foreach ($props as $k => $v) {
            if (is_null($v) || ($k == 'summary') || !$ref->getProperty($k)->isPublic()) continue;
            $k = $this->_treatKey($k);
            $update_key($p, $k, $v);
        }
        if ($extra = $this->getExtraProperties()) {
            foreach ($extra as $k => $v) {
                $k = $this->_treatKey($k);
                $update_key($p, $k, $v);
            }
        }
        $sb->appendLine('/**');
        $sb->appendLine('* ' . implode("\n* ", $p));
        $sb->append('*/');
        return '' . $sb;
    }
    /**
     * treate property tag key 
     * @param string $key 
     * @return string 
     */
    private function _treatKey(string $key): string
    {
        $key = str_replace('_', '-', $key);
        return $key;
    }
}
