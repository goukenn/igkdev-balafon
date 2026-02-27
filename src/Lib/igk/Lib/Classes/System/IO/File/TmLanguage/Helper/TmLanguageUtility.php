<?php
// @author: C.A.D. BONDJE DOUE
// @file: TmLanguageUtility.php
// @date: 20241106 17:17:18
namespace IGK\System\IO\File\TmLanguage\Helper;
use Exception;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\IO\File\TmLanguage\ITmLanguageLoaderListener;
use IGK\System\IO\File\TmLanguage\TmLanguageCaptureRegexContainer;
use IGK\System\Text\IRegexMatcherContainer;
use IGK\System\Text\RegexMatcherContainer;
use IGKException;
use ReflectionException;
/**
 * 
 * @package IGK\System\IO\File\TmLanguage\Helper
 * @author C.A.D. BONDJE DOUE
 */

/**
* auto generate doc.
* @package IGK\System\IO\File\TmLanguage\Helper
*/
abstract class TmLanguageUtility
{

    /**
    * auto generate doc.
    * @param null|ITmLanguageLoaderListener $listener
    * @return void
    */
    public static function LoadPatterns(array $patterns, IRegexMatcherContainer $container, $repos, ?ITmLanguageLoaderListener $listener = null)
    {
        $_last = null;
        foreach ($patterns as $k => $v) {
            $include = igk_getv($v, 'include');
            if ($include && igk_str_startwith($include, '#')) {
                $_id = substr($include, 1);
                $_s = igk_getv($repos, $_id);
                $pattern = ($_s ? igk_getv($_s, 0) : null) ?? igk_die('missing pattern');
                $container->append($pattern);
            } else {
                // load definition to container 
                $_last = self::LoadDefinition($container, $v, $listener);
            }
        }
    }

    /**
    * auto generate doc.
    * @param null|ITmLanguageLoaderListener $listener
    * @return mixed
    */
    public static function LoadDefinition($container, $v, ?ITmLanguageLoaderListener $listener = null)
    {
        $v_ref = $container;
        $_last = null;
        if (!$listener) {
            extract((array)igk_extract_obj($v, 'name|begin|end|match|while|captures|beginCaptures|endCaptures|patterns|tokenID'));
            if ($begin) {
                if ($while) {
                } else if ($end) {
                    $_last = $v_ref->begin($begin, $end, $tokenID ?? $name, $name);
                }
            } else if ($match) {
                $_last = $v_ref->match($match, $name, $tokenID ?? $name);
            }
            if ($_last instanceof RegexMatcherContainer) {
                $_last = $_last->last();
            }
        } else {
            $_last = $listener->createPattern($v);
            $v_ref->append($_last);
        }
        $targ = compact('patterns', 'captures', 'beginCaptures', 'endCaptures', '_last');
        igk_reg_hook('tm:language_loading_complete', function ($e) use ($targ, $listener, $v) {
            list($v_container, $v_repos) = igk_extract($e->args, 'container|repos');
            extract($targ);
            // load pattern 
            if ($patterns) {
                TmLanguageUtility::LoadPatterns($patterns, $_last, $v_repos, $listener);
            }
            foreach (['captures', 'beginCaptures', 'endCaptures'] as $kk) {
                if ($$kk) {
                    $_last->{$kk} = self::LoadCaptures($$kk, $v_repos, $v_container, $listener);
                }
            }
            if ($listener) {
                $listener->loadComplete($v, $v_container, $v_repos);
            }
        });
        return $_last;
    }
    /**
     * load captures 
     * @param mixed $captures 
     * @param mixed $repos 
     * @param mixed $container 
     * @param mixed $listener 
     * @return void 
     * @throws Exception 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public static function LoadCaptures($captures, $repos, $container, $listener)
    {
        $cap = [];
        foreach ($captures as $k => $v) {
            if (is_string($v)) {
                $cap[$k] = $v;
            } else {
                list($name, $patterns) = igk_extract($v, 'name|patterns');
                $v_refPattern = null;
                if ($patterns) {
                    $refOnly = new TmLanguageCaptureRegexContainer($container);
                    self::LoadPatterns($patterns, $refOnly, $repos, $listener);
                    $v_refPattern = $refOnly->getPatterns();
                }
                $cap[$k] = igk_createobj(['name' => $name, 'patterns' => $v_refPattern]);
            }
        }
        return $cap;
    }

    /**
    * auto generate doc.
    * @param null|ITmLanguageLoaderListener $listener
    * @return void
    */
    public static function LoadRepository($repository, RegexMatcherContainer $container, &$v_trepos = null, ?ITmLanguageLoaderListener $listener = null)
    {
        $v_trepos = [];
        $v_ref = $container->referenceOnly();
        foreach ($repository as $k => $rep) {
            $_last = self::LoadDefinition($v_ref, $rep, $listener);
            $v_trepos[$k] = [
                $_last,
            ];
        }
    }

    /**
    * auto generate doc.
    * @param string $file
    * @return RegexMatcherContainer
    */
    public static function CreateRegexMatcherContainerFromFile(string $file, ?ITmLanguageLoaderListener $listener = null)
    {
        $json_data = json_decode(file_get_contents($file));
        return $json_data ? self::CreateRegexMatcherContainerFromData($json_data, $listener) : null;
    }
    /**
     * create regex match container from data
     * @param mixed $json_data 
     * @return RegexMatcherContainer 
     * @throws Exception 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public static function CreateRegexMatcherContainerFromData($json_data, ?ITmLanguageLoaderListener $listener = null)
    {
        list($scope, $patterns, $repository) = igk_extract($json_data, '$scope|patterns|repository');
        if (!$scope) {
            igk_die('missing scope');
        }
        if (is_null($patterns)) {
            igk_die('missing patterns');
        }
        $container = new RegexMatcherContainer;
        igk_hook_clear('tm:language_loading_complete');
        $repos = [];
        $repository && self::LoadRepository($repository, $container, $repos, $listener);
        $patterns && self::LoadPatterns($patterns, $container, $repos, $listener);
        igk_hook('tm:language_loading_complete', compact('container', 'repos'));
        return $container;
    }
}