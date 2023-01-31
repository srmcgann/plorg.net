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
      .highlight{
        background: #400;
        color: #f00;
      }
      .dataTable{
        border-collapse: collapse;
      }
      td{
        padding: 10px;
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
      convert = total = 0
      rows = []
      async function getXTZ_exchangeVal(){
        await fetch('/XTZ_to_USD.php').then(res=>res.json()).then(data=>{
          convert = +data[1]
        })
      }
      headers = '<tr><th>pkh</th><th>bal</th><th>USD</th><th>originalName</th><th>name</th></tr>'
      getXTZ_exchangeVal()
      getBal = (pkh, joined, name, originalName, updated, email) =>{
        let sendData = {pkh}
        fetch('/getBalanceFromPkh.php',{
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify(sendData),
        })
        .then(res=>res.json()).then(data=>{
          bal=+data[1]
          let table = document.querySelectorAll('.dataTable')[0]
          table.innerHTML = ''

          let tr
          let td
          tr = document.createElement('tr')

          td = document.createElement('td')
          td.innerHTML = pkh
          tr.appendChild(td)

          td = document.createElement('td')
          td.innerHTML = bal
          tr.appendChild(td)
          if(bal>1){
            tr.classList = 'highlight'
          }
          
          td = document.createElement('td')
          USD = td.innerHTML = " ($" + (Math.round(total*convert*100)/100)+")"
          tr.appendChild(td)
          
          td = document.createElement('td')
          td.innerHTML = originalName
          tr.appendChild(td)
          
          td = document.createElement('td')
          td.innerHTML = name
          tr.appendChild(td)
          
          /*
          td = document.createElement('td')
          td.innerHTML = updated
          tr.appendChild(td)
          
          td = document.createElement('td')
          td.innerHTML = email
          tr.appendChild(td)
          */
          
          rows = [...rows, [+bal, tr]]
          rows.sort((a,b)=>b[0]-a[0])
          let grandTotal = 0
          rows.map((v, i)=>{
            grandTotal += v[0]
            if(!(i%5))table.innerHTML += headers
            table.appendChild(v[1])
          })
          document.querySelector('#total').innerHTML = " ($" + (Math.round(grandTotal*convert*100)/100)+")"
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
      <table class="dataTable"></table>
        <script>
        <?
          $sql = "SELECT * FROM users";
          $res = mysqli_query($link, $sql);
          for($i=0; $i<mysqli_num_rows($res); ++$i){
            $row           = mysqli_fetch_assoc($res);
            $pkh           = $row['pkh'];
            $joined        = $row['joined'];
            $name          = $row['name'];
            $originalName  = $row['originalName'];
            $updated       = $row['updated'];
            $email         = $row['emaail'];
            echo "getBal(\"$pkh\",\"$joined\",\"$name\",\"$originalName\",\"$updated\",\"$email\");";
          }
        ?>
      </script>
    </div>
  </body>
</html>

