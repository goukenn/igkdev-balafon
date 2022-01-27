<?php


///<summary>Represente class: IGKLog</summary>

use IGK\Helper\IO;

/**
 * Represente IGKLog class
 */
final class IGKLog extends IGKObject
{
    const ERRORLOGFILE = "Data/Logs/.global-error." . IGK_TODAY . ".log";
    const LOGFILE = "Data/Logs/.global." . IGK_TODAY . ".log";
    private static $sm_instance;
    ///<summary></summary>
    /**
     * 
     */
    private function __construct()
    {
    }
    ///<summary></summary>
    /**
     * 
     */
    public function ClearLog()
    {
        $f = $this->getLogFile();
        $r = fopen($f, "w+");
        fclose($r);
    }
    ///<summary></summary>
    /**
     * 
     */
    public static function getInstance()
    {
        if (!isset($_SESSION)) {
            igk_die("/|\ must start session");
        }
        if (self::$sm_instance == null) {
            self::$sm_instance = igk_get_class_instance(__CLASS__, function () {
                return new IGKLog();
            });
        }
        return self::$sm_instance;
    }
    ///<summary>write log to IGK_LOG_FILE</summary>
    /**
     * write log to IGK_LOG_FILE
     * @param mixed $msg
     * @param mixed $tag
     * @param mixed $traceindex
     */
    public static function Append($msg, $tag = null, $traceindex = 0)
    {
        // + igk_wln($msg);
        // + igk_trace();
        // + igk_exit();

        if (is_array($msg)) {
            $s = "Array(" . count($msg) . "):[\n";
            $o = "";
            foreach ($msg as $k => $v) {
                $s .= $k . ":";
                if (is_array($v)) {
                    $s .= "Array";
                } else if (is_object($v))
                    $s .= get_class($v);
                else
                    $s .= $v;
                $s .= "\n";
            }
            $s .= "]";
            $msg = $s;
        }
        $f = "";
        if (!($f = igk_const("IGK_LOG_FILE")))
            $f = igk_ilog_file();
        if (!defined('IGK_NO_TRACELOG')) {
            if (!igk_sys_env_production()) {
                igk_ilog_trace(igk_trace_function(2 + $traceindex));
                $msg = array("msg" => $msg, "trace" => igk_ilog_get_trace());
            }
        }
        if (empty($tag)) {
            $tag = IGK_LOG_SYS;
        }
        igk_log_append($f, $msg, $tag);
        if (igk_environment()->is("DEV")) {
            error_log("[{$tag}] - $msg");
        }

        /**
         * 
         */
        if (!igk_sys_configs()->no_db_log){
            \IGK\Models\DbLogs::add($msg, $tag);
        }
    }
    ///<summary></summary>
    /**
     * 
     */
    public function getLogFile()
    {
        $app = igk_app();
        $s = igk_io_basedir(IGKLog::LOGFILE);
        if ($app && isset($app->Configs))
            return igk_getv($app->Configs, "LogFile", $s);
        else
            return $s;
    }
    ///<summary></summary>
    ///<param name="msg"></param>
    /**
     * 
     * @param mixed $msg
     */
    public function write($msg)
    {
        $this->write_i("IGKLOG", $msg);
    }
    ///<summary></summary>
    ///<param name="tag"></param>
    ///<param name="message"></param>
    ///<param name="eval" default="1"></param>
    /**
     * 
     * @param mixed $tag
     * @param mixed $message
     * @param mixed $eval the default value is 1
     */
    public function write_i($tag, $message, $eval = 1)
    {
        $f = $this->getLogFile();
        if (empty($f)) {
            return;
        }
        if (is_array($message) && $eval) {
            IGKOb::Start();
            igk_dump_array($message);
            $c = IGKOb::Content();
            IGKOb::Clear();
            $message = "Array[" . $c . "]";
        }
        $r = null;
        if (!file_exists($f) && !IO::CreateDir(dirname($f)))
            return;
        $r = @fopen($f, file_exists($f) ? "a+" : "w+");
        if (is_array($message)) {
            IGKOb::Start();
            var_dump($message);
            $message = IGKOb::Content();
            IGKOb::Clear();
        }
        if ($r) {
            fwrite($r, date("h:i:s") . ": - [" . $tag . "] - " . $message . IGK_LF);
            fclose($r);
        }
    }
}
