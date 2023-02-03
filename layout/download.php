<?php include_once '../layout/sub-head.php'; ?>
<style>
<?php include_once '../metro/css/style.css'; ?>
</style>
<?php
include_once "./nav.php";
include_once '../config/conn.php';
include_once '../config/__utils.php';

$table_list = array('file_uploaded', 'brand_series', 'min_metadata', 'pasa_extract');
?>

<title>Download: Bashed Brand</title>

<div class="container-fluid">
    <div class="grid">
        <div class="row mt-10">
            <div class="stub">
                <?= nav($conn, 'table', ''); ?>
            </div>
            <div class="cell">
                <form action="./download_csv.php" method="post" class="multi-browse pos-top-center">
                    
                    <p>Input File Name</p>
                    <div class="input" style="width: 25%;">
                        <input type="text" data-role="input" data-clear-button="false" data-role-input="true" name="fileName" id="fileName">
                    </div>

                    <button type="submit" name="downloadBashed" id="downloadBashed" class="command-button primary rounded mt-3 size-small submit-import">
                        <span class="mif-checkmark icon"></span>
                        <span class="caption">
                            Download
                            <small>Download Bashed Brand</small>
                        </span>
                    </button>
                
                </form>
            </div>
            
        </div>
    </div>

</div>

<?php include_once '../layout/sub-footer.php'; ?>
<script src="../metro/js/script.js"></script>