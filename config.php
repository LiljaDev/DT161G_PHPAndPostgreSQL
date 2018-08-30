<?PHP
/*******************************************************************************
 * Projekt, Kurs: DT161G
 * File: config.php
 * Desc: Config file for Projekt, used by Config class
 *       Returns array of config values on include
 *
 * Johan Lilja
 * joli1407
 * joli1407@student.miun.se
 ******************************************************************************/
return [
    "db" => [
        "host" => "studentpsql.miun.se",
        "port" => "5432",
        "dbName" => "joli1407",
        "user" => "joli1407",
        "pw" => "TFCN8vLFX"
    ],
    "default_link_array" => [
        "Home" => "index.php"
    ],
    "member_link_array" => [
        "Home" => "index.php",
        "User page" => "userpage.php"
    ],
    "admin_link_array" => [
        "Home" => "index.php",
        "User page" => "userpage.php",
        "Adminsida" => "admin.php"
    ]
];
?>