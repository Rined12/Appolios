<?php
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "OPcache reset successfully.\n";
} else {
    echo "OPcache is not enabled or function does not exist.\n";
}
