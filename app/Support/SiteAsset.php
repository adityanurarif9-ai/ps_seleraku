<?php

namespace App\Support;

class SiteAsset
{
    // Inline only repository-owned styles; never fetch a URL or user supplied path.
    public static function styles(string $name): string
    {
        if (! in_array($name, ['login.css', 'pwa.css', 'fonts/poppins-v24.css'], true)) {
            throw new \InvalidArgumentException('Unknown critical stylesheet');
        }
        $relative = ltrim(parse_url(static::url($name), PHP_URL_PATH), '/');
        return file_get_contents(public_path($relative));
    }

    public static function url(string $path): string
    {
        $base = explode('?', $path, 2)[0];
        $files = config('performance-assets.files', []);
        $mapped = $files[$base] ?? null;
        if (! $mapped) {
            return asset($path);
        }
        if (preg_match('/\\.(css|js)$/', $base)) {
            if (! app()->environment('production')) {
                return asset($path);
            }
            $expected = config('performance-assets.source_hashes', [])[$base] ?? null;
            if (! $expected || ! is_file(public_path($base)) || hash('sha256', str_replace("\r\n", "\n", file_get_contents(public_path($base)))) !== $expected) {
                return asset($path);
            }
        }

        return asset($mapped);
    }
}
