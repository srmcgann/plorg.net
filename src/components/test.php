<?
  require('/var/www/html/code.whitehot.ninja/public/db.php');
  $teststring1 = '"ele"';
  $teststring2 = '"ele"';
  $teststring3 = '\"ele\"';
  $teststring4 = '\\"ele\\"';
  echo mysqli_real_escape_string($link, $teststring1) . "\n";
  echo mysqli_real_escape_string($link, $teststring2) . "\n";
  echo mysqli_real_escape_string($link, $teststring3) . "\n";
  echo mysqli_real_escape_string($link, $teststring4) . "\n";
?>
