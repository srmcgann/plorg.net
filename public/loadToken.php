<?
  require('db.php');
  $data = json_decode(file_get_contents('php://input'));
  $id = mysqli_real_escape_string($link, $data->{'id'});
  $loggedinUserName = mysqli_real_escape_string($link, $data->{'loggedinUserName'});
	$passhash = mysqli_real_escape_string($link, $data->{'passhash'});

  $admin = false;
  $includePrivate = false;

  if($loggedinUserName){
    $sql = 'SELECT * FROM users WHERE name LIKE "' . $loggedinUserName . '" AND passhash = "' .  $passhash . '" AND enabled = 1';
    if($res = mysqli_query($link, $sql)){
      $row = mysqli_fetch_assoc($res);
      $pkh = $row['pkh'];
      $loggedinUserData = $row;
      if($row['admin']) $admin = true;
      $loggedinPasshash = $row['passhash'];
    }
  }
  $sql='SELECT * FROM users WHERE name LIKE "' . $name . '" AND enabled = 1';
  $res = mysqli_query($link, $sql);
  $row=mysqli_fetch_assoc($res);
  $userID = $row['id'];
  if(strtoupper($row['name']) === strtoupper($loggedinUserName) && $passhash == $loggedinPasshash  || $admin){
    $includePrivate = true;
  }

  unset($row['passhash']);
  $ret = $row;
  if($includePrivate){
    $sql1=$sql = "SELECT * FROM items WHERE id = " . $id;
  } else {
    $sql1=$sql = "SELECT * FROM items WHERE id = " . $id . ' AND private = 0';
  }
  $res = mysqli_query($link, $sql);
  if(mysqli_num_rows($res)){
    $ret = mysqli_fetch_assoc($res);
    $sql = 'SELECT * FROM comments WHERE itemID = ' . $id . ' ORDER BY date DESC';
    $res = mysqli_query($link, $sql);
    $comments = [];
    for($i= 0; $i < mysqli_num_rows($res); ++$i){
      $comments[] = mysqli_fetch_assoc($res);
    }
    $ret['comments'] = $comments;
    echo json_encode([true, $ret]);
  }else{
    echo '[false]';
  }

?>
