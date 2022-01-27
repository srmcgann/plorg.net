<?
  //require('../db.php');
  $ipfsURL = 'https://ipfs.dweet.net/ipfs/';
  $output = shell_exec($command = "sudo -u cantelope /usr/local/bin/ipfs files stat /sync 2>&1");
  $file = explode(' ',shell_exec($command = "sudo -u cantelope /usr/local/bin/ipfs ls " .  explode("\n", $output)[0]))[2];
  echo explode("\n", $ipfsURL . explode("\n", $output)[0] . '/' . $file)[0];
?>
