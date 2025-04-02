<?php
$context         = Timber::context();
$timber_post = $context['post'];

if (post_password_required($timber_post->ID)) {
  Timber::render('single-password.twig', $context);
} else {
  $templates = array('single-' . $timber_post->ID . '.twig', 'single-' . $timber_post->post_type . '.twig', 'single-' . $timber_post->slug . '.twig', 'single.twig');
  Timber::render($templates, $context);
}
