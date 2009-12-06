# Note originale

MimeDetect strives to provide consistent and accurate server side MIME type 
detection. It supports the PHP FileInfo Extension, the UNIX 'file' command, 
then tries to use the MIME type supplied with the file object. If everything
fails it will select a MIME type based on file extension. 

MimeDetect is Distributed with a magic database to make FileInfo based MIME
detection more consistent across servers. 

# Ajout par Jean-Philippe Fleury

MimeDetect est à l'origine un module pour Drupal écrit par Darrel O'Pry et sous licence GPL version 2 ou toute version ultérieure. Voir <http://drupal.org/project/mimedetect> pour plus de détails.

J'ai modifié la version 6.x-1.2 du module pour rendre ce dernier indépendant de Drupal. Les modifications effectuées sont grosso modo:

- le changement de type du paramètre `$file`, de l'objet au tableau. Ainsi `$file->filepath` devient `$file['filepath']` et `$file->filename` devient `$file['filename']`;

- l'ajout du fichier `file.inc.php`, qui constitue une partie du fichier `includes/file.inc` de Drupal 6. Seule la fonction `file_get_mimetype()` a été conservée. Voir <http://api.drupal.org/api/drupal/includes--file.inc/6/source> pour la source complète. Ce fichier est sous licence GPL version 2 ou toute version ultérieure;

- le passage en paramètre des variables récupérées par `variable_get()`;

- le passage à la licence GPL version 3 ou toute version ultérieure.

## Licence

Ce programme est un logiciel libre; vous pouvez le redistribuer ou le
modifier suivant les termes de la GNU General Public License telle que
publiée par la Free Software Foundation: soit la version 3 de cette
licence, soit (à votre gré) toute version ultérieure.

Ce programme est distribué dans l'espoir qu'il vous sera utile, mais SANS
AUCUNE GARANTIE: sans même la garantie implicite de COMMERCIALISABILITÉ
ni d'ADÉQUATION À UN OBJECTIF PARTICULIER. Consultez la Licence publique
générale GNU pour plus de détails.

Vous devriez avoir reçu une copie de la Licence publique générale GNU avec
ce programme; si ce n'est pas le cas, consultez
<http://www.gnu.org/licenses/>.
