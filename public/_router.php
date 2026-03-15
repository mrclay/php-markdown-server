<?php
// Handle requests for missing files. If the URL path matches a file in the
// pages directory, then serve the content as HTML.

use function MrClay\MarkdownServer\findMatchingMarkdownFile;
use function MrClay\MarkdownServer\serveMarkdownFile;

require_once dirname(__DIR__) . '/md-server/lib.php';

$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$mdFile = findMatchingMarkdownFile($requestUri);
if (!$mdFile) {
    header("HTTP/1.0 404 Not Found");
    echo "<h1>404 - Page Not Found</h1>";
    exit;
}

serveMarkdownFile($mdFile, $requestUri, __DIR__);
