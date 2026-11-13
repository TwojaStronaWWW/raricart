<?php
declare(strict_types=1);

header("Content-Type: text/plain; charset=UTF-8");

$host = strtolower($_SERVER['HTTP_HOST'] ?? '');

if ($host === 'test.raricart.pl') {
    echo "User-agent: *\nDisallow: /";
} else {
    echo "User-agent: *\nAllow: /\nAllow: /llms.txt\nDisallow: /admin/\nDisallow: /api/\nDisallow: /drafts/\nDisallow: /bin/\nDisallow: /config/\nSitemap: https://raricart.pl/sitemap.xml";
}
