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
    class Pitches {
        public $count0_0_ball;
        public $count0_0_called_strike;
        public $count0_0_foul;
        public $count0_0_ball_in_play;
        public $count0_0_swinging_strike;
        public $count1_0_ball;
        public $count1_0_called_strike;
        public $count1_0_foul;
        public $count1_0_ball_in_play;
        public $count1_0_swinging_strike;
        public $count2_0_ball;
        public $count2_0_called_strike;
        public $count2_0_foul;
        public $count2_0_ball_in_play;
        public $count2_0_swinging_strike;
        public $count3_0_ball;
        public $count3_0_called_strike;
        public $count3_0_foul;
        public $count3_0_ball_in_play;
        public $count3_0_swinging_strike;
        public $count0_1_ball;
        public $count0_1_called_strike;
        public $count0_1_foul;
        public $count0_1_ball_in_play;
        public $count0_1_swinging_strike;
        public $count1_1_ball;
        public $count1_1_called_strike;
        public $count1_1_foul;
        public $count1_1_ball_in_play;
        public $count1_1_swinging_strike;
        public $count2_1_ball;
        public $count2_1_called_strike;
        public $count2_1_foul;
        public $count2_1_ball_in_play;
        public $count2_1_swinging_strike;
        public $count3_1_ball;
        public $count3_1_called_strike;
        public $count3_1_foul;
        public $count3_1_ball_in_play;
        public $count3_1_swinging_strike;
        public $count0_2_ball;
        public $count0_2_called_strike;
        public $count0_2_foul;
        public $count0_2_ball_in_play;
        public $count0_2_swinging_strike;
        public $count1_2_ball;
        public $count1_2_called_strike;
        public $count1_2_foul;
        public $count1_2_ball_in_play;
        public $count1_2_swinging_strike;
        public $count2_2_ball;
        public $count2_2_called_strike;
        public $count2_2_foul;
        public $count2_2_ball_in_play;
        public $count2_2_swinging_strike;
        public $count3_2_ball;
        public $count3_2_called_strike;
        public $count3_2_foul;
        public $count3_2_ball_in_play;
        public $count3_2_swinging_strike;
    }

    $pitches = new Pitches();

    $gameState = new GameStatus();
    $gameState->visDefense = new Defense();
    $gameState->homeDefense = new Defense();

    getLineScores($link, $gamedate, $hometeam, $gamenum, $month, $day, $year);

?>

<h1 class="section-separator">VISITORS - BATTING</h1>

<?php
    getVisitorLineup($link, $gameid, $gameState, $visBoxScoreStats, $homeBoxScoreStats);
    getStartingPitchers($link, $gamedate, $hometeam, $gamenum, $visPitcherBoxScoreStats, $homePitcherBoxScoreStats);
?>

<div class="boxscore-stats">
  <?php

  // ******************** TESTING CODE - REMOVE WHEN FINISHED ******************

//   $sql = "SELECT * FROM EVENTS where PLAYERID='troum001'";

//   $result = mysqli_query($link, $sql);

//   $resultCheck = mysqli_num_rows($result);

//     if($resultCheck > 0) {
//         while ($row = mysqli_fetch_assoc($result)) {
//             if($row['CATEGORY'] === 'play') {

//                 $current_balls = 0;
//                 $current_strikes = 0;
//                 $current_pitches = $row['PITCHES'];
//                 $this_result = '';
//                 $current_count = '';

//                 for($i = 0; $i < strlen($current_pitches); $i++) {

//                     $current_count = 'count' . $current_balls . '_' . $current_strikes;

//                     if($current_pitches[$i] !== '.' || $current_pitches[$i] !== '*' || $current_pitches[$i] !== '>') {
//                         if($current_pitches[$i] === 'B') {
//                             $current_balls++;
//                             $this_result = 'ball';
//                         }
//                         else if($current_pitches[$i] === 'C') {
//                             $current_strikes++;
//                             $this_result = 'called_strike';
//                         }
//                         else if($current_pitches[$i] === 'S' || $current_pitches[$i] === 'T') {
//                             $current_strikes++;
//                             $this_result = 'swinging_strike';
//                         }
//                         else if($current_pitches[$i] === 'F') {
//                             if($current_strikes < 2) {
//                                 $current_strikes++;
//                                 $this_result = 'foul';
//                             }
//                         }
//                         else if($current_pitches[$i] === 'X') {
//                             $this_result = 'ball_in_play';
//                         }
//                         else if($current_pitches[$i] === 'H') {
//                             $this_result = 'HBP';
//                         }

