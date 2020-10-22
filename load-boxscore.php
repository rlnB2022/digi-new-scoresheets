<?php
    include "configtest.php";
    include "functions.php";

    $gameid = $_POST['gameid'];

    $visBoxScoreStats = [];
    $visPitcherBoxScoreStats = [];
    $homeBoxScoreStats = [];
    $homePitcherBoxScoreStats =[];

    $hometeam = substr($gameid,0,3);
    $gamedate = substr($gameid,3,8);
    $gamenum = substr($gameid,11,1);
    $year = substr($gamedate,0,4);
    $month = getMonth(substr($gamedate,4,2));
    $day = substr($gamedate,6,2);

    class TotalBases {
        public $playerid;
        public $bases;
    }

    class TotalRBI {
        public $playerid;
        public $rbi;
    }

    class TotalPickedOff {
        public $playerid;
        public $num;
    }

    class Defense {
        public $pos_P = '';
        public $pos_C = '';
        public $pos_1B = '';
        public $pos_2B = '';
        public $pos_3B = '';
        public $pos_SS = '';
        public $pos_LF = '';
        public $pos_CF = '';
        public $pos_RF = '';
        public $pos_DH = '';
    }

    class GameStatus {
        public $outs = 0;
        public $outs_in_the_inning = 0;
        public $teamatbat = 0;

        public $outs_this_row = 0;

        public $visTeamLOB = 0;
        public $homeTeamLOB = 0;

        public $visTeamGDP = 0;
        public $homeTeamGDP = 0;

        public $visTeamGTP = 0;
        public $homeTeamGTP = 0;

        public $visTeamPickoffs = 0;
        public $homeTeamPickoffs = 0;

        public $risp_vis_ab = 0;
        public $risp_vis_h = 0;
        public $risp_home_ab = 0;
        public $risp_home_h = 0;

        public $vis_errors = 0;
        public $home_errors = 0;

        public $visStolenBases = 0;
        public $homeStolenBases = 0;

        public $visCaughtStealing = 0;
        public $homeCaughtStealing = 0;

        public $visPassedBalls = 0;
        public $homePassedBalls = 0;

        public $visOutfieldAssists = 0;
        public $homeOutfieldAssists = 0;

        public $visDefense;
        public $homeDefense;

        public $visLineup = [];
        public $homeLineup = [];

        public $vis_double_plays_turned = 0;
        public $home_double_plays_turned = 0;

        public $vis_triple_plays_turned = 0;
        public $home_triple_plays_turned = 0;

        public $runneron1st = 'None';
        public $runneron2nd = 'None';
        public $runneron3rd = 'None';

        public function clearRunners() {
            $this->runneron1st = 'None';
            $this->runneron2nd = 'None';
            $this->runneron3rd = 'None';
        }
    }
    // class Pitches {
    //     public $count0_0_ball;
    //     public $count0_0_called_strike;
    //     public $count0_0_foul;
    //     public $count0_0_ball_in_play;
    //     public $count0_0_swinging_strike;
    //     public $count1_0_ball;
    //     public $count1_0_called_strike;
    //     public $count1_0_foul;
    //     public $count1_0_ball_in_play;
    //     public $count1_0_swinging_strike;
    //     public $count2_0_ball;
    //     public $count2_0_called_strike;
    //     public $count2_0_foul;
    //     public $count2_0_ball_in_play;
    //     public $count2_0_swinging_strike;
    //     public $count3_0_ball;
    //     public $count3_0_called_strike;
    //     public $count3_0_foul;
    //     public $count3_0_ball_in_play;
    //     public $count3_0_swinging_strike;
    //     public $count0_1_ball;
    //     public $count0_1_called_strike;
    //     public $count0_1_foul;
    //     public $count0_1_ball_in_play;
    //     public $count0_1_swinging_strike;
    //     public $count1_1_ball;
    //     public $count1_1_called_strike;
    //     public $count1_1_foul;
    //     public $count1_1_ball_in_play;
    //     public $count1_1_swinging_strike;
    //     public $count2_1_ball;
    //     public $count2_1_called_strike;
    //     public $count2_1_foul;
    //     public $count2_1_ball_in_play;
    //     public $count2_1_swinging_strike;
    //     public $count3_1_ball;
    //     public $count3_1_called_strike;
    //     public $count3_1_foul;
    //     public $count3_1_ball_in_play;
    //     public $count3_1_swinging_strike;
    //     public $count0_2_ball;
    //     public $count0_2_called_strike;
    //     public $count0_2_foul;
    //     public $count0_2_ball_in_play;
    //     public $count0_2_swinging_strike;
    //     public $count1_2_ball;
    //     public $count1_2_called_strike;
    //     public $count1_2_foul;
    //     public $count1_2_ball_in_play;
    //     public $count1_2_swinging_strike;
    //     public $count2_2_ball;
    //     public $count2_2_called_strike;
    //     public $count2_2_foul;
    //     public $count2_2_ball_in_play;
    //     public $count2_2_swinging_strike;
    //     public $count3_2_ball;
    //     public $count3_2_called_strike;
    //     public $count3_2_foul;
    //     public $count3_2_ball_in_play;
    //     public $count3_2_swinging_strike;
    // }

    // $pitches = new Pitches();

    $gameState = new GameStatus();
    $gameState->visDefense = new Defense();
    $gameState->homeDefense = new Defense();

    getLineScores($link, $gamedate, $hometeam, $gamenum, $month, $day, $year);

?>

<?php
    getLineup($link, $gameid, $gameState, $visBoxScoreStats, 'vis');
    getLineup($link, $gameid, $gameState, $homeBoxScoreStats, 'home');
    getStartingPitchers($link, $gamedate, $hometeam, $gamenum, $visPitcherBoxScoreStats, $homePitcherBoxScoreStats);
?>

