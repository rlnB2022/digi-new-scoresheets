<?php
    include "configtest.php";

    class batterStat {
        public $playerid;
        public $stat_ab;
        public $stat_hit_1b;
        public $stat_hit_2b;
        public $stat_hit_3b;
        public $stat_hit_hr;
        public $stat_bb;
        public $stat_k;
        public $stat_lob;
        public $stat_r;
        public $stat_rbi;
        public $stat_gidp;
        public $stat_sb;
        public $stat_cs;
        public $stat_hbp;
        public $stat_sh;
        public $stat_sf;
    }

    function getDayOfWeek($info) {
        if($info === 'Mon') {
            return 'Monday';
        }
        else if($info === 'Tue') {
            return 'Tuesday';
        }
        else if($info === 'Wed') {
            return 'Wednesday';
        }
        else if($info === 'Thu') {
            return 'Thursday';
        }
        else if($info === 'Fri') {
            return 'Friday';
        }
        else if($info === 'Sat') {
            return 'Saturday';
        }
        else if($info === 'Sun') {
            return 'Sunday';
        }
    }

    function getMonth($info) {
        $months = ['March', 'April', 'May', 'June', 'July', 'August', 'September'];
        $monthNum = $info -3;  // earliest month is March, the 0-index in $months above

        return $months[$monthNum];
    }

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

    $gameid = $_POST['gameid'];

    $hometeam = substr($gameid,0,3);
    $gamedate = substr($gameid,3,8);
    $gamenum = substr($gameid,11,1);
    $year = substr($gamedate,0,4);
    $month = getMonth(substr($gamedate,4,2));
    $day = substr($gamedate,6,2);

    $sql = "SELECT * FROM GAMELOGS WHERE DATE = '" . $gamedate  . "' AND HOMETEAM = '" . $hometeam . "' AND GAMENUM = '" . $gamenum . "' LIMIT 1";

    $result = mysqli_query($link, $sql);

    $resultCheck = mysqli_num_rows($result);

    if($resultCheck > 0) {
        $row = mysqli_fetch_assoc($result);
        
        $boxscorerows = ceil($row['outs'] / 6);
        $vislinescore = str_split($row['vislinescore']);
        $homelinescore = str_split($row['homelinescore']);

        ?>
        <h1 class="date-header">
            <?php echo getDayOfWeek($row['dayofweek']) . ", " . $month . " " . $day . ", " . $year; ?>
        </h1>
        <div class="boxscore-grid">
            <div></div>
            <?php
                for($i = 0; $i < $boxscorerows; $i++) { ?>
                    <div class="grid-item"><?php echo $i+1; ?></div>
                <?php
                } ?>
            
            <div class="grid-item"></div>
            <div class="grid-item">R</div>
            <div class="grid-item">H</div>
            <div class="grid-item">E</div>
            <div><?php echo getTeamName($row['visteam']); ?></div>
            <?php
                for($i = 0; $i < $boxscorerows; $i++) { ?>
                    <div class="grid-item"><?php echo $vislinescore[$i]; ?></div>
                <?php
                }
            ?>
            <div class="grid-item"></div>
            <div class="grid-item"><?php echo $row['visscore']; ?></div>
            <div class="grid-item"><?php echo $row['vish']; ?></div>
            <div class="grid-item"><?php echo $row['viserrors']; ?></div>
            <div><?php echo getTeamName($row['hometeam']); ?></div>
            <?php
                for($i = 0; $i < $boxscorerows; $i++) { ?>
                    <div class="grid-item"><?php echo $homelinescore[$i]; ?></div>
                <?php
                }
            ?>
            <div class="grid-item"></div>
            <div class="grid-item"><?php echo $row['visscore']; ?></div>
            <div class="grid-item"><?php echo $row['vish']; ?></div>
            <div class="grid-item"><?php echo $row['viserrors']; ?></div>
        </div>
    <?php
    }
    else {
        echo "NOTHING FOUND";
    }
?>