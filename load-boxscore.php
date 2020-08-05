<?php
    include "configtest.php";

    class batterStat {
        public $playerid = '';
        public $stat_ab = 0;
        public $stat_1b = 0;
        public $stat_2b = 0;
        public $stat_3b = 0;
        public $stat_hr = 0;
        public $stat_bb = 0;
        public $stat_ibb = 0;
        public $stat_k = 0;
        public $stat_lob = 0;
        public $stat_r = 0;
        public $stat_rbi = 0;
        public $stat_gidp = 0;
        public $stat_sb = 0;
        public $stat_cs = 0;
        public $stat_hbp = 0;
        public $stat_sh = 0;
        public $stat_sf = 0;

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

    function getWalks($row, $walktype) {
        global $visBoxScoreStats;
        global $homeBoxScoreStats;
        global $runnersOnBase;

        $teamatbat = $row['TEAMATBAT'] === '0' ? $visBoxScoreStats : $homeBoxScoreStats;

        if(strpos($row['OUTCOME'],'3-H') !== false) {
            assignRBI($row, 1);
        }

        // assign a bb to batter stats and move batter to 1st
        foreach($teamatbat as $vs) {
            if($vs->playerid === $row['PLAYERID']) {
                
                $vs->stat_bb++;

                if($walktype === 'IW') {
                    $vs->stat_ibb++;
                }

            break;
            }
        }

        // move baserunners
        moveBaseRunners($row, 'Walk');
        
    }

    function moveBaseRunners($row, $outcome) {
        global $visBoxScoreStats;
        global $homeBoxScoreStats;
        global $runnersOnBase;

        $teamatbat = $row['TEAMATBAT'] === '0' ? $visBoxScoreStats : $homeBoxScoreStats;

        // runner out at 3rd
        if(strpos($row['OUTCOME'], '3X3')) {
            $runnersOnBase->runneron3rd = '';
        }

        // runner out at Home
        if(strpos($row['OUTCOME'], '3XH')) {
            $runnersOnBase->runneron3rd = '';
        }

        // runner out at Home
        if(strpos($row['OUTCOME'], '2XH')) {
            $runnersOnBase->runneron2nd = '';
        }

        // runner out at 2nd
        if(strpos($row['OUTCOME'], '2X2')) {
            $runnersOnBase->runneron2nd = '';
        }

        // runner out at Home
        if(strpos($row['OUTCOME'], '1XH')) {
            $runnersOnBase->runneron1st = '';
        }

        // runner out at 1st
        if(strpos($row['OUTCOME'], '1X1')) {
            $runnersOnBase->runneron1st = '';
        }

        // runner on 3rd scores
        if(strpos($row['OUTCOME'], '3-H') !== false) {
            foreach($teamatbat as $vs) {
                if($vs->playerid === $runnersOnBase->runneron3rd) {
                    $vs->stat_r++;
                    $runnersOnBase->runneron3rd = '';
                break;
                }
            }
        }

        // runner on 2nd to Home
        if(strpos($row['OUTCOME'],'2-H') !== false) {
            foreach($teamatbat as $vs) {
                if($vs->playerid === $runnersOnBase->runneron2nd) {
                    $vs->stat_r++;
                    $runnersOnBase->runneron2nd = '';
                break;
                }
            }
        }

        // runner on 2nd to 3rd
        if(strpos($row['OUTCOME'],'2-3') !== false) {
            $runnersOnBase->runneron3rd = $runnersOnBase->runneron2nd;
            $runnersOnBase->runneron2nd = '';
        }

        // runner on 1st to Home
        if(strpos($row['OUTCOME'],'1-H') !== false) {
            foreach($teamatbat as $vs) {
                if($vs->playerid === $runnersOnBase->runneron1st) {
                    $vs->stat_r++;
                    $runnersOnBase->runneron1st = '';
                break;
                }
            }
        }

        // runner on 1st to 3rd
        if(strpos($row['OUTCOME'],'1-3') !== false) {
            $runnersOnBase->runneron3rd = $runnersOnBase->runneron1st;
            $runnersOnBase->runneron1st = '';
        }

        // runner on 1st to 2nd
        if(strpos($row['OUTCOME'],'1-2') !== false) {
            $runnersOnBase->runneron2nd = $runnersOnBase->runneron1st;
            $runnersOnBase->runneron1st = '';
        }

        // batter to Home
        if(strpos($row['OUTCOME'],'B-H') !== false) {
            foreach($teamatbat as $vs) {
                if($vs->playerid === $row['PLAYERID']) {
                    $vs->stat_r++;
                break;
                }
            }
        }
        else if(strpos($row['OUTCOME'],'B-3') !== false) {
            $runnersOnBase->runneron3rd = $row['PLAYERID'];
        }
        else if(strpos($row['OUTCOME'],'B-2') !== false) {
            $runnersOnBase->runneron2nd = $row['PLAYERID'];
        }
        else if(strpos($row['OUTCOME'],'B-1') !== false) {
            $runnersOnBase->runneron1st = $row['PLAYERID'];
        }
        else if($outcome === 'Walk' || $outcome === 'Single') {
            $runnersOnBase->runneron1st = $row['PLAYERID'];
        }

    }

    function getSingles($row) {
        global $visBoxScoreStats;
        global $homeBoxScoreStats;

        $teamatbat = $row['TEAMATBAT'] === '0' ? $visBoxScoreStats : $homeBoxScoreStats;

        // record a single and at-bat for batter
        foreach($teamatbat as $vs) {
            if($vs->playerid === $row['PLAYERID']) {
                $vs->stat_1b++;
                $vs->stat_ab++;
            }
        }

        moveBaseRunners($row, 'Single');

        // check for RBI
        checkRBI($row);
    }

    function checkRBI($row) {
        global $visBoxScoreStats;
        global $homeBoxScoreStats;
        $count = 0; // track number of runs scored

        $teamatbat = $row['TEAMATBAT'] === '0' ? $visBoxScoreStats : $homeBoxScoreStats;

        // runner on 3rd scores
        if(strpos($row['OUTCOME'], '3-H') !== false) {
            foreach($visBoxScoreStats as $vs) {
                if($vs->playerid === $row['PLAYERID']) {
                    $count++;
                break;
                }
            }
        }

        if(strpos($row['OUTCOME'], '2-H') !== false) {
            foreach($visBoxScoreStats as $vs) {
                if($vs->playerid === $row['PLAYERID']) {
                    $count++;
                break;
                }
            }
        }

        if(strpos($row['OUTCOME'], '1-H') !== false) {
            foreach($visBoxScoreStats as $vs) {
                if($vs->playerid === $row['PLAYERID']) {
                    $count++;
                break;
                }
            }
        }

        if(strpos($row['OUTCOME'], '3-H') !== false) {
            foreach($visBoxScoreStats as $vs) {
                if($vs->playerid === $row['PLAYERID']) {
                    $count++;
                break;
                }
            }
        }

        // now count how many RBI were not earned - use substr_count to count the number of times NR appears in OUTCOME
        $count = $count - substr_count($row['OUTCOME'],'NR');

        // assign RBI
        foreach($visBoxScoreStats as $vs) {
            if($vs->playerid === $row['PLAYERID']) {
                $teamatbat->stat_rbi = $teamatbat->stat_rbi + $count;
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
            if($row['OUTCOME'][0]==='W' && $row['OUTCOME'][1] !== 'P') {
                getWalks($row, 'W');
            }

            // intentional walks
            if(strpos($row['OUTCOME'], 'IW') !== false) {
                getWalks($row, 'IW');
            }

            // singles, not stolen bases
            // if($row['OUTCOME'][0]==='S' and $row['OUTCOME'][1] !== 'B') {
            //     getSingles($row);
            // }
            // else {
            //     $runnersOnBase->clearRunners();
            // }
        }
    }

    ?>
    <div class="boxscore-bottom-border"></div>
    <div class="boxscore-center boxscore-bottom-border">AB</div>
    <div class="boxscore-center boxscore-bottom-border">R</div>
    <div class="boxscore-center boxscore-bottom-border">H</div>
    <div class="boxscore-center boxscore-bottom-border">RBI</div>
    <div class="boxscore-center boxscore-bottom-border">BB</div>
    <div class="boxscore-center boxscore-bottom-border">K</div>
    <div class="boxscore-center boxscore-bottom-border">LOB</div>

    <?php
    foreach($visBoxScoreStats as $vs) {
        ?>
        <div class="boxscore-name boxscore-cell"><?php echo getPlayerName($link, $vs->playerid) ?></div>
        <div class="boxscore-cell boxscore-center"><?php echo $vs->stat_ab ?></div>
        <div class="boxscore-cell boxscore-center"><?php echo $vs->stat_r ?></div>
        <div class="boxscore-cell boxscore-center"><?php echo $vs->getHits() ?></div>
        <div class="boxscore-cell boxscore-center"><?php echo $vs->stat_rbi ?></div>
        <div class="boxscore-cell boxscore-center"><?php echo $vs->stat_bb ?></div>
        <div class="boxscore-cell boxscore-center"><?php echo $vs->stat_k ?></div>
        <div class="boxscore-cell boxscore-cell-end boxscore-center"><?php echo $vs->stat_lob ?></div>
        <?php
    }
?>

</div>