<?php
require_once 'vendor/autoload.php';
use nochso\Omni\PhpCsFixer\PrettyPSR;
return PrettyPSR::createIn(['./src', './tests']);
