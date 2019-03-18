<?php
session_start();

include ('../config/config.php');
include('../lib/bddlib.php');

$vue = 'addArticle.phtml';      //vue qui sera affichée dans le layout
$titlePage = 'Modifier un article';  //titre de la page qui sera mis dans title et h1

// on teste si un article est sélectionné 

try
{
    $bdd = connexion();

    $sth = $bdd->prepare('SELECT * FROM '.DB_PREFIXE.'categorie');
    $sth->execute();
    $categories = $sth->fetchAll(PDO::FETCH_ASSOC);

   //On récupère l'id de l'article à modifier quand il est sélectionné
    if(array_key_exists('id',$_GET))
    {
        $id = $_GET['id'];
        
        $sth = $bdd->prepare('SELECT * FROM '.DB_PREFIXE.'article WHERE a_id = :id');
        $sth->bindValue('id',$id,PDO::PARAM_INT);
        $sth->execute();
        $article = $sth->fetch(PDO::FETCH_ASSOC);
        
        $title = $article['a_title'];
        $datePublished = new DateTime($article['a_date_published']);
        $content = $article['a_content'];
        $valide = $article['a_valide']; //erreur revoir comment faire
        $categorie = $article['a_categorie'];
        $picture = $article['a_picture'];
        $author = $article['a_author'];

    }

    //S'il a des données en entrée - Le formulaire est posté
    if(array_key_exists('title',$_POST))
    {
        //var_dump($_POST);
        $erreurTitle = []; 

        $id = $_POST['id']; 

        /* Récupération des données de l'article */

        $title = trim($_POST['title']);
        $content = trim($_POST['content']);
        $dateArticle = $_POST['date'];
        $timeArticle = $_POST['time'];
        $datePublished = new DateTime($dateArticle.' '.$timeArticle);
        $picture = $_POST['picture'];
        $categorie = $_POST['categorie'];
        $author = $_POST['author'];
        $valide = isset($_POST['valide'])?true:false;

        
        //le formulaire est posté
        if($title == '')
            $erreurTitle[] = 'Le titre ne peut-être vide !';

        if($_FILES['picture']["tmp_name"]!= '')
        {
            $tmpNewPicture = uploadFile('picture','');
            if(!$tmpNewPicture)
                $erreurTitle[] = 'Une erreur s\'est produite lors de l\'upload de l\'image !';
            else
            {
                //On supprime l'ancienne image
                delFile(UPLOADS_DIR.$picture);
                
                $picture = $tmpNewPicture;
               
            }
        }

        // on insert dans la bdd */
        if(count($erreurTitle) == 0)
        {
            //Préparation requête
            $sth = $bdd->prepare('UPDATE '.DB_PREFIXE.'article SET a_title = :title ,a_date_published=:datePublished,
            a_content=:content,a_picture=:picture,a_categorie=:categorie,a_valide=:valide WHERE a_id=:id');

            $sth->bindValue('id',$id,PDO::PARAM_INT);
            $sth->bindValue('title',$title,PDO::PARAM_STR);
            $sth->bindValue('datePublished',$datePublished->format('Y-m-d H:i:s'));
            $sth->bindValue('content',$content,PDO::PARAM_STR);
            $sth->bindValue('picture',$picture,PDO::PARAM_STR);
            $sth->bindValue('categorie',$categorie,PDO::PARAM_INT);
            $sth->bindValue('valide',$valide,PDO::PARAM_BOOL);
            $sth->execute();

            addFlashBag('L\'article a bien été modifé');
            header('Location:listeArticle.php');
            exit();
        }
    }

    // On vérifie si l'image existe sur le disque pour la passer à la vue 
    if(file_exists(UPLOADS_DIR.$picture) && $picture != null)
        $pictureDisplay = true;

}
catch(PDOException $e)
{
    /** On affiche une autre vue car ici l'erreur est critique. 
     * Dans l'avenir il faudra ici envoyer un email à l'admin par exemple car il n'est pas normal d'avoir une erreur de connexion au 
     * serveur ou une erreur SQL !
     */
    $vue = 'erreur.phtml';
    //Si une exception est envoyée par PDO (exemple : serveur de BDD innaccessible) on arrive ici
    $messageErreur = 'Une erreur de connexion a eu lieu :'.$e->getMessage();
}

include('tpl/layout.phtml');