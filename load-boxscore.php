<?php
    include "configtest.php";

    class batterStat {
        public $playerid;
        public $stat_ab;
        public $stat_1b;
        public $stat_2b;
        public $stat_3b;
        public $stat_hr;
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

        public function getHits() {
            return $this->stat_1b + $this->stat_2b + $this->stat_3b + $this->stat_hr;
        }
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

    function getLineScore($linescore) {
        $pattern = "/\(/";
        $array_to_return = [];

        $tmp_array = str_split($linescore);

        // loop through array check for any '(' or ')' characters
        for($i = 0, $max = count($tmp_array); $i < $max; $i++) {
            if(preg_match($pattern, $tmp_array[$i]) === 1) {
                // double-digit runs score this inning
                // grab the next two digits
                array_push($array_to_return, $tmp_array[$i+1].$tmp_array[$i+2]);
                // increment $i by 3
                $i += 3;
            }
            else {
                array_push($array_to_return, $tmp_array[$i]);
            }
        }

        return $array_to_return;
    }

    $gameid = $_POST['gameid'];

    $visBoxScoreStats = [];
    $homeBoxScoreStats = [];

    $hometeam = substr($gameid,0,3);
    $gamedate = substr($gameid,3,8);
    $gamenum = substr($gameid,11,1);
    $year = substr($gamedate,0,4);
    $month = getMonth(substr($gamedate,4,2));
    $day = substr($gamedate,6,2);

    $visLineup = [];
    $homeLineup = [];

    function getPlayerName($linky, $playerid) {

        $fullname = '';

        $sql = "SELECT * FROM PLAYERS WHERE playerid = '" . $playerid . "'";

        $result = mysqli_query($linky, $sql);

        $resultCheck = mysqli_num_rows($result);

        if($resultCheck > 0) {
            $row = mysqli_fetch_assoc($result);
            $fullname = $row['firstname'] . " " . $row['lastname'];
        }
        else {
            echo "NOPE";
        }

        return $fullname;
    }

    function getStrikeouts($row) {
        global $visBoxScoreStats;
        global $homeBoxScoreStats;

        $teamatbat = $row['TEAMATBAT'] === '0' ? $visBoxScoreStats : $homeBoxScoreStats;

        foreach($teamatbat as $vs) {
            if($vs->playerid === $row['PLAYERID']) {
                $vs->stat_k++;
                $vs->stat_ab++;
            }
        }
    }

    function getWalks($row) {
        global $visBoxScoreStats;
        global $homeBoxScoreStats;
        global $runnersOnBase;

        $teamatbat = $row['TEAMATBAT'] === '0' ? $visBoxScoreStats : $homeBoxScoreStats;

        // move baserunners
        if(strpos($row['OUTCOME'],'3-H') !== false) {
            
            foreach($teamatbat as $vs) {
                if($vs->playerid === $runnersOnBase->runneron3rd) {
                    // give runner credit for a RUN
                    $vs->stat_r++;
                }
                else if($vs->playerid === $row['PLAYERID']) {
                    // give batter credit for RBI
                    $vs->stat_rbi++;
                }
            }
            
            // move runner on 3rd home
            $runnersOnBase->runneron3rd = '';
        }

        if(strpos($row['OUTCOME'],'2-3') !== false) {
            // move runner on 2nd to 3rd
            $runnersOnBase->runneron3rd = $runnersOnBase->runneron2nd;
            $runnersOnBase->runneron2nd = '';
        }
        if(strpos($row['OUTCOME'],'1-2') !== false) {
            // move runner on 1st to 2nd
            $runnersOnBase->runneron2nd = $runnersOnBase->runneron1st;
            $runnersOnBase->runneron1st = '';
        }
        // assign a bb to batter stats
        // move batter to 1st
        foreach($teamatbat as $vs) {
            if($vs->playerid === $row['PLAYERID']) {
                $vs->stat_bb++;
                $runnersOnBase->runneron1st = $vs->playerid;
            break;
            }
        }
    }

     class GameStatus {
        public $runneron1st;
        public $runneron2nd;
        public $runneron3rd;

        public function totalRunnersOnBase() {
            return $this->runneron1st + $this->runneron2nd + $this->runneron3rd;
        }

        public function clearRunners() {
            $this->runneron1st = '';
            $this->runneron2nd = '';
            $this->runneron3rd = '';
        }
    };

    $runnersOnBase = new GameStatus();

    $sql = "SELECT * FROM GAMELOGS WHERE DATE = '" . $gamedate  . "' AND HOMETEAM = '" . $hometeam . "' AND GAMENUM = '" . $gamenum . "' LIMIT 1";

    $result = mysqli_query($link, $sql);

    $resultCheck = mysqli_num_rows($result);

    if($resultCheck > 0) {
        $row = mysqli_fetch_assoc($result);
        
        $boxscorerows = ceil($row['outs'] / 6);

        $vislinescore = getLineScore($row['vislinescore']);
        $homelinescore = getLineScore($row['homelinescore']);

        ?>
        <h1 class="date-header">
            <?php echo getDayOfWeek($row['dayofweek']) . ", " . $month . " " . $day . ", " . $year; ?>
        </h1>
        <div class="boxscore-grid">
            <div></div>
            <?php
                for($i = 0; $i < 9; $i++) { ?>
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
            <div class="grid-item"><?php echo $row['homescore']; ?></div>
            <div class="grid-item"><?php echo $row['homeh']; ?></div>
            <div class="grid-item"><?php echo $row['homeerrors']; ?></div>
        </div>
    <?php
    }
    else {
        echo "NOTHING FOUND";
    }

?>

<h1 class="section-separator">VISITORS - BATTING</h1>

<?php
  // get visitor lineup first
  $sql = "SELECT * FROM VISLINEUPS WHERE GAMEID = '" . $gameid . "'";

  $result = mysqli_query($link, $sql);

  $resultCheck = mysqli_num_rows($result);

  if($resultCheck > 0) {
    $row = mysqli_fetch_assoc($result);

    for($i = 0; $i < 9; $i++) {
        array_push($visLineup, $row['batter' . ($i + 1)]);
        $batterS = new batterStat();
        $batterS->playerid = $visLineup[$i];
        array_push($visBoxScoreStats, $batterS);
    }

    // foreach($visBoxScoreStats as $bStats) {
    //     echo $bStats->playerid;
    // }
  }

    // get home lineup next
    $sql = "SELECT * FROM HOMELINEUPS WHERE GAMEID = '" . $gameid . "'";

    $result = mysqli_query($link, $sql);
  
    $resultCheck = mysqli_num_rows($result);
  
    if($resultCheck > 0) {
      $row = mysqli_fetch_assoc($result);
  
      for($i = 0; $i < 9; $i++) {
        array_push($homeLineup, $row['batter' . ($i + 1)]);
        $batterS = new batterStat();
        $batterS->playerid = $homeLineup[$i];
        array_push($homeBoxScoreStats, $batterS);
      }
    }
?>

<div class="boxscore-stats">
  <?php
    $sql = "SELECT * FROM EVENTS WHERE GAMEID = '" . $gameid . "'";

    $result = mysqli_query($link, $sql);

    $resultCheck = mysqli_num_rows($result);

    if($resultCheck > 0) {
        while ($row = mysqli_fetch_assoc($result)) { 
            // strikeouts
            if($row['OUTCOME'][0]==='K') {
                getStrikeouts($row);
            }
            // walks, not wild pitches
            if($row['OUTCOME'][0]==='W' and $row['OUTCOME'][1] !== 'P') {
                getWalks($row);
            }
            else {
                $runnersOnBase->clearRunners();
            }
        }
    }

    // verify strikeouts
    foreach($visBoxScoreStats as $vs) {
        if($vs->stat_k > 0) {
            ?><div><?php
            echo getPlayerName($link, $vs->playerid) . " has " . $vs->stat_k . " strikeouts.";
            ?></div><?php
        }
    }

    foreach($homeBoxScoreStats as $vs) {
        if($vs->stat_k > 0) {
            ?><div><?php
            echo getPlayerName($link, $vs->playerid) . " has " . $vs->stat_k . " strikeouts.";
            ?></div><?php
        }
    }

    // verify walks
    foreach($visBoxScoreStats as $vs) {
        if($vs->stat_bb > 0) {
            ?><div><?php
            echo getPlayerName($link, $vs->playerid) . " has " . $vs->stat_bb . " walks.";
            ?></div><?php
        }
    }

    foreach($homeBoxScoreStats as $vs) {
        if($vs->stat_bb > 0) {
            ?><div><?php
            echo getPlayerName($link, $vs->playerid) . " has " . $vs->stat_bb . " walks.";
            ?></div><?php
        }
    }

  ?>

</div>
