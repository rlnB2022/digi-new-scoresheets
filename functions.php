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
            
            array_push($gameState->visLineup, $batterS->playerid);
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
    public $batter_stat_gitp = 0;
    public $batter_stat_sb = 0;
    public $batter_stat_cs = 0;
    public $batter_stat_hbp = 0;
    public $batter_stat_sh = 0;
    public $batter_stat_sf = 0;
    public $fielder_stat_error = 0;
    public $pitcher_stat_wp = 0;
    public $pitcher_stat_pb = 0;
    public $pitcher_stat_bk = 0;
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
    public $pitcher_stat_pickoff = 0;
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

function isRISP($gameState) {
    // runners in scoring position (RISP)
    if($gameState->runneron2nd !=='None' || $gameState->runneron3rd !=='None') {
        return true;
    }

    return false;
}

function addRISP_AB($atbat, &$gameState) {
    if($atbat === '0') {
        $gameState->risp_vis_ab++;
    }
    else {
        $gameState->risp_home_ab++;
    }
}

function addRISP_H($atbat, &$gameState) {
    if($atbat === '0') {
        $gameState->risp_vis_h++;
    }
    else {
        $gameState->risp_home_h++;
    }
}

function getStrikeouts($row, &$gameState, &$visBoxScoreStats, &$homeBoxScoreStats) {

    // record the strikeout
    $teamatbat = $row['TEAMATBAT'] === '0' ? $visBoxScoreStats : $homeBoxScoreStats;

    foreach($teamatbat as $vs) {
        if($vs->playerid === $row['PLAYERID']) {
            $vs->batter_stat_k++;
            $vs->batter_stat_ab++;
        break;
        }
    }

    $gameState->outs_this_row++;
    $gameState->outs_in_the_inning++;

    // add at-bat if RISP
    if(isRISP($gameState)) {
        addRISP_AB($row['TEAMATBAT'], $gameState);
    }

    // if batter doesn't advance to any base because of a bad throw, passed ball or wild pitch, then record the out
    // if(strpos($row['OUTCOME'], 'B-1') === false && strpos($row['OUTCOME'], 'B-2') === false && strpos($row['OUTCOME'], 'B-3') === false && strpos($row['OUTCOME'], 'B-H') === false) {
    //     $gameState->outs_in_the_inning++;
    // }

}

function getWalks($row, &$gameState, &$visBoxScoreStats, &$homeBoxScoreStats) {

    $teamatbat = $row['TEAMATBAT'] === '0' ? $visBoxScoreStats : $homeBoxScoreStats;

    // assign a bb to batter stats
    foreach($teamatbat as $vs) {
        if($vs->playerid === $row['PLAYERID']) {

            $vs->batter_stat_bb++;

            // assign ibb to batter stats
            if(strpos($row['OUTCOME'], 'IW') !== false) {
                $vs->batter_stat_ibb++;
            }

        break;
        }
    }
}

function getHitByPitch($row, &$visBoxScoreStats, &$homeBoxScoreStats) {

    $teamatbat = $row['TEAMATBAT'] === '0' ? $visBoxScoreStats : $homeBoxScoreStats;

    // assign a hbp to batter stats and move batter to 1st
    foreach($teamatbat as $vs) {
        if($vs->playerid === $row['PLAYERID']) {
            $vs->batter_stat_hbp++;
        break;
        }
    }

}

