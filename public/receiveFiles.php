<?
  if($_POST['pkh'] && isset($_FILES['file0'])){
    $maxFileSize=104857600; // 100Mb
    require('db.php');
    $pkh = $_POST['pkh'];
    $sql = 'SELECT enabled FROM users WHERE pkh = "'.$pkh.'" AND enabled = 1';
    $res = mysqli_query($link, $sql);
    if(!mysqli_num_rows($res)) die();
    $hash=md5($_POST['pkh']);
    $uploads_dir = "/var/www/html/plorg.net/dist_public/scratchfolder/$hash";
    mkdir($uploads_dir);
    $maxSimultaneousUploads = 1;
    $ct=0;
    foreach ($_FILES as $key => $value) {
      if($ct < $maxSimultaneousUploads){
        $tmp_name = $value["tmp_name"];
        $name = basename($_FILES[$key]['name']);
        $size = $_FILES[$key]['size'];
        $allegedType = $_FILES[$key]['type'];
        if($size > 0 && $size < $maxFileSize){
          move_uploaded_file($tmp_name, "$uploads_dir/$name");
          $type = mime_content_type("$uploads_dir/$name");
        }
        $ct++;
      }
    }
    if($size < $maxFileSize){
      $escaped_name = escapeshellarg($name);
      if($type == 'application/x-zip-compressed'){
        $zipOutput = shell_exec("cd $uploads_dir; unzip $escaped_name 2>&1; rm $escaped_name");
        chdir($uploads_dir);
        $files = glob('*');
        $indexFound = false;
        for($i=0; $i<sizeof($files); ++$i){
          if(strtolower($files[$i]) == 'index.html' || strtolower($files[$i]) == 'index.htm'){
            $indexFound = true;
          }
        }
        if($indexFound){
          $output = shell_exec($command = "sudo -u cantelope /usr/local/bin/ipfs add $uploads_dir -r -q 2>&1");
        } else {
          echo json_encode([false, 'index file not found!']);
          die();
        }
      } else {
        $output = shell_exec($command = "sudo -u cantelope /usr/local/bin/ipfs add ".escapeshellarg("$uploads_dir/$name")." -q 2>&1");
      }
      $t = 2;
      
      $ipfs_hash=($s=explode("\n", $output))[sizeof($s)-$t];
      shell_exec("rm -rf $uploads_dir");
      echo json_encode([true, $ipfsURL . $ipfs_hash, $type, $size, $allegedType == $type ? 1 : 0]);
      die();
    } else {
      echo json_encode([false, 'your file has too much scrumf! keep it under 100MB eh?']);
    }
  }
  echo json_encode([false]);
?>