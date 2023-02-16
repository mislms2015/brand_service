<?php
include_once "../config/conn.php";

$filename = $_GET['file_name'];
$pasa_type = $_GET['pasa_type'];

$query = mysqli_query($conn, "SELECT * FROM pasa_extract WHERE pasa_type_upload = '$pasa_type'"); 

if($query->num_rows > 0){ 
    $delimiter = ","; 
    
    // Create a file pointer 
    $f = fopen('php://memory', 'w'); 
    // Set column headers 
    $fields = array('sequence_number', 'transaction_id', 'date_registered', 'primary_min', 'brand', 'mran', 'recipient_min', 'amount', 'date_initiated', 'date_failed', 'date_requested', 'date_debit_confirmed', 'date_credit_confirmed', 'denomination_id', 'pasa_type', 'BRAND2', 'STATUS'); 
    fputcsv($f, $fields, $delimiter); 
    
    // Output each row of the data, format line as csv and write to file pointer 
    while($row = $query->fetch_assoc()){

        $lineData = array($row['sequence_number'], $row['transaction_id'], $row['date_registered'], $row['primary_min'], $row['brand'], $row['mran'], $row['recipient_min'], $row['amount'], $row['date_initiated'], $row['date_failed'], $row['date_requested'], $row['date_debit_confirmed'], $row['date_credit_confirmed'], $row['denomination_id'], $row['pasa_type'], $row['BRAND2'], $row['STATUS']);

        fputcsv($f, $lineData, $delimiter);
    }
    
    // Move back to beginning of file 
    fseek($f, 0);
    
    // Set headers to download file rather than displayed 
    header('Content-Type: text/csv'); 
    header('Content-Disposition: attachment; filename="' . $filename . '";'); 
    
    //output all remaining data on a file pointer 
    fpassthru($f);
    
} 
exit;


?>