function getStolenBase($row, &$gameState, &$visBoxScoreStats, &$homeBoxScoreStats) {

    $total_bases_stolen_this_row = 0;

    $teamatbat = $row['TEAMATBAT'] === '0' ? $visBoxScoreStats : $homeBoxScoreStats;

    // assign a sb to batter stats and move batter home
    if(strpos($row['OUTCOME'], 'SBH') !== false) {
        foreach($teamatbat as $vs) {
            if($vs->playerid === $gameState->runneron3rd) {
                $vs->batter_stat_sb++;
                $vs->batter_stat_r++;
                $gameState->runneron3rd = 'None';
                $total_bases_stolen_this_row++;
            break;
            }
        }
    }

    // assign a sb to batter stats and move batter 3rd
    if(strpos($row['OUTCOME'], 'SB3') !== false) {
        foreach($teamatbat as $vs) {
            if($vs->playerid === $gameState->runneron2nd) {
                $vs->batter_stat_sb++;
                $total_bases_stolen_this_row++;

                if(strpos($row['OUTCOME'], '2-') === false) {
                    // move runner to next base
                    $gameState->runneron3rd = $gameState->runneron2nd;
                    $gameState->runneron2nd = 'None';
                }

            break;
            }
        }
    }

    // assign a sb to batter stats and move batter to 2nd
    if(strpos($row['OUTCOME'], 'SB2') !== false) {
        foreach($teamatbat as $vs) {
            if($vs->playerid === $gameState->runneron1st) {
                $vs->batter_stat_sb++;
                $total_bases_stolen_this_row++;
                
                if(strpos($row['OUTCOME'], '1-') === false) {
                    // move runner to next base
                    $gameState->runneron2nd = $gameState->runneron1st;
                    $gameState->runneron1st = 'None';
                }

            break;
            }
        }
    }

    if($row['TEAMATBAT'] === '0') {
        $gameState->visStolenBases += $total_bases_stolen_this_row++;
    }
    else {
        $gameState->homeStolenBases += $total_bases_stolen_this_row++;
    }

}

function setDoublePlay($atbat, &$gameState) {
    if($atbat === '0') {
        $gameState->vis_double_plays_turned++;
    }
    else {
        $gameState->home_double_plays_turned++;
    }
}

function getPickoff($row, &$gameState, &$visPitcherBoxScoreStats, &$homePitcherBoxScoreStats) {

    $teamatbat = $row['TEAMATBAT'] === '0' ? $homePitcherBoxScoreStats : $visPitcherBoxScoreStats;

    // remove appropriate base runner
    if(strpos($row['OUTCOME'], 'PO1') !== false) {
        $gameState->runneron1st = 'None';
    }
    else if(strpos($row['OUTCOME'], 'PO2') !== false) {
        $gameState->runneron2nd = 'None';
    }
    else if(strpos($row['OUTCOME'], 'PO3') !== false) {
        $gameState->runneron3rd = 'None';
    }

    // record an out
    $gameState->outs_in_the_inning++;
    $gameState->outs_this_row++;

    // record pickoff for pitcher
    foreach($teamatbat as $vs) {
        if($vs->playerid === $row['PITCHERID']) {

            $vs->pitcher_stat_pickoff++;
            
            if($row['TEAMATBAT'] === '0') {
                $gameState->homeTeamPickoffs++;
            }
            else {
                $gameState->visTeamPickoffs++;
            }
        break;
        }
    }

}

function getCaughtStealing($row, &$gameState, &$visBoxScoreStats, &$homeBoxScoreStats) {

    $total_bases_caught_stealing_this_row = 0;

    $teamatbat = $row['TEAMATBAT'] === '0' ? $visBoxScoreStats : $homeBoxScoreStats;

    // assign a sb to batter stats
    if(strpos($row['OUTCOME'], 'CSH') !== false) {

        foreach($teamatbat as $vs) {
            if($vs->playerid === $gameState->runneron3rd) {
                $vs->batter_stat_cs++;
                $gameState->runneron3rd = 'None';
                $total_bases_caught_stealing_this_row++;
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
                $total_bases_caught_stealing_this_row++;
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
                $total_bases_caught_stealing_this_row++;
            break;
            }
        }

    }

    $gameState->outs_in_the_inning += $total_bases_caught_stealing_this_row;
    $gameState->outs_this_row += $total_bases_caught_stealing_this_row;

    if($row['TEAMATBAT'] === '0') {
        $gameState->visCaughtStealing += $total_bases_caught_stealing_this_row++;
    }
    else {
        $gameState->homeCaughtStealing += $total_bases_caught_stealing_this_row++;
    }

}

