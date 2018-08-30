<?php
/*******************************************************************************
 * Projekt, Kurs: DT161G
 * File: role.class.php
 * Desc: Class Role for Projekt
 *       Corresponds to a row in the role db table
 * Johan Lilja
 * joli1407
 * joli1407@student.miun.se
 ******************************************************************************/

class Role {
    private $id;
    private $role, $roleText;

    public function __construct($id, $role, $roleText)
    {
        $this->id = $id;
        $this->role = $role;
        $this->roleText = $roleText;
    }

    public function getId(){
        return $this->id;
    }

    public function getRole(){
        return $this->role;
    }

    public function getRoleText(){
        return $this->roleText;
    }
}