<?php

function getLineScores($linky, $gamedate, $hometeam, $gamenum, $month, $day, $year) {
    $sql = "SELECT * FROM GAMELOGS WHERE DATE = '" . $gamedate  . "' AND HOMETEAM = '" . $hometeam . "' AND GAMENUM = '" . $gamenum . "' LIMIT 1";

    $result = mysqli_query($linky, $sql);

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
}

function getVisitorLineup($linky, $gameid, &$gameState, &$visBoxScoreStats, &$homeBoxScoreStats) {

    $sql = "SELECT * FROM VISLINEUPS WHERE GAMEID = '" . $gameid . "'";

    $result = mysqli_query($linky, $sql);

    $resultCheck = mysqli_num_rows($result);

    if($resultCheck > 0) {
        $row = mysqli_fetch_assoc($result);

        for($i = 0; $i < 9; $i++) {
            // add playerid to visBoxScoreStats
            $batterS = new playerStat();
            $batterS->playerid = $row['batter' . ($i + 1)];
            $batterS->playerpos = $row['pos' . ($i + 1)];
            $batterS->status = 'starter';

            $tempPos = 'pos_' . $batterS->playerpos;

            // assign position to visDefense
            $gameState->visDefense->$tempPos = $batterS->playerid;

            array_push($visBoxScoreStats, $batterS);
        }
    }
}

function getStartingPitchers($linky, $gamedate, $hometeam, $gamenum, &$visPitcherBoxScoreStats, &$homePitcherBoxScoreStats) {

    // this function is redundant - should grab data from getlinescore function
    // no sense in another database query
    $sql = "SELECT * FROM GAMELOGS WHERE DATE = '" . $gamedate  . "' AND HOMETEAM = '" . $hometeam . "' AND GAMENUM = '" . $gamenum . "' LIMIT 1";

    $result = mysqli_query($linky, $sql);

    $resultCheck = mysqli_num_rows($result);

    if($resultCheck > 0) {
        $row = mysqli_fetch_assoc($result);
        $batterS = new playerStat();
        $batterS->playerid = $row['visstarterid'];
        $batterS->playerpos = 'P';
        $batterS->status = 'starter';
        array_push($visPitcherBoxScoreStats, $batterS);

        $batterS = new playerStat();
        $batterS->playerid = $row['homestarterid'];
        $batterS->playerpos = 'P';
        $batterS->status = 'starter';
        array_push($homePitcherBoxScoreStats, $batterS);
    }
}

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
    public $fielder_stat_error = 0;
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
        return $this->batter_stat_1b + $this->batter_stat_2b + $this->batter_stat_3b + $this->batter_stat_hr;
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
        echo "NOT FOUND";
    }

    return $fullname;
}

function getStrikeouts($row, &$gameState, &$visBoxScoreStats, &$homeBoxScoreStats) {

    $teamatbat = $row['TEAMATBAT'] === '0' ? $visBoxScoreStats : $homeBoxScoreStats;

    foreach($teamatbat as $vs) {
        if($vs->playerid === $row['PLAYERID']) {
            $vs->batter_stat_k++;
            $vs->batter_stat_ab++;
        }
    }

    // runners in scoring position (RISP)
    if($gameState->runneron2nd !=='None' || $gameState->runneron3rd !=='None') {
        if($row['TEAMATBAT'] === '0') {
            $gameState->risp_vis_ab++;
        }
        else {
            $gameState->risp_home_ab++;
        }
    }

    $gameState->outs_in_the_inning++;

    moveBaseRunners($row, 'k', $gameState, $visBoxScoreStats, $homeBoxScoreStats);
}

function getWalks($row, $walktype, &$gameState, &$visBoxScoreStats, &$homeBoxScoreStats) {

    $teamatbat = $row['TEAMATBAT'] === '0' ? $visBoxScoreStats : $homeBoxScoreStats;

    // assign a bb to batter stats and move batter to 1st
    foreach($teamatbat as $vs) {
        if($vs->playerid === $row['PLAYERID']) {
            $vs->batter_stat_bb++;

            if($walktype === 'IW') {
                $vs->batter_stat_ibb++;
            }

        break;
        }
    }

    // move baserunners
    moveBaseRunners($row, 'Walk', $gameState, $visBoxScoreStats, $homeBoxScoreStats);

    checkRBI($row, $gameState, $visBoxScoreStats, $homeBoxScoreStats);
}

