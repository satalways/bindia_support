<?php

$fromPath = realpath(__DIR__ . '/../support_project/storage/app/public');
$toPath = __DIR__ . '/storage';

try {
    if (symlink($fromPath, $toPath)) {
        echo 'OK';
    } else {
        echo 'Symlink not done.';
    }
} catch (Exception $e) {
    echo $e->getMessage();
}