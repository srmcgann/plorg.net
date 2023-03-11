<?
  require('db.php');
  $data = json_decode(file_get_contents('php://input'));
  $passhash = mysqli_real_escape_string($link, $data->{'passhash'});
  $connected = mysqli_real_escape_string($link, $data->{'connected'});
  if($passhash){
    $sql="SELECT * FROM users WHERE BINARY passhash = \"$passhash\" AND enabled = 1";
    $res = mysqli_query($link, $sql);
    if(mysqli_num_rows($res)){
      $row = mysqli_fetch_assoc($res);
      if(!!$row['connected'] !== !!$connected){
        $connectedStatus = $connected ? '1' : '0';
        $sql = "UPDATE users SET connected = $connectedStatus WHERE passhash = \"$passhash\" AND enabled = 1";
        mysqli_query($link, $sql);
      }
      echo json_encode([true, $row['name'], $row['id'], $row['avatar'], $row['pkh'], !!$row['isAdmin'], $connected, $row['passhash'], $row['hash']]);
    } else {
      echo json_encode([false, $sql]);
    }
  } else {
    echo json_encode([false, $sql]);
  }
?>