function getHitByPitch($row, &$gameState, &$visBoxScoreStats, &$homeBoxScoreStats) {

    $teamatbat = $row['TEAMATBAT'] === '0' ? $visBoxScoreStats : $homeBoxScoreStats;

    // assign a hbp to batter stats and move batter to 1st
    foreach($teamatbat as $vs) {
        if($vs->playerid === $row['PLAYERID']) {
            $vs->batter_stat_hbp++;
        break;
        }
    }

    // move baserunners
    moveBaseRunners($row, 'HBP', $gameState, $visBoxScoreStats, $homeBoxScoreStats);

    checkRBI($row, $gameState, $visBoxScoreStats, $homeBoxScoreStats);
}

function getStolenBase($row, &$gameState, &$visBoxScoreStats, &$homeBoxScoreStats) {

    $teamatbat = $row['TEAMATBAT'] === '0' ? $visBoxScoreStats : $homeBoxScoreStats;

    // assign a sb to batter stats and move batter home
    if(strpos($row['OUTCOME'], 'SBH') !== false) {
        foreach($teamatbat as $vs) {
            if($vs->playerid === $gameState->runneron3rd) {

                $vs->batter_stat_sb++;
                $vs->batter_stat_r++;
                $gameState->runneron3rd = 'None';
            break;
            }
        }
    }

    // assign a sb to batter stats and move batter 3rd
    if(strpos($row['OUTCOME'], 'SB3') !== false) {
        foreach($teamatbat as $vs) {
            if($vs->playerid === $gameState->runneron2nd) {
                $vs->batter_stat_sb++;
                $gameState->runneron3rd = $vs->playerid;
                $gameState->runneron2nd = 'None';
            break;
            }
        }
    }

    // assign a sb to batter stats and move batter to 2nd
    if(strpos($row['OUTCOME'], 'SB2') !== false) {
        foreach($teamatbat as $vs) {
            if($vs->playerid === $gameState->runneron1st) {
                $vs->batter_stat_sb++;
                $gameState->runneron2nd = $vs->playerid;
                $gameState->runneron1st = 'None';
            break;
            }
        }
    }

    if($row['TEAMATBAT'] === '0') {
        $gameState->visStolenBases++;
    }
    else {
        $gameState->homeStolenBases++;
    }

}

function getCaughtStealing($row, &$gameState, &$visBoxScoreStats, &$homeBoxScoreStats) {

    $teamatbat = $row['TEAMATBAT'] === '0' ? $visBoxScoreStats : $homeBoxScoreStats;

    // assign a sb to batter stats
    if(strpos($row['OUTCOME'], 'CSH') !== false) {

        foreach($teamatbat as $vs) {
            if($vs->playerid === $gameState->runneron3rd) {
                $vs->batter_stat_cs++;
                $gameState->runneron3rd = 'None';
            break;
            }
        }
    }

    // assign a cs to batter stats
    if(strpos($row['OUTCOME'], 'CS3') !== false) {
        foreach($teamatbat as $vs) {
            if($vs->playerid === $gameState->runneron2nd) {
                $vs->batter_stat_cs++;
                $gameState->runneron2nd = 'None';
            break;
            }
        }
    }

    // assign a cs to batter stats
    if(strpos($row['OUTCOME'], 'CS2') !== false) {
        foreach($teamatbat as $vs) {
            if($vs->playerid === $gameState->runneron1st) {
                $vs->batter_stat_cs++;
                $gameState->runneron1st = 'None';
            break;
            }
        }
    }

    $gameState->outs_in_the_inning++;

    if($row['TEAMATBAT'] === '0') {
        $gameState->visCaughtStealing++;
    }
    else {
        $gameState->homeCaughtStealing++;
    }

}

