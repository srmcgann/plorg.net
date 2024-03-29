<?
  require('db.php');
  $data = json_decode(file_get_contents('php://input'));
  $token = mysqli_real_escape_string($link, $data->{'token'});
  $mode = mysqli_real_escape_string($link, $data->{'mode'});
  $newpass = mysqli_real_escape_string($link, $data->{'newpass'});
  $hash = mysqli_real_escape_string($link, $data->{'hash'});
  $pkh = mysqli_real_escape_string($link, $data->{'pkh'});
  $email = mysqli_real_escape_string($link, $data->{'email'});
  if($hash && $pkh){
    $passhash = password_hash($newpass, PASSWORD_DEFAULT);
    switch($mode){
      case 0:
        $sql = 'UPDATE users SET passhash = "'.$passhash.'" WHERE LOWER(name) = LOWER("'.$token.'") AND enabled = 1 AND pkh = "'.$pkh.'" AND LOWER(email) = LOWER("'.$email.'") AND hash = "'.$hash.'"';
      break;
      case 1:
        $sql = 'UPDATE users SET passhash = "'.$passhash.'" WHERE pkh = "'.$token.'" AND enabled = 1 AND pkh = "'.$pkh.'" AND LOWER(email) = LOWER("'.$email.'") AND hash = "'.$hash.'"';
      break;
      case 2:
        $sql = 'UPDATE users SET passhash = "'.$passhash.'" WHERE LOWER(email) = LOWER("'.$token.'") AND enabled = 1 AND pkh = "'.$pkh.'" AND LOWER(email) = LOWER("'.$email.'") AND hash = "'.$hash.'"';
      break;
    }
    mysqli_query($link, $sql);
    if(mysqli_affected_rows($link)){
      $sql = 'SELECT * FROM users WHERE hash = "'.$hash.'"';
      $res = mysqli_query($link, $sql);
      $row = mysqli_fetch_assoc($res);
      echo json_encode([true, $row['name'], $row['id'], $row['avatar'], $row['pkh'], !!$row['isAdmin'], 1, $passhash, $hash]);    
    } else {
      echo '[false]';
    }
  } else {
    echo '[false]';
  }
?>
