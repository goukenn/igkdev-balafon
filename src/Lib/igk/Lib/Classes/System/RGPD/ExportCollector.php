<?php
// @author: C.A.D. BONDJE DOUE
// @file: ExportCollector.php
// @desc: what the projects hand over for a user's data export; the core zips it (see AccountManager)
namespace IGK\System\RGPD;

/**
 * A project registered on AccountManager::HOOK_COLLECT adds its files here: `$collector->add('my-project/data.json', $array)`.
 * Paths are relative to the zip and never leave it.
 * @package IGK\System\RGPD
 */
class ExportCollector
{
    /** @var array<string, string> zip path => content */
    private $m_contents = [];
    /** @var array<string, string> zip path => local file */
    private $m_files = [];

    /**
     * add a file made of a value (arrays and objects are written as json)
     * @param string $path path in the zip
     * @param mixed $data
     */
    public function add(string $path, $data): self
    {
        $this->m_contents[self::Clean($path)] = is_string($data)
            ? $data
            : json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PARTIAL_OUTPUT_ON_ERROR);
        return $this;
    }

    /** add a file of the disk (an uploaded document...) */
    public function addFile(string $path, string $localFile): self
    {
        if (is_file($localFile)) {
            $this->m_files[self::Clean($path)] = $localFile;
        }
        return $this;
    }

    /** @return string[] zip paths in the collection */
    public function paths(): array
    {
        return array_keys($this->m_contents + $this->m_files);
    }

    /** write every collected entry into an opened zip */
    public function writeTo(\ZipArchive $zip): int
    {
        foreach ($this->m_contents as $p => $c) {
            $zip->addFromString($p, $c);
        }
        foreach ($this->m_files as $p => $f) {
            $zip->addFile($f, $p);
        }
        return count($this->m_contents) + count($this->m_files);
    }

    /** a zip path: forward slashes, no `..`, no drive or leading slash */
    public static function Clean(string $path): string
    {
        $parts = [];
        foreach (preg_split('#[\\\\/]+#', $path) as $s) {
            if ($s === '' || $s === '.' || $s === '..' || strpos($s, ':') !== false) {
                continue;
            }
            $parts[] = $s;
        }
        return $parts ? implode('/', $parts) : 'file';
    }
}
