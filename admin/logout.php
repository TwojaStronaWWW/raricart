<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.php';

require_post_method(false);
check_login(true);
check_csrf(false);

destroy_admin_session();

header("Location: index.php");
exit();
