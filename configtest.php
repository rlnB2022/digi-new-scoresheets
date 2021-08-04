<?php
    define('DB_SERVER', 'mysql.digidugout.com');
    define('DB_USERNAME', 'rick99gtp');
    define('DB_PASSWORD', 'RbLbNb2019*!');
    define('DB_NAME', 'digidugout_main');
 
    /* Attempt to connect to MySQL database */
    $link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
 
    // Check connection
    if($link === false){
        die("ERROR: Could not connect. " . mysqli_connect_error());
    }
?>