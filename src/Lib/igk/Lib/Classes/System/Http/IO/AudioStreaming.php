<?php
// @author: C.A.D. BONDJE DOUE
// @file: AudioStreaming.php
// @date: 20260621 23:42:30
namespace IGK\System\Http\IO;


/**
 * 
 * @package IGK\System\Http\IO
 * @author C.A.D. BONDJE DOUE
 */
class AudioStreaming
{
    /**
     * 
     * @param string $file 
     * @param string $mimetype 
     * @return void 
     */
    public static function StreamAudioFile(string $file, $mimetype = 'audio/mpeg')
    {
        $length = $size = filesize($file);
        $start = 0;
        $end = $size - 1;
        $headers = [];

        if ($range = igk_server()->HTTP_RANGE) {
            if (preg_match('/bytes=\s*(\d+)-(\d*)/', $range, $matches)) {
                $start = (int)$matches[1];
                if (!empty($matches[2])) {
                    $end = (int)intval($matches[2]);
                }
            }
            $length = $end - $start + 1;
            // Sécurité : si le start est hors limites
            if (($start >= $size) || ($end >= $size) || ($start > $end)) {
                header("HTTP/1.1 416 Range Not Satisfiable");
                header("Content-Range: bytes */$size");
                header('Content-Length: 0');
                exit;
            }
            $headers = [
                'HTTP/1.1 206 Partial Content',
                "Content-Range: bytes $start-$end/$size"
            ];
        } else {
            igk_ilog('start serving: ' . igk_io_collapse_path($file));
            $headers = ['HTTP/1.1 200 OK'];
        }
        $headers[] = 'Content-Type: audio/mpeg';
        $headers[] = 'Accept-Ranges: bytes';
        $headers[] = 'Content-length: ' . $length;
        $headers[] = 'X-Accel-Buffering: no'; // for nginx
        while (ob_get_level()) {
            ob_end_clean();
        }
        foreach ($headers as $k) {
            header($k);
        }

        $fp = fopen($file, 'rb');
        fseek($fp, $start);

        $buffer = 8192; // 8 Ko par morceau
        $bytesSent = 0;
        // 2. Forcer la désactivation de la compression (Gzip/Deflate) qui stocke les echo
        if (function_exists('apache_setenv')) {
            @apache_setenv('no-gzip', 1);
            @apache_setenv('dont-vary', 1);
        }
        @ini_set('zlib.output_compression', 'Off');
        @ini_set('implicit_flush', 1); // Force PHP à flush à chaque echo automatiquement        
        while (!feof($fp) && $bytesSent < $length && (connection_status() == 0)) {            
            $sendLength = min($buffer, $length - $bytesSent);
            $data = fread($fp, $sendLength);
            if ($data === false || $data === '') {
                break;
            }
            echo $data;
            if (ob_get_length()) {
                ob_flush();
            }
            flush();
            $bytesSent += $sendLength;
        } 
        fclose($fp);
    }
}
