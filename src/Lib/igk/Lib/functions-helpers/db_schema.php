<?php
// @author: C.A.D. BONDJE DOUE
// @filename: db_schema.php
// @date: 20260513 16:22:25
// @desc: 

/**
 * 
 * @param string $expression 
 * @return array{type: null|string, size: null|int, auto_increment: bool, is_primary: bool, foreign_key: null|string, not_null: null, default: null} 
 */
function igk_db_schema_parse(string $expression)
{
    $def = igk_db_schema_table_column_blueprint();
    $mark = [
        "auto_increment" => function (&$def) {
            $def['auto_increment'] = true;
            return true;
        },
        'primary' => function (&$def, &$s) {
            $def['is_primary'] = true;
            return true;
        },
        'not null' => function (&$def) {
            $def['not_null'] = true;
            return true;
        },
        'index' => function (&$def) {
            $def['index'] = true;
            return true;
        },
        'unique' => function (&$def) {
            $def['unique'] = true;
            return true;
        }
    ];
    igk_db_schema_parse_load($expression, $def, $mark);
    return $def;
}
/**
 * 
 * @param string $expression 
 * @param array &$def 
 * @param array $mark 
 * @return void 
 */
function igk_db_schema_parse_load(string $expression, array &$def, array $mark)
{
    $ln = strlen($expression);
    $pos = 0;
    $s = '';
    $litteral = false;
    $escaped = false;
    while ($pos < $ln) {
        $ch = $expression[$pos];
        if ($litteral) {
            if ($escaped) {
                $escaped = false;
            } else {
                if ($ch == "\\") {
                    $escaped = true;
                } else {
                    if ($ch == "\"") {
                        $litteral = false;
                    }
                }
            }
        } else {
            if ($ch == "\"") {
                $litteral = true;
            } else {
                igk_db_schema_parse_load_def($s, $def, $mark);
            }
        }
        $s .= $ch;
        $pos++;
    }
    if (!empty($s)) {
        igk_db_schema_parse_load_def($s, $def, $mark);
    }
}
/**
 * 
 * @param mixed $s 
 * @param mixed &$def 
 * @param mixed $mark 
 * @return void 
 */
function igk_db_schema_parse_load_def(string &$s, &$def, $mark)
{
    if (preg_match('/^default\\s*\(/', $s, $match)) {
        if (false !== ($c = strrpos($s, ')'))) {
            if (false !== ($l = strrpos($s, '"'))) {
                if ($l > $c) {
                    return;
                }
            }
            $v = substr($s, strlen($match[0]), -1);
            $s = substr($s, $c + 1);
            $def['default'] = is_numeric($v) ? floatval($v) : igk_str_remove_quote(trim($v));
        }
        return;
    }

    if (!isset($def['type']) && preg_match('/^(?P<t>\\w+)\\s*\(\\s*(?P<s>[\\d ]+)?\)/', $s, $tab)) {
        $def['type'] = $tab['t'];
        $def['size'] = isset($tab['s']) ? intval($tab['s']) : 9;
        $s = substr($s, strlen($tab[0]));
        return;
    }
    if ($fc = isset($mark[$s]) ? $mark[$s] : null) {
        if ($fc($def, $s)) {
            $s = '';
        }
    }
}
/**
 * 
 * @param null|string $list 
 * @return array|array<string|int, mixed> 
 */
function igk_db_schema_tables_from_litteral(?string $list)
{
    if (empty($list)) {
        return [];
    }
    $l = [];
    $s = '';
    $ln = strlen($list);
    $c = 0;
    $mark = [
        '{' => function ($inf) {
            $inf->tablename = $inf->s;
            $d = ['::parent' => &$inf->l];
            $inf->l[$inf->tablename] = &$d;
            $inf->l = &$d; // change pointer 
            $inf->s = '';
            $inf->mode = 0;
            $inf->data = [];
        },
        '}' => function ($inf) {
            if ($inf->mode == 0) {
                igk_db_schema_parse_append($inf);
            }
            $d = &$inf->l;
            $l = &$d['::parent'];
            unset($d['::parent']);
            $inf->l = &$l; // change pointer 
            $inf->s = '';
            $inf->mode = 0;
            $inf->data = [];
        },
        '[' => function ($inf) {
            $inf->name = $inf->s;
            $inf->s = '';
            $inf->mode = 1;
            $inf->data = [];
        },
        ']' => function ($inf) {
            if (!empty($inf->s)) {
                $inf->data = igk_db_schema_parse($inf->s);
                $inf->s = '';
            }
            if (!empty($inf->data)) {
                $n = trim($inf->name);
                $inf->l[$n] = $inf->data;
                $inf->data = [];
            } else
                $inf->l[] = $inf->name;
            $inf->mode = 0;
        },
        ',' => function ($inf) {
            if ($inf->mode == 0) {
                igk_db_schema_parse_append($inf);
                $inf->name = null;
                $inf->s = null;
            }
        }
    ];
    $ch = '';
    $inf = (object)['name' => null, 'data' => null, 'mode' => 0, 'l' => &$l, 's' => &$s, 'ch' => &$ch];
    while ($c < $ln) {
        $ch = $list[$c];
        if ($fc = isset($mark[$ch]) ? $mark[$ch] : null) {
            $fc($inf);
            $ch = '';
        }
        $s .= $ch;
        $c++;
    }
    igk_db_schema_parse_append($inf);
    return empty($l) ? array_map('trim', explode(',', $list)) : $l;
}
/**
 * 
 * @param mixed $inf 
 * @return void 
 */
function igk_db_schema_parse_append($inf)
{
    if (!empty($inf->s)) {
        $inf->l[] = trim($inf->s);
    }
}
/**
 * init table defintion blue print
 * @return array{type: ?string, size: ?int, auto_increment: bool, is_primary: bool, foreign_key: ?string, not_null: null, default: null} 
 */
function igk_db_schema_table_column_blueprint()
{
    return [
        'type' => null,
        'size' => null,
        'auto_increment' => false,
        'is_primary' => null,
        'foreign_key' => null,
        'not_null' => false,
        'unique' => false,
        'index' => null,
        'unique_columns' => null,
        'default' => null
    ];
}

/**
 * 
 * @param mixed $cl column info 
 * @param mixed $def index
 * @return void 
 */
function igk_db_schema_load_column_info($cl, $def)
{
    if ($def['auto_increment']) {
        $cl["clAutoIncrement"] = true;
        $cl["clIsPrimary"] = true;
    } else {
        if ($def['is_primary']) {
            $cl["clIsPrimary"] = true;
        }
    }
    $cl["clNotNull"] = $def['not_null'];
    if ($def['unique'])
        $cl["clIsUnique"] = true;
    $cl["clIndex"] = $def['index'];

    $cl["clType"] = $def['type'];
    if ($r = $def['size']) {
        $cl['clTypeLength'] = $r;
    }
}
