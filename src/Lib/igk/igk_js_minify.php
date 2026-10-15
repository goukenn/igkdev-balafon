<?php

// @author: C.A.D. BONDJE DOUE
// @filename: Untitled-2
// @date: 20261014 11:54:22
// @desc: 

 
/**
 * Insertion automatique de ";" : un saut de ligne doit-il devenir ";"
 * avant le token qui commence a $s[$p] ?
 * $t     : FIN de la sortie deja produite (une fenetre de quelques dizaines d'octets suffit)
 * $ctl   : le dernier token est le ")" d'un if / for / while / with / switch / catch
 * $rxEnd : le dernier token est un litteral regex
 */
function igk_js_asi($t, $s, $p, $ctl, $rxEnd)
{
    $t = rtrim($t, ' ');
    if ($t === '' || $ctl) {
        return false;
    }
    $c = $s[$p];
    $d = $s[$p + 1] ?? '';
    // return / break / continue : un saut de ligne termine l'instruction
    if (preg_match('#(?<![\w$.])(?:return|break|continue)$#', $t)) {
        return $c !== ';' && $c !== '}';
    }
    // le token suivant commence-t-il une nouvelle instruction ?
    $w = preg_match('#\G[\w$]+#', $s, $mw, 0, $p) ? $mw[0] : '';
    $new = ($w !== '') ? !in_array($w, ['in', 'instanceof', 'of'], true)
        : ($c === '"' || $c === "'" || $c === '~' || $c === '#' || ($c === '!' && $d !== '=')
            || ($c === '+' && $d === '+') || ($c === '-' && $d === '-'));
    if (!$new) {
        return false;
    }
    if (substr($t, -1) === '}') {
        return !in_array($w, ['else', 'catch', 'finally', 'while'], true);
    }
    // le token precedent termine-t-il une expression ?
    return $rxEnd || (preg_match('#(?:[\w$)\]"\'`]|\+\+|--)$#', $t)
        && !preg_match('#(?<![\w$.])(?:else|do|typeof|void|delete|new|in|instanceof|of|case|throw|var|let|const|function|class|extends|import|export|default|try|finally|await|if|for|while|with|switch|catch)$#', $t));
}

/**
 * Fin d'un template literal : $p = index de la backtick ouvrante.
 * Retourne l'index qui suit la backtick fermante (ou strlen($s) si non fermee).
 * Gere les ${ ... } imbriques (chaines, templates, commentaires, regex).
 */
function igk_js_template_end($s, $p)
{
    $n = strlen($s);
    $p++;
    while ($p < $n) {
        $p += strcspn($s, '\\`$', $p);
        if ($p >= $n) {
            break;
        }
        $c = $s[$p];
        if ($c === '\\') {
            $p += 2;
        } else if ($c === '`') {
            return $p + 1;
        } else if (($s[$p + 1] ?? '') === '{') {
            $p = igk_js_expr_end($s, $p + 2);
        } else {
            $p++;
        }
    }
    return $n;
}

/** Fin d'une expression ${ ... } : $p = index qui suit "${". Retourne l'index qui suit le "}" fermant. */
function igk_js_expr_end($s, $p)
{
    static $rx = '~\G/(?:\\\\.|\[(?:\\\\.|[^\]\\\\\r\n])*\]|[^/\[\\\\\r\n])+/[dgimsuvy]*~';
    $n = strlen($s);
    $start = $p;
    $d = 1;
    while ($p < $n) {
        $p += strcspn($s, "{}\"'`/", $p);
        if ($p >= $n) {
            break;
        }
        $c = $s[$p];
        if ($c === '{') {
            $d++;
            $p++;
        } else if ($c === '}') {
            if (--$d === 0) {
                return $p + 1;
            }
            $p++;
        } else if ($c === '`') {
            $p = igk_js_template_end($s, $p);
        } else if ($c === '"' || $c === "'") {
            $p++;
            while ($p < $n && $s[$p] !== $c) {
                $p += strcspn($s, $c . '\\', $p);
                if ($p < $n && $s[$p] === '\\') {
                    $p += 2;
                }
            }
            $p++;
        } else { // "/" : commentaire, regex ou division
            $e = $s[$p + 1] ?? '';
            if ($e === '/') {
                $q = strpos($s, "\n", $p);
                $p = $q === false ? $n : $q + 1;
            } else if ($e === '*') {
                $q = strpos($s, '*/', $p + 2);
                $p = $q === false ? $n : $q + 2;
            } else {
                // regex si le dernier caractere significatif n'est pas une fin de valeur
                $q = $p - 1;
                while ($q >= $start && ctype_space($s[$q])) {
                    $q--;
                }
                $lc = $q >= $start ? $s[$q] : '';
                if (($lc === '' || !preg_match('/[\w$)\]\'"`]/', $lc)) && preg_match($rx, $s, $mm, 0, $p)) {
                    $p += strlen($mm[0]);
                } else {
                    $p++;
                }
            }
        }
    }
    return $n;
}

