<?
  require('../db.php');
  $output = shell_exec($command = "sudo -u cantelope /usr/local/bin/ipfs files stat /syncWallets 2>&1");
  $file = explode(' ',shell_exec($command = "sudo -u cantelope /usr/local/bin/ipfs ls " .  explode("\n", $output)[0]))[2];
  echo explode("\n", $ipfsURL . explode("\n", $output)[0] . '/' . $file)[0];
?>
