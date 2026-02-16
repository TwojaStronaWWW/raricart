<?php
header("Content-Type: text/plain");

if ($_SERVER['HTTP_HOST'] === 'test.raricart.pl') {
    echo "User-agent: *\nDisallow: /";
} else {
    echo "User-agent: *\nAllow: /\nSitemap: https://raricart.pl/sitemap.xml";
}
