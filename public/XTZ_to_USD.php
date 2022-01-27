<?
  $db_user="user";
  $db_pass=file_get_contents('/home/cantelope/plorgpw');
  $db_host="localhost";
  $db="payway";
  $link = mysqli_connect($db_host, $db_user, $db_pass, $db);

  $sql = 'SELECT * FROM currencies WHERE currency = "XTZ"';
  if($res = mysqli_query($link, $sql)){
    echo json_encode([true, mysqli_fetch_assoc($res)['price_usd']]);
    die();
  }
  echo '[false]';
?>
