<?
  require('db.php');
  $data = json_decode(file_get_contents('php://input'));
  $userName = mysqli_real_escape_string($link, $data->{'userName'});
  $passhash = mysqli_real_escape_string($link, $data->{'passhash'});
  $sql = 'SELECT * FROM users WHERE LOWER(name) = LOWER("'.$userName.'") AND BINARY passhash = "'.$passhash.'" AND enabled = 1;';
  $res = mysqli_query($link, $sql);
  if(mysqli_num_rows($res)){
    $row = mysqli_fetch_assoc($res);
    echo json_encode([!!mysqli_num_rows($res), $row['name'], $row['id'], $row['passhash'], $row['avatar'], $row['transactionsPerPage'], $row['isAdmin']]);
  } else {
    echo json_encode([false,'']);
  }
?>