<div class="boxscore-stats">
    <?php
        $sql = "SELECT * FROM EVENTS WHERE GAMEID = '" . $gameid . "'";

        $result = mysqli_query($link, $sql);

        $resultCheck = mysqli_num_rows($result);

        $batterData = '';
        $runnerData;

        $numRunnersOnBase = 0;

        if($resultCheck > 0) {

            while ($row = mysqli_fetch_assoc($result)) { 

                // clear baserunners if new team batting and assign team LOB
                if($row['CATEGORY'] === 'play') {

                    $numRunnersOnBase = getNumRunnersOnBase($gameState);

                    checkEndOfInning($row, $gameState);

                    $batterDataLength = getBatterDataLength($row['OUTCOME']);
                    $runnerMovement = getRunnerMovement($row['OUTCOME']);
                    $batterAdvances = false;

                    $batterData = substr($row['OUTCOME'], 0, $batterDataLength);
                    $runnerData = array();

                    $teamatbat = $row['TEAMATBAT'] === '0' ? $visBoxScoreStats : $homeBoxScoreStats;

                    // runners movement
                    if($runnerMovement) {

                        $runnerString = substr($row['OUTCOME'], $batterDataLength + 1, strlen($row['OUTCOME']) - 1);

                        if(strpos($row['OUTCOME'], ';') !== false) {

                            $lastpos = 0;
                            $current_pos = 0;
                            $positions = array();

                            while(($lastpos = strpos($runnerString, ';', $lastpos)) !== false) {
                                $positions[] = $lastpos;

                                $lastpos = $lastpos + 1;
                            }

                            foreach($positions as $value) {
                                $runnerData[] = substr($runnerString, $current_pos, ($value - $current_pos));

                                $current_pos = $value + 1;
                            }

                            $runnerData[] = substr($runnerString, $current_pos, (strlen($runnerString) - $current_pos));

                        }
                        else {
                            $runnerData[] = substr($runnerString, 0, strlen($runnerString));
                        }
                    }

                    $gameState->outs_this_row = 0;

                    // if($row['TEAMATBAT'] === '0') {
                    //     showMessage('inning: ' . $row['INNING'] . ' ' . $gameState->risp_vis_h . '-' . $gameState->risp_vis_ab);
                    // }



                    // check RBI
                    checkRBI($row['TEAMATBAT'], $runnerData, $row['PLAYERID'], $gameState, $visBoxScoreStats, $homeBoxScoreStats);

                    // Defensive Indifference
                    if(strpos($batterData, 'DI') !== false || strpos($batterData, 'OA') !== false) {
                        foreach($runnerData as $rd) {
                            moveBaseRunners($row, $rd, $gameState, $visBoxScoreStats, $homeBoxScoreStats, $visPitcherBoxScoreStats, $homePitcherBoxScoreStats);
                        }
                    }

                    // strikeouts
                    if($batterData[0] === 'K') {
                        // record strikeout

                        getStrikeouts($row['TEAMATBAT'], $row['PLAYERID'], $gameState, $visBoxScoreStats, $homeBoxScoreStats);

                        // check for WP
                        if(strpos($batterData, 'WP') !== false) {
                            getWildPitch($row['TEAMATBAT'], $row['PITCHERID'], $gameState, $visPitcherBoxScoreStats, $homePitcherBoxScoreStats);

                            foreach($runnerData as $rd) {
                                moveBaseRunners($row, $rd, $gameState, $visBoxScoreStats, $homeBoxScoreStats, $visPitcherBoxScoreStats, $homePitcherBoxScoreStats);
                            }

                        }
                        // check for PB
                        else if(strpos($batterData, 'PB') !== false) {
                            getPassedBall($row['TEAMATBAT'], $gameState, $visBoxScoreStats, $homeBoxScoreStats);

                            // move base runners
                            foreach($runnerData as $rd) {
                                moveBaseRunners($row, $rd, $gameState, $visBoxScoreStats, $homeBoxScoreStats, $visPitcherBoxScoreStats, $homePitcherBoxScoreStats);
                            }
                        }
                        else if(strpos($batterData, 'FO') !== false) {
                            // swinging strike, out only on base paths
                            getFO($row, $row['PITCHERID'], $batterData, $runnerData, $gameState, $visBoxScoreStats, $homeBoxScoreStats, $visPitcherBoxScoreStats, $homePitcherBoxScoreStats);
                        }

                        // check SB
                        if(strpos($batterData, 'SB') !== false) {
                            getStolenBase($row['TEAMATBAT'], $batterData, $gameState, $visBoxScoreStats, $homeBoxScoreStats);

                            foreach($runnerData as $rd) {
                                moveBaseRunners($row, $rd, $gameState, $visBoxScoreStats, $homeBoxScoreStats, $visPitcherBoxScoreStats, $homePitcherBoxScoreStats);
                            }

                            $runnerAdvances = false;

                            if(strpos($batterData, 'SB3') !== false) {
                                foreach($runnerData as $rd) {
                                    if(strpos($rd, '2-H') !== false || strpos($rd, '2XH') !== false) {
                                        $runnerAdvances = true;
                                    break;
                                    }
                                }

                                if(!$runnerAdvances) {
                                    $gameState->runneron3rd = $gameState->runneron2nd;
                                    $gameState->runneron2nd = 'None';
                                }
                            }

                            if(strpos($batterData, 'SB2') !== false) {
                                foreach($runnerData as $rd) {
                                    if(strpos($rd, '1-H') !== false || strpos($rd, '1XH') !== false || strpos($rd, '1-3') !== false || strpos($rd, '1X3') !== false) {
                                        $runnerAdvances = true;
                                    break;
                                    }
                                }

                                if(!$runnerAdvances) {
                                    $gameState->runneron2nd = $gameState->runneron1st;
                                    $gameState->runneron1st = 'None';
                                }
                            }
                        }

                        // check CS
                        if(strpos($batterData, 'CS') !== false) {
                            getCaughtStealing($row['TEAMATBAT'], $batterData, $gameState, $visBoxScoreStats, $homeBoxScoreStats);
                        }

                        // check pickoffs
                        if(strpos($batterData, 'PO') !== false) {
                            getPickoff($row, $batterData, $runnerData, $gameState, $visBoxScoreStats, $homeBoxScoreStats, $visPitcherBoxScoreStats, $homePitcherBoxScoreStats);
                        }

                        // double play?
                        if(strpos($batterData, 'DP') !== false || strpos($batterData, 'NDP' === false)) {
                            setDoublePlay($row['TEAMATBAT'], $gameState);
                        }

                        foreach($runnerData as $rd) {
                            if(strpos($rd, 'B-') !==false) {
                                // batter advances
                                advanceBatter($row['TEAMATBAT'], $rd, $row['PLAYERID'], $gameState);
                                if(strpos($runnerData, 'E') !== false) {
                                    getErrors($row, $rd, $gameState, $visBoxScoreStats, $homeBoxScoreStats, $visPitcherBoxScoreStats, $homePitcherBoxScoreStats);
                                }
                                $batterAdvances = true;
                            break;
                            }
                        }

                        if(!$batterAdvances) {
                            $gameState->outs_in_the_inning++;
                        }

                    }

                    // walks
                    else if($batterData[0]==='W' && $batterData[1] !== 'P') {
                        getWalks($row['TEAMATBAT'], $batterData, $row['PLAYERID'], $gameState, $visBoxScoreStats, $homeBoxScoreStats);

                        // check for WP
                        if(strpos($batterData, 'WP') !== false) {
                            getWildPitch($row['TEAMATBAT'], $row['PITCHERID'], $gameState, $visPitcherBoxScoreStats, $homePitcherBoxScoreStats);
                        }

                        // // check for PB
                        if(strpos($batterData, 'PB') !== false) {
                            getPassedBall($row['TEAMATBAT'], $gameState, $visBoxScoreStats, $homeBoxScoreStats);
                        }

                        // // check SB
                        if(strpos($batterData, 'SB') !== false) {
                            getStolenBase($row['TEAMATBAT'], $batterData, $gameState, $visBoxScoreStats, $homeBoxScoreStats);
                        }

                        // // check pickoffs
                        if(strpos($batterData, 'PO') !== false) {
                            getPickoff($row, $batterData, $runnerData, $gameState, $visBoxScoreStats, $homeBoxScoreStats, $visPitcherBoxScoreStats, $homePitcherBoxScoreStats);
                        }

                        // // if double-play, record it
                        if(strpos($batterData, 'DP') !== false || strpos($batterData, 'NDP' === false)) {
                            setDoublePlay($row['TEAMATBAT'], $gameState);
                        }

                        foreach($runnerData as $rd) {
                            moveBaseRunners($row, $rd, $gameState, $visBoxScoreStats, $homeBoxScoreStats, $visPitcherBoxScoreStats, $homePitcherBoxScoreStats);
                        }

                        $runnerAdvances = false;

                        if(strpos($batterData, 'SB3') !== false) {
                            foreach($runnerData as $rd) {
                                if(strpos($rd, '2-H') !== false || strpos($rd, '2XH') !== false) {
                                    $runnerAdvances = true;
                                break;
                                }
                            }

                            if(!$runnerAdvances) {
                                $gameState->runneron3rd = $gameState->runneron2nd;
                                $gameState->runneron2nd = 'None';
                            }
                        }

                        foreach($runnerData as $rd) {
                            if(strpos($rd, 'B-') !==false) {
                                // batter advances
                                advanceBatter($row['TEAMATBAT'], $rd, $row['PLAYERID'], $gameState);

                                if(strpos($runnerData, 'E') !== false) {
                                    getErrors($row, $rd, $gameState, $visBoxScoreStats, $homeBoxScoreStats, $visPitcherBoxScoreStats, $homePitcherBoxScoreStats);
                                }

                                $batterAdvances = true;
                            break;
                            }
                        }

                        if(!$batterAdvances) {
                            $gameState->runneron1st = $row['PLAYERID'];
                        }
                    }

                    // Intentional Walk
                    else if(strpos($batterData, 'IW') !== false) {

                        getWalks($row['TEAMATBAT'], $batterData, $row['PLAYERID'], $gameState, $visBoxScoreStats, $homeBoxScoreStats);

                        // move base runners
                        foreach($runnerData as $rd) {
                            moveBaseRunners($row, $rd, $gameState, $visBoxScoreStats, $homeBoxScoreStats, $visPitcherBoxScoreStats, $homePitcherBoxScoreStats);
                        }

                        // move runner to 1st
                        $gameState->runneron1st = $row['PLAYERID'];
                    }

                    // hit by pitch
                    else if(strpos($batterData, 'HP') !== false) {
                        getHitByPitch($row, $visBoxScoreStats, $homeBoxScoreStats);

                        // move base runners
                        foreach($runnerData as $rd) {
                            moveBaseRunners($row, $rd, $gameState, $visBoxScoreStats, $homeBoxScoreStats, $visPitcherBoxScoreStats, $homePitcherBoxScoreStats);
                        }

                        // move runner to 1st
                        $gameState->runneron1st = $row['PLAYERID'];
                    }

                    // SINGLES, not stolen bases
                    else if($batterData[0]==='S' && $batterData[1] !== 'B') {
                        getSingles($row['TEAMATBAT'], $row['PLAYERID'], $gameState, $visBoxScoreStats, $homeBoxScoreStats);

                        // move base runners
                        foreach($runnerData as $rd) {
                            moveBaseRunners($row, $rd, $gameState, $visBoxScoreStats, $homeBoxScoreStats, $visPitcherBoxScoreStats, $homePitcherBoxScoreStats);
                        }

                        if(strpos($row['OUTCOME'], 'BX') !== false) {
                            foreach($runnerData as $rd) {
                                if(strpos($rd, 'BX1') !== false) {
                                    // if error is found, then he's safe at 1st
                                    if(strpos($rd, 'E') !== false) {
                                        getErrors($row, $rd, $gameState, $visBoxScoreStats, $homeBoxScoreStats, $visPitcherBoxScoreStats, $homePitcherBoxScoreStats);

                                        $gameState->runneron1st = $row['PLAYERID'];
                                    }
                                    else {
                                        $gameState->outs_in_the_inning++;
                                    }
                                }
                                else if(strpos($rd, 'BX2') !== false) {
                                    // if error is found, then he's safe at 2nd
                                    if(strpos($rd, 'E') !== false) {
                                        getErrors($row, $rd, $gameState, $visBoxScoreStats, $homeBoxScoreStats, $visPitcherBoxScoreStats, $homePitcherBoxScoreStats);

                                        // search $rd to determine if runner safe or out
                                        $firstOpenParenthesis = stripos($rd, '(') + 1;
                                        $firstClosedParenthesis = stripos($rd, ')');

                                        // get everything in between
                                        $insideParenthesis = substr($rd, $firstOpenParenthesis, ($firstClosedParenthesis-$firstOpenParenthesis));

                                        if(strpos($insideParenthesis, 'E') !== false) {
                                            $gameState->runneron2nd = $row['PLAYERID'];                                        
                                        }
                                        else {
                                            $gameState->outs_in_the_inning++;
                                        }

                                    }
                                    else {
                                        $gameState->outs_in_the_inning++;
                                    }
                                }
                                else if(strpos($rd, 'BX3') !== false) {
                                    // if error is found, then he's safe at 3rd
                                    if(strpos($rd, 'E') !== false) {
                                        getErrors($row, $rd, $gameState, $visBoxScoreStats, $homeBoxScoreStats, $visPitcherBoxScoreStats, $homePitcherBoxScoreStats);

                                        // search $rd to determine if runner safe or out
                                        $firstOpenParenthesis = stripos($rd, '(') + 1;
                                        $firstClosedParenthesis = stripos($rd, ')');

                                        // get everything in between
                                        $insideParenthesis = substr($rd, $firstOpenParenthesis, ($firstClosedParenthesis-$firstOpenParenthesis));

                                        if(strpos($insideParenthesis, 'E') !== false) {
                                            $gameState->runneron3rd = $row['PLAYERID'];                                        
                                        }
                                        else {
                                            $gameState->outs_in_the_inning++;
                                        }

                                    }
                                    else {
                                        $gameState->outs_in_the_inning++;
                                    }
                                }
                                else if(strpos($rd, 'BXH') !== false) {
                                    // if error is found, then he's safe at 2nd
                                    if(strpos($rd, 'E') !== false) {
                                        getErrors($row, $rd, $gameState, $visBoxScoreStats, $homeBoxScoreStats, $visPitcherBoxScoreStats, $homePitcherBoxScoreStats);

                                        foreach($teamatbat as $vs) {
                                            if($vs->playerid === $row['PLAYERID']) {
                                                $vs->batter_stat_r++;
                                            break;
                                            }
                                        }
                                    }
                                    else {
                                        $gameState->outs_in_the_inning++;
                                    }
                                }
                            }
                        }
                        else if(strpos($row['OUTCOME'], 'B-') !== false) {
                            // batter advances
                            foreach($runnerData as $rd) {
                                if(strpos($rd, 'B-') !== false) {
                                    advanceBatter($row['TEAMATBAT'], $rd, $row['PLAYERID'], $gameState);
                                    $batterAdvances = true;

                                    if(strpos($runnerData, 'E') !== false) {
                                        getErrors($row, $rd, $gameState, $visBoxScoreStats, $homeBoxScoreStats, $visPitcherBoxScoreStats, $homePitcherBoxScoreStats);
                                    }
                                    break;
                                }
                            }
                        }

                        if(!$batterAdvances) {
                            $gameState->runneron1st = $row['PLAYERID'];
                        }
                    }

                //     // doubles, not defensive interference
                    else if($batterData[0]==='D' && $batterData[1] !== 'I') {

                        getDoubles($row['TEAMATBAT'], $row['PLAYERID'], $gameState, $visBoxScoreStats, $homeBoxScoreStats);

                        // move base runners
                        foreach($runnerData as $rd) {
                            moveBaseRunners($row, $rd, $gameState, $visBoxScoreStats, $homeBoxScoreStats, $visPitcherBoxScoreStats, $homePitcherBoxScoreStats);
                        }

                        if(strpos($row['OUTCOME'], 'BX') !== false) {
                            foreach($runnerData as $rd) {
                                if(strpos($rd, 'BX3') !== false) {
                                    $batterAdvances = true;
                                    // if error is found, then he's safe at 3rd
                                    if(strpos($rd, 'E') !== false) {
                                        getErrors($row, $rd, $gameState, $visBoxScoreStats, $homeBoxScoreStats, $visPitcherBoxScoreStats, $homePitcherBoxScoreStats);

                                        // search $rd to determine if runner safe or out
                                        $firstOpenParenthesis = stripos($rd, '(') + 1;
                                        $firstClosedParenthesis = stripos($rd, ')');

                                        // get everything in between
                                        $insideParenthesis = substr($rd, $firstOpenParenthesis, ($firstClosedParenthesis-$firstOpenParenthesis));

                                        if(strpos($insideParenthesis, 'E') !== false) {
                                            $gameState->runneron3rd = $row['PLAYERID'];                                        
                                        }
                                        else {
                                            $gameState->outs_in_the_inning++;
                                        }

                                    }
                                    else {
                                        $gameState->outs_in_the_inning++;
                                    }
                                }
                                else if(strpos($rd, 'BXH') !== false) {
                                    $batterAdvances = true;
                                    // if error is found, then he's safe at 2nd
                                    if(strpos($rd, 'E') !== false) {
                                        getErrors($row, $rd, $gameState, $visBoxScoreStats, $homeBoxScoreStats, $visPitcherBoxScoreStats, $homePitcherBoxScoreStats);

                                        foreach($teamatbat as $vs) {
                                            if($vs->playerid === $row['PLAYERID']) {
                                                $vs->batter_stat_r++;
                                            break;
                                            }
                                        }
                                    }
                                    else {
                                        $gameState->outs_in_the_inning++;
                                    }
                                }
                            }
                        }
                        else if(strpos($row['OUTCOME'], 'B-') !== false) {
                            // batter advances
                            $batterAdvances = true;
                            foreach($runnerData as $rd) {
                                if(strpos($rd, 'B-') !== false) {
                                    advanceBatter($row['TEAMATBAT'], $rd, $row['PLAYERID'], $gameState);

                                    if(strpos($runnerData, 'E') !== false) {
                                        getErrors($row, $rd, $gameState, $visBoxScoreStats, $homeBoxScoreStats, $visPitcherBoxScoreStats, $homePitcherBoxScoreStats);
                                    }
                                    break;
                                }
                            }
                        }

                        if(!$batterAdvances) {
                            $gameState->runneron2nd = $row['PLAYERID'];
                        }
                    }

                    else if($batterData[0]==='T') {
                        // triple

                        getTriples($row['TEAMATBAT'], $row['PLAYERID'], $gameState, $visBoxScoreStats, $homeBoxScoreStats);

                        // move base runners
                        foreach($runnerData as $rd) {
                            moveBaseRunners($row, $rd, $gameState, $visBoxScoreStats, $homeBoxScoreStats, $visPitcherBoxScoreStats, $homePitcherBoxScoreStats);
                        }

                        if(strpos($row['OUTCOME'], 'BX') !== false) {
                            foreach($runnerData as $rd) {
                                if(strpos($rd, 'BX3') !== false) {
                                    // if error is found, then he's safe at 3rd
                                    if(strpos($rd, 'E') !== false) {
                                        getErrors($row, $rd, $gameState, $visBoxScoreStats, $homeBoxScoreStats, $visPitcherBoxScoreStats, $homePitcherBoxScoreStats);

                                        // search $rd to determine if runner safe or out
                                        $firstOpenParenthesis = stripos($rd, '(') + 1;
                                        $firstClosedParenthesis = stripos($rd, ')');

                                        // get everything in between
                                        $insideParenthesis = substr($rd, $firstOpenParenthesis, ($firstClosedParenthesis-$firstOpenParenthesis));

                                        if(strpos($insideParenthesis, 'E') !== false) {
                                            $gameState->runneron3rd = $row['PLAYERID'];                                        
                                        }
                                        else {
                                            $gameState->outs_in_the_inning++;
                                        }

                                    }
                                    else {
                                        $gameState->outs_in_the_inning++;
                                    }
                                }
                                else if(strpos($rd, 'BXH') !== false) {
                                    // if error is found, then he's safe at 2nd
                                    if(strpos($rd, 'E') !== false) {
                                        getErrors($row, $rd, $gameState, $visBoxScoreStats, $homeBoxScoreStats, $visPitcherBoxScoreStats, $homePitcherBoxScoreStats);

                                        foreach($teamatbat as $vs) {
                                            if($vs->playerid === $row['PLAYERID']) {
                                                $vs->batter_stat_r++;
                                            break;
                                            }
                                        }
                                    }
                                    else {
                                        $gameState->outs_in_the_inning++;
                                    }
                                }
                            }
                        }
                        else if(strpos($row['OUTCOME'], 'B-') !== false) {
                            // batter advances
                            foreach($runnerData as $rd) {
                                if(strpos($rd, 'B-') !== false) {
                                    advanceBatter($row['TEAMATBAT'], $rd, $row['PLAYERID'], $gameState);
                                    $batterAdvances = true;

                                    if(strpos($runnerData, 'E') !== false) {
                                        getErrors($row, $rd, $gameState, $visBoxScoreStats, $homeBoxScoreStats, $visPitcherBoxScoreStats, $homePitcherBoxScoreStats);
                                    }
                                    break;
                                }
                            }
                        }

                        if(!$batterAdvances) {
                            $gameState->runneron3rd = $row['PLAYERID'];
                        }
                    }

                    else if(strpos($batterData, 'HR') !== false) {
                        // homerun

                        getHomeRuns($row['TEAMATBAT'], $row['PLAYERID'], $gameState, $visBoxScoreStats, $homeBoxScoreStats);

                        foreach($runnerData as $rd) {
                            moveBaseRunners($row, $rd, $gameState, $visBoxScoreStats, $homeBoxScoreStats, $visPitcherBoxScoreStats, $homePitcherBoxScoreStats);
                        }
                    }

                    // balk
                    else if(strpos($batterData, 'BK') !== false) {
                        // balk

                        getBalk($row['TEAMATBAT'], $row['PITCHERID'], $gameState, $visPitcherBoxScoreStats, $homePitcherBoxScoreStats);

                        foreach($runnerData as $rd) {
                            moveBaseRunners($row, $rd, $gameState, $visBoxScoreStats, $homeBoxScoreStats, $visPitcherBoxScoreStats, $homePitcherBoxScoreStats);
                        }
                    }

                    else if(strpos($batterData, 'GDP') !== false) {
                        // ground into double play
                        getGDP($row, $row['TEAMATBAT'], $row['PLAYERID'], $batterData, $runnerData, $gameState, $visBoxScoreStats, $homeBoxScoreStats);
                    }

                    else if(strpos($batterData, 'GTP') !== false) {
                        // ground into triple play

                        getGTP($row['TEAMATBAT'], $row['PLAYERID'], $batterData, $gameState, $visBoxScoreStats, $homeBoxScoreStats);

                    }

                    else if(strpos($batterData, 'FLE') !== false) {
                        // Error on foul fly ball
                        // assign the error and resume
                        if(strpos($batterData, 'E') !== false) {
                            getErrors($row, $runnerData, $gameState, $visBoxScoreStats, $homeBoxScoreStats, $visPitcherBoxScoreStats, $homePitcherBoxScoreStats);
                        }
                    }

                    // get caught stealing
                    else if(strpos($batterData, 'CS') !== false) {
                        getCaughtStealing($row['TEAMATBAT'], $batterData, $gameState, $visBoxScoreStats, $homeBoxScoreStats);
                    }

                    else if(strpos($batterData, 'PO') !== false) {
                        getPickoff($row, $batterData, $runnerData, $gameState, $visBoxScoreStats, $homeBoxScoreStats, $visPitcherBoxScoreStats, $homePitcherBoxScoreStats);
                    }

                    else if(strpos($batterData, 'E') !== false) {

                        if(isRISP($gameState)) {
                            addRISP_AB($row['TEAMATBAT'], $gameState);
                        }

                        getErrors($row, $batterData, $gameState, $visBoxScoreStats, $homeBoxScoreStats, $visPitcherBoxScoreStats, $homePitcherBoxScoreStats);

                        foreach($runnerData as $rd) {
                            moveBaseRunners($row, $rd, $gameState, $visBoxScoreStats, $homeBoxScoreStats, $visPitcherBoxScoreStats, $homePitcherBoxScoreStats);
                        }

                        if(strpos($row['OUTCOME'], 'B-') !== false) {
                            // batter advances
                            foreach($runnerData as $rd) {
                                if(strpos($rd, 'B-') !== false) {
                                    advanceBatter($row['TEAMATBAT'], $rd, $row['PLAYERID'], $gameState);
                                    $batterAdvances = true;

                                    if(strpos($runnerData, 'E') !== false) {
                                        getErrors($row, $rd, $gameState, $visBoxScoreStats, $homeBoxScoreStats, $visPitcherBoxScoreStats, $homePitcherBoxScoreStats);
                                    }
                                    break;
                                }
                            }
                        }
                        else {
                            $gameState->runneron1st = $row['PLAYERID'];
                        }

                        foreach($teamatbat as $vs) {
                            if($vs->playerid === $row['PLAYERID']) {
                                $vs->batter_stat_ab++;
                            break;
                            }
                        }

                        foreach($runnerData as $rd) {
                            if(strpos($rd, 'E') !== false) {
                                getErrors($row, $rd, $gameState, $visBoxScoreStats, $homeBoxScoreStats, $visPitcherBoxScoreStats, $homePitcherBoxScoreStats);
                            }
                        }
                    }

                    else if(strpos($batterData, 'FO') !== false) {
                        // force out
                        getFO($row, $batterData, $runnerData, $gameState, $visBoxScoreStats, $homeBoxScoreStats, $visPitcherBoxScoreStats, $homePitcherBoxScoreStats);
                    }

                    else if(strpos($batterData, 'FC') !== false) {
                        // fielder's choice

                        getFC($row['TEAMATBAT'], $row['PLAYERID'], $batterData, $gameState, $visBoxScoreStats, $homeBoxScoreStats);

                        foreach($runnerData as $rd) {
                            moveBaseRunners($row, $rd, $gameState, $visBoxScoreStats, $homeBoxScoreStats, $visPitcherBoxScoreStats, $homePitcherBoxScoreStats);
                        }

                        // batter advances
                        foreach($runnerData as $rd) {
                            if(strpos($rd, 'B-') !== false) {
                                advanceBatter($row['TEAMATBAT'], $rd, $row['PLAYERID'], $gameState);
                                $batterAdvances = true;

                                if(strpos($runnerData, 'E') !== false) {
                                    getErrors($row, $rd, $gameState, $visBoxScoreStats, $homeBoxScoreStats, $visPitcherBoxScoreStats, $homePitcherBoxScoreStats);
                                }
                                break;
                            }
                        }

                        if(!$batterAdvances) {
                            $gameState->runneron1st = $row['PLAYERID'];
                        }
                    }

                    // wild pitch
                    else if(strpos($batterData, 'WP') !== false) {
                        getWildPitch($row['TEAMATBAT'], $row['PITCHERID'], $gameState, $visPitcherBoxScoreStats, $homePitcherBoxScoreStats);

                        foreach($runnerData as $rd) {
                            moveBaseRunners($row, $rd, $gameState, $visBoxScoreStats, $homeBoxScoreStats, $visPitcherBoxScoreStats, $homePitcherBoxScoreStats);

                            if(strpos($rd, 'E') !== false) {
                                getErrors($row, $rd, $gameState, $visBoxScoreStats, $homeBoxScoreStats, $visPitcherBoxScoreStats, $homePitcherBoxScoreStats);
                            }
                        }
                    }

                    // check for PB
                    else if(strpos($batterData, 'PB') !== false) {
                        getPassedBall($row['TEAMATBAT'], $gameState, $visBoxScoreStats, $homeBoxScoreStats);

                        // move base runners
                        foreach($runnerData as $rd) {
                            moveBaseRunners($row, $rd, $gameState, $visBoxScoreStats, $homeBoxScoreStats, $visPitcherBoxScoreStats, $homePitcherBoxScoreStats);
                        }
                    }

                    // get steals
                    else if(strpos($batterData, 'SB') !== false) {
                        getStolenBase($row['TEAMATBAT'], $batterData, $gameState, $visBoxScoreStats, $homeBoxScoreStats);

                        foreach($runnerData as $rd) {
                            moveBaseRunners($row, $rd, $gameState, $visBoxScoreStats, $homeBoxScoreStats, $visPitcherBoxScoreStats, $homePitcherBoxScoreStats);
                        }

                        $runnerAdvances = false;

                        if(strpos($batterData, 'SB3') !== false) {
                            foreach($runnerData as $rd) {
                                if(strpos($rd, '2-H') !== false || strpos($rd, '2XH') !== false) {
                                    $runnerAdvances = true;
                                break;
                                }
                            }

                            if(!$runnerAdvances) {
                                $gameState->runneron3rd = $gameState->runneron2nd;
                                $gameState->runneron2nd = 'None';
                            }
                        }

                        if(strpos($batterData, 'SB2') !== false) {
                            foreach($runnerData as $rd) {
                                if(strpos($rd, '1-H') !== false || strpos($rd, '1XH') !== false || strpos($rd, '1-3') !== false || strpos($rd, '1X3') !== false) {
                                    $runnerAdvances = true;
                                break;
                                }
                            }

                            if(!$runnerAdvances) {
                                $gameState->runneron2nd = $gameState->runneron1st;
                                $gameState->runneron1st = 'None';
                            }
                        }
                    }

                    else if(is_numeric($batterData[0])) {

                        if(isRISP($gameState)) {
                            addRISP_AB($row['TEAMATBAT'], $gameState);
                        }

                        getOut($row['TEAMATBAT'], $batterData, $runnerData, $gameState, $visBoxScoreStats, $homeBoxScoreStats);

                        if(strpos($batterData, 'SF') !== false) {
                            // no at-bat for sac fly

                            // showMessage('Before: ' . $gameState->risp_vis_h . '-' . $gameState->risp_vis_ab);

                            foreach($teamatbat as $vs) {
                                if($vs->playerid === $row['PLAYERID']) {
                                    $vs->batter_stat_sf++;
                                    $gameState->outs_in_the_inning++;
                                break;
                                }
                            }

                            if(isRISP($gameState)) {
                                if($row['TEAMATBAT'] === '0') {
                                    $gameState->risp_vis_ab--;
                                }
                                else {
                                    $gameState->risp_home_ab--;
                                }
                            }

                        }
                        else if(strpos($batterData, 'SH') !== false) {
                            // no at-bat for sac bunts

                            foreach($teamatbat as $vs) {
                                if($vs->playerid === $row['PLAYERID']) {
                                    $vs->batter_stat_sh++;
                                    $gameState->outs_in_the_inning++;
                                break;
                                }
                            }

                            if(isRISP($gameState)) {
                                if($row['TEAMATBAT'] === '0') {
                                    $gameState->risp_vis_ab--;
                                }
                                else {
                                    $gameState->risp_home_ab--;
                                }
                            }

                        }
                        else {

                            foreach($teamatbat as $vs) {
                                if($vs->playerid === $row['PLAYERID']) {
                                    $vs->batter_stat_ab++;
                                    $gameState->outs_in_the_inning++;
                                break;
                                }
                            }

                            if(strpos($batterData, 'E') !== false) {
                                getErrors($row, $batterData, $gameState, $visBoxScoreStats, $homeBoxScoreStats, $visPitcherBoxScoreStats, $homePitcherBoxScoreStats);

                                foreach($runnerData as $rd) {
                                    if(strpos($runnerData, 'B-') !==false) {
                                        // batter advances
                                        advanceBatter($row['TEAMATBAT'], $rd, $row['PLAYERID'], $gameState);
                                        if(strpos($runnerData, 'E') !== false) {
                                            getErrors($row, $rd, $gameState, $visBoxScoreStats, $homeBoxScoreStats, $visPitcherBoxScoreStats, $homePitcherBoxScoreStats);
                                        }
                                        $batterAdvances = true;
                                    break;
                                    }
                                }

                                if(!$batterAdvances) {
                                    $gameState->runneron1st = $row['PLAYERID'];
                                }
                            }

                            if(strpos($batterData, 'DP') !== false) {
                                setDoublePlay($row['TEAMATBAT'], $gameState);
                            }

                        }

                        foreach($runnerData as $rd) {
                            moveBaseRunners($row, $rd, $gameState, $visBoxScoreStats, $homeBoxScoreStats, $visPitcherBoxScoreStats, $homePitcherBoxScoreStats);
                        }
                    }

                    if($gameState->outs_in_the_inning === 3) {
                        // record Team LOB
                        // showMessage('Inning: ' . $row['INNING']);
                        if($gameState->teamatbat === 0) {
                            if(strpos($row['OUTCOME'], 'DP') !== false || strpos($row['OUTCOME'], 'CS') !== false) {
                                $numRunnersOnBase--;
                            }

                            $gameState->visTeamLOB += $numRunnersOnBase;
                        }
                        else {
                            $gameState->homeTeamLOB += $numRunnersOnBase;
                        }
                    }

                }
                else {
                    // check for SUB
                    checkForSub($row, $gameState, $visBoxScoreStats, $homeBoxScoreStats);
                }

            }
        }

    ?>

    <div class="sm-font grid-padding grid-underline pos-color">BATTER</div>
    <div class="sm-font grid-padding grid-underline center-item pos-color">AB</div>
    <div class="sm-font grid-padding grid-underline center-item pos-color">R</div>
    <div class="sm-font grid-padding grid-underline center-item pos-color">H</div>
    <div class="sm-font grid-padding grid-underline center-item pos-color">RBI</div>
    <div class="sm-font grid-padding grid-underline center-item pos-color">BB</div>
    <div class="sm-font grid-padding grid-underline center-item pos-color">K</div>

    <?php
        $count = 0;
        foreach($visBoxScoreStats as $vs) {
    ?>
    <div class="grid-padding  <?php if($vs->status === 'sub') { echo 'boxscore-sub'; } ?>"><?php echo getPlayerName($link, $vs->playerid) . ', ' ?><span class="pos-color"><?php echo $vs->playerpos; ?></span></div>
    <div class="grid-padding center-item pos-color"><?php echo $vs->batter_stat_ab ?></div>
    <div class="grid-padding center-item pos-color"><?php echo $vs->batter_stat_r ?></div>
    <div class="grid-padding center-item pos-color"><?php echo $vs->getHits() ?></div>
    <div class="grid-padding center-item pos-color"><?php echo $vs->batter_stat_rbi ?></div>
    <div class="grid-padding center-item pos-color"><?php echo $vs->batter_stat_bb ?></div>
    <div class="grid-padding center-item pos-color"><?php echo $vs->batter_stat_k ?></div>
    <?php
        $count++;
    }
    ?>
