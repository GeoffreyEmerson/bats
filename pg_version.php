<html><body>

<?php
  $dbconn = pg_connect("host=$_ENV["HOST"] port=$_ENV["PORT"] dbname=$_ENV["DBNAME"] user=$_ENV["USER"] password=$_ENV["USER_PASS"]");

  $ver = pg_version($dbconn);

  echo $ver['client'];
?>

</body>