function moveBaseRunners($row, &$gameState, &$visBoxScoreStats, &$homeBoxScoreStats) {

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
    if(strpos($row['OUTCOME'], '2-H') !== false) {
        foreach($teamatbat as $vs) {
            if($vs->playerid === $gameState->runneron2nd) {
                $vs->batter_stat_r++;
                $gameState->runneron2nd = 'None';
            break;
            }
        }
    }

    // runner on 2nd to 3rd
    if(strpos($row['OUTCOME'], '2-3') !== false) {
        $gameState->runneron3rd = $gameState->runneron2nd;
        $gameState->runneron2nd = 'None';
    }

    // runner on 1st to Home
    if(strpos($row['OUTCOME'], '1-H') !== false) {
        foreach($teamatbat as $vs) {
            if($vs->playerid === $gameState->runneron1st) {
                $vs->batter_stat_r++;
                $gameState->runneron1st = 'None';
            break;
            }
        }
    }

    // runner on 1st to 3rd
    if(strpos($row['OUTCOME'], '1-3') !== false) {
        $gameState->runneron3rd = $gameState->runneron1st;
        $gameState->runneron1st = 'None';
    }

    // runner on 1st to 2nd
    if(strpos($row['OUTCOME'], '1-2') !== false) {
        $gameState->runneron2nd = $gameState->runneron1st;
        $gameState->runneron1st = 'None';
    }

}

function getSingles($row, &$gameState, &$visBoxScoreStats, &$homeBoxScoreStats) {

    $error_on_single = false;

    $teamatbat = $row['TEAMATBAT'] === '0' ? $visBoxScoreStats : $homeBoxScoreStats;

    // record a single and at-bat for batter
    foreach($teamatbat as $vs) {
        if($vs->playerid === $row['PLAYERID']) {
            $vs->batter_stat_1b++;
            $vs->batter_stat_ab++;
        }
    }

    // add at-bat and hit if RISP
    if(isRISP($gameState)) {
        addRISP_AB($row['TEAMATBAT'], $gameState);
        addRISP_H($row['TEAMATBAT'], $gameState);
    }

    // check errors
    if(strpos($row['OUTCOME'], 'E') !== false) {
        getErrors($row, $gameState, $visBoxScoreStats, $homeBoxScoreStats, $visPitcherBoxScoreStats, $homePitcherBoxScoreStats);
        $error_on_single = true;
    }

    moveBaseRunners($row, $gameState, $visBoxScoreStats, $homeBoxScoreStats);

    // if runner gets thrown out at 1st after everything is resolved
    if(strpos($row['OUTCOME'], 'BX1') !== false) {
        // if error is found after BX1, then he's safe at 1st
        if($error_on_single) {
            $error_loc = strpos($row['OUTCOME', 'E']);
            $bx_loc = strpos($row['OUTCOME', 'BX']);

            if($error_loc > $bx_loc) {
                // runner is safe at 1st
                $gameState->runneron1st = $row['PLAYERID'];
            }
        }
        else {
            $gameState->outs_in_the_inning++;
        }
    }
    else if(strpos($row['OUTCOME'], 'BX2') !== false) {
        // if error is found after BX2, then he's safe at 1st
        if($error_on_single) {
            $error_loc = strpos($row['OUTCOME', 'E']);
            $bx_loc = strpos($row['OUTCOME', 'BX']);

            if($error_loc > $bx_loc) {
                // runner is safe at 2nd
                $gameState->runneron2nd = $row['PLAYERID'];
            }
        }
        else {
            $gameState->outs_in_the_inning++;
        }
    }
    else if(strpos($row['OUTCOME'], 'BX3') !== false) {
        // if error is found after BX3 then he's safe at 1st
        if($error_on_single) {
            $error_loc = strpos($row['OUTCOME', 'E']);
            $bx_loc = strpos($row['OUTCOME', 'BX']);

            if($error_loc > $bx_loc) {
                // runner is safe at 3rd
                $gameState->runneron3rd = $row['PLAYERID'];
            }
        }
        else {
            $gameState->outs_in_the_inning++;
        }
    }

}

