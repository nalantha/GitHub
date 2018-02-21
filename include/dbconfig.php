<?php
class dbconfig {
 
   protected static $DB_SERVER = "localhost";
   protected static $DB_USERNAME = "pvdiadmin";
   protected static $DB_PASSWORD = "h2ng2m2WA";
   protected static $DB_DATABASE = "pvdi";
 
   static $db; 
   function __construct() {
       self::$db = self::connect(); 
   }
 
   // open connection
   protected static function connect() {
       $link = mysqli_connect(self::$DB_SERVER, self::$DB_USERNAME, 
                              self::$DB_PASSWORD, self::$DB_DATABASE); 
       if (mysqli_connect_errno()){
           echo "Failed to connect to MySQL: " . mysqli_connect_error();
       }
       return $link;
   }
 
}

class webcaldb {
 
   protected static $DB_SERVER = "localhost";
   protected static $DB_USERNAME = "webcaladmin";
   protected static $DB_PASSWORD = "webcaladmin1234";
   protected static $DB_DATABASE = "webcal";

   static $db;
   function __construct() {
       self::$db = self::connect();
   }

   // open connection
   public static function connect() {
       $link = mysqli_connect(self::$DB_SERVER, self::$DB_USERNAME,
                              self::$DB_PASSWORD, self::$DB_DATABASE);
       if (mysqli_connect_errno()){
           echo "Failed to connect to MySQL: " . mysqli_connect_error();
       }
       return $link;
   }
}
?>
