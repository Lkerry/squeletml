<?php
/**
 * @file
 * Provide server side mime type detection.
 *
 * @author Darrel O'Pry, http://www.drupal.org/user/22202
 * @copyright Copyright(c) 2007, Darrel O'Pry
 * @copyright Copyright(c) 2009, Jean-Philippe Fleury
 */

/**
 * Detect File Mime Type.
 *
 * @param $file Un tableau associatif contenant le chemin complet du fichier
 *   (`$file['filepath']`) ainsi que son nom (`$file['filename']`). The
 *   filepath property is used to locate the file and if the mime detection
 *   fails, the mimetype property is returned.
 * @param $mimedetect_enable_file_binary Un booléen déterminant si la commande
 *   `file` doit être utilisée si besoin.
 * @param $mimedetect_file_binary Chemin vers `file`.
 * @param $mapping Un tableau associatif personnalisé de types MIME à utilsier
 *   si besoin à la place du tableau par défaut de la fonction
 *   `file_get_mimetype()`. Exemple: array ('rmi' => 'audio/midi')
 * @return String containing the file's MIME type.
 */
function mimedetect_mime($file, $mimedetect_enable_file_binary = FALSE, $mimedetect_file_binary = '/usr/bin/file', $mapping = NULL) {
  $path_mimedetect = dirname(__FILE__);
  
  // An additional array of mimetypes not included in file_get_mimetype().
  static $additional_mimes = array(
    // Audio types
    'rmi' => 'audio/midi',
    'aidff' => 'audio/x-aiff',
    // Image types
    'cod' => 'image/cis-cod',
    'jfif' => 'image/pipeg',
    'cmx' => 'image/x-cmx',
    // Video types
    'mpa' => 'video/mpeg',
    'mpv2' => 'video/mpeg',
    'asr' => 'video/x-ms-asf',
  );

  $mime = FALSE;
  $magic_file = $path_mimedetect .'/magic';

  // Try to use the fileinfo extension first.
  if (extension_loaded('fileinfo')) {
    static $finfo = FALSE;
    if ($finfo || $finfo = @finfo_open(FILEINFO_MIME, $magic_file)) {
      $mime = finfo_file($finfo, realpath($file['filepath']));
    }
  }

  // Try the 'file' binary.
  if (!$mime && $mimedetect_enable_file_binary
    && ($filebin = $mimedetect_file_binary)
    && is_executable($filebin))
  {
    // On OSX the -i switch is -I, so if we use the long flags everyone is
    // happy. I checked back to version 3.41 and it still supports the long
    // names but if you run into problems you can use " -bi ".
    $command = $filebin .' --brief --mime --magic-file='. escapeshellarg($magic_file) .' '. escapeshellarg($file['filepath']);
    $mime = trim(exec($command));
    // with text we often get charset like 'text/plain; charset=us-ascii'
    $mime = split(';', $mime);
    $mime = trim($mime[0]);
  }

  // ASF derived media formats are hard to detect with magic. They're typically
  // all reported as video/x-ms-asf or application/octet-stream. These aren't
  // really informative about the media type, so we attempt to figure it out by
  // extension. I expect OGG to present similar difficulties in determining how
  // it should be played.
  if (!$mime || $mime == 'application/octet-stream') {
    // Try core's mime mapping first...
    $mime = file_get_mimetype($file['filename'], $mapping);
    // ...and if that doesn't turn up anything try our additional mappings.
    if ($mime == 'application/octet-stream') {
      $mime = file_get_mimetype($file['filename'], $additional_mimes);
    }
  }

  return $mime;
}
?>