function getDoubles($row, &$gameState, &$visBoxScoreStats, &$homeBoxScoreStats) {

    $error_on_double = false;

    $teamatbat = $row['TEAMATBAT'] === '0' ? $visBoxScoreStats : $homeBoxScoreStats;

    // record a single and at-bat for batter
    foreach($teamatbat as $vs) {
        if($vs->playerid === $row['PLAYERID']) {
            $vs->batter_stat_2b++;
            $vs->batter_stat_ab++;
        }
    }

    // add at-bat and hit if RISP
    if(isRISP($gameState)) {
        addRISP_AB($row['TEAMATBAT'], $gameState);
        addRISP_H($row['TEAMATBAT'], $gameState);
    }

    // check errors
    if(strpos($row['OUTCOME'], 'E') !== false) {
        getErrors($row, $gameState, $visBoxScoreStats, $homeBoxScoreStats, $visPitcherBoxScoreStats, $homePitcherBoxScoreStats);
        $error_on_double = true;
    }

    moveBaseRunners($row, $gameState, $visBoxScoreStats, $homeBoxScoreStats);

    if(strpos($row['OUTCOME'], 'BX2') !== false) {
        // if error is found after BX2, then he's safe at 1st
        if($error_on_double) {
            $error_loc = strpos($row['OUTCOME', 'E']);
            $bx_loc = strpos($row['OUTCOME', 'BX']);

            if($error_loc > $bx_loc) {
                // runner is safe at 2nd
                $gameState->runneron2nd = $row['PLAYERID'];
            }
        }
        else {
            $gameState->outs_in_the_inning++;
        }
    }
    else if(strpos($row['OUTCOME'], 'BX3') !== false) {
        // if error is found after BX3 then he's safe at 1st
        if($error_on_double) {
            $error_loc = strpos($row['OUTCOME', 'E']);
            $bx_loc = strpos($row['OUTCOME', 'BX']);

            if($error_loc > $bx_loc) {
                // runner is safe at 3rd
                $gameState->runneron3rd = $row['PLAYERID'];
            }
        }
        else {
            $gameState->outs_in_the_inning++;
        }
    }

}

function getTriples($row, &$gameState, &$visBoxScoreStats, &$homeBoxScoreStats) {

    $teamatbat = $row['TEAMATBAT'] === '0' ? $visBoxScoreStats : $homeBoxScoreStats;

    // record a single and at-bat for batter
    foreach($teamatbat as $vs) {
        if($vs->playerid === $row['PLAYERID']) {
            $vs->batter_stat_3b++;
            $vs->batter_stat_ab++;
        }
    }

    // add at-bat and hit if RISP
    if(isRISP($gameState)) {
        addRISP_AB($row['TEAMATBAT'], $gameState);
        addRISP_H($row['TEAMATBAT'], $gameState);
    }
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

    // add at-bat and hit if RISP
    if(isRISP($gameState)) {
        addRISP_AB($row['TEAMATBAT'], $gameState);
        addRISP_H($row['TEAMATBAT'], $gameState);
    }

}

function getWildPitch($row, &$gameState, &$visPitcherBoxScoreStats, &$homePitcherBoxScoreStats) {

    $teamatbat = $row['TEAMATBAT'] === '0' ? $visPitcherBoxScoreStats : $homePitcherBoxScoreStats;

    // store stat for pitcher
    foreach($teamatbat as $vs) {
        if($vs->playerid === $row['PITCHERID']) {
            $vs->pitcher_stat_wp++;
        break;
        }
    }

}

function getPassedBall($row, &$gameState, &$visPitcherBoxScoreStats, &$homePitcherBoxScoreStats) {

    $teamatbat = $row['TEAMATBAT'] === '0' ? $visPitcherBoxScoreStats : $homePitcherBoxScoreStats;

    // store stat for pitcher
    foreach($teamatbat as $vs) {
        if($vs->playerid === $row['PITCHERID']) {
            $vs->pitcher_stat_pb++;
        break;
        }
    }

}

