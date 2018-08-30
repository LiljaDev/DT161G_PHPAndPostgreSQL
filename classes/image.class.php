<?php
/*******************************************************************************
 * Projekt, Kurs: DT161G
 * File: image.class.php
 * Desc: Class Image for Projekt
 *       Basic image class that corresponds to a row in the image db table
 *       If a dateTime is not supplied to the constructor a default time will be set.
 * Johan Lilja
 * joli1407
 * joli1407@student.miun.se
 ******************************************************************************/


class Image {
    private $id;
    private $category_id;
    private $image;
    private $hash;
    private $dateTime;

    public function __construct($category_id, $image, $dateTime = null, $id = null){
        $this->id = $id;
        $this->category_id = $category_id;
        $this->image = $image;

        if($dateTime){
            $this->dateTime = new DateTime($dateTime);
        }
        else{
            $this->dateTime = new DateTime('2000-01-01T00:00:00');
        }

        $this->hash = md5($this->image);
    }

    public function getId(){
        return $this->id;
    }

    public function getCategoryId(){
        return $this->category_id;
    }

    public function getImage(){
        return $this->image;
    }

    public function getDateTime(){
        return $this->dateTime;
    }

    public function getHash(){
        return $this->hash;
    }
}