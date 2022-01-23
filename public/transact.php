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
  $passhash = mysqli_real_escape_string($link, $data->{'passhash'});
  $mint = $data->{'mint'}->{'item'};
  if(isset($mint->{'address'})){
    $sql = "SELECT pkh FROM users WHERE id = " . intval($mint->{'userID'});
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
  
  $sql = 'SELECT * FROM users WHERE name LIKE "%'.$userName.'%" AND passhash = "'.$passhash.'" AND enabled = 1';
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
      $command = "TEZOS_CLIENT_DIR=/home/cantelope/.local/bin/tezos-client;sudo /home/cantelope/.local/bin/tezos-client transfer $adjustedAmount from $sender to $recipient $burn -D < /var/www/html/plorg.net/dist_public/temp/$filename 2>&1";
      $output = shell_exec($command);
      $prelim_success = strpos($output, 'This transaction was successfully applied') !== false;
      if($prelim_success){
        if($mint->{'id'}){
          $row=mysqli_fetch_assoc($res);
          $userID = $row['id'];
          $newMintID = 0;
          $sql = 'SELECT * FROM items WHERE id = ' . $mint->{'id'} . ' AND enabled = 1';
          $res = mysqli_query($link, $sql);
          if(mysqli_num_rows($res)){
            $row = mysqli_fetch_assoc($res);
            $targetID = $row['id'];
            if($row['mints']<$row['editions']){
              $ak=[
                'title',
                'address',
                'userID',
                'description',
                'image',
                'private',
                'metaData',
                'views',
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
              ($row['description']).'","'.
              ($row['image']).'","'.
              ($row['private']).'","'.
              ($row['metaData']).'",'.
              (0).',"'.
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
                  $sql = "UPDATE items SET mints = mints+1 WHERE id = $targetID";
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
        $command = "TEZOS_CLIENT_DIR=/home/cantelope/.local/bin/tezos-client;sudo /home/cantelope/.local/bin/tezos-client transfer $adjustedAmount from $sender to $recipient $burn < /var/www/html/plorg.net/dist_public/temp/$filename 2>&1";
        $output = shell_exec($command);
        $success = strpos($output, 'Operation found in block:') !== false;
        if($success && $royalty){
          $sql2 = "SELECT pkh FROM users WHERE id = " . intval($mint->{'creatorID'});
          if($res2 = mysqli_query($link, $sql2)){
            $creatorpkh = mysqli_fetch_assoc($res2)['pkh'];
            $burn = $agreeToFees == 'true' ? ('--burn-cap .06425') : '';
            $royaltiesCommand = "TEZOS_CLIENT_DIR=/home/cantelope/.local/bin/tezos-client;sudo /home/cantelope/.local/bin/tezos-client transfer $royalty from $sender to $creatorpkh $burn < /var/www/html/plorg.net/dist_public/temp/$filename 2>&1";
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
        echo json_encode([$success, $output, $command, $mint, $mint->{'id'} ? $newMintID : '']);
      } else {
        echo json_encode([false, 'the payment failed', $command]);
        die();
      }
      @unlink('./temp/'.$filename);
    } else {
      echo json_encode([false, '']);
      die();
    }
  }
?>