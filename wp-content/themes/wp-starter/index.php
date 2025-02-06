<?php

use Timber\Timber;

$context          = Timber::context();
$context['posts'] = Timber::get_posts();
$templates        = array('index.twig');

if (is_home()) {
  array_unshift($templates, 'home.twig', 'page.twig');
}
Timber::render($templates, $context);
