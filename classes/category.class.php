<?php
/*******************************************************************************
 * Projekt, Kurs: DT161G
 * File: category.class.php
 * Desc: Class Category for Projekt
 *       Basic category class that corresponds to a row in the category db table
 *
 * Johan Lilja
 * joli1407
 * joli1407@student.miun.se
 ******************************************************************************/

class Category{
    private $id;
    private $category;
    private $memberId;

    public function __construct($category, $memberId, $id = null)
    {
        $this->id = $id;
        $this->category = $category;
        $this->memberId = $memberId;
    }

    public function getId(){
        return $this->id;
    }

    public function getCategory(){
        return $this->category;
    }

    public function getMemberId(){
        return $this->memberId;
    }
}