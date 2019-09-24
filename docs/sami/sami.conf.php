<?php

use Sami\Sami;
use Symfony\Component\Finder\Finder;

$srcdirs = ['src', 'packages'];
$srcdirs = array_map(function ($p) {
  return __DIR__ . "/../../" . $p;
}, $srcdirs);

$iterator = Finder::create()
  ->files()
  ->name("*.php")
  ->exclude("Tests")
  ->exclude("Resources")
  ->exclude("Behat")
  ->exclude("vendor")
  ->in($srcdirs);

return new Sami($iterator, array(
  'theme'     => 'default',
  'title'     => 'Korowai Framework API',
  'build_dir' => __DIR__ . '/../build/html/api',
  'cache_dir' => __DIR__ . '/../cache/html/api'
));
