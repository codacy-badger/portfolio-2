<?php
use Model\AccountManager;
use Model\ComManager;
use Model\FormManager;
use Model\Manager;
use Model\ProjectManager;
use Model\Logout;
use Model\Auth;

require_once '../model/Manager.php';
require_once '../model/ProjectManager.php';
require_once '../model/FormManager.php';
require_once '../model/ComManager.php';
require_once '../model/AccountManager.php';
require_once '../vendor/autoload.php';
require_once 'TwigController.php';

function getProject($id)
{
    $projectManager = new ProjectManager();
    $project = $projectManager->getProject($id);
    return $project;
}

function getProjects()
{
    $projectManager = new ProjectManager();
    $projects = $projectManager->getProjects();
    return $projects;
}

function getConnectionForm()
{
    $form = new FormManager();
    $connection_form = $form->getConnectionForm();
    return $connection_form;
}

function getComments($id)
{
    $comManager = new ComManager();
    $comments = $comManager->getComments($id);
    return $comments;
}

function getConnection()
{
    $accountManager = new AccountManager();
    $member = $accountManager->connection(filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL), filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS));
    return $member;
}

function getCreatAccountForm()
{
    $form = new FormManager();
    $account_form = $form->getCreateAccountForm();
    return $account_form;
}

function getCommentForm()
{
    $form = new FormManager();
    $comment_form = $form->getCommentForm();
    return $comment_form;
}

function addAccount($firstName, $email, $password)
{
    $accountManger = new AccountManager();
    $affectedLines = $accountManger->createAccount($firstName, $email, $password);

    if ($affectedLines === false) {
        throw new Exception('Impossible de créer votre compte !');
    }
    else {
        $accountManger->sendEmailSuccess($firstName, $email);
    }

}

function getHomePage()
{

    useTwig('home.twig', ['projectlist' => getProjects()]);
}

function getContactPage()
{
    $form = new FormManager();
    $contact_form = $form->getContactForm();
    useTwig('contact.twig', ['contactform' => $contact_form]);
}


function getProjectPage($id)
{
    if (Auth::isLogged() && $id > 0) {
        useTwig('single.twig', [
            'project' => getProject($id),
            'commentform' => getCommentForm(),
            'commentlist' => getComments($id),
        ]);
    } elseif ($id > 0) {
        useTwig('single.twig', [
            'project' => getProject($id),
            'connectionform' => getConnectionForm() ,
            'commentlist' => getComments($id),
            'accountform' => getCreatAccountForm()
        ]);
    } else
    {
        throw new Exception('Aucun identifiant de portfolio envoyé');
    }

}

function askConnection($slug, $id)
{
    if (!empty(filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL)) and !empty(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS))){
        if (!filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL)) {
            $error_connection = "Format d'email erroné";
            useTwig('single.twig', [
                'project' => getProject($id),
                'connectionform' => getConnectionForm(),
                'commentlist' => getComments($id),
                'accountform' => getCreatAccountForm(),
                'error_connection' => $error_connection
            ]);
        } else {
            getConnection();
            if (getConnection() or Auth::adminIsLogged()) {
                $success_connection = 'Vous êtes connecté vous pouvez laisser des commentaires';
                useTwig('single.twig', [
                    'project' => getProject($id),
                    'commentform' => getCommentForm(),
                    'commentlist' => getComments($id),
                    'success_connection' => $success_connection
                ]);
            } else {
                $error_connection = 'Mauvais identifiant ou mot de passe !';
                useTwig('single.twig', [
                    'project' => getProject($id),
                    'connectionform' => getConnectionForm(),
                    'commentlist' => getComments($id),
                    'member' => getConnection(),
                    'accountform' => getConnectionForm(),
                    'error_connection' => $error_connection

                ]);
            }
        }
    } else {
        throw new Exception('Vous n\'avez pas renseigné toutes les données');
    }
}


