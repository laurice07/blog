<?php
/** Connexion à la base de données via PDO
 * @param void
 * @return PDO connexion vers la base de données
 */
function connexion()
{
    $dbh = new PDO(DB_SGBD.':host='.DB_SGBD_URL.';dbname='.DB_DATABASE.';charset='.DB_CHARSET, DB_USER, DB_PASSWORD);
    //On dit à PDO de nous envoyer une exception s'il n'arrive pas à se connecter ou s'il rencontre une erreur
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    return $dbh;
}

/** Permet d'afficher tous les noms des colonnes d'une table 
 * @param string $table nom de la table dans la base de données
 * @param PDO $dbh objet de connexion vers la base
 * @return string liste séparée par une virgule des noms des colonnes
*/
function getColumnName($table,$dbh)
{
    $q = $dbh->prepare("DESCRIBE ".$table);
    $q->execute();
    $table_fields = $q->fetchAll(PDO::FETCH_COLUMN);
    $stringFields = '';
    foreach($table_fields as $field)
        $stringFields .= $field.',';

    return $stringFields;
}

/** Vérifie si un fichier a bien été déplacé et  */
function uploadFile($postName, $folder)
{
    if ($_FILES[$postName]["error"] == UPLOAD_ERR_OK) {
        $tmp_name = $_FILES[$postName]["tmp_name"];
        // basename() peut empêcher les attaques de système de fichiers;
        // la validation/assainissement supplémentaire du nom de fichier peut être approprié
        $name = uniqid().'-'.basename($_FILES[$postName]["name"]);
        if(move_uploaded_file($tmp_name, UPLOADS_DIR.$folder.'/'.$name))
            return $name;
    }
    
    return false;
}

/** Vérifie si un fichier a bien été déplacé et  */
function delFile($file)
{
    if(file_exists($file) && !is_dir($file))
        unlink($file);
}

/** function addFlashBag
 * Ajoute une valeur au flashbag
 * @param string $texte
 * @return void
 */
function addFlashBag($texte)
{
    if(!isset($_SESSION['flashbag']) || !is_array($_SESSION['flashbag']))
        $_SESSION['flashbag'] = array();

    $_SESSION['flashbag'][] = $texte;
}

/** function getFlashBag
 * Ajoute une valeur au flashbag
 * @param void
 * @return array flashbag
 */
function getFlashBag()
{
    if(isset($_SESSION['flashbag']) && is_array($_SESSION['flashbag']))
    {
        $flashbag = $_SESSION['flashbag'];
        unset($_SESSION['flashbag']);
        return $flashbag;
    }
    return false;
}

