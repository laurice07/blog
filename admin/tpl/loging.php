<?php
session_start();

/* cours Fabien
if(isset($_SESSION['connected']) && $_SESSION['connected'] === true)
    header('Location:index.php');

//Si l'utilisateur a les droits !!! 
$_SESSION['connected'] = true;
$_SESSION['user'] = ['id'=>10,'prenom'=>'Fabien'];*/

include ('../config/config.php');
include('../lib/bddlib.php');

/******* VARIABLES ********/

/******* PROGRAMME ********/
try
{
// 1.capter les données saisies
// 2.Voir si l'user exit
// 3.comparer le mot de passe saisie avec celui de la base
    // si ok on valide la connection
    // si NOK on renvoi vers le formulaire avec un message d'erreur

    if(array_key_exists('email',$_POST))
        $email = trim($_POST['email']);
        $password = $_POST['pass'];
        $password = password_hash($password,PASSWORD_DEFAULT); //Criptage (Hash) du mot de passe
    

    // il faut tester si cette adresse mail existe déja
    $sth = $dbh->prepare('SELECT u_email, u_password FROM '.DB_PREFIXE.'user WHERE u_email = :email');
    $sth->bindValue('email',$email,PDO::PARAM_STR);
    $user = $sth->fetchAll(PDO::FETCH_ASSOC);
    var_dump($user)
    
    //if($user != false || $password )
       

}
catch(PDOException $e)
{
    $vue = 'erreur.phtml';
    $messageErreur = 'Une erreur de connexion a eu lieu :'.$e->getMessage();
}

?>