function askInscription($slug, $id)
{
    if (!empty(filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL)) and !empty(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS))){
        if (!filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL)) {
            $error_inscription = "Format d'email erroné";
            useTwig('single.twig', [
                'project' => getProject($id),
                'connectionform' => getConnectionForm(),
                'commentlist' => getComments($id),
                'accountform' => getConnectionForm(),
                'error_inscription' => $error_inscription
            ]);
        } elseif (getConnection()) {
            $error_inscription = "Vous avez déja un compte, veuillez vous connecter";
            useTwig('single.twig', [
                'project' => getProject($id),
                'connectionform' => getConnectionForm(),
                'commentlist' => getComments($id),
                'accountform' => getConnectionForm(),
                'error_inscription' => $error_inscription
            ]);
        } else {
            addAccount(filter_input(INPUT_POST, 'first_name_account', FILTER_SANITIZE_SPECIAL_CHARS), filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL), filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS));
            $success_inscription = 'Votre compte est créé vous pouvez laisser des commentaires';
            useTwig('single.twig', [
                'project' => getProject($id),
                'commentform' => getCommentForm(),
                'commentlist' => getComments($id),
                'success_inscription' => $success_inscription
            ]);
        }
    }
    else {
        throw new Exception('Tous les champs ne sont pas remplis !');
    }
}

function askDisconnection($slug, $id)
{
    Logout::disconnection();
    $succes_connection = "Vous êtes déconnecté";
    useTwig('single.twig', [
        'project' => getProject($id),
        'connectionform' => getConnectionForm(),
        'commentlist' => getComments($id),
        'accountform' => getConnectionForm(),
        'success_connection' => $succes_connection
    ]);
}

function addComment($projectId, $first_name, $comment)
{
    $commentManager = new ComManager();
    $affectedLines = $commentManager->projectComment($projectId, $first_name, $comment);

    if ($affectedLines === false) {
        throw new Exception('Impossible d\'ajouter le commentaire !');
    }
    else {
        return true;
    }

}

function askResetingPassword($id)
{
    $form = new FormManager();
    $ask_reseting_form = $form->getResetPasswordForm();
    $succes_connection = "renseignez votre e-mail pour réinitialiser votre mot de passe";
    useTwig('single.twig', [
        'project' => getProject($id),
        'connectionform' => getConnectionForm(),
        'resetpasswordform' => $ask_reseting_form,
        'accountform' => getCreatAccountForm(),
        'success_connection' => $succes_connection
    ]);
}

function askNewPassword($id)
{
    $accountManager = new AccountManager();
    $findAccount = $accountManager->forgotPassword(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_SPECIAL_CHARS));
    return $findAccount;
    if (!empty(filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL)) && $findAccount == true) {
        $success_connection = 'Vous allez recevoir un email pour réinitialiser votre mot de passe';
        useTwig('single.twig', [
            'project' => getProject($id),
            'connectionform' => getConnectionForm(),
            'commentlist' => getComments($id),
            'success_connection' => $success_connection
        ]);
    }
    else {
        $error_connection = 'Votre e-mail n\'est pas reconnu';
        useTwig('single.twig', [
            'project' => getProject($id),
            'connectionform' => getConnectionForm(),
            'commentlist' => getComments($id),
            'resetpasswordform' => $ask_reseting_form,
            'accountform' => getCreatAccountForm(),
            'success_connection' => $succes_connection
        ]);
    }
}


//    } elseif (filter_input(INPUT_GET, 'action', FILTER_SANITIZE_SPECIAL_CHARS) == 'resetPassword') {
//        changePassword();
//        if (filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS) && filter_input(INPUT_GET, '', FILTER_SANITIZE_SPECIAL_CHARS) == 'key' && changePassword() == true) {
//            $success_connection = 'Votre nouveau mot de passe est enregistré avec succès, vous pouvez vous connecter';
//            print_r ($twig->render('single.twig', ['project' => project(), 'connectionform' => connectionForm(), 'accountform' => accountForm(), 'success_connection' => $success_connection]));
//        } else {
//            $error_connection = 'Votre mots de passe n\'est pas enregistré';
//            print_r ($twig->render('single.twig', ['project' => project(), 'connectionform' => connectionForm(), 'commentlist' => listComment(), 'resetpasswordform' => askResetingPassword(), 'accountform' => accountForm(), 'error_connection' => $error_connection]));
//        }

function checkIfPasswordKeyExist(){
    $accountManager = new AccountManager();
    $findPassworKey = $accountManager->findPasswordKey(filter_input(INPUT_GET, 'key', FILTER_SANITIZE_SPECIAL_CHARS));
    return $findPassworKey;
}

function newPasswordForm()
{
    $form = new FormManager();
    $reset_form = $form->getNewPasswordForm();

    return $reset_form;
}

function changePassword()
{
    $accountManager = new AccountManager();
    $resetPassword = $accountManager->changePassword(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS), filter_input(INPUT_GET, 'key', FILTER_SANITIZE_SPECIAL_CHARS));
    return $resetPassword;
}