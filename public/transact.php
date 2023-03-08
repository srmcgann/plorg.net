<?
  require('db.php');
  $data = json_decode(file_get_contents('php://input'));
  $sender = escapeshellarg(mysqli_real_escape_string($link, $data->{'sender'}));
  $recipient = escapeshellarg(mysqli_real_escape_string($link, $data->{'recipient'}));
  $amount = escapeshellarg(mysqli_real_escape_string($link, $data->{'amount'}));
  $mnemonic = mysqli_real_escape_string($link, $data->{'mnemonic'});
  $agreeToFees = mysqli_real_escape_string($link, $data->{'agreeToFees'});
  $suffix = mysqli_real_escape_string($link, $data->{'suffix'});
  $userName = mysqli_real_escape_string($link, $data->{'userName'});
  $userHash = mysqli_real_escape_string($link, $data->{'userHash'});
  $passhash = mysqli_real_escape_string($link, $data->{'passhash'});
  $mint = $data->{'mint'}->{'item'};
  if(isset($mint->{'address'})){
    $sql = "SELECT pkh FROM users WHERE hash = \"" . $mint->{'userHash'} . '"';
    $res = mysqli_query($link, $sql);
    if(mysqli_num_rows($res)){
      $row=mysqli_fetch_assoc($res);
      $purchaserPKH = $row['pkh'];
      if(escapeshellarg($row['pkh']) != $recipient){
        echo json_encode([false, 'recipient address does not match the item owner. whhhhaaaaa?',$row['pkh'], $recipient, intval($mint->{'userID'})]);
        die();
      }
    } else {
      echo json_encode([false, 'item-user wallet address was not found in the database! whhhhaaaaa?']);
      die();
    }
  }
  
  $sql = 'SELECT * FROM users WHERE LOWER(name) = LOWER("'.$userName.'") AND passhash = "'.$passhash.'" AND enabled = 1';
  $res = mysqli_query($link, $sql);
  if(mysqli_num_rows($res)){
    if($recipient && $amount && $mnemonic){
      $passphrase = str_replace(' ', '_', str_replace("\n", '', $mnemonic));
      $alias = md5($passphrase);
      $filename = $alias;
      file_put_contents('./temp/'.$filename, $passphrase);
      $burn = $agreeToFees == 'true' ? ('--burn-cap .06425') : '';
      $doRoyalties = $mint->{'id'} && !intval($mint->{'originalContent'});
      $royalty = 0;
      if($doRoyalties){
        $royalty = $mint->{'royalties'}/100*floatval(str_replace("'",'',$amount));
        $adjustedAmount = escapeshellarg(floatval(str_replace("'",'',$amount)) - $royalty);
      }else{
        $adjustedAmount = $amount;
      }
      $command = "sudo octez-client transfer $adjustedAmount from $sender to $recipient $burn -D < /var/www/html/plorg.dweet.net/dist_public/temp/$filename 2>&1";
      $output = shell_exec($command);
      $prelim_success = strpos($output, 'This transaction was successfully applied') !== false;
      if($prelim_success){
        if($mint->{'id'}){
          $row=mysqli_fetch_assoc($res);
          $userID = $row['id'];
          $newMintID = 0;
          $sql = 'SELECT * FROM items WHERE hash = "' . $mint->{'hash'} . '" AND enabled = 1';
          $res = mysqli_query($link, $sql);
          if(mysqli_num_rows($res)){
            $row = mysqli_fetch_assoc($res);
            $newHash = md5(hash_file('md5', $ipfsURL . $row['ipfs']) . strtotime('now') . rand());
            $targetHash = $row['hash'];
            $row['title'] = mysqli_real_escape_string($link, $row['title']);
            $row['description'] = mysqli_real_escape_string($link, $row['description']);
            if($row['mints']<$row['editions']){
              $ak=[
                'title',
                'address',
                'userID',
                'userHash',
                'creatorHash',
                'description',
                'image',
                'private',
                'metaData',
                'views',
                'hash',
                'price',
                'royalties',
                'history',
                'created',
                'ipfs',
                'creatorID',
                'editions',
                'edition',
                'mints',
                'enabled',
                'type',
                'size',
                'listed',
                'originalContent',
                'suffix',
                'allowDownload'
              ];
              //$ak=array_keys($row);
              //unset($ak[0]); //id
              //unset($ak[3]); //date
              $sql = 'INSERT INTO items ('.implode(',',$ak).') VALUES("'.
              ($row['title']).'","'.
              ($purchaserPKH).'","'.
              ($userID).'","'.
              ($userHash).'","'.
              ($row['creatorHash']).'","'.
              ($row['description']).'","'.
              ($row['image']).'","'.
              ($row['private']).'","'.
              ($row['metaData']).'",'.
              (0).',"'.
              ($newHash).'","'.
              ($row['price']).'","'.
              ($row['royalties']).'","'.
              ($row['history']).'","'.
              ($row['created']).'","'.
              ($row['ipfs']).'",'.
              ($row['creatorID']).','.
              ($row['editions']).','.
              ($row['mints']+1).','.
              (0).','.
              ($row['enabled']).',"'.
              ($row['type']).'",'.
              ($row['size']).','.
              (0).','.
              (0).',"'.
              ($suffix).'",'.
              (1).')';
              if(mysqli_query($link, $sql1=$sql)){
                $newMintID = mysqli_insert_id($link);
              }
              if($mint->{'id'} && $newMintID){
                  $sql = "UPDATE items SET mints = mints+1, updated = \"".date("Y-m-d H:i:s")."\" WHERE hash = \"$targetHash\"";
                  mysqli_query($link, $sql);
              }
            } else {
              echo json_encode([false, 'the item to be minted has been minted to completion!']);
            }
          }else{
            echo json_encode([false, 'the item to be minted was not found in the database! whhhhaaaaa?']);
          }
          if(!$newMintID){
            echo json_encode([false, 'creating the new token failed for unknown reasons! whhhhaaaaa?',$sql1]);
            die();
          }
        }
        $command = "sudo octez-client transfer $adjustedAmount from $sender to $recipient $burn < /var/www/html/plorg.dweet.net/dist_public/temp/$filename 2>&1";
        $output = shell_exec($command);
        $success = strpos($output, 'Operation found in block:') !== false;
        if($success && $royalty){
          $sql2 = "SELECT pkh FROM users WHERE hash = \"" . $mint->{'creatorHash'} . '"';
          if($res2 = mysqli_query($link, $sql2)){
            $creatorpkh = mysqli_fetch_assoc($res2)['pkh'];
            $burn = $agreeToFees == 'true' ? ('--burn-cap .06425') : '';
            $royaltiesCommand = "sudo octez-client transfer $royalty from $sender to $creatorpkh $burn < /var/www/html/plorg.dweet.net/dist_public/temp/$filename 2>&1";
            $royaltiesOutput = shell_exec($royaltiesCommand);
          } else {
            echo json_encode([false, 'transaction failed, but mint succeeded! whhhhaaaaa?']);
            die();
          }
        }
        if($mint->{'id'} && $newMintID && !$success){
          echo json_encode([false, 'transaction failed, but mint succeeded! whhhhaaaaa?']);
          die();
        }
        echo json_encode([$success, $output, $command, $mint, $mint->{'id'} ? $newMintID : '', $newHash, $sql1]);
      } else {
        echo json_encode([false, 'the payment failed', $command]);
        die();
      }
      @unlink('./temp/'.$filename);
    } else {
      echo json_encode([false, '', $sql]);
      die();
    }
  }
?>
