<?php

class playerStat {
    public $status = ''; // starter or sub
    public $playerid = '';
    public $playerpos = '';
    public $batter_stat_ab = 0;
    public $batter_stat_1b = 0;
    public $batter_stat_2b = 0;
    public $batter_stat_3b = 0;
    public $batter_stat_hr = 0;
    public $batter_stat_bb = 0;
    public $batter_stat_ibb = 0;
    public $batter_stat_k = 0;
    public $batter_stat_lob = 0;
    public $batter_stat_r = 0;
    public $batter_stat_rbi = 0;
    public $batter_stat_rbi_2_out = 0;
    public $batter_stat_gidp = 0;
    public $batter_stat_sb = 0;
    public $batter_stat_cs = 0;
    public $batter_stat_hbp = 0;
    public $batter_stat_sh = 0;
    public $batter_stat_sf = 0;
    public $pitcher_stat_wp = 0;
    public $pitcher_stat_er = 0;
    public $pitcher_stat_r = 0;
    public $pitcher_stat_bb = 0;
    public $pitcher_stat_k = 0;
    public $pitcher_batters_faced = 0;
    public $pitcher_innings_pitched = 0;
    public $pitcher_stat_1b = 0;
    public $pitcher_stat_2b = 0;
    public $pitcher_stat_3b = 0;
    public $pitcher_stat_hr = 0;
    // NEED TO FINISH THIS UP **********************************************************    

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

function getMonth($info) {
    $months = ['March', 'April', 'May', 'June', 'July', 'August', 'September'];
    $monthNum = $info -3;  // earliest month is March, the 0-index in $months above

    return $months[$monthNum];
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
    global $outs_in_the_inning;

    $teamatbat = $row['TEAMATBAT'] === '0' ? $visBoxScoreStats : $homeBoxScoreStats;

    foreach($teamatbat as $vs) {
        if($vs->playerid === $row['PLAYERID']) {
            $vs->stat_k++;
            $vs->stat_ab++;
        }
    }

    $outs_in_the_inning++;

    moveBaseRunners($row, 'k');
}

function getWalks($row, $walktype) {
    global $visBoxScoreStats;
    global $homeBoxScoreStats;
    global $runnersOnBase;

    $teamatbat = $row['TEAMATBAT'] === '0' ? $visBoxScoreStats : $homeBoxScoreStats;

    // assign a bb to batter stats and move batter to 1st
    foreach($teamatbat as $vs) {
        if($vs->playerid === $row['PLAYERID']) {
            // $message = 'Add to walk total';
            // showMessage($message);
            $vs->stat_bb++;

            if($walktype === 'IW') {
                // $message = 'Add to intentional walk total';
                // showMessage($message);
                // $vs->stat_ibb++;
            }

        break;
        }
    }

    // move baserunners
    moveBaseRunners($row, 'Walk');

    checkRBI($row);
}

function getHitByPitch($row) {
    global $visBoxScoreStats;
    global $homeBoxScoreStats;
    global $runnersOnBase;

    $teamatbat = $row['TEAMATBAT'] === '0' ? $visBoxScoreStats : $homeBoxScoreStats;

    // assign a hbp to batter stats and move batter to 1st
    foreach($teamatbat as $vs) {
        if($vs->playerid === $row['PLAYERID']) {
            // $message = 'Add to HBP total';
            // showMessage($message);
            $vs->stat_hbp++;
        break;
        }
    }

    // move baserunners
    moveBaseRunners($row, 'HBP');

    checkRBI($row);
}

function getStolenBase($row) {
    global $visBoxScoreStats;
    global $homeBoxScoreStats;
    global $runnersOnBase;

    $teamatbat = $row['TEAMATBAT'] === '0' ? $visBoxScoreStats : $homeBoxScoreStats;

    // assign a sb to batter stats and move batter home
    if(strpos($row['OUTCOME'], 'SBH') !== false) {
        $message = $runnersOnBase->runneron3rd . ' steals home!';
        // showMessage($message);

        foreach($teamatbat as $vs) {
            if($vs->playerid === $runnersOnBase->runneron3rd) {
                // $message = ('Add to stolen base total for ' . $vs->playerid);
                // showMessage($message);

                $vs->stat_sb++;
                $vs->stat_r++;
                $message = ('Remove runner on 3rd');

                // showMessage($message);
                $runnersOnBase->runneron3rd = 'None';
            break;
            }
        }
    }

    // assign a sb to batter stats and move batter 3rd
    if(strpos($row['OUTCOME'], 'SB3') !== false) {
        $message = $runnersOnBase->runneron2nd . ' steals third!';
        // showMessage($message);
        foreach($teamatbat as $vs) {
            if($vs->playerid === $runnersOnBase->runneron2nd) {
                // $message = ('Add to stolen base total for ' . $vs->playerid);
                // showMessage($message);
                $vs->stat_sb++;
                $message = ('Remove runner on 2nd');
                // showMessage($message);
                $runnersOnBase->runneron3rd = $vs->playerid;
                $runnersOnBase->runneron2nd = 'None';
            break;
            }
        }
    }

    // assign a sb to batter stats and move batter to 2nd
    if(strpos($row['OUTCOME'], 'SB2') !== false) {
        $message = $runnersOnBase->runneron1st . ' steals second!';
        // showMessage($message);
        foreach($teamatbat as $vs) {
            if($vs->playerid === $runnersOnBase->runneron1st) {
                // $message = 'Add to stolen base total for ' . $vs->playerid;
                // showMessage($message);
                $vs->stat_sb++;
                $message = 'Remove runner on 1st';
                // showMessage($message);
                $runnersOnBase->runneron2nd = $vs->playerid;
                $runnersOnBase->runneron1st = 'None';
            break;
            }
        }
    }
}

function getCaughtStealing($row) {
    global $visBoxScoreStats;
    global $homeBoxScoreStats;
    global $runnersOnBase;
    global $outs_in_the_inning;

    $teamatbat = $row['TEAMATBAT'] === '0' ? $visBoxScoreStats : $homeBoxScoreStats;

    // assign a sb to batter stats
    if(strpos($row['OUTCOME'], 'CSH') !== false) {
        $message = $runnersOnBase->runneron3rd . ' caught stealing home!';
        // showMessage($message);

        foreach($teamatbat as $vs) {
            if($vs->playerid === $runnersOnBase->runneron3rd) {
                // $message = 'Add to caught stealing base total for ' . $vs->playerid;
                // showMessage($message);

                $vs->stat_cs++;
                $message = 'Remove runner on 3rd';

                // showMessage($message);
                $runnersOnBase->runneron3rd = 'None';
            break;
            }
        }
    }

    // assign a cs to batter stats
    if(strpos($row['OUTCOME'], 'CS3') !== false) {
        $message = $runnersOnBase->runneron2nd . ' caught stealing third!';
        // showMessage($message);
        foreach($teamatbat as $vs) {
            if($vs->playerid === $runnersOnBase->runneron2nd) {
                // $message = 'Add to caught stealing base total for ' . $vs->playerid;
                // showMessage($message);
                $vs->stat_cs++;
                $message = 'Remove runner on 2nd';
                // showMessage($message);
                $runnersOnBase->runneron2nd = 'None';
            break;
            }
        }
    }

    // assign a cs to batter stats
    if(strpos($row['OUTCOME'], 'CS2') !== false) {
        $message = $runnersOnBase->runneron1st . ' caught stealing second!';
        // showMessage($message);
        foreach($teamatbat as $vs) {
            if($vs->playerid === $runnersOnBase->runneron1st) {
                // $message = 'Add to caught stealing base total for ' . $vs->playerid;
                // showMessage($message);
                $vs->stat_cs++;
                $message = 'Remove runner on 1st';
                // showMessage($message);
                $runnersOnBase->runneron1st = 'None';
            break;
            }
        }
    }

    $outs_in_the_inning++;

}

function moveBaseRunners($row, $outcome) {
    global $visBoxScoreStats;
    global $homeBoxScoreStats;
    global $runnersOnBase;
    global $outs_in_the_inning;

    $batterMoved = false;

    $teamatbat = $row['TEAMATBAT'] === '0' ? $visBoxScoreStats : $homeBoxScoreStats;

    // runner out at Home
    if(strpos($row['OUTCOME'], '3XH')) {
        $message = 'runner on 3rd out at home';
        // showMessage($message);
        $runnersOnBase->runneron3rd = 'None';
        $outs_in_the_inning++;
    }

    // runner out at 3rd
    if(strpos($row['OUTCOME'], '3X3')) {
        // showMessage('runner on 3rd out at 3rd');
        $runnersOnBase->runneron3rd = 'None';
        $outs_in_the_inning++;
    }

    // runner out at Home
    if(strpos($row['OUTCOME'], '2XH')) {
        // showMessage('runner on 2nd out at home');
        $runnersOnBase->runneron2nd = 'None';
        $outs_in_the_inning++;
    }

    // runner out at 3rd
    if(strpos($row['OUTCOME'], '2X3')) {
        // showMessage('runner on 2nd out at 3rd');
        $runnersOnBase->runneron2nd = 'None';
        $outs_in_the_inning++;
    }

    // runner out at 2nd
    if(strpos($row['OUTCOME'], '2X2')) {
        // showMessage('runner on 2nd out at 2nd');
        $runnersOnBase->runneron2nd = 'None';
        $outs_in_the_inning++;
    }

    // runner out at Home
    if(strpos($row['OUTCOME'], '1XH')) {
        // showMessage('runner on 1st out at home');
        $runnersOnBase->runneron1st = 'None';
        $outs_in_the_inning++;
    }

    // runner out at 3rd
    if(strpos($row['OUTCOME'], '1X3')) {
        // showMessage('runner on 1st out at 3rd');
        $runnersOnBase->runneron1st = 'None';
        $outs_in_the_inning++;
    }

    // runner out at 2nd
    if(strpos($row['OUTCOME'], '1X2')) {
        // showMessage('runner on 1st out at 2nd');
        $runnersOnBase->runneron2nd = 'None';
        $outs_in_the_inning++;
    }

    // runner out at 1st
    if(strpos($row['OUTCOME'], '1X1')) {
        // showMessage('runner on 1st out at 1st');
        $runnersOnBase->runneron1st = 'None';
        $outs_in_the_inning++;
    }

    // runner on 3rd scores
    if(strpos($row['OUTCOME'], '3-H') !== false) {
        $message = ($runnersOnBase->runneron3rd . ' on 3rd base scores');
        // showMessage($message);
        foreach($teamatbat as $vs) {
            if($vs->playerid === $runnersOnBase->runneron3rd) {
                // $message = 'Add run to ' . $vs->playerid;
                // showMessage($message);
                $vs->stat_r++;
                $message = 'Remove runner on 3rd';
                // showMessage($message);
                $runnersOnBase->runneron3rd = 'None';
            break;
            }
        }
    }

    // runner on 2nd to Home
    if(strpos($row['OUTCOME'],'2-H') !== false) {
        $message = $runnersOnBase->runneron2nd . ' on 2nd base scores';
        // showMessage($message);
        foreach($teamatbat as $vs) {
            if($vs->playerid === $runnersOnBase->runneron2nd) {
                // $message = 'Add run to ' . $vs->playerid;
                // showMessage($message);
                $vs->stat_r++;
                $message = 'Remove runner on 2nd';
                // showMessage($message);
                $runnersOnBase->runneron2nd = 'None';
            break;
            }
        }
    }

    // runner on 2nd to 3rd
    if(strpos($row['OUTCOME'],'2-3') !== false) {
        $message = $runnersOnBase->runneron2nd . ' goes to 3rd';
        // showMessage($message);
        $runnersOnBase->runneron3rd = $runnersOnBase->runneron2nd;
        $message = 'Runner no longer on 2nd';
        // showMessage($message);
        $runnersOnBase->runneron2nd = 'None';
    }

    // runner on 1st to Home
    if(strpos($row['OUTCOME'],'1-H') !== false) {
        $message = $runnersOnBase->runneron1st . ' on 1st base scores';
        // showMessage($message);
        foreach($teamatbat as $vs) {
            if($vs->playerid === $runnersOnBase->runneron1st) {
                // $message = 'Add run to ' . $vs->playerid;
                // showMessage($message);
                $vs->stat_r++;
                $message = 'Remove runner on 1st';
                // showMessage($message);
                $runnersOnBase->runneron1st = 'None';
            break;
            }
        }
    }

    // runner on 1st to 3rd
    if(strpos($row['OUTCOME'],'1-3') !== false) {
        $message = $runnersOnBase->runneron1st . ' goes to 3rd';
        // showMessage($message);
        $runnersOnBase->runneron3rd = $runnersOnBase->runneron1st;
        $message = 'Runner on 1st no longer';
        // showMessage($message);
        $runnersOnBase->runneron1st = 'None';
    }

    // runner on 1st to 2nd
    if(strpos($row['OUTCOME'],'1-2') !== false) {
        $message = $runnersOnBase->runneron1st . ' goes to 2nd';
        // showMessage($message);
        $runnersOnBase->runneron2nd = $runnersOnBase->runneron1st;
        $message = 'Runner on 1st no longer';
        // showMessage($message);
        $runnersOnBase->runneron1st = 'None';
    }

    // batter to Home
    if(strpos($row['OUTCOME'],'B-H') !== false) {
        $message = $row['PLAYERID'] . ' scores!';
        // showMessage($message);
        foreach($teamatbat as $vs) {
            if($vs->playerid === $row['PLAYERID']) {
                // $message = 'Add run to ' . $vs->playerid;
                // showMessage($message);
                $vs->stat_r++;
            break;
            }
        }
    }
    else if(strpos($row['OUTCOME'],'B-3') !== false) {
        $message = $row['PLAYERID'] . ' goes to 3rd.';
        // showMessage($message);
        $batterMoved = true;
        $runnersOnBase->runneron3rd = $row['PLAYERID'];
    }
    else if(strpos($row['OUTCOME'],'B-2') !== false) {
        $message = $row['PLAYERID'] . ' goes to 2nd.';
        // showMessage($message);
        $batterMoved = true;
        $runnersOnBase->runneron2nd = $row['PLAYERID'];
    }
    else if(strpos($row['OUTCOME'],'B-1') !== false) {
        $message = $row['PLAYERID'] . ' goes to 1st.';
        // showMessage($message);
        $batterMoved = true;
        $runnersOnBase->runneron1st = $row['PLAYERID'];
    }

    if(!$batterMoved) {
        if($outcome === 'Walk' || $outcome === 'Single' || $outcome === 'HBP') {
            $message = $row['PLAYERID'] . ' goes to 1st with a ' . $outcome;
            // showMessage($message);
            $runnersOnBase->runneron1st = $row['PLAYERID'];
        }
        else if($outcome === 'Double') {
            $message = $row['PLAYERID'] . ' goes to 2nd with a double.';
            // showMessage($message);
            $runnersOnBase->runneron2nd = $row['PLAYERID'];
        }
        else if($outcome === 'Triple') {
            $message = $row['PLAYERID'] . ' goes to 3rd with a triple.';
            // showMessage($message);
            $runnersOnBase->runneron3rd = $row['PLAYERID'];
        }
        else if($outcome === 'Error') {
            $message = $row['PLAYERID'] . ' goes to 1st on the error.';
            // showMessage($message);
            $runnersOnBase->runneron1st = $row['PLAYERID'];
        }
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
    global $outs_in_the_inning;

    $teamatbat = $row['TEAMATBAT'] === '0' ? $visBoxScoreStats : $homeBoxScoreStats;

    // record a homerun and at-bat for batter
    foreach($teamatbat as $vs) {
        if($vs->playerid === $row['PLAYERID']) {
            $vs->stat_hr++;
            $vs->stat_r++;
            $vs->stat_ab++;
            $vs->stat_rbi++;
            if($outs_in_the_inning === 2) {
                $vs->stat_rbi_2_out++;
            }
        }
    }

    moveBaseRunners($row, 'Homerun');

    // check for RBI
    checkRBI($row);
}

function getWildPitch($row) {
    global $visPitcherBoxScoreStats;
    global $homePitcherBoxScoreStats;

    $teamatbat = $row['TEAMATBAT'] === '0' ? $visPitcherBoxScoreStats : $homePitcherBoxScoreStats;

    // store stat for pitcher
    foreach($teamatbat as $vs) {
        if($vs->playerid === $row['PITCHERID']) {
            $vs->pitcher_stat_wp++;
        }
    }

    // check for runner movement
    moveBaseRunners($row, '');
}

function checkRBI($row) {
    global $visBoxScoreStats;
    global $homeBoxScoreStats;
    global $outs_in_the_inning;

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

            if($outs_in_the_inning === 2) {
                $vs->stat_rbi_2_out += $rbiCount;
            }
        break;
        }
    }
}

