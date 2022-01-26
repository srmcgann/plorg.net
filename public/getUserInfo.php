
<?
  require('db.php');
  $data = json_decode(file_get_contents('php://input'));
  $userHash = mysqli_real_escape_string($link, $data->{'userHash'});
  $creatorHash = mysqli_real_escape_string($link, $data->{'creatorHash'});
  $sql='SELECT * FROM users WHERE hash = "' . $userHash . '" AND enabled = 1';
  if($res = mysqli_query($link, $sql)){
    $ar = [];
    $row=mysqli_fetch_assoc($res);
    $ar['name'] = $row['name'];
    $ar['pkh'] = $row['pkh'];
    $ar['avatar'] = $row['avatar'];
    $ar['joined'] = $row['joined'];
    $ar['hash'] = $row['hash'];
    $ar['id'] = $row['id'];
    $sql = 'SELECT id FROM items WHERE creatorHash = "' . $userHash . '" AND userHash = "' . $userHash . '"';
    $res = mysqli_query($link, $sql);
    $numCreated = mysqli_num_rows($res);
    $ar['numCreated'] = $numCreated;
    $sql = 'SELECT id FROM items WHERE userHash = "' . $userHash . '"';
    $res = mysqli_query($link, $sql);
    $numOwned = mysqli_num_rows($res);
    $ar['numOwned'] = $numOwned;
    
    if($creatorHash != $userHash){
      $ar2 = [];
      $sql='SELECT * FROM users WHERE hash = "' . $creatorHash . '"';
      $res = mysqli_query($link, $sql);
      $row=mysqli_fetch_assoc($res);
      $ar2['name'] = $row['name'];
      $ar2['pkh'] = $row['pkh'];
      $ar2['avatar'] = $row['avatar'];
      $ar2['joined'] = $row['joined'];
      $ar2['hash'] = $row['hash'];
      $ar2['id'] = $row['id'];
      echo json_encode([1, $ar, $ar2]);
    } else {
      echo json_encode([1, $ar, $ar]);
    }
    die();
  }
  echo json_encode([0]);
  
?>
