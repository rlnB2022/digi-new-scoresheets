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
        public $stat_wp = 0;

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

    $gameTeamAtBat = 0;

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

        checkRBI($row);

        // assign a bb to batter stats and move batter to 1st
        foreach($teamatbat as $vs) {
            if($vs->playerid === $row['PLAYERID']) {
                $message = 'Add to walk total';
                showMessage($message);
                $vs->stat_bb++;

                if($walktype === 'IW') {
                    $message = 'Add to intentional walk total';
                    showMessage($message);
                    $vs->stat_ibb++;
                }

            break;
            }
        }

        // move baserunners
        moveBaseRunners($row, 'Walk');
    }

    function getHitByPitch($row) {
        global $visBoxScoreStats;
        global $homeBoxScoreStats;
        global $runnersOnBase;

        $teamatbat = $row['TEAMATBAT'] === '0' ? $visBoxScoreStats : $homeBoxScoreStats;

        checkRBI($row);

        // assign a hbp to batter stats and move batter to 1st
        foreach($teamatbat as $vs) {
            if($vs->playerid === $row['PLAYERID']) {
                $message = 'Add to HBP total';
                showMessage($message);
                $vs->stat_hbp++;
            break;
            }
        }

        // move baserunners
        moveBaseRunners($row, 'HBP');
    }

    function getStolenBase($row) {
        global $visBoxScoreStats;
        global $homeBoxScoreStats;
        global $runnersOnBase;

        $teamatbat = $row['TEAMATBAT'] === '0' ? $visBoxScoreStats : $homeBoxScoreStats;

        // assign a sb to batter stats and move batter home
        if(strpos($row['OUTCOME'], 'SBH') !== false) {
            $message = $runnersOnBase->runneron3rd . ' steals home!';
            showMessage($message);

            foreach($teamatbat as $vs) {
                if($vs->playerid === $runnersOnBase->runneron3rd) {
                    $message = ('Add to stolen base total for ' . $vs->playerid);
                    showMessage($message);

                    $vs->stat_sb++;
                    $vs->stat_r++;
                    $message = ('Remove runner on 3rd');

                    showMessage($message);
                    $runnersOnBase->runneron3rd = '';
                break;
                }
            }
        }

        // assign a sb to batter stats and move batter 3rd
        if(strpos($row['OUTCOME'], 'SB3') !== false) {
            $message = $runnersOnBase->runneron2nd . ' steals third!';
            showMessage($message);
            foreach($teamatbat as $vs) {
                if($vs->playerid === $runnersOnBase->runneron2nd) {
                    $message = ('Add to stolen base total for ' . $vs->playerid);
                    showMessage($message);
                    $vs->stat_sb++;
                    $message = ('Remove runner on 2nd');
                    showMessage($message);
                    $runnersOnBase->runneron3rd = $vs->playerid;
                    $runnersOnBase->runneron2nd = '';
                break;
                }
            }
        }

        // assign a sb to batter stats and move batter to 2nd
        if(strpos($row['OUTCOME'], 'SB2') !== false) {
            $message = $runnersOnBase->runneron1st . ' steals second!';
            showMessage($message);
            foreach($teamatbat as $vs) {
                if($vs->playerid === $runnersOnBase->runneron1st) {
                    $message = 'Add to stolen base total for ' . $vs->playerid;
                    showMessage($message);
                    $vs->stat_sb++;
                    $message = 'Remove runner on 1st';
                    $runnersOnBase->runneron2nd = $vs->playerid;
                    $runnersOnBase->runneron1st = '';
                break;
                }
            }
        }
    }

    function getCaughtStealing($row) {
        global $visBoxScoreStats;
        global $homeBoxScoreStats;
        global $runnersOnBase;

        $teamatbat = $row['TEAMATBAT'] === '0' ? $visBoxScoreStats : $homeBoxScoreStats;

        // assign a sb to batter stats
        if(strpos($row['OUTCOME'], 'CSH') !== false) {
            $message = $runnersOnBase->runneron3rd . ' caught stealing home!';
            showMessage($message);

            foreach($teamatbat as $vs) {
                if($vs->playerid === $runnersOnBase->runneron3rd) {
                    $message = 'Add to caught stealing base total for ' . $vs->playerid;
                    showMessage($message);

                    $vs->stat_cs++;
                    $message = 'Remove runner on 3rd';

                    showMessage($message);
                    $runnersOnBase->runneron3rd = '';
                break;
                }
            }
        }

        // assign a cs to batter stats
        if(strpos($row['OUTCOME'], 'CS3') !== false) {
            $message = $runnersOnBase->runneron2nd . ' caught stealing third!';
            showMessage($message);
            foreach($teamatbat as $vs) {
                if($vs->playerid === $runnersOnBase->runneron2nd) {
                    $message = 'Add to caught stealing base total for ' . $vs->playerid;
                    showMessage($message);
                    $vs->stat_cs++;
                    $message = 'Remove runner on 2nd';
                    showMessage($message);
                    $runnersOnBase->runneron2nd = '';
                break;
                }
            }
        }

        // assign a cs to batter stats
        if(strpos($row['OUTCOME'], 'CS2') !== false) {
            $message = $runnersOnBase->runneron1st . ' caught stealing second!';
            showMessage($message);
            foreach($teamatbat as $vs) {
                if($vs->playerid === $runnersOnBase->runneron1st) {
                    $message = 'Add to caught stealing base total for ' . $vs->playerid;
                    showMessage($message);
                    $vs->stat_cs++;
                    $message = 'Remove runner on 1st';
                    $runnersOnBase->runneron1st = '';
                break;
                }
            }
        }
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
            $message = ($runnersOnBase->runneron3rd . ' on 3rd base scores');
            showMessage($message);
            foreach($teamatbat as $vs) {
                if($vs->playerid === $runnersOnBase->runneron3rd) {
                    $message = 'Add run to ' . $vs->playerid;
                    showMessage($message);
                    $vs->stat_r++;
                    $message = 'Remove runner on 3rd';
                    showMessage($message);
                    $runnersOnBase->runneron3rd = '';
                break;
                }
            }
        }

        // runner on 2nd to Home
        if(strpos($row['OUTCOME'],'2-H') !== false) {
            $message = $runnersOnBase->runneron2nd . ' on 2nd base scores';
            showMessage($message);
            foreach($teamatbat as $vs) {
                if($vs->playerid === $runnersOnBase->runneron2nd) {
                    $message = 'Add run to ' . $vs->playerid;
                    showMessage($message);
                    $vs->stat_r++;
                    $message = 'Remove runner on 2nd';
                    showMessage($message);
                    $runnersOnBase->runneron2nd = '';
                break;
                }
            }
        }

        // runner on 2nd to 3rd
        if(strpos($row['OUTCOME'],'2-3') !== false) {
            $message = $runnersOnBase->runneron2nd . ' goes to 3rd';
            showMessage($message);
            $runnersOnBase->runneron3rd = $runnersOnBase->runneron2nd;
            $message = 'Runner no longer on 2nd';
            showMessage($message);
            $runnersOnBase->runneron2nd = '';
        }

        // runner on 1st to Home
        if(strpos($row['OUTCOME'],'1-H') !== false) {
            $message = $runnersOnBase->runneron1st . ' on 1st base scores';
            showMessage($message);
            foreach($teamatbat as $vs) {
                if($vs->playerid === $runnersOnBase->runneron1st) {
                    $message = 'Add run to ' . $vs->playerid;
                    showMessage($message);
                    $vs->stat_r++;
                    $message = 'Remove runner on 1st';
                    showMessage($message);
                    $runnersOnBase->runneron1st = '';
                break;
                }
            }
        }

        // runner on 1st to 3rd
        if(strpos($row['OUTCOME'],'1-3') !== false) {
            $message = $runnersOnBase->runneron1st . ' goes to 3rd';
            showMessage($message);
            $runnersOnBase->runneron3rd = $runnersOnBase->runneron1st;
            $message = 'Runner on 1st no longer';
            showMessage($message);
            $runnersOnBase->runneron1st = '';
        }

        // runner on 1st to 2nd
        if(strpos($row['OUTCOME'],'1-2') !== false) {
            $message = $runnersOnBase->runneron1st . ' goes to 2nd';
            showMessage($message);
            $runnersOnBase->runneron2nd = $runnersOnBase->runneron1st;
            $message = 'Runner on 1st no longer';
            showMessage($message);
            $runnersOnBase->runneron1st = '';
        }

        // batter to Home
        if(strpos($row['OUTCOME'],'B-H') !== false) {
            $message = $row['PLAYERID'] . ' scores!';
            showMessage($message);
            foreach($teamatbat as $vs) {
                if($vs->playerid === $row['PLAYERID']) {
                    $message = 'Add run to ' . $vs->playerid;
                    showMessage($message);
                    $vs->stat_r++;
                break;
                }
            }
        }
        else if(strpos($row['OUTCOME'],'B-3') !== false) {
            $message = $row['PLAYERID'] . ' goes to 3rd.';
            showMessage($message);
            $runnersOnBase->runneron3rd = $row['PLAYERID'];
        }
        else if(strpos($row['OUTCOME'],'B-2') !== false) {
            $message = $row['PLAYERID'] . ' goes to 2nd.';
            showMessage($message);
            $runnersOnBase->runneron2nd = $row['PLAYERID'];
        }
        else if(strpos($row['OUTCOME'],'B-1') !== false) {
            $message = $row['PLAYERID'] . ' goes to 1st.';
            showMessage($message);
            $runnersOnBase->runneron1st = $row['PLAYERID'];
        }
        else if($outcome === 'Walk' || $outcome === 'Single' || $outcome === 'HBP') {
            $message = $row['PLAYERID'] . ' goes to 1st with a ' . $outcome;
            showMessage($message);
            $runnersOnBase->runneron1st = $row['PLAYERID'];
        }
        else if($outcome === 'Double') {
            $message = $row['PLAYERID'] . ' goes to 2nd with a double.';
            showMessage($message);
            $runnersOnBase->runneron2nd = $row['PLAYERID'];
        }
        else if($outcome === 'Triple') {
            $message = $row['PLAYERID'] . ' goes to 3rd with a triple.';
            showMessage($message);
            $runnersOnBase->runneron3rd = $row['PLAYERID'];
        }
        else if($outcome === 'Error') {
            $message = $row['PLAYERID'] . ' goes to 1st on the error.';
            showMessage($message);
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

    function getDoubles($row) {
        global $visBoxScoreStats;
        global $homeBoxScoreStats;

        $teamatbat = $row['TEAMATBAT'] === '0' ? $visBoxScoreStats : $homeBoxScoreStats;

        // record a double and at-bat for batter
        foreach($teamatbat as $vs) {
            if($vs->playerid === $row['PLAYERID']) {
                $vs->stat_2b++;
                $vs->stat_ab++;
            }
        }

        moveBaseRunners($row, 'Double');

        // check for RBI
        checkRBI($row);
    }

    function getTriples($row) {
        global $visBoxScoreStats;
        global $homeBoxScoreStats;

        $teamatbat = $row['TEAMATBAT'] === '0' ? $visBoxScoreStats : $homeBoxScoreStats;

        // record a triple and at-bat for batter
        foreach($teamatbat as $vs) {
            if($vs->playerid === $row['PLAYERID']) {
                $vs->stat_3b++;
                $vs->stat_ab++;
            }
        }

        moveBaseRunners($row, 'Triple');

        // check for RBI
        checkRBI($row);
    }

    function getHomeruns($row) {
        global $visBoxScoreStats;
        global $homeBoxScoreStats;

        $teamatbat = $row['TEAMATBAT'] === '0' ? $visBoxScoreStats : $homeBoxScoreStats;

        // record a homerun and at-bat for batter
        foreach($teamatbat as $vs) {
            if($vs->playerid === $row['PLAYERID']) {
                $vs->stat_hr++;
                $vs->stat_r++;
                $vs->stat_ab++;
                $vs->stat_rbi++;
            }
        }

        moveBaseRunners($row, 'Homerun');

        // check for RBI
        checkRBI($row);
    }

    function getWildPitch($row) {
        global $visBoxScoreStats;
        global $homeBoxScoreStats;

        $teamatbat = $row['TEAMATBAT'] === '0' ? $visBoxScoreStats : $homeBoxScoreStats;

        // store stat for pitcher
        foreach($teamatbat as $vs) {
            if($vs->playerid === $row['PITCHERID']) {
                $vs->stat_wp++;
            }
        }

        // check for runner movement
        moveBaseRunners($row, '');
    }

    function checkRBI($row) {
        global $visBoxScoreStats;
        global $homeBoxScoreStats;

        $teamatbat = $row['TEAMATBAT'] === '0' ? $visBoxScoreStats : $homeBoxScoreStats;

        $rbiCount = 0;

        // runner on 3rd scores
        if(strpos($row['OUTCOME'], '3-H') !== false) {
            $rbiCount++;
        }

        if(strpos($row['OUTCOME'], '2-H') !== false) {
            $rbiCount++;
        }

        if(strpos($row['OUTCOME'], '1-H') !== false) {
            $rbiCount++;
        }

        // now count how many RBI were not earned - use substr_count to count the number of times NR appears in OUTCOME
        $rbiCount -= substr_count($row['OUTCOME'],'NR');

        // assign RBI
        foreach($teamatbat as $vs) {
            if($vs->playerid === $row['PLAYERID']) {
                $vs->stat_rbi += $rbiCount;
            break;
            }
        }
    }

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
            // clear baserunners if new team batting
            checkEndOfInning($row);

            // strikeouts
            if($row['OUTCOME'][0]==='K') {
                $message = (($row['PLAYERID'] . ' strikes out.'));
                showMessage($message);
                getStrikeouts($row);
            }

            // walks, not wild pitches
            else if($row['OUTCOME'][0]==='W' && $row['OUTCOME'][1] !== 'P') {
                $message = ($row['PLAYERID'] . ' gets a walk.');
                showMessage($message);
                getWalks($row, 'W');
            }

            // intentional walks
            else if(strpos($row['OUTCOME'], 'IW') !== false) {
                $message = ($row['PLAYERID'] . ' gets an intentional walk.');
                showMessage($message);
                getWalks($row, 'IW');
            }

            // singles, not stolen bases
            else if($row['OUTCOME'][0]==='S' and $row['OUTCOME'][1] !== 'B') {
                $message = ($row['PLAYERID'] . ' gets a single.');
                showMessage($message);
                getSingles($row);
            }

            // doubles, not defensive interference
            else if($row['OUTCOME'][0]==='D' and $row['OUTCOME'][1] !== 'I') {
                $message = ($row['PLAYERID'] . ' gets a double.');
                showMessage($message);
                getDoubles($row);
            }

            // triples
            else if($row['OUTCOME'][0]==='T') {
                $message = ($row['PLAYERID'] . ' gets a triple.');
                showMessage($message);
                getTriples($row);
            }

            // homeruns
            else if(strpos($row['OUTCOME'], 'HR') !== false) {
                $message = ($row['PLAYERID'] . ' gets a homerun.');
                showMessage($message);
                getHomeRuns($row);
            }

            // wild pitch
            else if(strpos($row['OUTCOME'], 'WP') !== false) {
                $message = 'WILD PITCH!';
                showMessage($message);
                getWildPitch($row);
            }

            // hit by pitch
            else if(strpos($row['OUTCOME'], 'HP') !== false) {
                $message = $row['PLAYERID'] . ' is HBP.';
                showMessage($message);
                getHitByPitch($row);
            }

            // get steals
            if(strpos($row['OUTCOME'], 'SB') !== false) {
                $message = 'STOLEN BASE';
                showMessage($message);
                getStolenBase($row);
            }

            // get caught stealing
            if(strpos($row['OUTCOME'], 'CS') !== false) {
                $message = 'CAUGHT STEALING';
                showMessage($message);
                getCaughtStealing($row);
            }

            // ERRORS
            if($row['OUTCOME'][0] === 'E') {
                $message = 'Error on the play';
                showMessage($message);
                getErrors($row, 'Error');
            }

            // POPUPS, NOT ERRORS
            if($row['OUTCOME'][0] === '1' || $row['OUTCOME'][0] === '2' || $row['OUTCOME'][0] === '3' || $row['OUTCOME'][0] === '4' || $row['OUTCOME'][0] === '5' || $row['OUTCOME'][0] === '6' || $row['OUTCOME'][0] === '7' || $row['OUTCOME'][0] === '8' || $row['OUTCOME'][0] === '9' || strpos($row['OUTCOME'], 'FC') !== false) {
                getOut($row);
            }
        }
    }

    function showMessage($msg) {
        // echo "<script type='text/javascript'>alert('$msg');</script>";
    }

    function getOut($row) {
        global $visBoxScoreStats;
        global $homeBoxScoreStats;
        global $runnersOnBase;

        $outs = 0;

        $teamatbat = $row['TEAMATBAT'] === '0' ? $visBoxScoreStats : $homeBoxScoreStats;

        if(strpos($row['OUTCOME'], '(1)') !== false) {
            // runner on 1st is out
            $message = $runnersOnBase->runneron1st . ' out at 2nd.';
            showMessage($message);
            $message = $runnersOnBase->runneron1st . ' removed from 1st.';
            showMessage($message);
            $runnersOnBase->runneron1st = '';
            $outs++;
        }

        if(strpos($row['OUTCOME'], '(2)') !== false) {
            // runner on 2nd is out
            $message = $runnersOnBase->runneron2nd . ' out at 3rd.';
            showMessage($message);
            $message = $runnersOnBase->runneron2nd . ' removed from 2nd.';
            showMessage($message);
            $runnersOnBase->runneron2nd = '';
            $outs++;
        }

        if(strpos($row['OUTCOME'], '(3)') !== false) {
            // runner on 3rd is out
            $message = $runnersOnBase->runneron3rd . ' out at home.';
            showMessage($message);
            $message = $runnersOnBase->runneron3rd . ' removed from 3rd.';
            showMessage($message);
            $runnersOnBase->runneron3rd = '';
            $outs++;
        }

        if(strpos($row['OUTCOME'], 'GDP') !== false) {
            $outs++;
        }

        if(strpos($row['OUTCOME'], 'SF') !== false) {
            // no at-bat for sac fly
            $message = 'Batter is out on a sac fly.';
            showMessage($message);
            // check for baserunner movement
            moveBaseRunners($row, '');
            foreach($teamatbat as $vs) {
                if($vs->playerid === $row['PLAYERID']) {
                    $vs->stat_sf++;
                    $vs->stat_rbi++;
                }
            }
        }
        else if(strpos($row['OUTCOME'], 'SH') !== false) {
            // no at-bat for sac bunts
            $message = 'Batter is out on a sac hit.';
            showMessage($message);
            // check for baserunner movement
            moveBaseRunners($row, '');
            foreach($teamatbat as $vs) {
                if($vs->playerid === $row['PLAYERID']) {
                    $vs->stat_sh++;
                }
            }
        }
        else {
            $message = 'Batter is out.';
            showMessage($message);
            // check for baserunner movement
            moveBaseRunners($row, '');
            foreach($teamatbat as $vs) {
                if($vs->playerid === $row['PLAYERID']) {
                    $vs->stat_ab++;
                }
            }
        }
    }

    function getErrors($row) {
        // Batter to 1st unless it states otherwise
        moveBaseRunners($row, 'Error');
    }

    function checkEndOfInning($row) {
        global $gameTeamAtBat;
        global $runnersOnBase;

        if($row['TEAMATBAT'] != $gameTeamAtBat && $row['CATEGORY'] !== 'com' && $row['CATEGORY'] !== 'sub') {
            $message = ('runners cleared.');
            showMessage($message);
            $runnersOnBase->clearRunners();
            $gameTeamAtBat ^= 1;
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