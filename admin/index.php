<?php

/*session_start();

if(!isset($_SESSION['connected']) || $_SESSION['connected'] !== true)
    header('Location:login.php');*/


/**On inclu d'abord le fichier de configuration */
include('../config/config.php');
/**On inclu ensuite nos librairies dont le programme a besoin */
include('../lib/bddlib.php');

/** On définie nos variables nécessaire pour la vue et le layout */
$vue = 'index.phtml';   //vue qui sera affichée dans le layout
$titlePage =  'Accueil';  //titre de la page qui sera mis dans title et h1 dans le layout
$menuSelected = 'home';       //menu qui sera sélect dans la nav du layout

//var_dump($_SESSION);
include 'tpl/layout.phtml';

?>

