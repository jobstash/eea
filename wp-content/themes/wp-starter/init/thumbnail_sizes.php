<?php

// Remove default image sizes
add_filter('intermediate_image_sizes_advanced', 'prefix_remove_default_images');

function prefix_remove_default_images($sizes)
{
   unset($sizes['thumbnail']);
   unset($sizes['medium']);
   unset($sizes['large']);
   unset($sizes['medium_large']);

   return $sizes;
}

function manage_thumbnails()
{
   // Add thumbnails to the following post types
   add_theme_support('post-thumbnails');

   // Customize thumbnail size
   set_post_thumbnail_size(600, 600, true);

   // Add Custom sizes for responsive images
   add_image_size('mobile-small', 300, 300, false);
   add_image_size('mobile-large', 600, 600, false);
   add_image_size('tablet', 900, 900, false);
   add_image_size('desktop', 1200, 1200, false);
   add_image_size('large-desktop', 1600, 1600, false);

   // Existing custom size
   add_image_size('square', 1000, 1000, true);
}

add_action('after_setup_theme', 'manage_thumbnails');