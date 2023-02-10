<?php
include_once "../config/conn.php";

$pasa_initial = array();
$brand_series_initial = array();


####get all brand_series and bash to pasa extract
$investigate_brand_series = mysqli_query($conn, "SELECT brand_id, series FROM brand_series");

while ($row = mysqli_fetch_object($investigate_brand_series)) {
    array_push($brand_series_initial, array($row->brand_id, $row->series. '%'));
}

$query_series = "UPDATE pasa_extract SET BRAND2 = ? WHERE primary_min LIKE ?";
$stmt_update = $conn->prepare($query_series);
$stmt_update->bind_param("ss", $brand_id, $series);

foreach($brand_series_initial as $bsi):
        
        $conn->query("START TRANSACTION");
        $brand_id = $bsi[0];
        $series = $bsi[1];
        $stmt_update->execute();
    
endforeach;

$stmt_update->close();
$conn->query("COMMIT");

//------------------------------------------------------------------------------------------------------

####get all pasa extract and bash to min_metadata
$investigate = mysqli_query($conn, "SELECT sequence_number, primary_min FROM pasa_extract");

while ($row = mysqli_fetch_object($investigate)) {
    array_push($pasa_initial, array($row->sequence_number, $row->primary_min));
}

$stmt = $conn->prepare("SELECT
brand_id
FROM
min_metadata
    WHERE
    min = ?");
$stmt->bind_param('s', $min);

$query = "UPDATE pasa_extract SET BRAND2 = ? WHERE sequence_number = ?";
$stmt_update = $conn2->prepare($query);
$stmt_update->bind_param("ss", $brand, $sequence_number);

foreach($pasa_initial as $pasa):
    $min = $pasa[1];
    $stmt->execute();

    $stmt->bind_result($col1);

    while ($stmt->fetch()) {
        $brand_temp = $col1;
        $sequence_number_temp = $pasa[0];
        
        $conn2->query("START TRANSACTION");
        $brand = $brand_temp;
        $sequence_number = $sequence_number_temp;
        $stmt_update->execute();
    }
    
endforeach;

$stmt->close();
$conn->query("COMMIT");
$stmt_update->close();
$conn2->query("COMMIT");

echo 'success';
?>