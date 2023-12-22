<?php
// @author: C.A.D. BONDJE DOUE
// @file: MetaDataHost.php
// @date: 20231221 14:23:00
namespace IGK\System\Html\Metadatas;

use IGKEvents;
use IGKException;
use IGKHtmlDoc;

///<summary></summary>
/**
 * 
 * @package IGK\System\Html\Metadatas
 */
class MetaDataHost
{
    private $m_host;
    private $m_register;
    public function __construct(IGKHtmlDoc $host)
    {
        $this->m_host = $host;
    }
    /**
     * 
     * @return (CoreDocMetadata|OpenGraphMetadata|TwitterMetadata|AppLinkMetadata|AppleWebAppMetadata)[] 
     */
    public static function InitCoreMetaDataDefinition()
    {
        return [
            new CoreDocMetadata(),
            new OpenGraphMetadata(),
            new TwitterMetadata(),
            new AppLinkMetadata(),
            new AppleWebAppMetadata()
        ];
    }
    /**
     * bind metadata settings
     * @param array|IMetadataDefinition $settings 
     * @return void 
     * @throws IGKException 
     */
    public function bind(array $settings)
    {
        $register = &$this->m_register;
        if (is_null($register)) {
            $register = static::InitCoreMetaDataDefinition();
        }

        // core fields 
        $m = $this->m_host->getMetas();
        $fields = [
            'title' => function ($v) use ($m) {
                $this->m_host->setTitle($v);
            },
            'description' => function ($v) use ($m) {
                $m->setDescription($v);
            },
            'keywords' => function ($v) use ($m) {
                $m->setKeywords($v);
            },
            'author' => function ($v) use ($m) {
                $m->setAuthor($v);
            },
            'charset'=>function($v) use($m){
                $m->setCharset($v);
            }
        ];

        foreach ($register as $p) {
            $tfile = array_fill_keys(array_keys($p->map()), $p->getSetDataCallback());
            $fields = array_merge($fields, $tfile);
        }

        igk_reg_hook(IGKEvents::HOOK_HTML_HEAD, function ($e) use ($register) {
            $opt = igk_getv($e->args, 'options');
            if ($opt && ($opt->Document === $this->m_host)) {
                foreach ($register as $m) {
                    if ($m->isDirty()) {
                        igk_wl($m->render());
                    }
                }
            }
        });

        // binding meta field register 
        foreach ($settings as $k => $v) {
            $fc = igk_getv($fields, $k);
            if ($fc) {
                $fc($v, $k);
            } else {
                igk_dev_wln_e('missing meta propery binding ', $k);
            }
        }
    }
}
