<?
  require('db.php');
  $sql = 'SELECT * FROM users WHERE name RLIKE "WHITEHOT +ROBOT"';
  $res = mysqli_query($link, $sql);
  echo  mysqli_num_rows($res) . "\n";
?>
