<?php

use Timber\Timber;



class TimberHelpers
{
  public function ec_get_timber_post($post_id)
  {
    return new TimberPost($post_id);
  }

  public function ec_get_timber_term($term_id)
  {
    var_dump($term_id);
    return new TimberTerm($term_id);
  }

  public function ec_get_page_by_template($template)
  {
    $args = array(
      'post_type'   => 'page',
      'post_status' => 'publish',
      'meta_query'  => array(
        array(
          'key'   => '_wp_page_template',
          'value' => $template,
        ),
      ),
    );

    return Timber::get_post($args);
  }

  public function php_shuffle($shuffle_this)
  {
    $shuffle_it = $shuffle_this;
    shuffle($shuffle_it);

    return $shuffle_it;
  }

  public function ec_remove_protocol($url)
  {
    return preg_replace("(^https?://)", "", $url);
  }

  public function ec_uglify_email($email)
  {

    $alwaysEncode = array('.', ':', '@');

    $result = '';

    // Encode string using oct and hex character codes
    for ($i = 0; $i < strlen($email); $i++) {
      // Encode 25% of characters including several that always should be encoded
      if (in_array($email[$i], $alwaysEncode) || mt_rand(1, 100) < 25) {
        if (mt_rand(0, 1)) {
          $result .= '&#' . ord($email[$i]) . ';';
        } else {
          $result .= '&#x' . dechex(ord($email[$i])) . ';';
        }
      } else {
        $result .= $email[$i];
      }
    }

    return $result;
  }
}

$timber_helpers = new TimberHelpers();
