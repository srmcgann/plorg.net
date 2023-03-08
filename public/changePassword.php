<?
  require('db.php');
  $data = json_decode(file_get_contents('php://input'));
  $userName = mysqli_real_escape_string($link, $data->{'userName'});
  $currentPassword = mysqli_real_escape_string($link, $data->{'currentPassword'});
  $newPassword = mysqli_real_escape_string($link, $data->{'newPassword'});
  $sql = 'SELECT passhash FROM users WHERE LOWER(name) = LOWER("'.$userName.'") AND enabled = 1';
  $res = mysqli_query($link, $sql);
  $success = false;
  if(mysqli_num_rows($res)){
    $row = mysqli_fetch_assoc($res);
    if(password_verify($currentPassword, $row['passhash'])){
      $newPasshash = password_hash($newPassword, PASSWORD_DEFAULT);
      $sql = 'UPDATE users SET passhash = "'.$newPasshash.'", updated = "'.date("Y-m-d H:i:s").'" WHERE LOWER(name) = LOWER("'.$userName.'") AND enabled = 1';
      mysqli_query($link, $sql);
      $success = true;
    }
  }
  echo json_encode([$success, $success ? $newPasshash : '']);
?>
