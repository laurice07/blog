<?php


include ('../config/config.php');
include('../lib/bddlib.php');

$vue = 'listeArticle.phtml';      //vue qui sera affichée dans index
$titlePage = 'Liste des articles';  //titre de la page qui sera mis dans title et h1


/******* VARIABLES ********/

$id = null;
$title = null;
$datePublished = new DateTime();
$content = null;
$picture = null;
$categorie = null;
$author = null;
$mail= null;
$password= null;

$vue = 'addArticle.phtml';      //vue qui sera affichée dans le layout
$titlePage = 'Ajouter un article';  //titre de la page qui sera mis dans title et h1
$menuSelected = 'addArticle';       //menu qui sera sélect dans la nav 


/******* PROGRAMME ********/

try
{
    $dbh = new PDO(DB_SGBD.':host='.DB_SGBD_URL.';dbname='.DB_DATABASE.';charset='.DB_CHARSET, DB_USER, DB_PASSWORD);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    /** On va récupérer les catégories dans la bdd*/
    $sth = $dbh->prepare('SELECT * FROM '.DB_PREFIXE.'categorie');
    $sth->execute();
    $categories = $sth->fetchAll(PDO::FETCH_ASSOC);

    if(array_key_exists('title', $_POST))
    {
        $erreurTitle = false;
        $title = trim($_POST['title']);
        $content = $_POST['content'];
        $dateArticle = $_POST['date'];
        $timeArticle = $_POST['time'];
        $datePublished = new DateTime($dateArticle.' '.$timeArticle);
        //$picture = $_POST['picture'];
        $categorie = $_POST['categorie'];
        $author = $_POST['author'];

       //le formulaire est posté 
        if($title == '')
        $erreur = 'Le titre ne peut-être vide !';

        /*recuperer image */
        if( isset($_FILES['picture']) ) // si formulaire soumis
        {
            $content_dir = '../img/uploads/'; // dossier où sera déplacé le fichier
            $tmp_file = $_FILES['picture']['tmp_name'];
                   

            // on vérifie maintenant l'extension
            $type_file = $_FILES['picture']['type'];
            if( !strstr($type_file, 'jpg') && !strstr($type_file, 'jpeg') && !strstr($type_file, 'bmp') && !strstr($type_file, 'gif') )
            {
                $erreur = 'Le fichier n\'est pas une image';
            }
           
            else  // on copie le fichier dans le dossier de destination
            $picture = uniqid().'-'.basename( $_FILES['picture']['name']);
            move_uploaded_file($tmp_file, $content_dir . $picture);
            echo "Le fichier a bien été uploadé";
        }
    
        /** Si j'ai pas d'erreur j'inserts dans la bdd */
        if($erreurTitle === false)
        {
                
            $sth = $dbh->prepare('INSERT INTO b_article(a_title, a_date_published, a_content, a_picture, a_categorie, a_author) VALUES(:title, :date_published, :content, :picture, :categorie, :author)');

           
           /* $sth->execute(array(

                'title' => $title,
                'date_published' => $datePublished,
                'content' => $content,
                'picture' => $picture,
                'categorie' => $categorie,
                'author' => $author
                ));*/

                $sth->bindValue('title',$title,PDO::PARAM_STR);
                $sth->bindValue('date_published',$datePublished->format('Y-m-d H:i:s'));
                $sth->bindValue('content',$content,PDO::PARAM_STR);
                $sth->bindValue('picture',$picture,PDO::PARAM_STR);
                $sth->bindValue('categorie',$categorie,PDO::PARAM_INT);
                $sth->bindValue('author',$author,PDO::PARAM_INT);
               
                $sth->execute();
                header('Location:listeArticle.php');
                exit(); 
                    
        }
    }
}
catch(PDOException $e)
{
    echo 'Une erreur s\'est produite : '.$e->getMessage();
}

include ('tpl/layout.phtml');



