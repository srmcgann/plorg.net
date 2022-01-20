<?
  require('db.php');
  $data = json_decode(file_get_contents('php://input'));
  $pkh = mysqli_real_escape_string($link, $data->{'pkh'});
  $connected = false;
  if($pkh){
    $sql="SELECT connected FROM users WHERE pkh = \"$pkh\" AND enabled = 1";
    $res = mysqli_query($link, $sql);
    if(mysqli_num_rows($res)){
      $row = mysqli_fetch_assoc($res);
      if(!!$row['connected']) $connected = true;
    }
  }
  echo json_encode([$connected]);
?>