function getBalk($row, &$gameState, &$visPitcherBoxScoreStats, &$homePitcherBoxScoreStats) {

    $teamatbat = $row['TEAMATBAT'] === '0' ? $visPitcherBoxScoreStats : $homePitcherBoxScoreStats;

    // store stat for pitcher
    foreach($teamatbat as $vs) {
        if($vs->playerid === $row['PITCHERID']) {
            $vs->pitcher_stat_bk++;
        }
    }

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
    $rbiCount -= substr_count($row['OUTCOME'], 'NR');

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
        moveBaseRunners($row, $gameState, $visBoxScoreStats, $homeBoxScoreStats);

        foreach($teamatbat as $vs) {
            if($vs->playerid === $row['PLAYERID']) {
                $vs->batter_stat_sf++;
                $outs++;
            break;
            }
        }
    }
    else if(strpos($row['OUTCOME'], 'SH') !== false) {
        // no at-bat for sac bunts
        // check for baserunner movement

        moveBaseRunners($row, $gameState, $visBoxScoreStats, $homeBoxScoreStats);

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

        moveBaseRunners($row, $gameState, $visBoxScoreStats, $homeBoxScoreStats);

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

}

function checkEndOfInning($row, &$gameState) {

    $count = 0;

    $teamatbat = $row['TEAMATBAT'] === '0' ? $visBoxScoreStats : $homeBoxScoreStats;

    if($row['TEAMATBAT'] != $gameState->teamatbat) {

        // assign runners LOB to correct team

        if($gameState->runneron1st !== 'None') {
            $count++;
        }

        if($gameState->runneron2nd !== 'None') {
            $count++;
        }

        if($gameState->runneron3rd !== 'None') {
            $count++;
        }

        if($row['TEAMATBAT'] === '1') {
            // showMessage('INNING: ' . $row['INNING'] . ', LOB: ' . $count);
        }

        // assign lob to player stats
        foreach($teamatbat as $vs) {
            if($vs->playerid === $row['PLAYERID']) {
                $vs->batter_stat_lob += $count;
            }
        }

        // assign team lob
        if($row['TEAMATBAT'] === '0') {
            $gameState->homeTeamLOB += $count;
        }
        else {
            $gameState->visTeamLOB += $count;
        }

        // prepare for new inning
        $gameState->clearRunners();
        $gameState->teamatbat ^= 1;
        $gameState->outs_in_the_inning = 0;

    }
    else {

        // assign lob to player stats
        foreach($teamatbat as $vs) {
            if($vs->playerid === $row['PLAYERID']) {
                $vs->batter_stat_lob += $count;
            }
        }
    }

}

function getGDP($row, &$gameState, &$visBoxScoreStats, &$homeBoxScoreStats) {

    $teamatbat = $row['TEAMATBAT'] === '0' ? $visBoxScoreStats : $homeBoxScoreStats;
    $battersafe = true;
    $current_outs = 0;

    if(strpos($row['OUTCOME'], '(B)') !== false) {
        $battersafe = false;
        $current_outs++;
    }

    if(strpos($row['OUTCOME'], '(1)') !== false) {
        $gameState->runneron1st = 'None';
        $current_outs++;
    }

    if(strpos($row['OUTCOME'], '(2)') !== false) {
        $gameState->runneron2nd = 'None';
        $current_outs++;
    }

    if(strpos($row['OUTCOME'], '(3)') !== false) {
        $gameState->runneron3rd = 'None';
        $current_outs++;
    }

    if($current_outs < 2) {
        $battersafe = false;
        $current_outs++;
    }

    moveBaseRunners($row, $gameState, $visBoxScoreStats, $homeBoxScoreStats);

    foreach($teamatbat as $vs) {
        if($vs->playerid === $row['PLAYERID']) {
            $vs->batter_stat_gidp++;

            if($row['TEAMATBAT'] === '0') {
                $gameState->visTeamGDP++;
            }
            else {
                $gameState->homeTeamGDP++;
            }
        }
    }
}

