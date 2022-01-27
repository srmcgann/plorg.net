<?
  require('db.php');
  $data = json_decode(file_get_contents('php://input'));
  $userName = mysqli_real_escape_string($link, $data->{'userName'});
  $newUserName = mysqli_real_escape_string($link, $data->{'newUserName'});
  $userHash = mysqli_real_escape_string($link, $data->{'userHash'});
  $available=str_replace(chr(10),'',file_get_contents('https://' . $baseURL . "/checkUserNameAvailability.php?userName=".urlencode($newUserName)))==="true";
  $success = false;
  if($available){
    $pkh = mysqli_real_escape_string($link, $data->{'pkh'});
    $sql = 'SELECT * FROM users WHERE (name LIKE "'.$userName.'" OR pkh LIKE "'.$userName.'") AND pkh = "'.$pkh.'" AND enabled = 1';
    $res = mysqli_query($link, $sql);
    if(mysqli_num_rows($res)){
      $row = mysqli_fetch_assoc($res);
      $userHash = $row['hash'];
      $sql = 'UPDATE users SET name = "'.$newUserName.'", updated = "'.date("Y-m-d H:i:s").'" WHERE (name LIKE "'.$userName.'" OR pkh LIKE "'.$userName.'") AND pkh = "'.$pkh.'" AND enabled = 1';
      mysqli_query($link, $sql);
      $sql = 'UPDATE items SET author = "'.$newUserName.'", updated = "'.date("Y-m-d H:i:s").'" WHERE creatorHash = "'.$userHash.'"';
      mysqli_query($link, $sql);
      
      $success = true;
    }
  }
  echo json_encode([$success]);
?>