function showMessage($msg) {
    echo "<script type='text/javascript'>alert('$msg');</script>";
}

function getOut($row) {
    global $visBoxScoreStats;
    global $homeBoxScoreStats;
    global $runnersOnBase;
    global $outs_in_the_inning;

    $outs = 0;

    $teamatbat = $row['TEAMATBAT'] === '0' ? $visBoxScoreStats : $homeBoxScoreStats;

    if(strpos($row['OUTCOME'], '(1)') !== false) {
        // runner on 1st is out
        $message = $runnersOnBase->runneron1st . ' out at 2nd.';
        // showMessage($message);
        $message = $runnersOnBase->runneron1st . ' removed from 1st.';
        // showMessage($message);
        $runnersOnBase->runneron1st = 'None';
        $outs++;
    }

    if(strpos($row['OUTCOME'], '(2)') !== false) {
        // runner on 2nd is out
        $message = $runnersOnBase->runneron2nd . ' out at 3rd.';
        // showMessage($message);
        $message = $runnersOnBase->runneron2nd . ' removed from 2nd.';
        // showMessage($message);
        $runnersOnBase->runneron2nd = 'None';
        $outs++;
    }

    if(strpos($row['OUTCOME'], '(3)') !== false) {
        // runner on 3rd is out
        $message = $runnersOnBase->runneron3rd . ' out at home.';
        // showMessage($message);
        $message = $runnersOnBase->runneron3rd . ' removed from 3rd.';
        // showMessage($message);
        $runnersOnBase->runneron3rd = 'None';
        $outs++;
    }

    if(strpos($row['OUTCOME'], 'GDP') !== false) {
        $outs++;
    }

    if(strpos($row['OUTCOME'], 'SF') !== false) {
        // no at-bat for sac fly
        $message = 'Batter is out on a sac fly.';
        // showMessage($message);
        // check for baserunner movement
        moveBaseRunners($row, '');

        foreach($teamatbat as $vs) {
            if($vs->playerid === $row['PLAYERID']) {
                $vs->stat_sf++;
                $vs->stat_rbi++;
                $outs++;
            }
        }
    }
    else if(strpos($row['OUTCOME'], 'SH') !== false) {
        // no at-bat for sac bunts
        $message = 'Batter is out on a sac hit.';
        // showMessage($message);
        // check for baserunner movement
        moveBaseRunners($row, '');
        foreach($teamatbat as $vs) {
            if($vs->playerid === $row['PLAYERID']) {
                $vs->stat_sh++;
                $outs++;
            }
        }
    }
    else {
        // showMessage('Batter is out.');
        // check for baserunner movement
        moveBaseRunners($row, '');
        foreach($teamatbat as $vs) {
            if($vs->playerid === $row['PLAYERID']) {
                $vs->stat_ab++;
                $outs++;
            }
        }
    }

    $outs_in_the_inning += $outs;

}