function moveBaseRunners($row, $outcome, &$gameState, &$visBoxScoreStats, &$homeBoxScoreStats) {

    $batterMoved = false;

    $teamatbat = $row['TEAMATBAT'] === '0' ? $visBoxScoreStats : $homeBoxScoreStats;

    // runner out at Home
    if(strpos($row['OUTCOME'], '3XH')) {
        $gameState->runneron3rd = 'None';
        $gameState->outs_in_the_inning++;
    }

    // runner out at 3rd
    if(strpos($row['OUTCOME'], '3X3')) {

        $gameState->runneron3rd = 'None';
        $gameState->outs_in_the_inning++;
    }

    // runner out at Home
    if(strpos($row['OUTCOME'], '2XH')) {

        $gameState->runneron2nd = 'None';
        $gameState->outs_in_the_inning++;
    }

    // runner out at 3rd
    if(strpos($row['OUTCOME'], '2X3')) {
        $gameState->runneron2nd = 'None';
        $gameState->outs_in_the_inning++;
    }

    // runner out at 2nd
    if(strpos($row['OUTCOME'], '2X2')) {
        $gameState->runneron2nd = 'None';
        $gameState->outs_in_the_inning++;
    }

    // runner out at Home
    if(strpos($row['OUTCOME'], '1XH')) {
        $gameState->runneron1st = 'None';
        $gameState->outs_in_the_inning++;
    }

    // runner out at 3rd
    if(strpos($row['OUTCOME'], '1X3')) {
        $gameState->runneron1st = 'None';
        $gameState->outs_in_the_inning++;
    }

    // runner out at 2nd
    if(strpos($row['OUTCOME'], '1X2')) {
        $gameState->runneron2nd = 'None';
        $gameState->outs_in_the_inning++;
    }

    // runner out at 1st
    if(strpos($row['OUTCOME'], '1X1')) {
        $gameState->runneron1st = 'None';
        $gameState->outs_in_the_inning++;
    }

    // runner on 3rd scores
    if(strpos($row['OUTCOME'], '3-H') !== false) {
        foreach($teamatbat as $vs) {
            if($vs->playerid === $gameState->runneron3rd) {
                $vs->batter_stat_r++;
                $gameState->runneron3rd = 'None';
            break;
            }
        }
    }

    // runner on 2nd to Home
    if(strpos($row['OUTCOME'],'2-H') !== false) {
        foreach($teamatbat as $vs) {
            if($vs->playerid === $gameState->runneron2nd) {
                $vs->batter_stat_r++;
                $gameState->runneron2nd = 'None';
            break;
            }
        }
    }

    // runner on 2nd to 3rd
    if(strpos($row['OUTCOME'],'2-3') !== false) {
        $gameState->runneron3rd = $gameState->runneron2nd;
        $gameState->runneron2nd = 'None';
    }

    // runner on 1st to Home
    if(strpos($row['OUTCOME'],'1-H') !== false) {
        foreach($teamatbat as $vs) {
            if($vs->playerid === $gameState->runneron1st) {
                $vs->batter_stat_r++;
                $gameState->runneron1st = 'None';
            break;
            }
        }
    }

    // runner on 1st to 3rd
    if(strpos($row['OUTCOME'],'1-3') !== false) {
        $gameState->runneron3rd = $gameState->runneron1st;
        $gameState->runneron1st = 'None';
    }

    // runner on 1st to 2nd
    if(strpos($row['OUTCOME'],'1-2') !== false) {
        $gameState->runneron2nd = $gameState->runneron1st;
        $gameState->runneron1st = 'None';
    }

    // batter to Home
    if(strpos($row['OUTCOME'],'B-H') !== false) {
        foreach($teamatbat as $vs) {
            if($vs->playerid === $row['PLAYERID']) {
                $vs->batter_stat_r++;
            break;
            }
        }
    }
    else if(strpos($row['OUTCOME'],'B-3') !== false) {
        $batterMoved = true;
        $gameState->runneron3rd = $row['PLAYERID'];
    }
    else if(strpos($row['OUTCOME'],'B-2') !== false) {
        $batterMoved = true;
        $gameState->runneron2nd = $row['PLAYERID'];
    }
    else if(strpos($row['OUTCOME'],'B-1') !== false) {
        $batterMoved = true;
        $gameState->runneron1st = $row['PLAYERID'];
    }

    if(!$batterMoved) {
        if($outcome === 'Walk' || $outcome === 'Single' || $outcome === 'HBP') {
            $gameState->runneron1st = $row['PLAYERID'];
        }
        else if($outcome === 'Double') {
            $gameState->runneron2nd = $row['PLAYERID'];
        }
        else if($outcome === 'Triple') {
            $gameState->runneron3rd = $row['PLAYERID'];
        }
        else if($outcome === 'Error') {
            $gameState->runneron1st = $row['PLAYERID'];
        }
    }

}

