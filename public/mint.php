<?
  require('db.php');
  $data = json_decode(file_get_contents('php://input'));
  $pkh = mysqli_real_escape_string($link, $data->{'pkh'});
  $userHash = mysqli_real_escape_string($link, $data->{'userHash'});
  $sql = "SELECT * FROM users WHERE pkh = \"$pkh\" AND hash = \"$userHash\" AND enabled = 1";
  $res = mysqli_query($link, $sql);
  if(mysqli_num_rows($res)){
    $row = mysqli_fetch_assoc($res);
    $creatorHash = $userHash = $row['hash'];
    $userID = $row['id'];
    
    $title = mysqli_real_escape_string($link, $data->{'title'});
    $description = mysqli_real_escape_string($link, $data->{'description'});
    $price = mysqli_real_escape_string($link, $data->{'price'});
    $royalties = mysqli_real_escape_string($link, $data->{'royalties'});
    $editions = intval(mysqli_real_escape_string($link, $data->{'editions'}));
    $image = mysqli_real_escape_string($link, $data->{'image'});
    $suffix = mysqli_real_escape_string($link, $data->{'suffix'});
    $ipfs = mysqli_real_escape_string($link, $data->{'ipfs'});
    $allowDownload = mysqli_real_escape_string($link, $data->{'allowDownload'});
    $type = mysqli_real_escape_string($link, $data->{'type'});
    $size = mysqli_real_escape_string($link, $data->{'size'});
    $userName = mysqli_real_escape_string($link, $data->{'userName'});
    $userHash = $creatorHash = mysqli_real_escape_string($link, $data->{'userHash'});
    $metadata = mysqli_real_escape_string($link, $data->{'metadata'});
    $hash = md5(hash_file('md5', $ipfsURL . $ipfs) . strtotime('now') . rand());
    $sql="INSERT INTO items (
            title,
            address,
            userID,
            description,
            image,
            metaData,
            price,
            royalties,
            edition,
            history,
            ipfs,
            allowDownload,
            suffix,
            creatorID,
            editions,
            mints,
            type,
            userHash,
            creatorHash,
            size,
            listed,
            originalContent,
            hash
            ) VALUES(
            \"$title\",
            \"$pkh\",
            $userID,
            \"$description\",
            \"$image\",
            \"$metaData\",
            \"$price\",
            \"$royalties\",
            1,
            \"[]\",
            \"$ipfs\",
            \"$allowDownload\",
            \"$suffix\",
            $userID,
            \"$editions\",
            0,
            \"$type\",
            \"$userHash\",
            \"$creatorHash\",
            \"$size\",
            1,
            1,
            \"$hash\")";
    $res = mysqli_query($link, $sql);
    if($insertID = mysqli_insert_id($link)){
      echo json_encode([true, $insertID, $hash, $sql]);
    } else {
      echo json_encode([false, $sql]);
    }
  } else {
    echo json_encode([false, $sql]);
  }
?>
