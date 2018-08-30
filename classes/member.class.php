<?php
/*******************************************************************************
 * Projekt, Kurs: DT161G
 * File: member.class.php
 * Desc: Class Member for Projekt
 *       Basic member class that corresponds to a row in the member db table
 * Johan Lilja
 * joli1407
 * joli1407@student.miun.se
 ******************************************************************************/


class Member {
    private $id;
    private $username, $password;
    private $roles;
    private $categories;

    public function __construct($id, $username, $password, $roles, $categories = null)
    {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->roles[] = $roles;
        if($categories == null){
            $this->categories = [];
        }
        else{
            $this->categories = $categories;
        }
    }

    public function addRole($role){
        $this->roles[] = $role;
    }

    public function addCategory($category){
        $this->categories[$category->getId()] = $category;
    }

    public function getId(){
        return $this->id;
    }

    public function getUsername(){
        return $this->username;
    }

    public function getPassword(){
        return $this->password;
    }

    public function getRoles(){
        return $this->roles;
    }

    public function getCategories(){
        return $this->categories;
    }
}