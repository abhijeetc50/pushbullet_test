<html>
    <head>
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
    </head>

    <body>
        <?php
        $date = date_default_timezone_set('Asia/Kolkata');
        $today = date("g:i a");

        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "jsondb";

        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $url = "https://www.zebapi.com/api/v1/market/ticker/btc/inr";
        $json = file_get_contents($url);
        $json_data = json_decode($json, true);

        $buy = $json_data['buy'];
        $sell = $json_data ['sell'];
        $currency = $json_data['currency'];
        $volume = $json_data ['volume'];
        $market = $json_data['market'];


        $sql = "INSERT INTO stdtable(buy, sell, currency, volume, market,time,date)"
                . " VALUES('$buy', '$sell', '$currency', '$volume', '$market','$today',CURRENT_TIMESTAMP)";
        $conn->query($sql);

        send_push('o.auno9nFeGUd0AGtx9qBnbCayzTPMRL1S');

        function send_push($token) {
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "jsondb";

            $conn = new mysqli($servername, $username, $password, $dbname);
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            $query = "SELECT * FROM stdtable ORDER BY id DESC LIMIT 1";

            if ($result = $conn->query($query)) {

                /* fetch object array */
                while ($row = $result->fetch_row()) {
                    $buydata = $row[2];
                    $selldata = $row[3];
//                    echo $buydata;
//                    echo $selldata;
                }
            }


            $query1 = "SELECT * FROM stdtable WHERE `date` > timestampadd(hour, -24, now()) ORDER BY buy asc LIMIT 1";

            if ($result1 = $conn->query($query1)) {

                /* fetch object array */
                while ($row1 = $result1->fetch_row()) {
                    $buydata1 = $row1[2];
                    echo $buydata1;
                }
            }
            $hourcounter = 0;
            if ($buydata < $buydata1) {
                $echo = "This is the lowest value";
            } else {
               $hourcounter++;
               if($hourcounter == 11){
                   $echo = "This is the hourly value $buydata";
                   $hourcounter = 0;
               } else {
                   $echo = "some error happened";
               }
            }

            $data = array(
                'type' => 'note',
                'title' => 'Bitcoin',
                'body' => "$echo. Current value is $buydata"
            );
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://api.pushbullet.com/v2/pushes');
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token
            ));
            $data = curl_exec($ch);
            curl_close($ch);
        }
        ?>
        <script>
            setTimeout(function () {
                window.location.reload();
            }, 5 * 60 * 1000);
            // just show current time stamp to see time of last refresh.
            document.write(new Date());
        </script>
    </body>

</html>