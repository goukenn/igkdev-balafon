<?php
// @author: C.A.D. BONDJE DOUE
// @file: AccountManager.php
// @date: 20260730 08:48:33
// @desc: GDPR / RGPD of the platform: which projects hold user data, and the data export (zip prepared by the core, kept 30 days)
namespace IGK\System\RGPD;

use IGK\Models\Users;

/**
 * Every project that handles user data registers three hooks:
 *  - HOOK_LIST_PROVIDERS (filter): pushes ['id', 'title', 'description', 'data' => [labels]] on `$e->output`, shown on the
 *    account page ("deleting your data also concerns these projects"). Erasure itself goes through HOOK_USER_CLEAN.
 *  - HOOK_COLLECT: receives ['user' => Users, 'collector' => ExportCollector] and adds its files.
 *  - HOOK_EXPORT_READY (optional): receives ['user', 'request'] to warn the user (e-mail).
 * An export is asked with RequestExport() (HOOK_EXPORT_REQUESTED tells the projects), built later by ProcessPending()
 * (cron / `balafon --run`), downloadable until its expiry (LIFETIME_DAYS), then purged by PurgeExpired().
 * Files live in <application data>/rgpd/<user guid>/ (request.json + the zip).
 * @package IGK\System\RGPD
 */
class AccountManager
{
    const BASE_HOOK = 'sys://rgpd-hook';
    const HOOK_REQUEST_REMOVE_ACCOUNT = self::BASE_HOOK . '/remove-account';
    const HOOK_LIST_PROVIDERS = self::BASE_HOOK . '/providers';
    const HOOK_EXPORT_REQUESTED = self::BASE_HOOK . '/export-requested';
    const HOOK_COLLECT = self::BASE_HOOK . '/collect';
    const HOOK_EXPORT_READY = self::BASE_HOOK . '/export-ready';

    const LIFETIME_DAYS = 30;
    const STATUS_PENDING = 'pending';
    const STATUS_READY = 'ready';
    const STATUS_EXPIRED = 'expired';

    /** @var string|null overrides the storage folder (tests) */
    public static $baseDir = null;

    public static function BaseDir(): string
    {
        return rtrim(self::$baseDir ?? (igk_io_applicationdatadir() . DIRECTORY_SEPARATOR . 'rgpd'), '/\\');
    }

    /** folder of a user; the guid is checked so it cannot point elsewhere */
    public static function UserDir(string $guid): string
    {
        if (!preg_match('/^[0-9A-Za-z{}-]{8,64}$/', $guid)) {
            throw new \InvalidArgumentException('invalid user guid');
        }
        return self::BaseDir() . DIRECTORY_SEPARATOR . trim($guid, '{}');
    }

    /**
     * projects that hold data of the user
     * @return array<int, array{id:string,title:string,description:string,data:string[]}>
     */
    public static function Providers(): array
    {
        $r = [];
        $items = igk_hook(self::HOOK_LIST_PROVIDERS, ['filter' => []], ['output' => []]);
        foreach (is_array($items) ? $items : [] as $i) {
            if (!is_array($i) || empty($i['id']) || empty($i['title'])) {
                continue;
            }
            $r[(string) $i['id']] = [
                'id' => (string) $i['id'],
                'title' => (string) $i['title'],
                'description' => (string) ($i['description'] ?? ''),
                'data' => array_values(array_map('strval', (array) ($i['data'] ?? []))),
            ];
        }
        uasort($r, function ($a, $b) { return strcasecmp($a['title'], $b['title']); });
        return array_values($r);
    }

    /**
     * ask for an export. A pending or still valid export is reused (nothing is asked twice).
     * @param mixed $user Users model
     * @param array $meta kept in the request (`meta`) for the projects: the page url to link in the e-mail...
     * @return array the request
     */
    public static function RequestExport($user, ?int $now = null, array $meta = []): array
    {
        $guid = (string) $user->clGuid;
        $cur = self::Status($guid, $now);
        if ($cur && in_array($cur['status'], [self::STATUS_PENDING, self::STATUS_READY], true)) {
            return $cur;
        }
        $req = [
            'guid' => $guid,
            'login' => (string) $user->clLogin,
            'status' => self::STATUS_PENDING,
            'requested_at' => date('c', $now ?? time()),
            'ready_at' => null,
            'expires_at' => null,
            'file' => null,
            'meta' => $meta,
        ];
        self::Write($guid, $req);
        igk_hook(self::HOOK_EXPORT_REQUESTED, ['user' => $user, 'request' => $req]);
        return $req;
    }