function getErrors($row) {
    // Batter to 1st unless it states otherwise
    moveBaseRunners($row, 'Error');
}

function checkEndOfInning($row) {
    global $gameTeamAtBat;
    global $runnersOnBase;
    global $outs_in_the_inning;
    global $visTeamLOB;
    global $homeTeamLOB;

    if($row['TEAMATBAT'] != $gameTeamAtBat && $row['CATEGORY'] !== 'com' && $row['CATEGORY'] !== 'sub') {
        // assign runners LOB to correct team

        if($gameTeamAtBat === 0) {
            // showMessage($runnersOnBase->runneron1st . ', ' . $runnersOnBase->runneron2nd . ', ' . $runnersOnBase->runneron3rd);
            $visTeamLOB += calculateTotalRunnersOnBase();
        }
        else {
            // showMessage($runnersOnBase->runneron1st . ', ' . $runnersOnBase->runneron2nd . ', ' . $runnersOnBase->runneron3rd);
            $homeTeamLOB += calculateTotalRunnersOnBase();
        }

        $runnersOnBase->clearRunners();
        $gameTeamAtBat ^= 1;
        $outs_in_the_inning = 0;
    }
}

function checkForStrikeouts($row) {
    if($row['OUTCOME'][0]==='K') {
        $message = (($row['PLAYERID'] . ' strikes out.'));
        // showMessage($message);
        getStrikeouts($row);
    }
}

