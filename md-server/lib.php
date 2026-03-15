<?php

namespace MrClay\MarkdownServer;

use Spatie\YamlFrontMatter\YamlFrontMatter;
use League\CommonMark\CommonMarkConverter;

require_once __DIR__ . '/vendor/autoload.php';

/**
 * @property string $_request_pathname This is prepopulated with the request URL pathname
 * @property string $_public_root_path This is prepopulated with the filesystem path to the public root.
 * @property string|null $title The page title (plain text encoding).
 * @property string|null $description The page's meta description (plain text encoding).
 * @property string|null $sidebar Set to use a different sidebar template (e.g. "about" => sidebar-about.php).
 * @property string|null $file If given a path (relative to md-server/files), the Markdown is ignored, and instead
 *                             the file is included using PHP include() and the output is placed in the page.
 * @property true|null $is_home Set to true for the home page
 * @property true|null $increase_headings If true, the Markdown headings like ### will be increased by one level.
 * @property string|null $body_class Adds CSS classname(s) to the body element.
 * @property string|null $main_class Adds CSS classname(s) to the main element.
 */
#[\AllowDynamicProperties]
class Meta
{
    public function __get($name)
    {
        return null;
    }
}

/**
 * Get HTML for main content.
 */
function content($newContent = null): string
{
    static $content;
    if ($newContent !== null) {
        $content = $newContent;
    }
    return $content;
}

/**
 * Get page metadata.
 */
function meta(): Meta
{
    static $meta = null;
    if ($meta === null) {
        $meta = new Meta();
    }
    return $meta;
}

function findMatchingMarkdownFile($requestUri)
{
    if (str_ends_with($requestUri, '/')) {
        $requestUri .= 'index';
    }

    // 404 if the found markdown file is not in the pages directory.
    $pagesDir = realpath(__DIR__ . "/pages");
    $candidateFile = realpath($pagesDir . $requestUri . '.md');

    return ($candidateFile && str_starts_with($candidateFile, $pagesDir)) ? $candidateFile : null;
}

function serveMarkdownFile($candidateFile, $requestUri, $publicRootPath)
{
    $object = YamlFrontMatter::parse(file_get_contents($candidateFile));
    $meta = meta();
    foreach ($object->matter() as $key => $item) {
        $meta->$key = $item;
    }
    $meta->_request_pathname = $requestUri;
    $meta->_public_root_path = $publicRootPath;

    if (!empty($meta->file)) {
        // Just include a file.
        ob_start();
        include __DIR__ . "/files/{$meta->file}";
        $content = ob_get_clean();
    } else {
        // We'll convert the markdown to HTML.
        $md = $object->body();
        $lines = explode("\n", $md);

        if ($meta->increase_headings === true) {
            $callback = function ($line) {
                if (!preg_match('~^#+ ~', $line, $m)) {
                    return $line;
                }
                return "#" . $line;
            };
            $lines = array_map($callback, $lines);
        }

        $converter = new CommonMarkConverter();
        $content = $converter->convert(implode("\n", $lines));
    }

    // An example of how to insert dynamic content in the middle of the markdown
    if (str_contains($content, '<p>{{EXAMPLE_REPLACEMENT}}</p>')) {
        ob_start();
        include __DIR__ . '/files/example-replacement.php';
        $content = str_replace('<p>{{EXAMPLE_REPLACEMENT}}</p>', ob_get_clean(), $content);
    }

    // Set content for templates
    content($content);

    include __DIR__ . '/templates/page.php';
}

function getMarkdownSitemapUrls(string $baseUrl): array
{
    $pagesDir = __DIR__ . '/pages';

    // Recursively scan for all .md files
    $iterator = new \RecursiveIteratorIterator(
        new \RecursiveDirectoryIterator($pagesDir, \RecursiveDirectoryIterator::SKIP_DOTS),
        \RecursiveIteratorIterator::SELF_FIRST
    );

    $urls = [];

    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'md') {
            $relativePath = substr($file->getPathname(), strlen($pagesDir));

            // Convert file path to URL
            // Remove the .md extension and create a URL path
            $urlPath = substr($relativePath, 0, -3);

            // Handle index files - they map to their directory
            if (str_ends_with($urlPath, '/index')) {
                // "/foo/bar/index" => "/foo/bar/"
                $urlPath = substr($urlPath, 0, -5);
            }

            // Ensure leading slash
            if (!str_starts_with($urlPath, '/')) {
                $urlPath = '/' . $urlPath;
            }

            $url = lib . phprtrim($baseUrl, '/') . $urlPath;

            // Try to get the last modified date from the file
            $lastmod = date('c', filemtime($file->getPathname()));

            $urls[] = [
                'loc' => $url,
                'lastmod' => $lastmod,
                'changefreq' => 'weekly',
                'priority' => ($urlPath === '/' ? '1.0' : '0.8')
            ];
        }
    }

    // Sort URLs for consistency
    usort($urls, function ($a, $b) {
        return strcmp($a['loc'], $b['loc']);
    });

    return $urls;
}