    /**
     * the request of a user, its status brought up to date (a ready export past its expiry is `expired`)
     */
    public static function Status(string $guid, ?int $now = null): ?array
    {
        $f = self::UserDir($guid) . DIRECTORY_SEPARATOR . 'request.json';
        if (!is_file($f) || !is_array($req = json_decode((string) file_get_contents($f), true))) {
            return null;
        }
        if (($req['status'] ?? '') === self::STATUS_READY && strtotime((string) $req['expires_at']) <= ($now ?? time())) {
            $req['status'] = self::STATUS_EXPIRED;
        }
        return $req;
    }

    /** path of the zip when it can be downloaded now, else null */
    public static function ZipPath(string $guid, ?int $now = null): ?string
    {
        $req = self::Status($guid, $now);
        if (!$req || $req['status'] !== self::STATUS_READY || empty($req['file'])) {
            return null;
        }
        $p = self::UserDir($guid) . DIRECTORY_SEPARATOR . basename((string) $req['file']);
        return is_file($p) ? $p : null;
    }

    /**
     * build the zip of every pending request
     * @param callable|null $resolver fn(string $guid): ?Users, default looks the user up in the database
     * @return int number of exports built
     */
    public static function ProcessPending(?callable $resolver = null, ?int $now = null): int
    {
        $resolver = $resolver ?? function (string $guid) {
            return Users::select_row([Users::FD_CL_GUID => $guid]);
        };
        $n = 0;
        foreach (glob(self::BaseDir() . DIRECTORY_SEPARATOR . '*' . DIRECTORY_SEPARATOR . 'request.json') ?: [] as $f) {
            $req = json_decode((string) file_get_contents($f), true);
            if (!is_array($req) || ($req['status'] ?? '') !== self::STATUS_PENDING) {
                continue;
            }
            $user = $resolver((string) $req['guid']);
            if (!$user) {
                continue;
            }
            $collector = new ExportCollector();
            $collector->add('README.txt', "Export of the personal data of " . $req['login'] . "\nrequested " . $req['requested_at'] . "\nvalid " . self::LIFETIME_DAYS . " days\n");
            igk_hook(self::HOOK_COLLECT, ['user' => $user, 'collector' => $collector]);
            $name = 'export-' . bin2hex(random_bytes(8)) . '.zip';
            $zip = new \ZipArchive();
            $dir = dirname($f);
            if ($zip->open($dir . DIRECTORY_SEPARATOR . $name, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
                continue;
            }
            $collector->writeTo($zip);
            $zip->close();
            $t = $now ?? time();
            $req['status'] = self::STATUS_READY;
            $req['ready_at'] = date('c', $t);
            $req['expires_at'] = date('c', $t + self::LIFETIME_DAYS * 86400);
            $req['file'] = $name;
            self::Write((string) $req['guid'], $req);
            igk_hook(self::HOOK_EXPORT_READY, ['user' => $user, 'request' => $req]);
            $n++;
        }
        return $n;
    }

    /**
     * delete the zip of every expired export
     * @return int number purged
     */
    public static function PurgeExpired(?int $now = null): int
    {
        $n = 0;
        foreach (glob(self::BaseDir() . DIRECTORY_SEPARATOR . '*' . DIRECTORY_SEPARATOR . 'request.json') ?: [] as $f) {
            $req = json_decode((string) file_get_contents($f), true);
            if (!is_array($req) || ($req['status'] ?? '') !== self::STATUS_READY || strtotime((string) $req['expires_at']) > ($now ?? time())) {
                continue;
            }
            $dir = dirname($f);
            if (!empty($req['file'])) {
                @unlink($dir . DIRECTORY_SEPARATOR . basename((string) $req['file']));
            }
            $req['status'] = self::STATUS_EXPIRED;
            $req['file'] = null;
            self::Write((string) $req['guid'], $req);
            $n++;
        }
        return $n;
    }

    /** remove everything kept for a user (account erased) */
    public static function Forget(string $guid): void
    {
        $dir = self::UserDir($guid);
        if (!is_dir($dir)) {
            return;
        }
        foreach (glob($dir . DIRECTORY_SEPARATOR . '*') ?: [] as $f) {
            is_file($f) && @unlink($f);
        }
        @rmdir($dir);
    }

    private static function Write(string $guid, array $req): void
    {
        $dir = self::UserDir($guid);
        is_dir($dir) || mkdir($dir, 0750, true);
        file_put_contents($dir . DIRECTORY_SEPARATOR . 'request.json', json_encode($req, JSON_PRETTY_PRINT));
    }
}
