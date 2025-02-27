<?php
/* Template Name: Blog Category Page */
$context = Timber::context();
$context['post'] = Timber::get_post();
$context['title'] = get_the_title();


// Get all posts from the "blog" category
$context['posts'] = Timber::get_posts([
  'post_type' => 'post',
  'category_name' => 'blog',
  'posts_per_page' => 9, // or specify a number like 10
  'paged'          => $paged,
]);

Timber::render('page-blog.twig', $context);