</div>
<div class="boxscore-stats-totals">
    <?php
        $total_ab = 0;
        $total_r = 0;
        $total_h = 0;
        $total_rbi = 0;
        $total_bb = 0;
        $total_k = 0;
        $total_ibb = 0;

        foreach($visBoxScoreStats as $vs) {
            if($vs->batter_stat_ab > 0) {
                $total_ab += $vs->batter_stat_ab;
            }
            if($vs->batter_stat_r > 0) {
                $total_r += $vs->batter_stat_r;
            }
            if($vs->batter_stat_h > 0) {
                $total_h += $vs->batter_stat_h;
            }
            if($vs->batter_stat_rbi > 0) {
                $total_rbi += $vs->batter_stat_rbi;
            }
            if($vs->batter_stat_bb > 0) {
                $total_bb += $vs->batter_stat_bb;
            }
            if($vs->batter_stat_k > 0) {
                $total_k += $vs->batter_stat_k;
            }
            if($vs->batter_stat_ibb > 0) {
                $total_ibb += $vs->batter_stat_ibb;
            }
        }
    ?>
    <div class="">TOTALS</div>
    <div class="center-item"><?php echo $total_ab; ?></div>
    <div class="center-item"><?php echo $total_r; ?></div>
    <div class="center-item"><?php echo $total_h; ?></div>
    <div class="center-item"><?php echo $total_rbi; ?></div>
    <div class="center-item"><?php echo $total_bb; ?></div>
    <div class="center-item"><?php echo $total_k; ?></div>
