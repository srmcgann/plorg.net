<?
  require('db.php');
  $data = json_decode(file_get_contents('php://input'));
  $pkh = mysqli_real_escape_string($link, $data->{'pkh'});
  $userHash = mysqli_real_escape_string($link, $data->{'userHash'});
  $itemHash = mysqli_real_escape_string($link, $data->{'itemHash'});
  $itemID = intval(mysqli_real_escape_string($link, $data->{'itemID'}));
  $sql = "SELECT * FROM users WHERE pkh = \"$pkh\" AND hash = \"$userHash\" AND enabled = 1";
  $res = mysqli_query($link, $sql);
  if(mysqli_num_rows($res)){
    $price = mysqli_real_escape_string($link, $data->{'price'});
    $royalties = mysqli_real_escape_string($link, $data->{'royalties'});
    $editions = intval(mysqli_real_escape_string($link, $data->{'editions'}));
    $sql = "UPDATE items SET price=\"$price\", royalties=\"$royalties\", edition=1, editions=$editions, listed=1 WHERE hash=\"$itemHash\"";
    if(mysqli_query($link, $sql)){
      echo json_encode([true, $itemID, $itemHash, $sql]);
    } else {
      echo json_encode([false, $sql]);
    }
  } else {
    echo json_encode([false, $sql]);
  }
?>