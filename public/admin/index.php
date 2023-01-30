<? require('../db.php'); ?>
<!DOCTYPE html>
<html>
  <head>
    <style>
      html,body{
        overflow-x: hidden;
        background: #000;
        color: #fff;
        font-family: courier, arial;
        font-size: 20px;
        line-height: 1.2em;
        margin: 0;
        min-height: 100vh;
      }
      .section_title{
        margin-top:10px;
        text-align: center;
        background:#234;
      }
      .dataTable{
        border-collapse: collapse;
      }
      td{
        padding: 6px;
      }
      #main{
        
      }
      #total{
        display: inline-block;
      }
    </style>
  </head>
  <body>
    <script>
      convert = total = 0;
      fetch('/XTZ_to_USD.php').then(res=>res.json()).then(data=>{
        convert = +data[1]
      })
      getBal = pkh =>{
        let sendData = {pkh}
        fetch('/getBalanceFromPkh.php',{
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify(sendData),
        })
        .then(res=>res.json()).then(data=>{
          document.querySelectorAll('#'+pkh)[0].innerHTML = data[1]
          if(+data[1]>1) document.querySelector('#row_'+pkh).style.background='#400'
          total += +data[1]
          document.querySelector('#total').innerHTML = +total + " ($" + (Math.round(total*convert*100)/100)+")"
        })
      }
    </script>
    <div class="section_title">
      Total all wallets: <div id="total">~</div>
    </div>
    <div class="section_title">
      user data
    </div>
    <div id="main">
      <table class="dataTable">
        <?
          $headers = "<tr><th>pkh</th><th>bal</th><th>originalName</th><th>name</th><th>originalName</th><th>email</th></tr>";
          $sql = "SELECT * FROM users";
          $res = mysqli_query($link, $sql);
          for($i=0; $i<mysqli_num_rows($res); ++$i){
            $row = mysqli_fetch_assoc($res);
            if(!($i%5)) echo $headers;
            echo "<tr id=\"row_{$row['pkh']}\">";
            echo "<td>{$row['pkh']}</td>";
            echo "<td><div class=\"balance\" id=\"{$row['pkh']}\">~</div></td>";
            //echo "<td>{$row['joined']}</td>";
            echo "<td>{$row['originalName']}</td>";
            echo "<td>{$row['email']}</td>";
            //echo "<td>{$row['updated']}</td>";
            echo "<td>{$row['name']}</td>";
            echo "<td>{$row['originalName']}</td>";
            echo "</tr>";
            echo "<script>getBal(\"{$row['pkh']}\")</script>";
          }
        ?>
      </table>
    </div>
  </body>
</html>
