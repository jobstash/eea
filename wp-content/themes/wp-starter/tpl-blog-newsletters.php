<?php
/* Template Name: Newsletters Category Page */

$context = Timber::context();
$context['post'] = Timber::get_post();
$context['title'] = get_the_title();

// Pagination (ensure $paged is defined)
$paged = get_query_var('paged') ? (int) get_query_var('paged') : 1;

// Optional: allow an embed (e.g., Beehiiv) to be configured via ACF.
// Field can exist either on the page itself or as an ACF "Options" field.
$context['newsletter_signup_html'] = (
  function_exists('get_field')
    ? (get_field('newsletter_signup_html', $context['post']->ID) ?: get_field('newsletter_signup_html', 'option'))
    : null
);

// Get all posts from the "newsletters" category
$context['posts'] = Timber::get_posts([
  'post_type'      => 'post',
  'category_name'  => 'newsletters',
  'posts_per_page' => 9,
  'paged'          => $paged,
]);

Timber::render('page-blog.twig', $context);
