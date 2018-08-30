<?php
/*******************************************************************************
 * Projekt, Kurs: DT161G
 * File: database.class.php
 * Desc: Singleton Class Database for Projekt
 * Johan Lilja
 * joli1407
 * joli1407@student.miun.se
 ******************************************************************************/

require_once "config.class.php";
require_once "role.class.php";
require_once "category.class.php";
require_once "member.class.php";
require_once "image.class.php";
require_once "resultenum.class.php";

class Database {

    private static $instance;
    private function __construct()
    {
    }

    public static function getInstance(){
        if(self::$instance == null){
            self::$instance = new Database();
        }

        return self::$instance;
    }

    /*******************************************************************************
     * Returns Member object based on $username
     * $status indicates result of query
     * TODO clean up queries
     ******************************************************************************/
    public function findUser($username, &$status){
        $conf = Config::getInstance();
        $dbconn = pg_connect($conf->getDbDsn());

        //Member and role data
        $mrResult = pg_query_params($dbconn, "SELECT m.id, m.username, m.password, mr.role_id, r.role, r.roletext
                                        FROM webproject.member m, webproject.member_role mr, webproject.role r
                                        WHERE m.username = $1 AND m.id = mr.member_id AND mr.role_id = r.id;", array($username));

        //Member and category data
        $mcResult = pg_query_params($dbconn, "SELECT m.id as member_id , c.id as category_id , c.category
                                        FROM webproject.member m, webproject.category c
                                        WHERE m.username = $1 AND m.id = c.member_id;", array($username));

        pg_close($dbconn);

        //Construct member from query result
        //If query returned something
        $row = pg_fetch_object($mrResult);
        if($row){
            $role = new Role($row->role_id, $row->role, $row->roletext);
            $m = new Member($row->id, $row->username, $row->password, $role);

            for($i = 1; $i < pg_num_rows($mrResult); ++$i) {
                $row = pg_fetch_object($mrResult);
                $m->addRole(new Role($row->role_id, $row->role, $row->roletext));
            }

            //Construct category objects from query and add to member
            for($i = 0; $i < pg_num_rows($mcResult); ++$i) {
                $row = pg_fetch_object($mcResult);
                $category = new Category($row->category, $row->member_id, $row->category_id);
                $m->addCategory($category);
            }
        }
        else{   //Else the username did not exist
            $status = ResultEnum::USER_NOT_FOUND;
            return false;
        }

        //Free mem
        pg_free_result($mrResult);
        pg_free_result($mcResult);

        $status = ResultEnum::OK;
        return $m;
    }

    /*******************************************************************************
     * Returns all Members from db
     * TODO clean up queries
     ******************************************************************************/
    public function findAllUsers(){
        $conf = Config::getInstance();
        $dbconn = pg_connect($conf->getDbDsn());

        //Member and role data
        $mrResult = pg_query($dbconn, "SELECT m.id, m.username, m.password, mr.role_id, r.role, r.roletext
                                            FROM webproject.member m, webproject.member_role mr, webproject.role r
                                            WHERE m.id = mr.member_id AND mr.role_id = r.id;");

        //Member and category data
        $mcResult = pg_query($dbconn, "SELECT m.id as member_id , c.id as category_id , c.category
                                        FROM webproject.member m, webproject.category c
                                        WHERE m.id = c.member_id");

        pg_close($dbconn);

        //Construct member from query result
        $row = pg_fetch_object($mrResult);
        $role = new Role($row->role_id, $row->role, $row->roletext);
        $members[$row->id] = new Member($row->id, $row->username, $row->password, $role);

        //Roles
        while($row = pg_fetch_object($mrResult)){
            if(array_key_exists($row->id, $members)){
                $members[$row->id]->addRole(new Role($row->role_id, $row->role, $row->roletext));
            }
            else{
                $role = new Role($row->role_id, $row->role, $row->roletext);
                $members[$row->id] = new Member($row->id, $row->username, $row->password, $role);
            }
        }

        //Construct category objects from query and add to member
        while($row = pg_fetch_object($mcResult)){
            $category = new Category($row->category, $row->member_id, $row->category_id);
            $members[$row->member_id]->addCategory($category);
        }

        //Free mem
        pg_free_result($mrResult);
        pg_free_result($mcResult);

        return $members;
    }

    /*******************************************************************************
     * Inserts image $image into category $categoryId unless a duplicate for the
     * member $username exists.
     * $status indicates the result
     ******************************************************************************/
    public function insertImage($username, $image, $categoryId, &$status){
        $userr = $this->findUser($username,$status);
        $conf = Config::getInstance();
        $dbconn = pg_connect($conf->getDbDsn());

        //Look for an image with the same hash
        $result = pg_query_params($dbconn, "SELECT * FROM webproject.image i, webproject.category c WHERE i.category_id = c.id AND c.member_id = $2 AND i.hash = $1", array($image->getHash(), $userr->getId()));
        //If we found a duplicate cleanup and abort
        if(pg_num_rows($result) != 0){
            pg_close();
            pg_free_result($result);
            $status = ResultEnum::IMAGE_DUPLICATE;
            return false;
        }
        pg_free_result($result);

        //Insert image
        $result = pg_query_params($dbconn, "INSERT INTO webproject.image (category_id, date_taken, hash, image) VALUES ($1, $2, $3, $4)", array($categoryId, $image->getDateTime()->format("Y-m-d H:i:s"), $image->getHash(), $image->getImage()));
        pg_close($dbconn);
        if(!$result){
            $status = ResultEnum::QUERY_FAILED;
            return false;
        }

        $status = ResultEnum::OK;
        return true;
    }

    /*******************************************************************************
     * Returns images uploaded by member $username
     * $status indicates the result
     * TODO minimize queries
     ******************************************************************************/
    public function findImages($username, &$status){
        $images = [];   //Images for return
        $user = $this->findUser($username,$status); //Get member object from $username
        if(!$user){
            return false;
        }

        $conf = Config::getInstance();
        $dbconn = pg_connect($conf->getDbDsn());
        //Fetch images from each member category
        foreach($user->getCategories() as $category){
            $result = pg_query_params($dbconn, "SELECT * FROM webproject.image i WHERE i.category_id = $1;", array($category->getId()));

            while($row = pg_fetch_object($result)){
                $images[] = new Image($row->category_id, $row->image, $row->date_taken, $row->id);
            }

            pg_free_result($result);
        }

        pg_close($dbconn);
        return $images;
    }

    /*******************************************************************************
     * Returns images from a specific category
     * $status indicates the result
     ******************************************************************************/
    public function findImagesInCategory($categoryId, &$status){
        $images = [];   //Images for return
        $conf = Config::getInstance();
        $dbconn = pg_connect($conf->getDbDsn());

        //Fetch image rows
        $result = pg_query_params($dbconn, "SELECT * FROM webproject.image i WHERE i.category_id = $1;", array($categoryId));

        //No rows returned
        if(pg_num_rows($result) == 0){
            $status = ResultEnum::CATEGORY_NOT_FOUND_OR_EMPTY;
            return false;
        }

        //Image objects from rows
        while($row = pg_fetch_object($result)){
            $images[] = new Image($row->category_id, $row->image, $row->date_taken, $row->id);
        }

        pg_free_result($result);
        pg_close($dbconn);
        $status = ResultEnum::OK;
        return $images;
    }

    /*******************************************************************************
     * Returns any image with hash $hash from member $username
     * $status indicates the result
     * TODO minimize queries
     ******************************************************************************/
    public function findImageDuplicate($username, $hash, &$status){
        $user = $this->findUser($username,$status); //Get user
        $conf = Config::getInstance();
        $dbconn = pg_connect($conf->getDbDsn());

        //Look through each category of $user
        $result = false;
        foreach($user->getCategories() as $category){
            $result = pg_query_params($dbconn, "SELECT * FROM webproject.image i WHERE i.category_id = $1 AND i.hash = $2;", array($category->getId(), $hash));
            if(pg_num_rows($result) != 0){
                break;
            }
        }
        pg_close($dbconn);

        if(!$result){
            $status = ResultEnum::QUERY_FAILED;
            return false;
        }

        //If we found a row then construct object
        $image = null;
        if($row = pg_fetch_object($result)){
            $image = new Image($row->category_id, $row->image, $row->date_taken, $row->id);
        }

        $status = ResultEnum::OK;
        return $image;
    }

    /*******************************************************************************
     * Inserts category $categoryName in member $username
     ******************************************************************************/
    public function insertCategory($username, $categoryName){
        $status = ResultEnum::OK;
        $user = $this->findUser($username,$status);
        $conf = Config::getInstance();
        $dbconn = pg_connect($conf->getDbDsn());
        $result = pg_query_params($dbconn, "INSERT INTO webproject.category (member_id, category) VALUES ($1, $2);", array($user->getId(), $categoryName));
        pg_close($dbconn);
        return $result;
    }

    /*******************************************************************************
     * Deletes category with id $categoryId from db
     ******************************************************************************/
    public function deleteCategory($categoryId){
        $conf = Config::getInstance();
        $dbconn = pg_connect($conf->getDbDsn());
        $result = pg_query_params($dbconn,"DELETE FROM webproject.category WHERE id=$1;", array($categoryId));
        pg_close($dbconn);
        return $result;
    }

    /*******************************************************************************
     * Insert new member $username, $password
     ******************************************************************************/
    public function insertUser($username, $password){
        $conf = Config::getInstance();
        $dbconn = pg_connect($conf->getDbDsn());
        $result = pg_query_params($dbconn, "INSERT INTO webproject.member (username, password) VALUES ($1, $2);", array($username, $password));

        if($result){
            $result = pg_query_params($dbconn, "SELECT id FROM webproject.member m WHERE m.username = $1;", array($username));
            $row = pg_fetch_object($result);

            $roleId = 1;
            $result = pg_query_params($dbconn, "INSERT INTO webproject.member_role (member_id, role_id) VALUES ($1, $2);", array($row->id, $roleId));
        }

        pg_close($dbconn);
        return $result;
    }

    /*******************************************************************************
     * Delete user with id $userId
     ******************************************************************************/
    public function deleteUser($userId){
        $conf = Config::getInstance();
        $dbconn = pg_connect($conf->getDbDsn());
        $result = pg_query_params($dbconn,"DELETE FROM webproject.member WHERE id=$1;", array($userId));
        pg_close($dbconn);
        return $result;
    }
}