function getSingles($row, &$gameState, &$visBoxScoreStats, &$homeBoxScoreStats) {

    $teamatbat = $row['TEAMATBAT'] === '0' ? $visBoxScoreStats : $homeBoxScoreStats;

    // record a single and at-bat for batter
    foreach($teamatbat as $vs) {
        if($vs->playerid === $row['PLAYERID']) {
            $vs->batter_stat_1b++;
            $vs->batter_stat_ab++;
        }
    }

    if($gameState->runneron2nd !=='None' || $gameState->runneron3rd !=='None') {
        if($row['TEAMATBAT'] === '0') {
            $gameState->risp_vis_ab++;
            $gameState->risp_vis_h++;
        }
        else {
            $gameState->risp_home_ab++;
            $gameState->risp_home_h++;
        }
    }

    moveBaseRunners($row, 'Single', $gameState, $visBoxScoreStats, $homeBoxScoreStats);

    // check for RBI
    checkRBI($row, $gameState, $visBoxScoreStats, $homeBoxScoreStats);
}

function getDoubles($row, &$gameState, &$visBoxScoreStats, &$homeBoxScoreStats) {

    $teamatbat = $row['TEAMATBAT'] === '0' ? $visBoxScoreStats : $homeBoxScoreStats;

    // record a double and at-bat for batter
    foreach($teamatbat as $vs) {
        if($vs->playerid === $row['PLAYERID']) {
            $vs->batter_stat_2b++;
            $vs->batter_stat_ab++;
        }
    }

    if($gameState->runneron2nd !=='None' || $gameState->runneron3rd !=='None') {
        if($row['TEAMATBAT'] === '0') {
            $gameState->risp_vis_ab++;
            $gameState->risp_vis_h++;
        }
        else {
            $gameState->risp_home_ab++;
            $gameState->risp_home_h++;
        }
    }

    moveBaseRunners($row, 'Double', $gameState, $visBoxScoreStats, $homeBoxScoreStats);

    // check for RBI
    checkRBI($row, $gameState, $visBoxScoreStats, $homeBoxScoreStats);
}

function getTriples($row, &$gameState, &$visBoxScoreStats, &$homeBoxScoreStats) {

    $teamatbat = $row['TEAMATBAT'] === '0' ? $visBoxScoreStats : $homeBoxScoreStats;

    // record a triple and at-bat for batter
    foreach($teamatbat as $vs) {
        if($vs->playerid === $row['PLAYERID']) {
            $vs->batter_stat_3b++;
            $vs->batter_stat_ab++;
        }
    }

    if($gameState->runneron2nd !=='None' || $gameState->runneron3rd !=='None') {
        if($row['TEAMATBAT'] === '0') {
            $gameState->risp_vis_ab++;
            $gameState->risp_vis_h++;
        }
        else {
            $gameState->risp_home_ab++;
            $gameState->risp_home_h++;
        }
    }

    moveBaseRunners($row, 'Triple', $gameState, $visBoxScoreStats, $homeBoxScoreStats);

    // check for RBI
    checkRBI($row, $gameState, $visBoxScoreStats, $homeBoxScoreStats);
}

function getHomeruns($row, &$gameState, &$visBoxScoreStats, &$homeBoxScoreStats) {

    $teamatbat = $row['TEAMATBAT'] === '0' ? $visBoxScoreStats : $homeBoxScoreStats;

    // record a homerun and at-bat for batter
    foreach($teamatbat as $vs) {
        if($vs->playerid === $row['PLAYERID']) {
            $vs->batter_stat_hr++;
            $vs->batter_stat_r++;
            $vs->batter_stat_ab++;
            $vs->batter_stat_rbi++;
            if($gameState->outs_in_the_inning === 2) {
                $vs->batter_stat_rbi_2_out++;
            }
        }
    }

    if($gameState->runneron2nd !=='None' || $gameState->runneron3rd !=='None') {
        if($row['TEAMATBAT'] === '0') {
            $gameState->risp_vis_ab++;
            $gameState->risp_vis_h++;
        }
        else {
            $gameState->risp_home_ab++;
            $gameState->risp_home_h++;
        }
    }

    moveBaseRunners($row, 'Homerun', $gameState, $visBoxScoreStats, $homeBoxScoreStats);

    // check for RBI
    checkRBI($row, $gameState, $visBoxScoreStats, $homeBoxScoreStats);
}

