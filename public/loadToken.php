<?
  require('db.php');
  $data = json_decode(file_get_contents('php://input'));
  $token = mysqli_real_escape_string($link, $data->{'token'});
  $tokenMode = mysqli_real_escape_string($link, $data->{'tokenMode'});
  $loggedinUserName = mysqli_real_escape_string($link, $data->{'loggedinUserName'});
	$passhash = mysqli_real_escape_string($link, $data->{'passhash'});

  $admin = false;
  $includePrivate = false;

  if($loggedinUserName){
    $sql = 'SELECT * FROM users WHERE LOWER(name)  LOWER("' . $loggedinUserName . '") AND BINARY passhash = "' .  $passhash . '" AND enabled = 1';
    if($res = mysqli_query($link, $sql)){
      $row = mysqli_fetch_assoc($res);
      $pkh = $row['pkh'];
      $loggedinUserData = $row;
      if($row['isAdmin']) $admin = true;
      $loggedinPasshash = $row['passhash'];
    }
  }
  $sql='SELECT * FROM users WHERE LOWER(name) LOWER("' . $name . '") AND enabled = 1';
  $res = mysqli_query($link, $sql);
  $row=mysqli_fetch_assoc($res);
  //$userID = $row['id'];
  if(strtoupper($row['name']) === strtoupper($loggedinUserName) && $passhash == $loggedinPasshash  || $admin){
    $includePrivate = true;
  }

  unset($row['passhash']);
  $ret = $row;
  if($includePrivate){
    if($tokenMode=='hash'){
      $sql1=$sql = "SELECT * FROM items WHERE hash = \"" . $token . '"';
    } else {
      $sql1=$sql = "SELECT * FROM items WHERE id = \"" . $token . '"';
    }
  } else {
    if($tokenMode=='hash'){
      $sql1=$sql = "SELECT * FROM items WHERE hash = \"" . $token . '" AND private = 0';
    } else {
      $sql1=$sql = "SELECT * FROM items WHERE id = \"" . $token . '" AND private = 0';
    }
  }
  $res = mysqli_query($link, $sql);
  if(mysqli_num_rows($res)){
    $ret = mysqli_fetch_assoc($res);
    $hash = $ret['hash'];
    $sql = 'SELECT * FROM comments WHERE itemHash = "' . $hash . '" ORDER BY date DESC';
    $res = mysqli_query($link, $sql);
    $comments = [];
    for($i= 0; $i < mysqli_num_rows($res); ++$i){
      $comments[] = mysqli_fetch_assoc($res);
    }
    $ret['comments'] = $comments;
    echo json_encode([true, $ret, $sql]);
  }else{
    echo '[false]';
  }

?>
