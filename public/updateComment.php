<?
  require('db.php');
  $data = json_decode(file_get_contents('php://input'));
  $userName = mysqli_real_escape_string($link, $data->{'userName'});
  $passhash = mysqli_real_escape_string($link, $data->{'passhash'});
  $comment = mysqli_real_escape_string($link, $data->{'comment'});
  $commentHash = mysqli_real_escape_string($link, $data->{'commentHash'});
  $sql = 'SELECT * FROM users WHERE LOWER(name) = LOWER("'.$userName.'") AND passhash = "'.$passhash.'" AND enabled = 1;';
  $res = mysqli_query($link, $sql);
  $success = false;
  if(mysqli_num_rows($res)){
    $row = mysqli_fetch_assoc($res);
    if($row['enabled']){
      if($row['admin']){
        $sql = 'UPDATE comments SET text = "'.$comment.'", updated = "'.date("Y-m-d H:i:s").'" WHERE hash = "'.$commentHash . '"';
      } else {
        $sql = 'UPDATE comments SET text = "'.$comment.'", updated = "'.date("Y-m-d H:i:s").'" WHERE hash = "'.$commentHash . '" AND userHash = "' . $row['hash'] . '"';
      }
      mysqli_query($link, $sql);
      $success = true;
    }
  }
  echo json_encode([$success]);
?>
