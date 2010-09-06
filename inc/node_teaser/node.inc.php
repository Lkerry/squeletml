<?php
// $Id: node.module,v 1.947.2.26 2010/08/06 11:41:13 goba Exp $

/**
 * @file
 * The core that allows content to be submitted to the site. Modules and scripts may
 * programmatically submit nodes using the usual form API pattern.
 */

/**
 * Generate a teaser for a node body.
 *
 * If the end of the teaser is not indicated using the <!--break--> delimiter
 * then we generate the teaser automatically, trying to end it at a sensible
 * place such as the end of a paragraph, a line break, or the end of a
 * sentence (in that order of preference).
 *
 * @param $body
 *   The content for which a teaser will be generated.
 * @param $size
 *   The desired character length of the teaser.
 * @return
 *   Un tableau dont le premier élément contient l'aperçu, et le second un booléen informant si l'aperçu représente (TRUE) ou non (FALSE) le texte entier passé en paramètre.
 */
function node_teaser($body, $size) {
  // Le paramètre `$format` a été supprimé, tou comme la recherche d'une valeur par défaut de `$size`, d'un délimiteur et d'un filtre PHP.
  
  // If we have a short body, the entire body is the teaser.
  if (drupal_strlen($body) <= $size) {
    return array ($body, TRUE);
  }

  // If the delimiter has not been specified, try to split at paragraph or
  // sentence boundaries.

  // The teaser may not be longer than maximum length specified. Initial slice.
  $teaser = truncate_utf8($body, $size);

  // Store the actual length of the UTF8 string -- which might not be the same
  // as $size.
  $max_rpos = strlen($teaser);

  // How much to cut off the end of the teaser so that it doesn't end in the
  // middle of a paragraph, sentence, or word.
  // Initialize it to maximum in order to find the minimum.
  $min_rpos = $max_rpos;

  // Store the reverse of the teaser.  We use strpos on the reversed needle and
  // haystack for speed and convenience.
  $reversed = strrev($teaser);

  // Build an array of arrays of break points grouped by preference.
  $break_points = array();

  // A paragraph near the end of sliced teaser is most preferable.
  $break_points[] = array('</p>' => 0);

  // If no complete paragraph then treat line breaks as paragraphs.
  $line_breaks = array('<br />' => 6, '<br>' => 4);
  // Newline only indicates a line break if line break converter
  // filter is present.
  if (isset($filters['filter/1'])) {
    $line_breaks["\n"] = 1;
  }
  $break_points[] = $line_breaks;

  // If the first paragraph is too long, split at the end of a sentence.
  $break_points[] = array('. ' => 1, '! ' => 1, '? ' => 1, '。' => 0, '؟ ' => 1);

  // Iterate over the groups of break points until a break point is found.
  foreach ($break_points as $points) {
    // Look for each break point, starting at the end of the teaser.
    foreach ($points as $point => $offset) {
      // The teaser is already reversed, but the break point isn't.
      $rpos = strpos($reversed, strrev($point));
      if ($rpos !== FALSE) {
        $min_rpos = min($rpos + $offset, $min_rpos);
      }
    }

    // If a break point was found in this group, slice and return the teaser.
    if ($min_rpos !== $max_rpos) {
      // Don't slice with length 0.  Length must be <0 to slice from RHS.
      if ($min_rpos === 0)
      {
        return array ($teaser, FALSE);
      }
      else
      {
        return array (substr($teaser, 0, 0 - $min_rpos), FALSE);
      }
    }
  }

  // If a break point was not found, still return a teaser.
  return array ($teaser, FALSE);
}
?>