function getWildPitch($row, &$gameState, &$visBoxScoreStats, &$homeBoxScoreStats, &$visPitcherBoxScoreStats, &$homePitcherBoxScoreStats) {

    $teamatbat = $row['TEAMATBAT'] === '0' ? $visPitcherBoxScoreStats : $homePitcherBoxScoreStats;

    // store stat for pitcher
    foreach($teamatbat as $vs) {
        if($vs->playerid === $row['PITCHERID']) {
            $vs->pitcher_stat_wp++;
        }
    }

    // check for runner movement
    moveBaseRunners($row, '', $gameState, $visBoxScoreStats, $homeBoxScoreStats);
}

function checkRBI($row, &$gameState, &$visBoxScoreStats, &$homeBoxScoreStats) {

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
            $vs->batter_stat_rbi += $rbiCount;

            if($gameState->outs_in_the_inning === 2) {
                $vs->batter_stat_rbi_2_out += $rbiCount;
            }
        break;
        }
    }
}

function showMessage($msg) {
    echo "<script type='text/javascript'>alert('$msg');</script>";
}

function getOut($row, &$gameState, &$visBoxScoreStats, &$homeBoxScoreStats) {

    $outs = 0;

    $total_outs_before = $gameState->outs_in_the_inning;

    $teamatbat = $row['TEAMATBAT'] === '0' ? $visBoxScoreStats : $homeBoxScoreStats;

    if(strpos($row['OUTCOME'], '(1)') !== false) {
        // runner on 1st is out
        $gameState->runneron1st = 'None';
        $outs++;
    }

    if(strpos($row['OUTCOME'], '(2)') !== false) {
        // runner on 2nd is out
        $gameState->runneron2nd = 'None';
        $outs++;
    }

    if(strpos($row['OUTCOME'], '(3)') !== false) {
        // runner on 3rd is out
        $gameState->runneron3rd = 'None';
        $outs++;
    }

    if(strpos($row['OUTCOME'], 'SF') !== false) {
        // no at-bat for sac fly
        // check for baserunner movement
        moveBaseRunners($row, '', $gameState, $visBoxScoreStats, $homeBoxScoreStats);

        foreach($teamatbat as $vs) {
            if($vs->playerid === $row['PLAYERID']) {
                $vs->batter_stat_sf++;
                $vs->batter_stat_rbi++;
                $outs++;
            break;
            }
        }
    }
    else if(strpos($row['OUTCOME'], 'SH') !== false) {
        // no at-bat for sac bunts
        // check for baserunner movement

        moveBaseRunners($row, '', $gameState, $visBoxScoreStats, $homeBoxScoreStats);

        foreach($teamatbat as $vs) {
            if($vs->playerid === $row['PLAYERID']) {
                $vs->batter_stat_sh++;
                $outs++;
            break;
            }
        }
    }
    else {
        // check for baserunner movement
        if($gameState->runneron2nd !=='None' || $gameState->runneron3rd !=='None') {
            if($row['TEAMATBAT'] === '0') {
                $gameState->risp_vis_ab++;
            }
            else {
                $gameState->risp_home_ab++;
            }
        }

        moveBaseRunners($row, '', $gameState, $visBoxScoreStats, $homeBoxScoreStats);

        foreach($teamatbat as $vs) {
            if($vs->playerid === $row['PLAYERID']) {
                $vs->batter_stat_ab++;
                $outs++;
            break;
            }
        }
    }

    checkRBI($row, $gameState, $visBoxScoreStats, $homeBoxScoreStats);

    $gameState->outs_in_the_inning += $outs;

    if(strpos($row['OUTCOME'], 'DP') !== false) {
        if($row['TEAMATBAT'] === '0') {
            $gameState->home_double_plays_turned++;
        }
        else {
            $gameState->vis_double_plays_turned++;
        }
    }

}

function getPositionByNumber($num) {
    $pos = ['', 'P', 'C', '1B', '2B', '3B', 'SS', 'LF', 'CF', 'RF'];
    
    return $pos[$num];
}

