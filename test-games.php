<?php
    // Include config file
    include "configtest.php";
?>
<!DOCTYPE html>
<html class="no-js">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Test List of Games</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@500&display=swap" rel="stylesheet">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script>
            $(document).ready(function() {
                let selectedDate = '';
                $("#mydate").on('change', function() {
                    // setup new date
                    selectedDate = $("#mydate").val();
                    selectedDate = selectedDate.replace(/-/g, "");

                    $("#gridgames").load("load-games.php", {
                        newDate: selectedDate
                    },function() {
                        $(window).scrollTop(0);
                        $('.button-choice').on('click', function(event) {
                            let thisGameId = '';
                            thisGameId = event.target.dataset.boxscore;

                            // show boxscore in a pop-up window
                            $("#myboxscore").load("load-boxscore.php", {
                                gameid: thisGameId
                            },function() {
                        // add visible class to .boxscore
                        $(".boxscore").css('display','block');
                    });
                        });
                    });
                });
                $('.button-choice').on('click', function(event) {
                    let thisGameId = '';
                    thisGameId = event.target.dataset.boxscore;
                    console.log(thisGameId);

                    // show boxscore in a pop-up window
                    $("#myboxscore").load("load-boxscore.php", {
                        gameid: thisGameId
                    },function() {
                        // add visible class to .boxscore
                        $(".boxscore").css('display','block');
                    });
                });
            });
        </script>
        <link rel="stylesheet" href="css/style-test.css">
    </head>
    <body>
        <?php
            $year = '2019';
            $month = '03';
            $day = '20';

            function getTeamName($name) {
                switch ($name) {
                    case "NLS":
                    return "NL All-Stars";
                    case "ALS":
                    return "AL All-Stars";
                    case "ANA":
                    if (curYear >= 2005) {
                        return "Los Angeles (A)";
                    } else {
                        return "Anaheim";
                    }
                    case "ARI":
                    return "Arizona";
                    case "ATL":
                    return "Atlanta";
                    case "BAL":
                    return "Baltimore";
                    case "BOS":
                    case "BSN":
                    return "Boston";
                    case "BRO":
                    return "Brooklyn";
                    case "CAL":
                    return "California";
                    case "CHA":
                    return "Chicago (A)";
                    case "CHN":
                    return "Chicago (N)";
                    case "CIN":
                    return "Cincinnati";
                    case "CLE":
                    return "Cleveland";
                    case "COL":
                    return "Colorado";
                    case "DET":
                    return "Detroit";
                    case "FLO":
                    return "Florida";
                    case "HOU":
                    return "Houston";
                    case "KCA":
                    case "KC1":
                    return "Kansas City";
                    case "LAA":
                    return "Los Angeles (A)";
                    case "LAN":
                    return "Los Angeles (N)";
                    case "MIA":
                    return "Miami";
                    case "MIL":
                    case "MLN":
                    return "Milwaukee";
                    case "MIN":
                    return "Minnesota";
                    case "MON":
                    return "Montreal";
                    case "NY1":
                    return "New York";
                    case "NYA":
                    return "New York (A)";
                    case "NYN":
                    return "New York (N)";
                    case "OAK":
                    return "Oakland";
                    case "PHI":
                    case "PHA":
                    return "Philadelphia";
                    case "PIT":
                    return "Pittsburgh";
                    case "SDN":
                    return "San Diego";
                    case "SEA":
                    case "SE1":
                    return "Seattle";
                    case "SFN":
                    return "San Francisco";
                    case "SLN":
                    case "SLA":
                    return "St. Louis";
                    case "TBA":
                    return "Tampa Bay";
                    case "TEX":
                    return "Texas";
                    case "TOR":
                    return "Toronto";
                    case "WAS":
                    case "WS1":
                    case "WS2":
                    return "Washington";
                    default:
                    return "Teamname?";
                }
            }
        ?>

        <header>
            <img src="/images/digidugout-logo.png" alt="logo">

            <form method="POST" name="myform">
                <input id="mydate" type="date" value="2019-03-20" min="1906-04-12" max="2019-09-29">
            </form>
            
            <div class="hamburger-menu">
                <div class="hamburger-line"></div>
                <div class="hamburger-line"></div>
                <div class="hamburger-line"></div>
            </div>
        </header>

        <section id="gridgames" class="grid-of-games">
            <div id="myboxscore" class="boxscore">
                
            </div>
            <?php

                $sql = "SELECT * FROM GAMELOGS WHERE date = " . $year . $month . $day;
                $result = mysqli_query($link, $sql);
                $resultCheck = mysqli_num_rows($result);

                if($resultCheck > 0) {
                    while ($row = mysqli_fetch_assoc($result)) { ?>
                        <div class="game-container">
                            <div class="game">
                                <div class="cell top-row-empty">
                                </div>
                                <div class="cell top-row-header">R</div>
                                <div class="cell top-row-header">H</div>
                                <div class="cell top-row-header">E</div>
                                <div class="btm-space vis-teamname">
                                    <?php echo getTeamName($row['visteam']); ?>
                                </div>
                                <div class="cell btm-space vis-runs-scored">
                                    <?php echo $row['visscore']; ?>
                                </div>
                                <div class="cell btm-space vis-hits">
                                    <?php echo $row['vish']; ?>
                                </div>
                                <div class="cell btm-space vis-errors">
                                    <?php echo $row['viserrors']; ?>
                                </div>
                                <div class="home-teamname">
                                    <?php echo getTeamName($row['hometeam']); ?>
                                </div>
                                <div class="cell home-runs-scored">
                                    <?php echo $row['homescore']; ?>
                                </div>
                                <div class="cell home-hits">
                                    <?php echo $row['homeh']; ?>
                                </div>
                                <div class="cell home-errors">
                                    <?php echo $row['homeerrors']; ?>
                                </div>
                            </div>
                            <div class="separator"></div>
                            <div class="pitchers-of-record">
                                <p class="pitcher-right-align">Winning Pitcher:</p>
                                <p><?php echo $row['winningpitchername']; ?></p>
                                <p class="pitcher-right-align">Losing Pitcher:</p>
                                <p><?php echo $row['losingpitchername']; ?></p>
                                <p class="pitcher-right-align">Save:</p>
                                <p><?php
                                    echo $row['savingpitchername']; ?>
                                </p>
                            </div>
                            <div class="separator"></div>
                            <div class="button-options">
                                <div class="btn-scoresheet button-choice">
                                    <img src="./images/recap.png" alt="Watch game unfold">
                                    <p>Scoresheet</p>
                                </div>
                                <div data-boxscore="<?php echo $row['hometeam'] . $year . $month . $day . $row['gamenum']; ?>" class="btn-box-score button-choice">
                                    <img src="./images/recap.png" alt="Watch game unfold">
                                    <p>Box Score</p>
                                </div>
                                <div class="btn-recap button-choice">
                                    <img src="./images/recap.png" alt="Watch game unfold">
                                    <p>Recap</p>
                                </div>
                            </div>
                        </div>
                <?php } }
            ?>
        </section>
        <script src="" async defer></script>
    </body>
</html>