</div>
<h2>BATTING</h2>
<div class="extra-base-hits-section">
    <?php
        $total_doubles = 0;
        $total_triples = 0;
        $total_homeruns = 0;
        $total_sacflies = 0;
        $total_sachits = 0;
        $total_hbp = 0;
        $team_total_bases = 0;
        $total_bases = [];
        $team_total_rbi = 0;
        $total_rbi = [];
        $team_total_rbi_2_out = 0;
        $team_2_out = [];

        foreach($visBoxScoreStats as $vs) {
            // add up all doubles
            $total_doubles += $vs->batter_stat_2b;
        }

        foreach($visBoxScoreStats as $vs) {
            // add up all triples
            $total_triples += $vs->batter_stat_3b;
        }

        foreach($visBoxScoreStats as $vs) {
            // add up all homeruns
            $total_homeruns += $vs->batter_stat_hr;
        }

        foreach($visBoxScoreStats as $vs) {
            // add up all sac flies
            $total_sacflies += $vs->batter_stat_sf;
        }

        foreach($visBoxScoreStats as $vs) {
            // add up all sac hits
            $total_sachits += $vs->batter_stat_sh;
        }

        foreach($visBoxScoreStats as $vs) {
            // add up all hbp
            $total_hbp += $vs->batter_stat_hbp;
        }

        foreach($visBoxScoreStats as $vs) {
            // total bases
            $bases = $vs->batter_stat_1b + ($vs->batter_stat_2b * 2) + ($vs->batter_stat_3b * 3) + ($vs->batter_stat_hr * 4);
            $team_total_bases += $bases;

            $newTotalBases = new TotalBases();

            $newTotalBases->playerid = $vs->playerid;
            $newTotalBases->bases = $bases;

            array_push($total_bases, $newTotalBases);
        }

        foreach($visBoxScoreStats as $vs) {
            // RBI
            $team_total_rbi += $vs->batter_stat_rbi;
            $newTotalRBI = new TotalRBI();
            $newTotalRBI->playerid = $vs->playerid;
            $newTotalRBI->rbi = $vs->batter_stat_rbi;

            array_push($total_rbi, $newTotalRBI);
        }

        foreach($visBoxScoreStats as $vs) {
            // 2-out RBI
            $team_total_rbi_2_out += $vs->batter_stat_rbi_2_out;
            $newTotalRBI = new TotalRBI();
            $newTotalRBI->playerid = $vs->playerid;
            $newTotalRBI->rbi = $vs->batter_stat_rbi_2_out;

            array_push($team_2_out, $newTotalRBI);
        }

        // doubles
        if($total_doubles > 0) {
            echo '<div class="extra-base-hits-row">2B:</div><div>';

            $found = false;

            foreach($visBoxScoreStats as $vs) {
                if($vs->batter_stat_2b > 0) {

                    if($found) {
                        echo '<br>';
                    }

                    echo getPlayerName($link, $vs->playerid);

                    if($vs->batter_stat_2b > 1) {
                        echo ' ' . $vs->batter_stat_2b;
                    }

                    $found = true;

                }
            }

            echo '</div>';
        }
        // triples
        if($total_triples > 0) {
            echo '<div class="extra-base-hits-row">3B: </div><div class="extra-base-hits-row-name">';

            $found = false;

            foreach($visBoxScoreStats as $vs) {
                if($vs->batter_stat_3b > 0) {

                    if($found) {
                        echo '<br>';
                    }

                    echo getPlayerName($link, $vs->playerid);
                    
                    if($vs->batter_stat_3b > 1) {
                        echo ' ' . $vs->batter_stat_3b;
                    }

                    $found = true;

                }
            }
            
            echo '</div>';
        }

        // homeruns
        if($total_homeruns > 0) {
            echo '<div class="extra-base-hits-row">HR: </div><div class="extra-base-hits-row-name">';

            $found = false;

            foreach($visBoxScoreStats as $vs) {
                if($vs->batter_stat_hr > 0) {

                    if($found) {
                        echo '<br>';
                    }

                    echo getPlayerName($link, $vs->playerid);
                    
                    if($vs->batter_stat_hr > 1) {
                        echo ' ' . $vs->batter_stat_hr;
                    }

                    $found = true;

                }
            }
            
            echo '</div>';
        }

        // intentional walks
        if($total_ibb > 0) {
            echo '<div class="extra-base-hits-row">IBB: </div><div class="extra-base-hits-row-name">';

            $found = false;

            foreach($visBoxScoreStats as $vs) {
                if($vs->batter_stat_ibb > 0) {

                    if($found) {
                        echo '<br>';
                    }

                    echo getPlayerName($link, $vs->playerid);
                    
                    if($vs->batter_stat_ibb > 1) {
                        echo ' ' . $vs->batter_stat_ibb;
                    }

                    $found = true;

                }
            }
            
            echo '</div>';
        }

        // sac flies
        if($total_sacflies > 0) {
            echo '<div class="extra-base-hits-row">SF: </div><div class="extra-base-hits-row-name">';

            $found = false;

            foreach($visBoxScoreStats as $vs) {
                if($vs->batter_stat_sf > 0) {

                    if($found) {
                        echo '<br>';
                    }

                    echo getPlayerName($link, $vs->playerid);
                    
                    if($vs->batter_stat_sf > 1) {
                        echo ' ' . $vs->batter_stat_sf;
                    }

                    $found = true;

                }
            }
            
            echo '</div>';
        }

        // sac hits
        if($total_sachits > 0) {
            echo '<div class="extra-base-hits-row">SH: </div><div class="extra-base-hits-row-name">';

            $found = false;

            foreach($visBoxScoreStats as $vs) {
                if($vs->batter_stat_sh > 0) {

                    if($found) {
                        echo '<br>';
                    }

                    echo getPlayerName($link, $vs->playerid);
                    
                    if($vs->batter_stat_sh > 1) {
                        echo ' ' . $vs->batter_stat_sh;
                    }

                    $found = true;

                }
            }
            
            echo '</div>';
        }

        // hbp
        if($total_hbp > 0) {
            echo '<div class="extra-base-hits-row">HBP: </div><div class="extra-base-hits-row-name">';

            $found = false;

            foreach($visBoxScoreStats as $vs) {
                if($vs->batter_stat_hbp > 0) {

                    if($found) {
                        echo '<br>';
                    }

                    echo getPlayerName($link, $vs->playerid);
                    
                    if($vs->batter_stat_hbp > 1) {
                        echo ' ' . $vs->batter_stat_hbp;
                    }

                    $found = true;

                }
            }
            
            echo '</div>';
        }

        // total bases
        if($team_total_bases > 0) {
            echo '<div class="extra-base-hits-row">TB: </div><div class="extra-base-hits-row-name">';

            usort($total_bases, function($first, $second) {
                return $first->bases < $second->bases;
            });
            foreach($total_bases as $tb) {
                if($tb->bases > 0) {
                    if($foundbases) {
                        echo '<br>';
                    }

                    echo getPlayerName($link, $tb->playerid);

                    if($tb->bases > 1) {
                        echo ' ' . $tb->bases;
                    }

                    $foundbases = true;

                }
            }

            echo '</div>';
        }

        // GDP
        if($gameState->visTeamGDP > 0) {
            $foundGDP = false;

            echo '<div class="extra-base-hits-row">GIDP: </div><div class="extra-base-hits-row-name">';

            foreach($visBoxScoreStats as $vs) {
                if($vs->batter_stat_gidp > 0) {
                    if($foundGIDP) {
                        echo '<br>';
                    }
    
                    echo getPlayerName($link, $vs->playerid);
    
                    if($vs->batter_stat_gidp > 1) {
                        echo ' ' . $vs->batter_stats_gidp;
                    }
    
                    $foundGIDP = true;
    
                }
            }

            echo '</div>';
        }

        // GTP
        if($gameState->visTeamGTP > 0) {
            $foundGTP = false;

            echo '<div class="extra-base-hits-row">GITP: </div><div class="extra-base-hits-row-name">';

            foreach($visBoxScoreStats as $vs) {
                if($vs->batter_stat_gitp > 0) {
                    if($foundGTP) {
                        echo '<br>';
                    }
    
                    echo getPlayerName($link, $vs->playerid);
    
                    if($vs->batter_stat_gidp > 1) {
                        echo ' ' . $vs->batter_stats_gitp;
                    }
    
                    $foundGTP = true;
    
                }
            }

            echo '</div>';
        }

        // total rbi
        if($team_total_rbi > 0) {
            $foundrbi = false;

            echo '<div class="extra-base-hits-row">RBI: </div><div class="extra-base-hits-row-name">';

            usort($total_rbi, function($first, $second) {
                return $first->rbi < $second->rbi;
            });

            foreach($total_rbi as $tr) {
                if($tr->rbi > 0) {
                    if($foundrbi) {
                        echo '<br>';
                    }

                    echo getPlayerName($link, $tr->playerid);

                    if($tr->rbi > 1) {
                        echo ' ' . $tr->rbi;
                    }

                    $foundrbi = true;

                }
            }

            echo '</div>';
        }

        // total 2-out rbi
        if($team_total_rbi_2_out > 0) {
            echo '<div class="extra-base-hits-row">2-out RBI: </div><div class="extra-base-hits-row-name">';

            usort($team_2_out, function($first, $second) {
                return $first->rbi < $second->rbi;
            });

            $foundrbi2 = false;

            foreach($team_2_out as $tr) {
                if($tr->rbi > 0) {
                    if($foundrbi2) {
                        echo '<br>';
                    }

                    echo getPlayerName($link, $tr->playerid);

                    if($tr->rbi > 1) {
                        echo ' ' . $tr->rbi;
                    }

                    $foundrbi2 = true;

                }
            }

            echo '</div>';
        }
    ?>
