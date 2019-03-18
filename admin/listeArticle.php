<?php
session_start();

include ('../config/config.php');
include ('../lib/bddlib.php');

$vue = 'listeArticle.phtml';      //vue qui sera affichée dans index
$titlePage = 'Liste des articles';  //titre de la page qui sera mis dans title et h1

/******* PROGRAMME ********/

try
{
    $dbh = connexion();
    
    $sth = $dbh->prepare('SELECT * FROM '.DB_PREFIXE.'article');
    
    $sth->execute();
    
    $articles = $sth->fetchAll(PDO::FETCH_ASSOC);
    //var_dump($articles);
    
    include('tpl/index.phtml');
    
}
catch(PDOException $e)
{
    $vue = 'erreur.phtml';
    $messageErreur =  'Une erreur s\'est produite : '.$e->getMessage();
}

include('tpl/layout.phtml');