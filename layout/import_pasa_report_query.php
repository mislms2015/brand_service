<?php include_once '../layout/sub-head.php'; ?>
<style>
<?php include_once '../metro/css/style.css'; ?>
</style>
<?php
include_once "./nav.php";
include_once '../config/conn.php';
include_once '../config/__utils.php';
?>

<title>Import: Pasa Report</title>

<div class="container-fluid">
    <div class="grid">
        <div class="row mt-10">
            <div class="stub">
                <?= nav($conn, 'import_pasa_report', 'active'); ?>
            </div>

            <div class="cell">

                <!-- Query logic: Start -->
                <?php
                $header = checkHeaderPasaReport();

                if (isset($_POST['submit'])) {
                
                    // Allowed mime types
                    $fileMimes = fileMimes();
                    $files = $_FILES['file'];
                
                    // Validate whether selected file is a CSV file
                    if (!empty($files)) {

                        for ($i = 0; $i < count($files['name']); $i++) {
                            $pasa_report_logs = array();
                            $filename = $files['name'][$i];
                            if (in_array($files['type'][$i], $fileMimes)) {
                                $err_msg = '';

                                // Open uploaded CSV file with read-only mode
                                $csvFile = fopen($files['tmp_name'][$i], 'r');

                                // Skip the first line
                                $numcols = fgetcsv($csvFile);

                                $temp_header = array();
                                for ($header_column = 0; $header_column < count($numcols); $header_column++) {
                                    array_push($temp_header, $numcols[$header_column]);
                                }
                                
                                $header_compare = array_diff($header,$temp_header);

                                //check file if uploaded
                                $check_file = mysqli_query($conn, "SELECT * FROM file_uploaded WHERE file_name = '". $filename ."'");

                                if (count($header_compare) > 0) {
                                    $err_msg = "<b><i>$filename header not match.</i></b>";
                                } else if (mysqli_num_rows($check_file) > 0) {
                                    $err_msg = "<b><i>$filename file already uploaded.</i></b>";
                                } else {
                                    $err_msg = '';
                                }
                    
                                if (count($header_compare) == 0 && mysqli_num_rows($check_file) == 0) {
                                    // insert filename to table for additional validation
                                    $file_upload_banner = $filename;

                                    $upload_file = "INSERT INTO file_uploaded (file_type, file_name, banner) VALUES ('pasa_report', '".$files['name'][$i]. "', '" .$file_upload_banner. "')";
                                    
                                    $file_upload_id = '';
                                    if ($conn->query($upload_file) === TRUE) {
                                        $last_id = $conn->insert_id;
                                        $file_upload_id = $last_id;
                                    } else {
                                        $file_upload_id = '999999999';
                                    }

                                    // Parse data from CSV file line by line
                                    $counter = 0;
                                    while (($getData = fgetcsv($csvFile, 100000000, ",")) !== FALSE) {
                                        if ($getData[9] == 'NULL' || $getData[9] == NULL || $getData[9] == '') {
                                            $status_active = 'SUCCESS';
                                        } else {
                                            $status_active = 'FAILED';
                                        }

                                        array_push($pasa_report_logs, array($file_upload_id, $getData[0], $getData[1], $getData[2], $getData[3], $getData[4], $getData[5], $getData[6], $getData[7], $getData[8], $getData[9], $getData[10], $getData[11], $getData[12], $getData[13], $getData[14], $status_active));

                                        $counter++;
                                    }

                                    // Close opened CSV file
                                    fclose($csvFile);

                                    //insert gigapay raw logs here:start
                                    $query = "INSERT INTO pasa_extract (id, sequence_number, transaction_id, date_registered, primary_min, brand, mran, recipient_min, amount, date_initiated, date_failed, date_requested, date_debit_confirmed, date_credit_confirmed, denomination_id, pasa_type, STATUS) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";


                                    $stmt = $conn->prepare($query);
                                    $stmt->bind_param("sssssssssssssssss", $upload_id, $sequence_number, $transaction_id, $date_registered, $primary_min, $brand, $mran, $recipient_min, $amount, $date_initiated, $date_failed, $date_requested, $date_debit_confirmed, $date_credit_confirmed, $denomination_id, $pasa_type, $status);

                                    $conn->query("START TRANSACTION");
                                    foreach ($pasa_report_logs as $res) {
                                        $upload_id = $res[0];
                                        $sequence_number = $res[1];
                                        $transaction_id = $res[2];
                                        $date_registered = $res[3];
                                        $primary_min = $res[4];
                                        $brand = $res[5];
                                        $mran = $res[6];
                                        $recipient_min = $res[7];
                                        $amount = $res[8];
                                        $date_initiated = $res[9];
                                        $date_failed = $res[10];
                                        $date_requested = $res[11];
                                        $date_debit_confirmed = $res[12];
                                        $date_credit_confirmed = $res[13];
                                        $denomination_id = $res[14];
                                        $pasa_type = $res[15];
                                        $status = $res[16];
                                        $stmt->execute();
                                    }
                                    $stmt->close();
                                    $conn->query("COMMIT");
                                    //insert gigapay raw logs here: end

                                    echo "
                                        <div class='remark info'>
                                            <pre class='fg-green'><b><i>$filename</i></b> successfully imported. $counter rows inserted.</pre>
                                        </div>

                                        <audio autoplay>
                                            <source src='../asset/sound/chime.mp3'>
                                        </audio>
                                    ";
                                } else {
                                    echo "
                                        <div class='remark warning'>
                                            <pre class='fg-red'>$err_msg</pre>
                                        </div>
                                    ";
                                }
                            } else {
                                echo "
                                    <div class='remark warning'>
                                        <pre class='fg-red'>$filename invalid file.</pre>
                                    </div>
                                ";
                            }
                            
                        }
                    }
                    else {
                        echo "
                            <div class='remark warning'>
                                <pre class='fg-red'>No file selected.</pre>
                            </div>
                        ";
                    }
                }
                ?>
                <!-- Query logic: End -->
            </div>
            
        </div>
    </div>

</div>

<?php include_once '../layout/sub-footer.php'; ?>
<script src="../metro/js/script.js"></script>