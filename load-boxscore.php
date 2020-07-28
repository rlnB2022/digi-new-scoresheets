<?php
    include "configtest.php";

    $gameid = $_POST['gameid'];

    $sql = "SELECT * FROM EVENTS WHERE GAMEID = '" . $gameid . "'";

    $result = mysqli_query($link, $sql);

    $resultCheck = mysqli_num_rows($result);

    if($resultCheck > 0) {
        while ($row = mysqli_fetch_assoc($result)) { ?>
            <div><?php
                if($row['CATEGORY'] === 'com') {
                ?>
                <div><?php
                echo $row['INNING'];
                ?></div>
                <?php
            }
            else if($row['CATEGORY'] === 'sub') {
                ?>
                <div><?php
                echo $row['TEAMATBAT'] . ' coming into the game.';
                ?></div>
                <?php
            }
            else {
                ?>
                <div><?php
                    echo $row['RESULT'];
                ?></div>
                <?php
            }
        }
    }
    else {
        echo "NOTHING FOUND";
    }
?>