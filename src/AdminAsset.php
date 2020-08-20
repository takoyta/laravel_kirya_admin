<?php declare(strict_types=1);

namespace KiryaDev\Admin;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

final class AdminAsset
{
    private const CACHE_DIR = 'admin-asset-cache';
    private const CSS_URL_PATTERN = <<<REGEXP
/url\((?!['"]?(?:data|http):)['"]?([^'"\)]*)['"]?\)/
REGEXP;

    private array $assets = [];

    public function __construct()
    {
        // Font
        $this->addStylesheet('https://fonts.googleapis.com/css?family=Montserrat', 'montserrat-font.css');

        // Bootstrap
        $this->addStylesheet('https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/css/bootstrap.min.css');

        // Font Awesome
        $this->addStylesheet('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.8.1/css/all.min.css');

        // Select2 (custom styles)
        $this->addStylesheet(dirname(__DIR__) . '/resources/assets/styles/select2.css');

        // Admin stylesheets
        $this->addStylesheet(dirname(__DIR__) . '/resources/assets/styles/stylesheets.css');

        // JQuery
        $this->addScript('https://code.jquery.com/jquery-3.3.1.min.js');
        $this->addScript('https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/js/bootstrap.bundle.js');
        $this->addScript('https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js');

        // $.datetimepicker
        $this->addStylesheet('https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.min.css');
        $this->addScript('https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.full.min.js');

        // include summernote css/js (require twitter bootstrap)
        $this->addStylesheet('https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.11/summernote-bs4.css');
        $this->addScript('https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.11/summernote-bs4.js');

        // Admin scripts
        $this->addScript(dirname(__DIR__) . '/resources/assets/scripts/admin.js');
    }

    public function addStylesheet(string $link, string $filename = null): void
    {
        $this->assets['stylesheets'][] = $this->resolveAsset($link, $filename, true);
    }

    public function addScript(string $src, string $filename = null): void
    {
        $this->assets['scripts'][] = $this->resolveAsset($src, $filename);
    }

    public function getAssets(string $type): array
    {
        return $this->assets[$type];
    }

    public function cleanCache(): void
    {
        $this->getStorage()->deleteDirectory(self::CACHE_DIR);
    }

    private function resolveAsset(string $path, string $filename = null, bool $isStyles = false): string
    {
        $cachePath = self::CACHE_DIR . '/' . substr(md5($path), 0, 6) . '_' . ($filename ?? $this->resolveFilename($path));

        $storage = $this->getStorage();
        if (!$storage->exists($cachePath)) {
            $content = $this->fetchAsset($path);
            if ($isStyles) {
                $content = $this->postProcessStyles($content, $path);
            }
            $storage->put($cachePath, $content);
        }

        return '/storage/' . $cachePath;
    }

    private function fetchAsset(string $path): string
    {
        if ($this->isUrl($path)) {
            return (string)file_get_contents($path, false, $this->getStreamContextWithBrowserUserAgent());
        }

        return (string)file_get_contents($path);
    }

    private function postProcessStyles(string $content, string $currentPath): string
    {
        $currentDir = dirname($currentPath);
        return preg_replace_callback(self::CSS_URL_PATTERN, function ($matches) use ($currentDir) {
            $origUrl = trim($matches[1]);

            $url = $this->isUrl($origUrl) ? $origUrl : ($currentDir . '/' . $origUrl);
            if (false === $url) {
                throw new \RuntimeException('Cannot resolve url: ' . $origUrl);
            }

            return 'url(' . $this->resolveAsset($url) . ')';
        }, $content);
    }

    private function getStorage()
    {
        return Storage::disk('public');
    }

    private function getStreamContextWithBrowserUserAgent()
    {
        // Google fonts not response correct font file for default header
        // Custom it on GoogleChrome
        $http = [
            'header' => 'user-agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/84.0.4147.105 Safari/537.36'
        ];

        return stream_context_create(compact('http'));
    }

    private function isUrl(string $path): bool
    {
        return str_starts_with($path, 'http://') || str_starts_with($path, 'https://');
    }

    private function resolveFilename(string $path): string
    {
        $filename = Str::before($path, '#');
        $filename = Str::before($filename, '?');
        $filename = Str::afterLast($filename, '/');
        $filename = Str::slug($filename, '.');

        return $filename;
    }
}