function getGTP($row, &$gameState, &$visBoxScoreStats, &$homeBoxScoreStats) {

    $teamatbat = $row['TEAMATBAT'] === '0' ? $visBoxScoreStats : $homeBoxScoreStats;
    $battersafe = true;
    $current_outs = 0;

    if(strpos($row['OUTCOME'], '(B)') !== false) {
        $battersafe = false;
        $current_outs++;
    }

    if(strpos($row['OUTCOME'], '(1)') !== false) {
        $gameState->runneron1st = 'None';
        $current_outs++;
    }

    if(strpos($row['OUTCOME'], '(2)') !== false) {
        $gameState->runneron2nd = 'None';
        $current_outs++;
    }

    if(strpos($row['OUTCOME'], '(3)') !== false) {
        $gameState->runneron3rd = 'None';
        $current_outs++;
    }

    if($current_outs < 3) {
        $battersafe = false;
        $current_outs++;
    }

    moveBaseRunners($row, $gameState, $visBoxScoreStats, $homeBoxScoreStats);

    foreach($teamatbat as $vs) {
        if($vs->playerid === $row['PLAYERID']) {
            $vs->batter_stat_gitp++;

            if($row['TEAMATBAT'] === '0') {
                $gameState->visTeamGTP++;
            }
            else {
                $gameState->homeTeamGTP++;
            }
        }
    }
}

function getFO($row, &$gameState, &$visBoxScoreStats, &$homeBoxScoreStats) {

    $batterSafeOn1st = false;
    $batterSafeOn2nd = false;
    $batterSafeOn3rd = false;

    if(strpos($row['OUTCOME'], '(1)') !== false) {
        $gameState->runneron1st = 'None';
        $gameState->outs_in_the_inning++;
        $gameState->outs_this_row++;
    }
    else if(strpos($row['OUTCOME'], '(2)') !== false) {
        $gameState->runneron2nd = 'None';
        $gameState->outs_in_the_inning++;
        $gameState->outs_this_row++;
    }
    else if(strpos($row['OUTCOME'], '(3)') !== false) {
        $gameState->runneron3rd = 'None';
        $gameState->outs_in_the_inning++;
        $gameState->outs_this_row++;
    }

    if($row['OUTCOME'][0] === 'E' ) {
        // runner goes to 1st on error
        $batterSafeOn1st = true;
        getErrors($row, $gameState, $visBoxScoreStats, $homeBoxScoreStats, $visPitcherBoxScoreStats, $homePitcherBoxScoreStats);
    }
    else if(strpos($row['OUTCOME'], 'BX1') !== false) {

        if(strpos($row['OUTCOME'], 'NDP') !== false) {
            $gameState->outs_in_the_inning++;
            $gameState->outs_this_row++;
        }
        else {
            $batterSafeOn1st = true;

            if(strpos($row['OUTCOME'], 'E') !== false) {
                getErrors($row, $gameState, $visBoxScoreStats, $homeBoxScoreStats, $visPitcherBoxScoreStats, $homePitcherBoxScoreStats);
            }
        }
    }
    else if(strpos($row['OUTCOME'], 'BX2') !== false) {
        if(strpos($row['OUTCOME'], 'NDP') !== false) {
            $gameState->outs_in_the_inning++;
            $gameState->outs_this_row++;
        }
        else {
            $batterSafeOn2nd = true;

            if(strpos($row['OUTCOME'], 'E') !== false) {
                getErrors($row, $gameState, $visBoxScoreStats, $homeBoxScoreStats, $visPitcherBoxScoreStats, $homePitcherBoxScoreStats);
            }
        }
    }
    else if(strpos($row['OUTCOME'], 'BX3') !== false) {
        if(strpos($row['OUTCOME'], 'NDP') !== false) {
            $gameState->outs_in_the_inning++;
            $gameState->outs_this_row++;
        }
        else {
            $batterSafeOn3rd = true;

            if(strpos($row['OUTCOME'], 'E') !== false) {
                getErrors($row, $gameState, $visBoxScoreStats, $homeBoxScoreStats, $visPitcherBoxScoreStats, $homePitcherBoxScoreStats);
            }
        }
    }
    else {
        if($gameState->outs_in_the_inning < 3) {

            if(strpos($row['OUTCOME'], 'B-2') !== false) {
                $batterSafeOn2nd = true;
            }
            else if(strpos($row['OUTCOME'], 'B-3') !== false) {
                $batterSafeAt3rd = true;
            }
            else {
                $batterSafeOn1st = true;
            }

        }
    }

    moveBaseRunners($row, $gameState, $visBoxScoreStats, $homeBoxScoreStats);

    if($batterSafeOn1st) {
        $gameState->runneron1st = $row['PLAYERID'];
    }
    else if($batterSafeOn1st) {
        $gameState->runneron2nd = $row['PLAYERID'];
    }
    else if($batterSafeOn3rd) {
        $gameState->runneron3rd = $row['PLAYERID'];
    }

    // record at-bat
    foreach($teamatbat as $vs) {
        if($vs->playerid === $row['PLAYERID']) {
            $gameState->batter_stat_ab++;
        }
    }

    if(isRISP($gameState)) {
        addRISP_AB($row['TEAMATBAT'], $gameState);
    }

    // check for DP
    if($gameState->outs_this_row === 2) {
        setDoublePlay($row['TEAMATBAT'], $gameState);
    }

}