function checkForWalks($row) {
    if($row['OUTCOME'][0]==='W' && $row['OUTCOME'][1] !== 'P') {
        $message = ($row['PLAYERID'] . ' gets a walk.');
        // showMessage($message);
        getWalks($row, 'W');
    }
}

function checkForIntentionalWalks($row) {
    if(strpos($row['OUTCOME'], 'IW') !== false) {
        $message = ($row['PLAYERID'] . ' gets an intentional walk.');
        // showMessage($message);
        getWalks($row, 'IW');
    }
}

function checkForSingles($row) {
    if($row['OUTCOME'][0]==='S' and $row['OUTCOME'][1] !== 'B') {
        $message = ($row['PLAYERID'] . ' gets a single.');
        // showMessage($message);
        getSingles($row);
    }
}

function checkForDoubles($row) {
    if($row['OUTCOME'][0]==='D' and $row['OUTCOME'][1] !== 'I') {
        $message = ($row['PLAYERID'] . ' gets a double.');
        // showMessage($message);
        getDoubles($row);
    }
}

function checkForTriples($row) {
    if($row['OUTCOME'][0]==='T') {
        $message = ($row['PLAYERID'] . ' gets a triple.');
        // showMessage($message);
        getTriples($row);
    }
}

function checkForHomeruns($row) {
    if(strpos($row['OUTCOME'], 'HR') !== false) {
        $message = ($row['PLAYERID'] . ' gets a homerun.');
        // showMessage($message);
        getHomeRuns($row);
    }
}

