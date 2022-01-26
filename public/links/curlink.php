<?
  require('../db.php');
  $output = shell_exec($command = "sudo -u cantelope /usr/local/bin/ipfs files stat /sync 2>&1");
  echo $ipfsURL . explode("\n", $output)[0];
?>
