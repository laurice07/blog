<?php
session_start();
include ('../config/config.php');
include('../lib/bddlib.php');

$vue = 'addUser.phtml';      //vue qui sera affichée dans le layout
$titlePage = 'Ajouter un utilisateurs';  //titre de la page qui sera mis dans title et h1


/******* VARIABLES ********/

$id = null;
$email = null;
$firstName = null;
$lastName = null;
$password = null;
$passCtrl = null;
$role= 'author';
$valide = null;


try
{
    $dbh = connexion();
    $erreurEmail = [];

    /** On va récupérer les roles dans la bdd*/
    
    if(array_key_exists('email', $_POST))
    {
        $email = trim($_POST['email']);
        $firstName = $_POST['firstName'];
        $lastName = $_POST['lastName'];
        $password = $_POST['pass'];
        $passCtrl = $_POST['passCtrl'];
        $role = $_POST['role'];
        $valide = isset($_post['valide'])?true:false;

        //il faut tester si une adresse mail est saisie
        if($email == '')
            $erreurEmail[] = 'L\'adresse mail ne peut-être vide !';

        // il faut tester si la confirmation du mot de passe est idem au 1°
        if($password != $passCtrl || $password == '')
            $erreurEmail[] = 'Le mot de passe ou sa confirmation ne sont pas corrects !';
        
        // il faut tester si cette adresse mail n'existe pas déja
        $sth = $dbh->prepare('SELECT u_email FROM '.DB_PREFIXE.'user WHERE u_email = :email');
        $sth->bindValue('email',$email,PDO::PARAM_STR);
        $user = $sth->fetch(PDO::FETCH_ASSOC);
        if($user != false)
            $erreurEmail[] = 'Cet Email existe déjà';

        //si tout est ok on insert les données
        if(count($erreurEmail) == 0)
        {
            //Criptage (Hash) du mot de passe
            $password = password_hash($password,PASSWORD_DEFAULT);
                        
            $sth = $dbh->prepare('INSERT INTO b_users(u_id, u_firstname, u_lastname, u_email, u_password, u_valide, u_role) 
            VALUES (NULL,  :firstName, :lastName, :email, :pass, :valide, :role)');

                $sth->bindValue('email',$email,PDO::PARAM_STR);
                $sth->bindValue('firstName',$firstName,PDO::PARAM_STR);;
                $sth->bindValue('lastName',$lastName,PDO::PARAM_STR);
                $sth->bindValue('pass',$password,PDO::PARAM_STR);
                $sth->bindValue('role',$role,PDO::PARAM_STR);
                $sth->bindValue('valide',$valide,PDO::PARAM_INT);
                $sth->execute();

                addFlashBag('utilisateur ajouté');
                header('Location:listeArticle.php');
                exit(); 
                    
        }
    }
}
catch(PDOException $e)
{
    $erreurEmail[] = 'Une erreur s\'est produite : '.$e->getMessage();
}

include ('tpl/layout.phtml');