function checkForWildPitch($row) {
    if(strpos($row['OUTCOME'], 'WP') !== false) {
        $message = 'WILD PITCH!';
        // showMessage($message);
        getWildPitch($row);
    }
}

function checkForHitByPitch($row) {
    if(strpos($row['OUTCOME'], 'HP') !== false) {
        $message = $row['PLAYERID'] . ' is HBP.';
        // showMessage($message);
        getHitByPitch($row);
    }
}

function checkForStolenBase($row) {
    if(strpos($row['OUTCOME'], 'SB') !== false) {
        $message = 'STOLEN BASE';
        // showMessage($message);
        getStolenBase($row);
    }
}

function checkForCaughtStealing($row) {
    if(strpos($row['OUTCOME'], 'CS') !== false) {
        $message = 'CAUGHT STEALING';
        // showMessage($message);
        getCaughtStealing($row);
    }
}

function checkForErrors($row) {
    if($row['OUTCOME'][0] === 'E') {
        $message = 'Error on the play';
        // showMessage($message);
        getErrors($row, 'Error');
    }
}

function checkForOuts($row) {
    if($row['OUTCOME'][0] === '1' || $row['OUTCOME'][0] === '2' || $row['OUTCOME'][0] === '3' || $row['OUTCOME'][0] === '4' || $row['OUTCOME'][0] === '5' || $row['OUTCOME'][0] === '6' || $row['OUTCOME'][0] === '7' || $row['OUTCOME'][0] === '8' || $row['OUTCOME'][0] === '9' || strpos($row['OUTCOME'], 'FC') !== false) {
        getOut($row);
    }
}

