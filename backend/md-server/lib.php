<?php

namespace MrClay\MarkdownServer;

use Spatie\YamlFrontMatter\YamlFrontMatter;
use League\CommonMark\CommonMarkConverter;

require_once __DIR__ . '/vendor/autoload.php';

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

function serveMarkdownFile($candidateFile, $requestUri)
{
    $object = YamlFrontMatter::parse(file_get_contents($candidateFile));
    $meta = (object)$object->matter();
    $meta->_request_uri = $requestUri;

    if (!empty($meta->file)) {
        // Just include a file.
        ob_start();
        include __DIR__ . "/files/{$meta->file}";
        $content = ob_get_clean();
    } else {
        // We'll convert the markdown to HTML.
        $md = $object->body();
        $lines = explode("\n", $md);

        if ($meta->increase_headings ?? false) {
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

    include __DIR__ . '/templates/page.php';
}

function getMarkdownSitemapUrls()
{
    $config = require __DIR__ . '/config.php';

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
                $urlPath = substr($urlPath, 0, -6); // Remove /index
            }

            // Ensure leading slash
            if (!str_starts_with($urlPath, '/')) {
                $urlPath = '/' . $urlPath;
            }

            $urlPath = rtrim($urlPath, '/');

            $url = $config->BASE_URL . $urlPath;

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