//                         if($this_result !== 'HBP') {
//                             $end_result = $current_count . "_" . $this_result;

//                             $pitches->$end_result++;
//                         }
//                     }
//                 }
//             }
//         }
//     }

//     echo '0-0: <br>';
//     echo $pitches->count0_0_ball . '<br>';
//     echo $pitches->count0_0_called_strike . '<br>';
//     echo $pitches->count0_0_foul . '<br>';
//     echo $pitches->count0_0_ball_in_play . '<br>';
//     echo $pitches->count0_0_swinging_strike . '<br>';

//     echo '1-0: <br>';
//     echo $pitches->count1_0_ball . '<br>';
//     echo $pitches->count1_0_called_strike . '<br>';
//     echo $pitches->count1_0_foul . '<br>';
//     echo $pitches->count1_0_ball_in_play . '<br>';
//     echo $pitches->count1_0_swinging_strike . '<br>';

//     echo '2-0: <br>';
//     echo $pitches->count2_0_ball . '<br>';
//     echo $pitches->count2_0_called_strike . '<br>';
//     echo $pitches->count2_0_foul . '<br>';
//     echo $pitches->count2_0_ball_in_play . '<br>';
//     echo $pitches->count2_0_swinging_strike . '<br>';

//     echo '3-0: <br>';
//     echo $pitches->count3_0_ball . '<br>';
//     echo $pitches->count3_0_called_strike . '<br>';
//     echo $pitches->count3_0_foul . '<br>';
//     echo $pitches->count3_0_ball_in_play . '<br>';
//     echo $pitches->count3_0_swinging_strike . '<br>';

//     echo '0-1: <br>';
//     echo $pitches->count0_1_ball . '<br>';
//     echo $pitches->count0_1_called_strike . '<br>';
//     echo $pitches->count0_1_foul . '<br>';
//     echo $pitches->count0_1_ball_in_play . '<br>';
//     echo $pitches->count0_1_swinging_strike . '<br>';

//     echo '1-1: <br>';

//     echo $pitches->count1_1_ball . '<br>';
//     echo $pitches->count1_1_called_strike . '<br>';
//     echo $pitches->count1_1_foul . '<br>';
//     echo $pitches->count1_1_ball_in_play . '<br>';
//     echo $pitches->count1_1_swinging_strike . '<br>';

//     echo '2-1: <br>';

//     echo $pitches->count2_1_ball . '<br>';
//     echo $pitches->count2_1_called_strike . '<br>';
//     echo $pitches->count2_1_foul . '<br>';
//     echo $pitches->count2_1_ball_in_play . '<br>';
//     echo $pitches->count2_1_swinging_strike . '<br>';

//     echo '3-1: <br>';

//     echo $pitches->count3_1_ball . '<br>';
//     echo $pitches->count3_1_called_strike . '<br>';
//     echo $pitches->count3_1_foul . '<br>';
//     echo $pitches->count3_1_ball_in_play . '<br>';
//     echo $pitches->count3_1_swinging_strike . '<br>';

//     echo '0-2: <br>';

//     echo $pitches->count0_2_ball . '<br>';
//     echo $pitches->count0_2_called_strike . '<br>';
//     echo $pitches->count0_2_foul . '<br>';
//     echo $pitches->count0_2_ball_in_play . '<br>';
//     echo $pitches->count0_2_swinging_strike . '<br>';

//     echo '1-2: <br>';

//     echo $pitches->count1_2_ball . '<br>';
//     echo $pitches->count1_2_called_strike . '<br>';
//     echo $pitches->count1_2_foul . '<br>';
//     echo $pitches->count1_2_ball_in_play . '<br>';
//     echo $pitches->count1_2_swinging_strike . '<br>';

//     echo '2-2: <br>';

//     echo $pitches->count2_2_ball . '<br>';
//     echo $pitches->count2_2_called_strike . '<br>';
//     echo $pitches->count2_2_foul . '<br>';
//     echo $pitches->count2_2_ball_in_play . '<br>';
//     echo $pitches->count2_2_swinging_strike . '<br>';

