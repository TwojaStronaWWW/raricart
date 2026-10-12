<?php
header("Content-Type: text/plain");

$host = strtolower($_SERVER['HTTP_HOST'] ?? '');

if ($host === 'test.raricart.pl') {
    echo "User-agent: *\nDisallow: /";
} else {
    echo "User-agent: *\nAllow: /\nDisallow: /admin/\nDisallow: /api/\nDisallow: /drafts/\nDisallow: /bin/\nDisallow: /config/\nSitemap: https://raricart.pl/sitemap.xml";
}