function getErrors($row, &$gameState, &$visBoxScoreStats, &$homeBoxScoreStats, &$visPitcherBoxScoreStats, &$homePitcherBoxScoreStats) {

    $player_error = '';
    $errorsMade = 0;

    if($row['OUTCOME'][0] === 'E') {
        // batter gets on base because of an error
        if($gameState->runneron2nd !=='None' || $gameState->runneron3rd !=='None') {
            if($row['TEAMATBAT'] === '0') {
                $gameState->risp_vis_ab++;
            }
            else {
                $gameState->risp_home_ab++;
            }
        }
    }

    $teamatbat = $row['TEAMATBAT'] === '0' ? $visBoxScoreStats : $homeBoxScoreStats;

    $pitcher_teamatbat = $row['TEAMATBAT'] === '0' ? $visPitcherBoxScoreStats : $homePitcherBoxScoreStats;

    // fielders other than the pitcher check
    for($i = 2; $i < 10; $i++) {

        $errorsMade = substr_count($row['OUTCOME'], 'E' . $i);

        if($errorsMade > 0) {
            // get position of fielder who made error
            $fielder_who_made_error = 'pos_' . getPositionByNumber($i);
            // assign error to that player
            $fieldingteam = $row['TEAMATBAT'] === '0' ? $homeBoxScoreStats : $visBoxScoreStats;

            foreach($fieldingteam as $vs) {

                if($vs->playerid === $gameState->visDefense->$fielder_who_made_error) {
                    $vs->fielder_stat_error += $errorsMade;

                    if($row['TEAMATBAT'] === '0') {
                        $gameState->homeErrors += $errorsMade;
                    }
                    else {
                        $gameState->visErrors += $errorsMade;
                    }
                break;
                }
            }
        }
    }

    // check pitchers
    $errorsMade = substr_count($row['OUTCOME'], 'E1');

    if($errorsMade > 0) {
        foreach($pitcher_teamatbat as $vs) {
            if($vs->playerid === $row['pitcherid']) {
                $vs->fielder_stat_error += $errorsMade;

                if($row['TEAMATBAT'] === '0') {
                    $gameState->homeErrors += $errorsMade;
                }
                else {
                    $gameState->visErrors += $errorsMade;
                }
            }
        }
    }

    // Batter to 1st unless it states otherwise
    moveBaseRunners($row, 'Error', $gameState, $visBoxScoreStats, $homeBoxScoreStats);

}

function checkEndOfInning($row, &$gameState) {

    $count = 0;

    if($row['TEAMATBAT'] != $gameState->teamatbat && $row['CATEGORY'] !== 'com' && $row['CATEGORY'] !== 'sub') {
        // assign runners LOB to correct team

        if($gameState->teamatbat === 0) {
            if($gameState->runneron1st !== 'None') {
                $count++;
            }

            if($gameState->runneron2nd !== 'None') {
                $count++;
            }

            if($gameState->runneron3rd !== 'None') {
                $count++;
            }

            $gameState->visTeamLOB += $count;
        }
        else {

        }

        $gameState->clearRunners();
        $gameState->teamatbat ^= 1;
        $gameState->outs_in_the_inning = 0;
    }
}

function checkForSub($row, &$gameState, &$visBoxScoreStats, &$homeBoxScoreStats) {

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

            // replace player in current lineup
            // foreach($lineup as $l) {
            //     if($l->playerid === $subBatter->playerid) {
            //         $l->playerid = $subBatter->playerid;
            //         $l->playerpos = $subBatter->playerpos;
            //     break;
            //     }
            // }

            if($row['PLAYERID'] === '0') {
                //insert new player into box score

                array_splice($visBoxScoreStats, $row['COUNT'] + $gameState->visSubCount, 0, [$subBatter]);

                $gameState->visSubCount++;
            }
            else {
                //insert new player into box score
                array_splice($homeBoxScoreStats, $row['COUNT'] + $gameState->homeSubCount, 0, [$subBatter]);

                $gameState->homeSubCount++;
            }

        }
    }
}

function getPosition($num) {
    $pos = ['P', 'C', '1B', '2B', '3B', 'SS', 'LF', 'CF', 'RF', 'DH'];

    return $pos[$num-1];
}

?>