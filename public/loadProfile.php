<?
  require('db.php');
  $data = json_decode(file_get_contents('php://input'));
  $pkh = mysqli_real_escape_string($link, $data->{'pkh'});
  $connected = mysqli_real_escape_string($link, $data->{'connected'});
  if($pkh){
    $sql="SELECT * FROM users WHERE pkh = \"$pkh\" AND enabled = 1";
    $res = mysqli_query($link, $sql);
    if(mysqli_num_rows($res)){
      $row = mysqli_fetch_assoc($res);
      if(!!$row['connected'] !== !!$connected){
        $connectedStatus = $connected ? '1' : '0';
        $sql = "UPDATE users SET connected = $connectedStatus WHERE pkh = \"$pkh\" AND enabled = 1";
        mysqli_query($link, $sql);
      }
      echo json_encode([true, $row['name'], $row['id'], $row['avatar'], $row['pkh'], !!$row['isAdmin'], $connected]);
    } else {
      $defaultAvatar = '';
      $sql = "INSERT INTO users (name, avatar, pkh, prefs, originalName, walletAlias, connected) VALUES(\"$pkh\", \"$defaultAvatar\", \"$pkh\",\"\",\"$pkh\",\"\", 1)";
      $res = mysqli_query($link, $sql);
      echo json_encode([true, $pkh, mysqli_insert_id($link), $defaultAvatar, $pkh, false, !$connected, $sql]);
    }
  } else {
    echo json_encode([false]);
  }
?>