function checkForSub($row, &$gameState, &$visBoxScoreStats, &$homeBoxScoreStats) {

    $subBatter = new playerStat();

    if($row['CATEGORY'] === 'sub') {

        // determine whether a new pitcher or batter is being substituted

        if($row['PITCHES'] !== '1') {
            // not a pitcher

            $teamatbat = $row['PLAYERID'] === '0' ? $visBoxScoreStats : $homeBoxScoreStats;

            $foundInLineup = false;

            for($i = 0; $i < 9; $i++) {
                if($gameState->visLineup[$i] === $row['INNING']) {
                    $foundInLineup = true;
                break;
                }
            }

            if(!$foundInLineup) {
                // assign playerid to new batter stats
                $subBatter->playerid = $row['INNING'];
                $subBatter->playerpos = getPosition($row['PITCHES']);
                $subBatter->status = 'sub';

                // place new sub in lineup
                if($row['PLAYERID'] === '0') {
                    // player being subbed out
                    $temp_playerid = $gameState->visLineup[$row['COUNT']-1];

                    $vCount = 0;

                    foreach($visBoxScoreStats as $vs) {
                        if($vs->playerid === $temp_playerid) {
                            array_splice($visBoxScoreStats, $vCount + 1, 0, [$subBatter]);
                            $gameState->visLineup[$row['COUNT']-1] = $temp_playerid;
                        break;
                        }
                        $vCount++;
                    }

                    $gameState->visLineup[$row['COUNT']-1] = $subBatter->playerid;
                    $gameState->visDefense->$temp_pos = $subBatter->playerid;
                }
                else {
                    // player being subbed out
                    $temp_playerid = $gameState->homeLineup[$row['COUNT']-1];

                    $hCount = 0;

                    foreach($homeBoxScoreStats as $vs) {
                        if($vs->playerid === $temp_playerid) {
                            array_splice($homeBoxScoreStats, $hCount + 1, 0, [$subBatter]);
                            $gameState->homeLineup[$row['COUNT']-1] = $temp_playerid;
                        break;
                        }
                        $hCount++;
                    }

                    $gameState->homeLineup[$row['COUNT']-1] = $subBatter->playerid;
                    $gameState->homeDefense->$temp_pos = $subBatter->playerid;                }
            }
            else {
                // switch position
                $temp_pos = 'pos_' . getPosition($row['PITCHES']);

                // assign playerid to new position
                $gameState->visDefense->$temp_pos = $row['INNING'];

                foreach($teamatbat as $ta) {
                    if($ta->playerid === $row['INNING']) {
                        $ta->playerpos .= '-' . getPosition($row['PITCHES']);
                    break;
                    }
                }
            }
        }
    }
}

function getPosition($num) {
    $pos = ['P', 'C', '1B', '2B', '3B', 'SS', 'LF', 'CF', 'RF', 'DH'];

    return $pos[$num-1];
}

?>