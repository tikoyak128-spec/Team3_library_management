
<?php
 define('BASE_URL' , 'https://localhost//?Library-Management-System');

  $servername = "localhost";
  $username = 'root';
  $db_name = 'library_management';
  $password = '';

  try{
    $conn = new PDO("mysql:host=$servername;dbname=$db_name",$username,$password);
    $conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    
    echo "Your library connect successfully";
  }catch(PDOException $e){
    echo "connect failed ".$e->getMessage();
  }

  


?>