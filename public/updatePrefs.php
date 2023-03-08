<?
  require('db.php');
  $data = json_decode(file_get_contents('php://input'));
  $userName = mysqli_real_escape_string($link, $data->{'userName'});
  $passhash = mysqli_real_escape_string($link, $data->{'passhash'});
  $pref = mysqli_real_escape_string($link, $data->{'pref'});
  $newval = mysqli_real_escape_string($link, $data->{'newval'});
  $success = false;
  $sql = 'SELECT * FROM users WHERE enabled = 1 AND LOWER(name) = LOWER("'.$userName.'") AND passhash = "'.$passhash.'"';
  $res = mysqli_query($link, $sql);
  if(mysqli_num_rows($res)){
    $row = mysqli_fetch_assoc($res);
    if($row['enabled']){
      $sql = 'UPDATE users SET ' . $pref . ' = ' . $newval . ', updated = "'.date("Y-m-d H:i:s").'" WHERE LOWER(name) = LOWER("'.$userName.'") AND enabled = 1';
      mysqli_query($link, $sql);
      $success = true;
    }
  }
  echo json_encode([$success,$sql,$userName]);
?>
