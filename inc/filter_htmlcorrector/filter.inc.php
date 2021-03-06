<?php
// $Id: filter.module,v 1.204.2.9 2009/08/10 11:04:37 goba Exp $

/**
 * @file
 * Framework for handling filtering of content.
 */

/**
 * Scan input and make sure that all HTML tags are properly closed and nested.
 */
function _filter_htmlcorrector($text) {
  // Prepare tag lists.
  static $no_nesting, $single_use;
  if (!isset($no_nesting)) {
    // Tags which cannot be nested but are typically left unclosed.
    $no_nesting = drupal_map_assoc(array('li', 'p'));

    // Single use tags in HTML4
    $single_use = drupal_map_assoc(array('base', 'meta', 'link', 'hr', 'br', 'param', 'img', 'area', 'input', 'col', 'frame'));
  }

  // Properly entify angles.
  $text = preg_replace('!<([^a-zA-Z/])!', '&lt;\1', $text);

  // Split tags from text.
  $split = preg_split('/<([^>]+?)>/', $text, -1, PREG_SPLIT_DELIM_CAPTURE);
  // Note: PHP ensures the array consists of alternating delimiters and literals
  // and begins and ends with a literal (inserting $null as required).

  $tag = false; // Odd/even counter. Tag or no tag.
  $stack = array();
  $output = '';
  foreach ($split as $value) {
    // Process HTML tags.
    if ($tag) {
      list($tagname) = explode(' ', strtolower($value), 2);
      // Closing tag
      if ($tagname{0} == '/') {
        $tagname = substr($tagname, 1);
        // Discard XHTML closing tags for single use tags.
        if (!isset($single_use[$tagname])) {
          // See if we possibly have a matching opening tag on the stack.
          if (in_array($tagname, $stack)) {
            // Close other tags lingering first.
            do {
              $output .= '</'. $stack[0] .'>';
            } while (array_shift($stack) != $tagname);
          }
          // Otherwise, discard it.
        }
      }
      // Opening tag
      else {
        // See if we have an identical 'no nesting' tag already open and close it if found.
        if (count($stack) && ($stack[0] == $tagname) && isset($no_nesting[$stack[0]])) {
          $output .= '</'. array_shift($stack) .'>';
        }
        // Push non-single-use tags onto the stack
        if (!isset($single_use[$tagname])) {
          array_unshift($stack, $tagname);
        }
        // Add trailing slash to single-use tags as per X(HT)ML.
        else {
          $value = rtrim($value, ' /') .' /';
        }
        $output .= '<'. $value .'>';
      }
    }
    else {
      // Passthrough all text.
      $output .= $value;
    }
    $tag = !$tag;
  }
  // Close remaining tags.
  while (count($stack) > 0) {
    $output .= '</'. array_shift($stack) .'>';
  }
  return $output;
}
?>