function igk_js_minify($s, $compact = 1, $nocompactchar = ' ')
{
    $count = strlen($s);
    $i = 0;
    $o = "";
    $m = 0;
    $symbolspace = "=;()\{\}+-*:<>[]|, ";
    $symbols = "+-<>=\{\}(),|[]*%";
    $mark = '*';
    $plain = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_$.';
    // detection regex / division
    $rx = '~\G/(?:\\\\.|\[(?:\\\\.|[^\]\\\\\r\n])*\]|[^/\[\\\\\r\n])+/[dgimsuvy]*~'; // litteral regex (une seule ligne)
    $vrx = '#(?:[\w$)\]"\'`]|\+\+|--)\s*$#'; // le dernier token est une valeur
    $kwrx = '#(?<![\w$.])(?:return|typeof|instanceof|in|of|new|delete|void|throw|case|do|else|yield|await)\s*$#';
    $srx = '~\G(?:"[^"\\\\]*+(?:\\\\[\s\S][^"\\\\]*+)*+"|\'[^\'\\\\]*+(?:\\\\[\s\S][^\'\\\\]*+)*+\')~'; // chaine "..." ou '...'
    $usrx = '~\G(?:\xEF\xBB\xBF|\xC2\xA0|\xE1\x9A\x80|\xE2\x80[\x80-\x8A\xAF]|\xE2\x81\x9F|\xE3\x80\x80|(\xE2\x80[\xA8\xA9]))~'; // blancs Unicode (octets UTF-8)
    // insertion automatique de ";"
    $ctlrx = '#(?<![\w$.])(?:if|for|while|with|switch|catch)\s*$#'; // "(" d'une instruction de controle
    $nl = 0;        // un saut de ligne a ete lu depuis le dernier token
    $pst = [];      // pile des "(" : 1 si "(" de controle
    $ctl = false;   // le dernier token est le ")" d'une instruction de controle
    $rxEnd = false; // le dernier token est un litteral regex
    $rep = 0;       // 1 : $ch remplace l'espace final de $o (evite de recopier $o)
    // NB : les tests sur la fin de $o utilisent substr($o, -N) : une regex ancree
    // en fin de chaine sur $o entier coute O(taille) a chaque appel (temps quadratique).
    while ($i < $count) {
        $ch = $s[$i];
        $i++;
        if ($m == 1) {
            if ($ch == "/") { // single line comment
                $e = strpos($s, IGK_LF, $i);
                if ($e === false) { // commentaire en derniere ligne, sans saut de ligne final
                    $i = $count;
                    $m = 0;
                    continue;
                }
                $i = $e + 1;
                $m = 5; // fin de commentaire : equivaut a un saut de ligne
                $nl = 1;
                continue;
            }
            if ($ch == $mark) { // multi line comment
                $e = strpos($s, '*/', $i);
                if ($e === false) {
                    $cm = substr($s, $i);
                    $i = $count;
                } else {
                    $cm = substr($s, $i, $e - $i);
                    $i = $e + 2;
                }
                if (strpbrk($cm, "\r\n") !== false) {
                    $nl = 1;
                }
                $m = 5; // un commentaire se comporte comme un espace
                continue;
            } else {
                // "/" a la position $i - 2 : division ou litteral regex ?
                $p = $i - 2;
                $t = substr($o, -40);
                if (($t !== '' && preg_match($vrx, $t) && !preg_match($kwrx, $t))
                    || !preg_match($rx, $s, $mm, 0, $p)) {
                    // division : on garde "/" et le caractere suivant est traite normalement
                    $o .= "/";
                    $m = 0;
                    $ctl = false;
                    $rxEnd = false;
                    $nl = 0;
                    $i--;
                    continue;
                }
                $o .= $mm[0];
                $i = $p + strlen($mm[0]);
                $ctl = false;
                $rxEnd = true;
                $nl = 0;
            }
            $ch = "";
        }
        // blancs Unicode hors chaine/commentaire : BOM (U+FEFF), NBSP, U+2000-200A, U+2028/2029...
        // (ex. BOM au debut d'un fichier fusionne) : equivalent a un espace / saut de ligne
        if (($ch === "\xEF" || $ch === "\xC2" || $ch === "\xE1" || $ch === "\xE2" || $ch === "\xE3")
            && preg_match($usrx, $s, $mm, 0, $i - 1)) {
            $i += strlen($mm[0]) - 1;
            if (isset($mm[1]) && $mm[1] !== '') {
                $nl = 1; // U+2028 / U+2029 : fin de ligne
            }
            $m = 5;
            continue;
        }
        switch ($ch) {
            case "/":
                if (($m % 5) == 0) {
                    $m = 1; // start comment-regex-separator
                }
                break;
            case "\"":
            case "'":
            case "`":
                if (($m % 5) == 0) {
                    if ($nl && $ch !== "`" && igk_js_asi(substr($o, -48), $s, $i - 1, $ctl, $rxEnd)) {
                        if (substr($o, -1) === ' ') {
                            $o[strlen($o) - 1] = ';';
                        } else {
                            $o .= ';';
                        }
                    }
                    if ($ch === "`") { // template literal (${} imbriques geres)
                        $e = igk_js_template_end($s, $i - 1);
                        $o .= substr($s, $i - 1, $e - $i + 1);
                        $i = $e;
                    } else if (preg_match($srx, $s, $mm, 0, $i - 1)) {
                        $o .= $mm[0];
                        $i += strlen($mm[0]) - 1;
                    } else {
                        $i--;
                        $rgx = igk_str_read_brank($s, $i, $ch, $ch, null, 1);
                        $o .= $rgx;
                        $i++;
                    }
                    $m = 4;
                    $ch = "";
                    $ctl = false;
                    $rxEnd = false;
                    $nl = 0;
                }
                break;
            case "\t":
            case IGK_LF:
            case "\r":
                if ($ch !== "\t") {
                    $nl = 1;
                }
                if ($m != 5) {
                    $m = 5;
                }
                break;
            case " ":
                if (($m % 5) == 0) {
                    $lni = strlen($o);
                    $_last = $lni > 0 ? $o[$lni - 1] : '';
                    if ((($lni > 0) && !empty($_last)) && ((!$compact && !empty($nocompactchar) && (strpos($nocompactchar, $_last) === false))
                        || (!empty($symbolspace) && strpos($symbolspace, $_last) === false)))
                        $o .= $ch;
                    $m = 5;
                }
                break;
            default:
                if ($m == 5) {
                    if ($nl && igk_js_asi(substr($o, -48), $s, $i - 1, $ctl, $rxEnd)) {
                        if (substr($o, -1) === ' ') {
                            $o[strlen($o) - 1] = ';';
                        } else {
                            $o .= ';';
                        }
                    } else if ((($lni = strlen($o)) > 0) && (strpos($symbolspace, $o[$lni - 1]) === false))
                        $o .= " ";
                    else if ($compact && ($lni > 0) && ($o[$lni - 1] == ' ') && (strpos($symbols, $ch) !== false)) {
                        $rep = 1; // l'espace final est remplace par $ch
                    } else {
                        $ct = strlen($o);
                        if (($ct > 0) && (strpos("+-", $o[$ct - 1]) !== false))
                            $o .= " ";
                    }
                }
                $m = 0;
                break;
        }
        if ($m == 0) {
            if ($ch !== '') {
                if ($ch === '(') {
                    $pst[] = preg_match($ctlrx, substr($o, -16));
                }
                $ctl = $ch === ')' && array_pop($pst);
                $rxEnd = false;
                $nl = 0;
            }
            if ($rep) {
                $o[strlen($o) - 1] = $ch;
                $rep = 0;
            } else {
                $o .= $ch;
            }
            // suite de caracteres "simples" (identifiants, nombres) : copiee d'un bloc
            if (($k = strspn($s, $plain, $i)) > 0) {
                $o .= substr($s, $i, $k);
                $i += $k;
            }
        }
        if ($m == 4)
            $m = 0;
    }
    if ($m == 1)
        $o .= "/";
    return $o;
}