<?php
// $Id: unicode.inc,v 1.29.2.1 2010/06/30 09:04:38 goba Exp $

/**
 * Truncate a UTF-8-encoded string safely to a number of characters.
 *
 * @param $string
 *   The string to truncate.
 * @param $len
 *   An upper limit on the returned string length.
 * @param $wordsafe
 *   Flag to truncate at last space within the upper limit. Defaults to FALSE.
 * @param $dots
 *   Flag to add trailing dots. Defaults to FALSE.
 * @return
 *   The truncated string.
 */
function truncate_utf8($string, $len, $wordsafe = FALSE, $dots = FALSE) {

  if (drupal_strlen($string) <= $len) {
    return $string;
  }

  if ($dots) {
    $len -= 4;
  }

  if ($wordsafe) {
    $string = drupal_substr($string, 0, $len + 1); // leave one more character
    if ($last_space = strrpos($string, ' ')) { // space exists AND is not on position 0
      $string = substr($string, 0, $last_space);
    }
    else {
      $string = drupal_substr($string, 0, $len);
    }
  }
  else {
    $string = drupal_substr($string, 0, $len);
  }

  if ($dots) {
    $string .= ' ...';
  }

  return $string;
}

/**
 * Count the amount of characters in a UTF-8 string. This is less than or
 * equal to the byte count.
 */
function drupal_strlen($text) {
  // La vérification de `$multibyte` a été supprimée.
  
  // Do not count UTF-8 continuation bytes.
  return strlen(preg_replace("/[\x80-\xBF]/", '', $text));
}

/**
 * Cut off a piece of a string based on character indices and counts. Follows
 * the same behavior as PHP's own substr() function.
 *
 * Note that for cutting off a string at a known character/substring
 * location, the usage of PHP's normal strpos/substr is safe and
 * much faster.
 */
function drupal_substr($text, $start, $length = NULL) {
  // La vérification de `$multibyte` a été supprimée.
  
  $strlen = strlen($text);
  // Find the starting byte offset
  $bytes = 0;
  if ($start > 0) {
  // Count all the continuation bytes from the start until we have found
  // $start characters
  $bytes = -1; $chars = -1;
  while ($bytes < $strlen && $chars < $start) {
    $bytes++;
    $c = ord($text[$bytes]);
    if ($c < 0x80 || $c >= 0xC0) {
      $chars++;
    }
  }
  }
  else if ($start < 0) {
  // Count all the continuation bytes from the end until we have found
  // abs($start) characters
  $start = abs($start);
  $bytes = $strlen; $chars = 0;
  while ($bytes > 0 && $chars < $start) {
    $bytes--;
    $c = ord($text[$bytes]);
    if ($c < 0x80 || $c >= 0xC0) {
      $chars++;
    }
  }
  }
  $istart = $bytes;

  // Find the ending byte offset
  if ($length === NULL) {
  $bytes = $strlen - 1;
  }
  else if ($length > 0) {
  // Count all the continuation bytes from the starting index until we have
  // found $length + 1 characters. Then backtrack one byte.
  $bytes = $istart; $chars = 0;
  while ($bytes < $strlen && $chars < $length) {
    $bytes++;
    $c = ord($text[$bytes]);
    if ($c < 0x80 || $c >= 0xC0) {
      $chars++;
    }
  }
  $bytes--;
  }
  else if ($length < 0) {
  // Count all the continuation bytes from the end until we have found
  // abs($length) characters
  $length = abs($length);
  $bytes = $strlen - 1; $chars = 0;
  while ($bytes >= 0 && $chars < $length) {
    $c = ord($text[$bytes]);
    if ($c < 0x80 || $c >= 0xC0) {
      $chars++;
    }
    $bytes--;
  }
  }
  $iend = $bytes;

  return substr($text, $istart, max(0, $iend - $istart + 1));
}
?>
