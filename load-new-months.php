<?php
    include "config.php";
    include "functions.php";

    $monthArr = array();

    $monthArr = $_POST['months'];
    
    for($i = $monthArr[0]; $i <= $monthArr[count($monthArr)-1]; $i++) {
        ?><a href="#" data-month="<?php echo $i; ?>" class="<?php if($i === $monthArr[0]) { echo 'active-month'; };?>" > <?php echo getCurrentMonthFull($i - 1); ?></a><div class="a-underline"></div>
    <?php
    }
?>