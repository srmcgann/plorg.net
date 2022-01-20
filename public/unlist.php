<?
  require('db.php');
  $data = json_decode(file_get_contents('php://input'));
  $pkh = mysqli_real_escape_string($link, $data->{'pkh'});
  $userID = intval(mysqli_real_escape_string($link, $data->{'userID'}));
  $itemID = intval(mysqli_real_escape_string($link, $data->{'itemID'}));
  $sql = "SELECT * FROM users WHERE pkh = \"$pkh\" AND id = $userID AND enabled = 1";
  $res = mysqli_query($link, $sql);
  if(mysqli_num_rows($res)){
    $sql = "UPDATE items SET listed=0 WHERE id=$itemID";
    if(mysqli_query($link, $sql)){
      echo json_encode([true, $itemID, $sql]);
    } else {
      echo json_encode([false, $sql]);
    }
  } else {
    echo json_encode([false, $sql]);
  }
?>