//     echo '3-2: <br>';

//     echo $pitches->count3_2_ball . '<br>';
//     echo $pitches->count3_2_called_strike . '<br>';
//     echo $pitches->count3_2_foul . '<br>';
//     echo $pitches->count3_2_ball_in_play . '<br>';
//     echo $pitches->count3_2_swinging_strike . '<br>';

  // **************************************

    $sql = "SELECT * FROM EVENTS WHERE GAMEID = '" . $gameid . "'";

    $result = mysqli_query($link, $sql);

    $resultCheck = mysqli_num_rows($result);

    $batterData = '';
    $runnerData;

    if($resultCheck > 0) {
        while ($row = mysqli_fetch_assoc($result)) { 

            // clear baserunners if new team batting and assign team LOB
            if($row['CATEGORY'] === 'play') {

                // ***********************************
                // testing the following code
                // ***********************************

                $batterDataLength = strlen($row['OUTCOME']);
                $runnerMovement = false;

                if(strpos($row['OUTCOME'], '.') !== false) {
                    $batterDataLength = strpos($row['OUTCOME'], '.');
                    $runnerMovement = true;
                }

                $batterData = substr($row['OUTCOME'], 0, $batterDataLength);
                $runnerData = array();

                // runners movement
                if($runnerMovement) {

                    $runnerString = substr($row['OUTCOME'], $batterDataLength + 1, strlen($row['OUTCOME']) - 1);
                    $lastpos = 0;
                    $positions = array();

                    if(strpos($row['OUTCOME'], ';') !== false) {

                        $current_pos = 0;

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

                checkEndOfInning($row, $gameState);

                $gameState->outs_this_row = 0;

                // strikeouts
                if($batterData[0] === 'K') {
                    // record strikeout and possible RISP team stat
                    getStrikeouts($row['TEAMATBAT'], $row['PLAYERID'], $gameState, $visBoxScoreStats, $homeBoxScoreStats);

                    // check for WP
                    if(strpos($row['OUTCOME'], 'WP') !== false) {
                        getWildPitch($row['TEAMATBAT'], $row['PITCHERID'], $gameState, $visPitcherBoxScoreStats, $homePitcherBoxScoreStats);
                    }

                    // check for PB
                    if(strpos($row['OUTCOME'], 'PB') !== false) {
                        getPassedBall($row['TEAMATBAT'], $row['PITCHERID'], $gameState, $visPitcherBoxScoreStats, $homePitcherBoxScoreStats);
                    }

                    // check SB
                    if(strpos($row['OUTCOME'], 'SB') !== false) {
                        getStolenBase($batterData, $runnerData, $gameState, $visBoxScoreStats, $homeBoxScoreStats);
                    }

                    // check CS
                    if(strpos($row['OUTCOME'], 'CS') !== false) {
                        getCaughtStealing($row, $gameState, $visBoxScoreStats, $homeBoxScoreStats);
                        $gameState->outs_this_row = 2;
                    }

                    // check pickoffs
                    if(strpos($row['OUTCOME'], 'PO') !== false) {
                        getPickoff($row['TEAMATBAT'], $row['PITCHERID'], $batterData, $runnerData, $gameState, $visPitcherBoxScoreStats, $homePitcherBoxScoreStats);
                    }

                    // move base runners
                    foreach($runnerData as $rd) {
                        moveBaseRunners($row, $rd, $gameState, $visBoxScoreStats, $homeBoxScoreStats);
                    }

                    // double play?
                    if($gameState->outs_this_row === 2) {
                        setDoublePlay($row['TEAMATBAT'], $gameState);
                    }

                }

                // walks
                else if($batterData[0]==='W' && $batterData[1] !== 'P') {
                    getWalks($row, $gameState, $visBoxScoreStats, $homeBoxScoreStats);

                    // check for WP
                    if(strpos($batterData, 'WP') !== false) {
                        getWildPitch($row, $gameState, $visPitcherBoxScoreStats, $homePitcherBoxScoreStats);
                    }

                    // check for PB
                    if(strpos($batterData, 'PB') !== false) {
                        getPassedBall($row, $gameState, $visPitcherBoxScoreStats, $homePitcherBoxScoreStats);
                    }

                    // check SB
                    if(strpos($batterData, 'SB') !== false) {
                        getStolenBase($row, $gameState, $visBoxScoreStats, $homeBoxScoreStats);
                    }

                    // check pickoffs
                    if(strpos($batterData, 'PO') !== false) {
                        getPickoff($row['TEAMATBAT'], $row['PITCHERID'], $batterData, $runnerData, $gameState, $visPitcherBoxScoreStats, $homePitcherBoxScoreStats);
                    }

                    // move base runners
                    foreach($runnerData as $rd) {
                        moveBaseRunners($row, $rd, $gameState, $visBoxScoreStats, $homeBoxScoreStats);
                    }

                    if(strpos($runnerData, 'B-H') !== false) {
                        foreach($visBoxScoreStats as $vs) {
                            if($vs->playerid === $row['PLAYERID']) {
                                $vs->batter_stat_r++;
                            break;
                            }
                        }
                    }
                    else if(strpos($runnerData, 'B-3') !== false) {
                        $gameState->runneron3rd = $row['PLAYERID'];
                    }
                    else if(strpos($runnerData, 'B-2') !== false) {
                        $gameState->runneron2nd = $row['PLAYERID'];
                    }
                    else {
                        $gameState->runneron1st = $row['PLAYERID'];
                    }

                    // RBI for bases-loaded
                    if(strpos($runnerData, '3-H') !== false) {
                        foreach($visBoxScoreStats as $vs) {
                            if($vs->playerid === $row['PLAYERID']) {
                                $vs->batter_stat_rbi++;
                            break;
                            }
                        }
                    }
                }

                // Intentional Walk
                else if(strpos($batterData, 'IW') !== false) {
                    getWalks($row, $gameState, $visBoxScoreStats, $homeBoxScoreStats);

                    // move base runners
                    foreach($runnerData as $rd) {
                        moveBaseRunners($row, $rd, $gameState, $visBoxScoreStats, $homeBoxScoreStats);
                    }

                    // move runner to 1st
                    $gameState->runneron1st = $row['PLAYERID'];
                }

                // hit by pitch
                else if(strpos($batterData, 'HP') !== false) {
                    getHitByPitch($row, $visBoxScoreStats, $homeBoxScoreStats);

                    // move base runners
                    foreach($runnerData as $rd) {
                        moveBaseRunners($row, $rd, $gameState, $visBoxScoreStats, $homeBoxScoreStats);
                    }

                    // move runner to 1st
                    $gameState->runneron1st = $row['PLAYERID'];
                }

                // SINGLES, not stolen bases
                else if($batterData[0]==='S' && $batterData[1] !== 'B') {
                    getSingles($row['TEAMATBAT'], $row['PLAYERID'], $gameState, $visBoxScoreStats, $homeBoxScoreStats);

                    // move base runners
                    foreach($runnerData as $rd) {
                        moveBaseRunners($row, $rd, $gameState, $visBoxScoreStats, $homeBoxScoreStats);
                    }

                    if(strpos($row['OUTCOME'], 'BX') !== false) {
                        foreach($runnerData as $rd) {
                            if(strpos($rd, 'BX1') !== false) {
                                // if error is found, then he's safe at 1st
                                if(strpos($rd, 'E') !== false) {
                                    getErrors($row, $runnerData, $gameState, $visBoxScoreStats, $homeBoxScoreStats, $visPitcherBoxScoreStats, $homePitcherBoxScoreStats);

                                    $gameState->runneron1st = $row['PLAYERID'];
                                }
                                else {
                                    $gameState->outs_in_the_inning++;
                                }
                            }
                            else if(strpos($rd, 'BX2') !== false) {
                                // if error is found, then he's safe at 2nd
                                if(strpos($rd, 'E') !== false) {
                                    getErrors($row, $runnerData, $gameState, $visBoxScoreStats, $homeBoxScoreStats, $visPitcherBoxScoreStats, $homePitcherBoxScoreStats);

                                    $gameState->runneron2nd = $row['PLAYERID'];
                                }
                                else {
                                    $gameState->outs_in_the_inning++;
                                }
                            }
                            else if(strpos($rd, 'BX3') !== false) {
                                // if error is found, then he's safe at 3rd
                                if(strpos($rd, 'E') !== false) {
                                    getErrors($row, $runnerData, $gameState, $visBoxScoreStats, $homeBoxScoreStats, $visPitcherBoxScoreStats, $homePitcherBoxScoreStats);

                                    $gameState->runneron3rd = $row['PLAYERID'];
                                }
                                else {
                                    $gameState->outs_in_the_inning++;
                                }
                            }
                            else if(strpos($rd, 'BXH') !== false) {
                                // if error is found, then he's safe at 2nd
                                if(strpos($rd, 'E') !== false) {
                                    getErrors($row, $runnerData, $gameState, $visBoxScoreStats, $homeBoxScoreStats, $visPitcherBoxScoreStats, $homePitcherBoxScoreStats);

                                    foreach($visBoxScoreStats as $vs) {
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
                    else {
                        $gameState->runneron1st = $row['PLAYERID'];
                    }
                }

                // doubles, not defensive interference
                else if($batterData[0]==='D' && $batterData[1] !== 'I') {
                    getDoubles($row['TEAMATBAT'], $row['PLAYERID'], $gameState, $visBoxScoreStats, $homeBoxScoreStats);

                    // move base runners
                    foreach($runnerData as $rd) {
                        moveBaseRunners($row, $rd, $gameState, $visBoxScoreStats, $homeBoxScoreStats);
                    }

                    if(strpos($row['OUTCOME'], 'BX') !== false) {
                        foreach($runnerData as $rd) {
                            if(strpos($rd, 'BX2') !== false) {
                                // if error is found, then he's safe at 1st
                                if(strpos($rd, 'E') !== false) {
                                    getErrors($row, $runnerData, $gameState, $visBoxScoreStats, $homeBoxScoreStats, $visPitcherBoxScoreStats, $homePitcherBoxScoreStats);

                                    $gameState->runneron2nd = $row['PLAYERID'];
                                }
                                else {
                                    $gameState->outs_in_the_inning++;
                                }
                            }
                            else if(strpos($rd, 'BX3') !== false) {
                                // if error is found, then he's safe at 3rd
                                if(strpos($rd, 'E') !== false) {
                                    getErrors($row, $runnerData, $gameState, $visBoxScoreStats, $homeBoxScoreStats, $visPitcherBoxScoreStats, $homePitcherBoxScoreStats);

                                    $gameState->runneron3rd = $row['PLAYERID'];
                                }
                                else {
                                    $gameState->outs_in_the_inning++;
                                }
                            }
                            else if(strpos($rd, 'BXH') !== false) {
                                // if error is found, then he's safe at 2nd
                                if(strpos($rd, 'E') !== false) {
                                    getErrors($row, $runnerData, $gameState, $visBoxScoreStats, $homeBoxScoreStats, $visPitcherBoxScoreStats, $homePitcherBoxScoreStats);

                                    foreach($visBoxScoreStats as $vs) {
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
                    else {
                        $gameState->runneron2nd = $row['PLAYERID'];
                    }
                }

                // // triples
                else if($batterData[0]==='T') {
                    getTriples($row['TEAMATBAT'], $row['PLAYERID'], $gameState, $visBoxScoreStats, $homeBoxScoreStats);

                    // move base runners
                    foreach($runnerData as $rd) {
                        moveBaseRunners($row, $rd, $gameState, $visBoxScoreStats, $homeBoxScoreStats);
                    }

                    if(strpos($row['OUTCOME'], 'BX') !== false) {
                        foreach($runnerData as $rd) {
                            if(strpos($rd, 'BX3') !== false) {
                                // if error is found, then he's safe at 3rd
                                if(strpos($rd, 'E') !== false) {
                                    getErrors($row, $runnerData, $gameState, $visBoxScoreStats, $homeBoxScoreStats, $visPitcherBoxScoreStats, $homePitcherBoxScoreStats);

                                    $gameState->runneron3rd = $row['PLAYERID'];
                                }
                                else {
                                    $gameState->outs_in_the_inning++;
                                }
                            }
                            else if(strpos($rd, 'BXH') !== false) {
                                // if error is found, then he's safe at 2nd
                                if(strpos($rd, 'E') !== false) {
                                    getErrors($row, $runnerData, $gameState, $visBoxScoreStats, $homeBoxScoreStats, $visPitcherBoxScoreStats, $homePitcherBoxScoreStats);

                                    foreach($visBoxScoreStats as $vs) {
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
                    else {
                        $gameState->runneron3rd = $row['PLAYERID'];
                    }
                }

                // // homeruns
                else if(strpos($batterData, 'HR') !== false) {
                    getHomeRuns($row['TEAMATBAT'], $row['PLAYERID'], $gameState, $visBoxScoreStats, $homeBoxScoreStats);

                    foreach($runnerData as $rd) {
                        moveBaseRunners($row, $rd, $gameState, $visBoxScoreStats, $homeBoxScoreStats);
                    }
                }

                // // balk
                else if(strpos($batterData, 'BK') !== false) {
                    getBalk($row['TEAMATBAT'], $row['PITCHERID'], $gameState, $visPitcherBoxScoreStats, $homePitcherBoxScoreStats);

                    foreach($runnerData as $rd) {
                        moveBaseRunners($row, $rd, $gameState, $visBoxScoreStats, $homeBoxScoreStats);
                    }
                }

                else if(strpos($batterData, 'GDP') !== false) {
                    getGDP($row['TEAMATBAT'], $row['PLAYERID'], $batterData, $gameState, $visBoxScoreStats, $homeBoxScoreStats);

                    foreach($runnerData as $rd) {
                        moveBaseRunners($row, $rd, $gameState, $visBoxScoreStats, $homeBoxScoreStats);
                    }

                    if(strpos($row['OUTCOME'], 'B-1') !== false) {
                        $gameState->runneron1st = $row['PLAYERID'];
                    }
                }

                // else if(strpos($batterData, 'GTP') !== false) {
                //     getGTP($row, $gameState, $visBoxScoreStats, $homeBoxScoreStats);
                // }

                // else if(strpos($batterData, 'FO') !== false) {
                //     getFO($row, $gameState, $visBoxScoreStats, $homeBoxScoreStats);
                // }

                // else if(strpos($batterData, 'FC') !== false) {
                //     getFC($row, $gameState, $visBoxScoreStats, $homeBoxScoreStats);
                // }

                // // POPUPS, NOT ERRORS
                // else if($row['OUTCOME'][0] === '1' || $row['OUTCOME'][0] === '2' || $row['OUTCOME'][0] === '3' || $row['OUTCOME'][0] === '4' || $row['OUTCOME'][0] === '5' || $row['OUTCOME'][0] === '6' || $row['OUTCOME'][0] === '7' || $row['OUTCOME'][0] === '8' || $row['OUTCOME'][0] === '9' || strpos($row['OUTCOME'], 'FC') !== false) {
                //     getOut($row, $gameState, $visBoxScoreStats, $homeBoxScoreStats);
                // }

                // // wild pitch
                // if(strpos($row['OUTCOME'], 'WP') !== false) {
                //     getWildPitch($row, $gameState, $visBoxScoreStats, $homeBoxScoreStats, $visPitcherBoxScoreStats, $homePitcherBoxScoreStats);
                //     $outcome = 'Wild Pitch';
                // }

                // // get steals
                // if(strpos($row['OUTCOME'], 'SB') !== false) {
                //     getStolenBase($row, $gameState, $visBoxScoreStats, $homeBoxScoreStats);
                //     $outcome = 'Stolen Base';
                // }

                // // get caught stealing
                // if(strpos($row['OUTCOME'], 'CS') !== false) {
                //     getCaughtStealing($row, $gameState, $visBoxScoreStats, $homeBoxScoreStats);
                //     $outcome = 'Caught Stealing';
                // }

                // // ERRORS
                // if(strpos($row['OUTCOME'], 'E') !== false) {
                //     getErrors($row, $gameState, $visBoxScoreStats, $homeBoxScoreStats, $visPitcherBoxScoreStats, $homePitcherBoxScoreStats);
                //     $outcome = 'Error';
                // }

                // // check RBI
                // checkRBI($row, $gameState, $visBoxScoreStats, $homeBoxScoreStats);

                // // move Base Runners
                // moveBaseRunners($row, $outcome, $gameState, $visBoxScoreStats, $homeBoxScoreStats);

                // // check for SUB
                // checkForSub($row, $gameState, $visBoxScoreStats, $homeBoxScoreStats);

            }
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
    $count = 0;
    foreach($visBoxScoreStats as $vs) {
        ?>
        <div class="boxscore-name boxscore-cell <?php if($vs->status === 'sub') { echo 'boxscore-sub'; } ?>"><?php echo getPlayerName($link, $vs->playerid) . ', ' . $vs->playerpos ?></div>
        <div class="boxscore-cell boxscore-center"><?php echo $vs->batter_stat_ab ?></div>
        <div class="boxscore-cell boxscore-center"><?php echo $vs->batter_stat_r ?></div>
        <div class="boxscore-cell boxscore-center"><?php echo $vs->getHits() ?></div>
        <div class="boxscore-cell boxscore-center"><?php echo $vs->batter_stat_rbi ?></div>
        <div class="boxscore-cell boxscore-center"><?php echo $vs->batter_stat_bb ?></div>
        <div class="boxscore-cell boxscore-center"><?php echo $vs->batter_stat_k ?></div>
        <div class="boxscore-cell boxscore-cell-end boxscore-center"><?php echo $vs->batter_stat_lob ?></div>
        <?php
            $count++;
    }
?>
</div>
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
            echo '2B: ';
            $found = false;
            foreach($visBoxScoreStats as $vs) {
                if($vs->batter_stat_2b > 0) {

                    if($found) {
                        echo '; ';
                    }

                    echo getPlayerName($link, $vs->playerid);

                    if($vs->batter_stat_2b > 1) {
                        echo '(' . $vs->batter_stat_2b . ')';
                    }

                    $found = true;

                }
            }
        }
        // triples
        if($total_triples > 0) {
            echo '<br>3B: ';
            $found = false;
            foreach($visBoxScoreStats as $vs) {
                if($vs->batter_stat_3b > 0) {

                    if($found) {
                        echo '; ';
                    }

                    echo getPlayerName($link, $vs->playerid);
                    
                    if($vs->batter_stat_3b > 1) {
                        echo '(' . $vs->batter_stat_3b . ')';
                    }

                    $found = true;

                }
            }
        }

        // homeruns
        if($total_homeruns > 0) {
            echo '<br>HR: ';
            $found = false;
            foreach($visBoxScoreStats as $vs) {
                if($vs->batter_stat_hr > 0) {

                    if($found) {
                        echo '; ';
                    }

                    echo getPlayerName($link, $vs->playerid);
                    
                    if($vs->batter_stat_hr > 1) {
                        echo '(' . $vs->batter_stat_hr . ')';
                    }

                    $found = true;

                }
            }
        }

        // intentional walks
        $found = false;

        foreach($visBoxScoreStats as $vs) {
            if($vs->batter_stat_ibb > 0) {

                if($found) {
                    echo '; ';
                }

                if(!$found) {
                    echo '<br>IBB: ';
                }

                echo getPlayerName($link, $vs->playerid);
                
                if($vs->batter_stat_ibb > 1) {
                    echo '(' . $vs->batter_stat_ibb . ')';
                }

                $found = true;

            }
        }

        // sac flies
        if($total_sacflies > 0) {
            echo '<br>SF: ';
            $found = false;

            foreach($visBoxScoreStats as $vs) {
                if($vs->batter_stat_sf > 0) {
                    if($found) {
                        echo '; ';
                    }

                    echo getPlayerName($link, $vs->playerid);
                    
                    if($vs->batter_stat_sf > 1) {
                        echo '(' . $vs->batter_stat_sf . ')';
                    }

                    $found = true;

                }
            }
        }

        // sac hits
        if($total_sachits > 0) {
            echo '<br>SH: ';
            $found = false;
            foreach($visBoxScoreStats as $vs) {
                if($vs->batter_stat_sh > 0) {

                    if($found) {
                        echo '; ';
                    }

                    echo getPlayerName($link, $vs->playerid);
                    
                    if($vs->batter_stat_sh > 1) {
                        echo '(' . $vs->batter_stat_sh . ')';
                    }

                    $found = true;

                }
            }
        }

        // hbp
        if($total_hbp > 0) {
            echo '<br>HBP: ';
            $found = false;
            foreach($visBoxScoreStats as $vs) {
                if($vs->batter_stat_hbp > 0) {
                    if($found) {
                        echo '; ';
                    }

                    echo getPlayerName($link, $vs->playerid);
                    
                    if($vs->batter_stat_hbp > 1) {
                        echo '(' . $vs->batter_stat_hbp . ')';
                    }

                    $found = true;

                }
            }
        }

        // total bases
        if($team_total_bases > 0) {
            echo '<br>TB: ';

            usort($total_bases, function($first, $second) {
                return $first->bases < $second->bases;
            });
            foreach($total_bases as $tb) {
                if($tb->bases > 0) {
                    if($foundbases) {
                        echo '; ';
                    }

                    echo getPlayerName($link, $tb->playerid);

                    if($tb->bases > 1) {
                        echo ' ' . $tb->bases;
                    }

                    $foundbases = true;

                }
            }
        }

        // GDP
        if($gameState->visTeamGDP > 0) {
            echo '<br>GIDP: ';
            foreach($visBoxScoreStats as $vs) {
                if($vs->batter_stat_gidp > 0) {
                    if($foundGIDP) {
                        echo '; ';
                    }
    
                    echo getPlayerName($link, $vs->playerid);
    
                    if($vs->batter_stat_gidp > 1) {
                        echo ' ' . $vs->batter_stats_gidp;
                    }
    
                    $foundGIDP = true;
    
                }
            }
        }

        // total rbi
        if($team_total_rbi > 0) {
            echo '<br>RBI: ';

            usort($total_rbi, function($first, $second) {
                return $first->rbi < $second->rbi;
            });
            foreach($total_rbi as $tr) {
                if($tr->rbi > 0) {
                    if($foundrbi) {
                        echo '; ';
                    }

                    echo getPlayerName($link, $tr->playerid);

                    if($tr->rbi > 1) {
                        echo ' ' . $tr->rbi;
                    }

                    $foundrbi = true;

                }
            }
        }

        // total 2-out rbi
        if($team_total_rbi_2_out > 0) {
            echo '<br>2-out RBI: ';

            usort($team_2_out, function($first, $second) {
                return $first->rbi < $second->rbi;
            });

            $foundrbi2 = false;

            foreach($team_2_out as $tr) {
                if($tr->rbi > 0) {
                    if($foundrbi2) {
                        echo '; ';
                    }

                    echo getPlayerName($link, $tr->playerid);

                    if($tr->rbi > 1) {
                        echo ' ' . $tr->rbi;
                    }

                    $foundrbi2 = true;

                }
            }
        }

        // Team LOB
        echo '<br>Team LOB: ' . $gameState->visTeamLOB;

        // Runners in scoring position hits per at-bat
        if($gameState->risp_vis_ab > 0) {
            echo '<br>With RISP: ' . $gameState->risp_vis_h . ' for ' . $gameState->risp_vis_ab . '.';
        }

        echo '<br>Fielding:';

        if($gameState->vis_double_plays_turned > 0) {
            echo '<br>DP: ' . $gameState->vis_double_plays_turned;
        }

        // errors
        if($gameState->visErrors > 0) {
            echo '<br>E: ';
            foreach($visBoxScoreStats as $vs) {
                if($vs->fielder_stat_error > 0) {
                    if($founderror) {
                        echo ', ';
                    }

                    echo getPlayerName($link, $vs->playerid) . '(' . $vs->fielder_stat_error . ')';

                    $founderror = true;

                }
            }
        }

        // Baserunning

        // stolen bases
        if($gameState->visStolenBases > 0) {
            echo '<br>SB: ';

            $team_stolen_bases = [];

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
                        echo '; ';
                    }
                    echo getPlayerName($link, $tsb->playerid);
                    if($tsb->bases > 1) {
                        echo ' ' . $tsb->bases;
                    }
                    $foundbases = true;
                }
            }  
        }

        // caught stealing
        if($gameState->visCaughtStealing > 0) {
            echo '<br>CS: ';

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
                        echo '; ';
                    }
                    echo getPlayerName($link, $tcs->playerid);
                    if($tcs->bases > 1) {
                        echo ' ' . $tcs->bases;
                    }
                    $foundbases = true;
                }
            }  
        }

    ?>
</div>