function checkForSub($row) {
    global $visBoxScoreStats;
    global $homeBoxScoreStats;
    global $visSubCount;
    global $homeSubCount;

    $subBatter = new playerStat();

    if($row['CATEGORY'] === 'sub') {
        // determine whether a new pitcher or batter is being substituted
        if($row['PITCHES'] !== '1') {
            // not a pitcher

            $teamatbat = $row['PLAYERID'] === '0' ? $visBoxScoreStats : $homeBoxScoreStats;

            // assign playerid to new batter stats
            $subBatter->playerid = $row['INNING'];
            $subBatter->playerpos = getPosition($row['PITCHES']);
            $subBatter->status = 'sub';

            // is the player listed in the lineup already, just moving positions?
            foreach($teamatbat as $vs) {
                if($vs->playerid === $subBatter->playerid) {
                    $vs->playerpos = $vs->playerpos . '-' . getPosition($row['PITCHES']);
                return;
                }
            }

            if($row['PLAYERID'] === '0') {
                //insert new player into lineup

                array_splice($visBoxScoreStats, $row['COUNT'] + $visSubCount, 0, [$subBatter]);

                $visSubCount++;
            }
            else {
                //insert new player into lineup
                array_splice($homeBoxScoreStats, $row['COUNT'] + $homeSubCount, 0, [$subBatter]);

                $homeSubCount++;
            }

        }
    }
}

function getPosition($num) {
    $pos = ['P', 'C', '1B', '2B', '3B', 'SS', 'LF', 'CF', 'RF', 'DH'];

    return $pos[$num-1];
}

function calculateTotalRunnersOnBase() {
    global $runnersOnBase;

    $count = 0;
    
    if($runnersOnBase->runneron1st !== 'None') {
        // showMessage('one');
        $count++;
    }

    if($runnersOnBase->runneron2nd !== 'None') {
        // showMessage('two');
        $count++;
    }

    if($runnersOnBase->runneron3rd !== 'None') {
        // showMessage('three');
        $count++;
    }

    showMessage($count . ' total runners');

    return $count;
}

function showRunners() {
    global $runnersOnBase;

    // showMessage('1st: ' . $runnersOnBase->runneron1st . ', 2nd: ' . $runnersOnBase->runneron2nd . ', 3rd: ' . $runnersOnBase->runneron3rd);
}
?>