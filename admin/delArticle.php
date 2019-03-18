<?php


include ('../config/config.php');
include ('../lib/bddlib.php');

$vue = 'listeArticle.phtml';      //vue qui sera affichée dans index
$titlePage = 'Liste des articles';  //titre de la page qui sera mis dans title et h1

/******* VARIABLES ********/
$id= $_GET['id'];

var_dump($id);

/******* PROGRAMME ********/

try
{
    if(array_key_exists('id', $_GET) == true){
        $dbh = connexion();

        /** On va récupérer le nom de la photo */
        $sth = $dbh->prepare('SELECT * FROM b_article WHERE a_id= :id');
        $sth->bindValue(':id', $id, PDO::PARAM_INT);
        $sth->execute();
        $article = $sth->fetch(PDO::FETCH_ASSOC);
        $img = $article['a_picture'];
        
        if($article)
        {    
        //echo UPLOADS_DIR.$img;
        //exit();
        
        // Efface l'image correspondante dans le dossier source
        //AVANT LA SUPPRESSION TESTER SI LE FICHIER EST SUR LE DISQUE ET SI $img != '' && $img != NULL
        delFile(UPLOADS_DIR.'img/'.$img);

        $sth = $dbh->prepare('DELETE FROM b_article WHERE a_id= :id' );  
        $sth->bindValue(':id', $id, PDO::PARAM_INT);
        $sth->execute();
       
        addFlashBag('L\'article a bien été supprimé');
        header('Location:listeArticle.php');
        exit(); 
        }
    }
    
}
catch(PDOException $e)
{
    echo 'Une erreur s\'est produite : '.$e->getMessage();
}


include('tpl/layout.phtml');

 
 


