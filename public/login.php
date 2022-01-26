<?
  require('db.php');
  $data = json_decode(file_get_contents('php://input'));
  $token = mysqli_real_escape_string($link, $data->{'token'});
  $password = mysqli_real_escape_string($link, $data->{'password'});
  $sql='SELECT * FROM users WHERE name LIKE "' . $token . '" AND enabled = 1';
  if(!$password || !$token) die();
  $res = mysqli_query($link, $sql);
  $hit = false;
  if(mysqli_num_rows($res)){
    for($i=0; $i<mysqli_num_rows($res) && !$hit;++$i){
      $row = mysqli_fetch_assoc($res);
      $hash = $row['hash'];
      $passhash = $row['passhash'];
      if($passhash == ''){
        echo json_encode([2, $row['name'], $row['id'], $row['avatar'], $row['pkh'], !!$row['isAdmin'], $connected, $passhash, $hash, $row['email'] ? true : false, 0]);
        die();
      } else if(password_verify($password, $passhash)){
        $hit = true;
        echo json_encode([true, $row['name'], $row['id'], $row['avatar'], $row['pkh'], !!$row['isAdmin'], $connected, $passhash, $hash]);
      }
    }
    if(!$hit){
      echo json_encode([false, $sql]);
    }
  } else {
    $sql='SELECT * FROM users WHERE pkh LIKE "' . $token . '" AND enabled = 1';
    $res = mysqli_query($link, $sql);
    if(mysqli_num_rows($res)){
      for($i=0; $i<mysqli_num_rows($res) && !$hit;++$i){
        $row = mysqli_fetch_assoc($res);
        $passhash = $row['passhash'];
        $hash = $row['hash'];
        if($passhash == ''){
          echo json_encode([2, $row['name'], $row['id'], $row['avatar'], $row['pkh'], !!$row['isAdmin'], $connected, $passhash, $hash, $row['email'] ? true : false, 1]);
          die();
        } else if(password_verify($password, $passhash)){
          $hit = true;
          echo json_encode([true, $row['name'], $row['id'], $row['avatar'], $row['pkh'], !!$row['isAdmin'], $connected, $passhash, $hash]);
        }
      }
      if(!$hit){
        echo json_encode([false, $sql]);
      }
    } else {
      $sql='SELECT * FROM users WHERE email LIKE "' . $token . '" AND enabled = 1';
      $res = mysqli_query($link, $sql);
      if(mysqli_num_rows($res)){
        for($i=0; $i<mysqli_num_rows($res) && !$hit;++$i){
          $row = mysqli_fetch_assoc($res);
          $passhash = $row['passhash'];
          $hash = $row['hash'];
          if($passhash == ''){
            echo json_encode([2, $row['name'], $row['id'], $row['avatar'], $row['pkh'], !!$row['isAdmin'], $connected, $passhash, $hash, $row['email'] ? true : false, 2]);
            die();
          } else if(password_verify($password, $passhash)){
            $hit = true;
            echo json_encode([true, $row['name'], $row['id'], $row['avatar'], $row['pkh'], !!$row['isAdmin'], $connected, $passhash, $hash]);
          }
        }
        if(!$hit){
          echo json_encode([false, $sql]);
        }
      } else {
        echo json_encode([false, $sql]);
      }
    }
  } 
?>

