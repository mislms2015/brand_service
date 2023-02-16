<?php
include_once "../config/conn.php";

$headers = [
    "User-Agent: PostmanRuntime/7.30.1",
    "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiIsImp0aSI6IjIwOTUyYzYwLWZmMmUtNDJjNC1iZWExLWJiMWJkZjA0MzQyNSJ9.eyJqdGkiOiIyMDk1MmM2MC1mZjJlLTQyYzQtYmVhMS1iYjFiZGYwNDM0MjUiLCJpYXQiOjE2MjQwMjE1NzEsIm5iZiI6MTYyNDAyMTU3MSwiZXhwIjoxNjI0MTA3OTcxfQ.CyxD8pO9PggVpOgXTwR9Eio6dWvWZYkZHyHoPfpI9OE",
    "X-Application-Token: eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJhcHBfaWQiOiJpb3MiLCJpYXQiOjE1OTg1ODY1MjYsIm5iZiI6MTU5ODU4NjUyNiwiZXhwIjoxOTEzOTQ2NTI2fQ.xVM2s_Owt4zNWLOlllhPXcRQ4b23x6KQpqs_2NGu9zPlQ9hjOsSS6pr9Qams7jfsyMPXtik2MFvv8V_nT8oG5Q"
];

$pasa_initial = array();

####get all pasa extract and bash to min_metadata
$investigate = mysqli_query($conn, "SELECT sequence_number, primary_min FROM pasa_extract WHERE BRAND2 IS NULL");

while ($row = mysqli_fetch_object($investigate)) {
    array_push($pasa_initial, array($row->sequence_number, $row->primary_min));
}

// $stmt = $conn->prepare("SELECT
// brand_id
// FROM
// min_metadata
//     WHERE
//     min = ?");
// $stmt->bind_param('s', $min);

$query = "UPDATE pasa_extract SET BRAND2 = ? WHERE sequence_number = ?";
$stmt_update = $conn2->prepare($query);
$stmt_update->bind_param("ss", $brand, $sequence_number);

foreach($pasa_initial as $pasa):
    // $min = $pasa[1];
    // $stmt->execute();

    // $stmt->bind_result($col1);

    // while ($stmt->fetch()) {

        $url = "https://app1.smart.com.ph/api/v2/get-brand?number=".$pasa[1];
        $ch = curl_init($url);
        //$ch = curl_init("https://api.github.com/user/repos");

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        curl_close($ch);

        $data = json_decode($response, true);

        $brand_temp = $data['data']['attributes']['brand_code'];;
        $sequence_number_temp = $pasa[0];
        
        $conn2->query("START TRANSACTION");
        $brand = $brand_temp;
        $sequence_number = $sequence_number_temp;
        $stmt_update->execute();
    // }
    
endforeach;

$stmt_update->close();
$conn2->query("COMMIT");

echo 'success';
?>