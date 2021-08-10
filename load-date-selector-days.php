<?php
    include "configtest.php";
    include "functions.php";

    $newDate = $_POST['newDate'];

    // get number of days in month
    $year = $newDate;

    $sql = "SELECT DISTINCT DATE FROM GAMELOGS_MAIN WHERE date like '$year%'";
    
    $result = mysqli_query($link, $sql);
    $resultCheck = mysqli_num_rows($result);
    $rowDay = '';
    $daysArray = array();

    // store days with games in array
    if($resultCheck > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $newArray[] = $row['DATE'];
        }
    }

    $curDate = min($newArray);

    $month = substr($curDate,4,2);
    $maxMonth = substr(max(...$newArray),4,2);
    $day = (int)substr($curDate,6,2);

    $curMonth = (int)$month;
    $curMaxMonth = (int)$maxMonth;
    $curDay = 0;

    while($curMonth <= $curMaxMonth) {
        ?>
        <div data-month-div="<?php echo $curMonth; ?>" class="day-grid-container
        <?php
            if($curMonth > (int)$month) { echo "hidden-days";}; ?>">
        <?php

        for($j = 1; $j <= daysInMonth($curMonth); $j++) {

                // add day to grid
                ?>
                <div data-grid-item="<?php echo $j; ?>" class="grid-item
                    <?php
                        if($j != (int)substr($newArray[$curDay], 6, 2)) {
                            echo " not-available";
                        }
                        else {
                            // if day is first day of month, make active
                            if($j === $day) {
                                echo " active";
                            }

                            $curDay++;
                        }
                    ?>
                    ">
                    <?php
                        echo $j;
                    ?>
                </div>
                <?php
                
        }
        // increment curMonth
        $curMonth++;
        ?>
        </div>
        <?php
    }


?>