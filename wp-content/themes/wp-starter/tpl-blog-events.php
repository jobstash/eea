<?php
/* Template Name: Events Category Page */
$context = Timber::context();
$context['post'] = Timber::get_post();
$context['title'] = get_the_title();

// Get all posts from the "blog" category
$context['posts'] = Timber::get_posts([
  'post_type' => 'post',
  'category_name' => 'events',
  'posts_per_page' => 6,
]);

$context['all_events'] = Timber::get_posts([
  'post_type' => 'post',
  'category_name' => 'events',
  'posts_per_page' => -1, // -1 means get ALL posts
]);

Timber::render('page-events.twig', $context);