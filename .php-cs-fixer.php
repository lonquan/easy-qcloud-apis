<?php

$config = new PhpCsFixer\Config();
$config->setRules([
    'declare_strict_types' => true,
    'blank_line_after_opening_tag' => true,
    'single_blank_line_at_eof' => true,
    'no_closing_tag' => true,
]);

return $config;
