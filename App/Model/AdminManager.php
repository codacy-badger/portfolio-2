<?php


namespace App\Model;

use App\Entity\AdminEntity;

class AdminManager extends Manager
{

    public function adminConnection(AdminEntity $admin)
    {

        $email = $admin->getEmail();
        $password = $admin->getPassword();

        $stmt = $this->bdd->prepare('SELECT id, password FROM admin WHERE email = ?');
        $stmt->bindParam(1, $email);
        $stmt->execute();
        $data = $stmt->fetch();
        $ifAuthentificated = password_verify($password, $data['password']);
        if($ifAuthentificated) {
            $password = password_hash($password, PASSWORD_DEFAULT);
            $session = new SessionManager();
            $session->vars['AuthAdmin'] = array(
                'email' => $email,
                'password' => $password
            );
        }
        return $ifAuthentificated;
    }


}