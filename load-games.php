<div id="myboxscore" class="boxscore">
                
</div>

<?php
    include "configtest.php";

    $year = '';
    $month = '';
    $day = '';

    function showMessage($msg) {
        echo "<script type='text/javascript'>alert('$msg');</script>";
    }

    function getTeamName($name, $year) {
        switch ($name) {
            case "NLS":
                return "NL All-Stars";
            case "ALS":
                return "AL All-Stars";
            case "ANA":
                if ($year >= 2005) {
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

    $newDate = $_POST['newDate'];

    $sql = "SELECT * FROM GAMELOGS WHERE date = $newDate";

    $result = mysqli_query($link, $sql);

    $resultCheck = mysqli_num_rows($result);

    $year = substr($newDate,0,4);
    $month = substr($newDate,4,2);
    $day = substr($newDate,6,2);

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
                        <?php echo getTeamName($row['visteam'], $year); ?>
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
                        <?php echo getTeamName($row['hometeam'], $year); ?>
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
                    <div data-boxscore="<?php echo $row['hometeam'] . $year . $month . $day . $row['gamenum']; ?>"  class="btn-box-score button-choice">
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