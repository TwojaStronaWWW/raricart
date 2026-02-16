<?php
header("Content-Type: text/plain");

$host = strtolower($_SERVER['HTTP_HOST']);

if (strpos($host, 'test.raricart.pl') !== false) {
    echo "User-agent: *\nDisallow: /";
} else {
    echo "User-agent: *\nAllow: /\nSitemap: https://raricart.pl/sitemap.xml";
}
