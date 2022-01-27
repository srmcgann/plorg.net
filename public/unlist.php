<?
  require('db.php');
  $data = json_decode(file_get_contents('php://input'));
  $pkh = mysqli_real_escape_string($link, $data->{'pkh'});
  $userHash = mysqli_real_escape_string($link, $data->{'userHash'});
  $itemHash = mysqli_real_escape_string($link, $data->{'itemHash'});
  $sql = "SELECT * FROM users WHERE pkh = \"$pkh\" AND hash = \"$userHash\" AND enabled = 1";
  $res = mysqli_query($link, $sql);
  if(mysqli_num_rows($res)){
    $sql = "UPDATE items SET listed=0, updated = \"".date("Y-m-d H:i:s")."\" WHERE hash=\"$itemHash\"";
    if(mysqli_query($link, $sql)){
      echo json_encode([true, $itemID, $sql]);
    } else {
      echo json_encode([false, $sql]);
    }
  } else {
    echo json_encode([false, $sql]);
  }
?>