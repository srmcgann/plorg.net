<?
  require('db.php');
  $data = json_decode(file_get_contents('php://input'));
  $userName = mysqli_real_escape_string($link, $data->{'userName'});
  $userHash = mysqli_real_escape_string($link, $data->{'userHash'});
  $passhash = mysqli_real_escape_string($link, $data->{'passhash'});
  $comment = mysqli_real_escape_string($link, $data->{'comment'});
  $itemID = mysqli_real_escape_string($link, $data->{'itemID'});
  $itemHash = mysqli_real_escape_string($link, $data->{'itemHash'});
  $sql = 'SELECT * FROM users WHERE LOWER(name) = LOWER("'.$userName.'") AND BINARY passhash = "'.$passhash.'" AND enabled = 1;';
  $res = mysqli_query($link, $sql);
  $success = false;
  if(mysqli_num_rows($res)){
    $row = mysqli_fetch_assoc($res);
    if($row['enabled']){
      $hash=md5($row['pkh'] . strtotime('now') . rand());
      $sql1=$sql = 'INSERT INTO comments (text, itemHash, itemID, userHash, userID, hash) VALUES("'.$comment.'","'.$itemHash.'", '.$itemID.',"'.$userHash.'", '.$row['id'].', "'.$hash.'")';
      mysqli_query($link, $sql);
      $success = true;
    }
  }

  if($success){
    $insertID = mysqli_insert_id($link);
    $sql = 'SELECT date FROM comments WHERE id = ' . $insertID;
    $res = mysqli_query($link, $sql);
    $row = mysqli_fetch_assoc($res);
    echo json_encode([$success, $insertID, $row['date'], $hash, $sql1]);
  } else {
    echo json_encode([$success]);
  }
?>
