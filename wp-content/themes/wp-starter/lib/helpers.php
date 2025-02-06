<?php

require_once 'helpers/log_helper.php';
require_once 'helpers/model_helper.php';
// require_once 'helpers/timber_helper.php';

/**
 * Get cache-busting hashed filename from assets.json.
 *
 * @param string $filename Original name of the file.
 *
 * @return string Current cache-busting hashed name of the file.
 */
function get_hashed_asset($filename)
{

  // Cache the decoded manifest so that we only read it in once.
  static $manifest = null;
  if (null === $manifest) {
    $manifest_path = get_template_directory() . '/dist/manifest.json';
    $manifest      = file_exists($manifest_path)
      ? json_decode(file_get_contents($manifest_path), true)
      : [];
  }


  // If the manifest contains the requested file, return the hashed name.

  if (array_key_exists($filename, $manifest)) {
    return $manifest[$filename]['file'];
  }

  // Assume the file has not been hashed when it was not found within the manifest.
  return $filename;
}

// Get hashed file path
function get_asset_path($filename)
{
  $hashed_asset_path = get_theme_file_uri() . '/dist/' . get_hashed_asset($filename);

  // Assume the file has not been hashed when it was not found within the manifest.
  return $hashed_asset_path;
}
