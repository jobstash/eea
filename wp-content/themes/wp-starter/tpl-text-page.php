<?php

/**
 * Template Name: Text Page
 * Description: A custom template for displaying text pages.
 */

use Timber\Timber;

$context = Timber::context();

$timber_post     = $context['post'];
Timber::render(array('page-text.twig'), $context);
