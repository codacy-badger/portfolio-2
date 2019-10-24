<?php

namespace Model;

require_once("model/Manager.php");

class PostManager extends Manager
{


    public function getPosts()
    {
        $db = $this->dbConnect();
        $req = $db->query('SELECT * FROM portfolio_post ORDER BY modification_date DESC LIMIT 0, 5');

        return $req;
    }

    public function getPost($id)
    {
        $db = $this->dbConnect();
        $req = $db->prepare('SELECT * FROM portfolio_post WHERE id = ?');
        $req->execute(array($id));
        $post = $req->fetch();
        return $post;
    }
}