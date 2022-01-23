<?
  $links =[];
  $output = shell_exec($command = "sudo -u cantelope /usr/local/bin/ipfs files stat /plorg.net.sync 2>&1");
  $links[] = ['https://ipfs.dweet.net/ipfs/' . explode("\n", $output)[0], 'site source'];
  $output = shell_exec($command = "sudo -u cantelope /usr/local/bin/ipfs files stat /sync 2>&1");
  $links[] = ['https://ipfs.dweet.net/ipfs/' . explode("\n", $output)[0], 'rolling database (live sync\'d w/ site)'];
  echo json_encode($links);
?>