</div>
<div class="extra-batting-stats">
    <?php

        // Team LOB
        echo '<div>Team LOB: ' . $gameState->visTeamLOB . '</div>';

        // Runners in scoring position hits per at-bat
        echo '<br><div>Team LOB: ' . $gameState->risp_vis_h . ' for ' . $gameState->risp_vis_ab . '.' . '</div>';
    ?>
</div>

    <?php

        $showFieldingStats = false;

        if($gameState->vis_double-plays_turned > 0) {
            $showFieldingStats = true;
        }
        else if($gameState->vis_errors > 0) {
            $showFieldingStats = true;
        }
        else if($gameState->visPassedBalls > 0) {
            $showFieldingStats = true;
        }
        else if($gameState->visOutfieldAssists > 0) {
            $showFieldingStats = true;
        }
        else if($gameState->vis_triple_plays_turned > 0) {
            $showFieldingStats = true;
        }

        if($showFieldingStats) {
            echo '<h2>Fielding</h2>';
            echo '<div class="extra-batting-stats">';

            if($gameState->vis_double_plays_turned > 0) {
                echo '<div>DP:</div><div>' . $gameState->vis_double_plays_turned . '</div>';
            }
    
            // Passed Balls
            if($gameState->visPassedBalls > 0) {
                echo '<br>PB: ';
                foreach($visBoxScoreStats as $vs) {
                    if($vs->batter_stat_pb > 0) {
                        echo getPlayerName($link, $vs->playerid);
                        if($vs->playerid > 1) {
                            echo ' ' . $vs->batter_stat_pb;
                        }
                    }
                }
            }
    
            // Outfield Assists
            if($gameState->visOutfieldAssists > 0) {
                echo '<br>Outfield Assists: ';
    
                foreach($visBoxScoreStats as $vs) {
                    if($vs->fielder_outfield_assists > 0) {
                        showMessage('found player');
                        echo getPlayerName($link, $vs->playerid);
                        if($vs->playerid > 1) {
                            echo ' ' . $vs->fielder_outfield_assists;
                        }
                    }
                }
            }
    
            // errors
            if($gameState->visErrors > 0) {
                echo '<br>E: ';
                foreach($visBoxScoreStats as $vs) {
                    if($vs->fielder_stat_error > 0) {
                        if($founderror) {
                            echo ', ';
                        }
    
                        echo getPlayerName($link, $vs->playerid);
                        
                        if($vs->fielder_stat_error > 1) {
                            echo ' (' . $vs->fielder_stat_error . ')';
                        }
    
                        $founderror = true;
    
                    }
                }
    
                foreach($visPitcherBoxScoreStats as $vs) {
                    if($vs->fielder_stat_error > 0) {
                        if($founderror) {
                            echo ', ';
                        }
    
                        echo getPlayerName($link, $vs->playerid);
    
                        if($vs->fielder_stat_error > 1) {
                            echo ' (' . $vs->fielder_stat_error . ')';
                        }
    
                        $founderror = true;
    
                    }
                }
            }
            echo '</div>';
        }

        // Baserunning

        $showBaseRunning = false;

        // stolen bases
        if($gameState->visStolenBases > 0) {
            $showBaseRunner = true;
        }
        else if($gameState->visCaughtStealing > 0) {
            $showBaseRunner = true;
        }
        else if($gameState->homeTeamPickoffs > 0) {
            $showBaseRunner = true;
        }

        if($showBaseRunner) {
            echo '<h2>Baserunning</h2>';
            echo '<div class="extra-base-hits-section">';

                if($gameState->visStolenBases > 0) {
                    echo '<div>SB:</div><div>';

                    $team_stolen_bases = [];

                    $foundRunner = false;
        
                    foreach($visBoxScoreStats as $vs) {
                        if($vs->batter_stat_sb > 0) {
                            // store total in array
                            $sb_array = new TotalBases();
                            $sb_array->playerid = $vs->playerid;
                            $sb_array->bases = $vs->batter_stat_sb;
        
                            array_push($team_stolen_bases, $sb_array);
                        }
                    }
        
                    usort($team_stolen_bases, function($first, $second) {
                        return $first->bases < $second->bases;
                    });
        
                    $foundbases = false;
                    foreach($team_stolen_bases as $tsb) {
                        if($tsb->bases > 0) {
                            if($foundbases) {
                                echo '<br>';
                            }
                            echo getPlayerName($link, $tsb->playerid);
                            if($tsb->bases > 1) {
                                echo ' ' . $tsb->bases;
                            }
                            $foundbases = true;
                        }
                    }
                    echo '</div>';
                }
                if($gameState->visCaughtStealing > 0) {
                    echo '<div>CS:</div>';
        
                    $team_caught_stealing = [];
        
                    foreach($visBoxScoreStats as $vs) {
                        if($vs->batter_stat_cs > 0) {
                            // store total in array
                            $cs_array = new TotalBases();
                            $cs_array->playerid = $vs->playerid;
                            $cs_array->bases = $vs->batter_stat_cs;
        
                            array_push($team_caught_stealing, $cs_array);
                        }
                    }
        
                    usort($team_caught_stealing, function($first, $second) {
                        return $first->bases < $second->bases;
                    });
        
                    $foundbases = false;
                    foreach($team_caught_stealing as $tcs) {
                        if($tcs->bases > 0) {
                            if($foundbases) {
                                echo '<br>';
                            }
                            echo getPlayerName($link, $tcs->playerid);
                            if($tcs->bases > 1) {
                                echo ' ' . $tcs->bases;
                            }
                            $foundbases = true;
                        }
                    }  
                }
                if($gameState->homeTeamPickoffs > 0) {
                    echo '<div>Pickoffs:</div>';
                    $team_pickedoff = [];
        
                    foreach($visBoxScoreStats as $vs) {
                        if($vs->batter_stat_pickedoff > 0) {
                            $newTotalPickedOff = new TotalPickedOff();
                            $newTotalPickedOff->playerid = $vs->playerid;
                            $newTotalPickedOff->num = $vs->batter_stat_pickedoff;
                
                            array_push($team_pickedoff, $newTotalPickedOff);
                        }
                    }
        
                    usort($team_pickedoff, function($first, $second) {
                        return $first->num < $second->num;
                    });
        
                    $foundPickedOff= false;
                    foreach($team_pickedoff as $tpo) {
                        if($tpo->num > 0) {
                            if($foundPickedOff) {
                                echo '<br>';
                            }
                            echo getPlayerName($link, $tpo->playerid);
                            if($tpo->num> 1) {
                                echo ' ' . $tpo->num;
                            }
                            $foundPickedOff = true;
                        }
                    }  
                }
            echo '</div>';
        }

    ?>
</div>