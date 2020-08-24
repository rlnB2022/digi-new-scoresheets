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

        public $visTeamLOB = 0;
        public $homeTeamLOB = 0;

        public $visSubCount = 0;
        public $homeSubCount = 0;

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

        public $vis_double_plays_turned = 0;
        public $home_double_plays_turned = 0;

        public $runneron1st = 'None';
        public $runneron2nd = 'None';
        public $runneron3rd = 'None';

        public function clearRunners() {
            $this->runneron1st = 'None';
            $this->runneron2nd = 'None';
            $this->runneron3rd = 'None';
        }
    }

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
    $sql = "SELECT * FROM EVENTS WHERE GAMEID = '" . $gameid . "'";

    $result = mysqli_query($link, $sql);

    $resultCheck = mysqli_num_rows($result);

    if($resultCheck > 0) {
        while ($row = mysqli_fetch_assoc($result)) { 
            // clear baserunners if new team batting and assign team LOB
            checkEndOfInning($row, $gameState);

            // strikeouts
            if($row['OUTCOME'][0]==='K') {
                // $message = (($row['PLAYERID'] . ' strikes out.'));
                // showMessage($message);
                getStrikeouts($row, $gameState, $visBoxScoreStats, $homeBoxScoreStats);
            }

            // // walks, not wild pitches
            if($row['OUTCOME'][0]==='W' && $row['OUTCOME'][1] !== 'P') {
                // $message = ($row['PLAYERID'] . ' gets a walk.');
                // showMessage($message);
                getWalks($row, 'W', $gameState, $visBoxScoreStats, $homeBoxScoreStats);
            }

            // // intentional walks
            if(strpos($row['OUTCOME'], 'IW') !== false) {
                // $message = ($row['PLAYERID'] . ' gets an intentional walk.');
                // showMessage($message);
                getWalks($row, 'IW', $gameState, $visBoxScoreStats, $homeBoxScoreStats);
            }

            // // singles, not stolen bases
            if($row['OUTCOME'][0]==='S' and $row['OUTCOME'][1] !== 'B') {
                // $message = ($row['PLAYERID'] . ' gets a single.');
                // showMessage($message);
                getSingles($row, $gameState, $visBoxScoreStats, $homeBoxScoreStats);
            }

            // // doubles, not defensive interference
            if($row['OUTCOME'][0]==='D' and $row['OUTCOME'][1] !== 'I') {
                // $message = ($row['PLAYERID'] . ' gets a double.');
                // showMessage($message);
                getDoubles($row, $gameState, $visBoxScoreStats, $homeBoxScoreStats);
            }

            // // triples
            if($row['OUTCOME'][0]==='T') {
                // $message = ($row['PLAYERID'] . ' gets a triple.');
                // showMessage($message);
                getTriples($row, $gameState, $visBoxScoreStats, $homeBoxScoreStats);
            }

            // // homeruns
            if(strpos($row['OUTCOME'], 'HR') !== false) {
                // $message = ($row['PLAYERID'] . ' gets a homerun.');
                // showMessage($message);
                getHomeRuns($row, $gameState, $visBoxScoreStats, $homeBoxScoreStats);
            }

            // // wild pitch
            if(strpos($row['OUTCOME'], 'WP') !== false) {
                // $message = 'WILD PITCH!';
                // showMessage($message);
                getWildPitch($row, $gameState, $visBoxScoreStats, $homeBoxScoreStats, $visPitcherBoxScoreStats, $homePitcherBoxScoreStats);
            }

            // // hit by pitch
            if(strpos($row['OUTCOME'], 'HP') !== false) {
                // $message = $row['PLAYERID'] . ' is HBP.';
                // showMessage($message);
                getHitByPitch($row, $gameState, $visBoxScoreStats, $homeBoxScoreStats);
            }

            // // get steals
            if(strpos($row['OUTCOME'], 'SB') !== false) {
                // $message = 'STOLEN BASE';
                // showMessage($message);
                getStolenBase($row, $gameState, $visBoxScoreStats, $homeBoxScoreStats);
            }

            // // get caught stealing
            if(strpos($row['OUTCOME'], 'CS') !== false) {
                // $message = 'CAUGHT STEALING';
                // showMessage($message);
                getCaughtStealing($row, $gameState, $visBoxScoreStats, $homeBoxScoreStats);
            }

            // // ERRORS
            if(strpos($row['OUTCOME'], 'E') !== false) {
                getErrors($row, $gameState, $visBoxScoreStats, $homeBoxScoreStats, $visPitcherBoxScoreStats, $homePitcherBoxScoreStats);
            }

            // // POPUPS, NOT ERRORS
            if($row['OUTCOME'][0] === '1' || $row['OUTCOME'][0] === '2' || $row['OUTCOME'][0] === '3' || $row['OUTCOME'][0] === '4' || $row['OUTCOME'][0] === '5' || $row['OUTCOME'][0] === '6' || $row['OUTCOME'][0] === '7' || $row['OUTCOME'][0] === '8' || $row['OUTCOME'][0] === '9' || strpos($row['OUTCOME'], 'FC') !== false) {
                getOut($row, $gameState, $visBoxScoreStats, $homeBoxScoreStats);
            }

            // // check for SUB
            checkForSub($row, $gameState, $visBoxScoreStats, $homeBoxScoreStats);
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
                    echo getPlayerName($link, $vs->playerid) . '(' . $vs->batter_stat_2b . ')';
                    $found = true;
                }
            }
        }
        // triples
        if($total_triples > 0) {
            echo '3B: ';
            $found = false;
            foreach($visBoxScoreStats as $vs) {
                if($vs->batter_stat_3b > 0) {
                    if($found) {
                        echo '; ';
                    }
                    echo getPlayerName($link, $vs->playerid) . '(' . $vs->batter_stat_3b . ')';
                    $found = true;
                }
            }
        }
        // homeruns
        if($total_homeruns > 0) {
            echo '<br>';
            echo 'HR: ';
            $found = false;
            foreach($visBoxScoreStats as $vs) {
                if($vs->batter_stat_hr > 0) {
                    if($found) {
                        echo '; ';
                    }
                    echo getPlayerName($link, $vs->playerid) . '(' . $vs->batter_stat_hr . ')';
                    $found = true;
                }
            }
        }

        // sac flies
        if($total_sacflies > 0) {
            echo '<br>';
            echo 'SF: ';
            $found = false;
            foreach($visBoxScoreStats as $vs) {
                if($vs->batter_stat_sf > 0) {
                    if($found) {
                        echo '; ';
                    }
                    echo getPlayerName($link, $vs->playerid) . '(' . $vs->batter_stat_sf . ')';
                    $found = true;
                }
            }
        }

        // sac hits
        if($total_sachits > 0) {
            echo '<br>';
            echo 'SH: ';
            $found = false;
            foreach($visBoxScoreStats as $vs) {
                if($vs->batter_stat_sh > 0) {
                    if($found) {
                        echo '; ';
                    }
                    echo getPlayerName($link, $vs->playerid) . '(' . $vs->batter_stat_sh . ')';
                    $found = true;
                }
            }
        }

        // hbp
        if($total_hbp > 0) {
            echo '<br>';
            echo 'HBP: ';
            $found = false;
            foreach($visBoxScoreStats as $vs) {
                if($vs->batter_stat_hbp > 0) {
                    if($found) {
                        echo '; ';
                    }
                    echo getPlayerName($link, $vs->playerid) . '(' . $vs->batter_stat_hbp . ')';
                    $found = true;
                }
            }
        }

        // total bases
        if($team_total_bases > 0) {
            echo '<br>';
            echo 'TB: ';

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

        // total rbi
        if($team_total_rbi > 0) {
            echo '<br>';
            echo 'RBI: ';

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
            echo '<br>';
            echo '2-out RBI: ';

            usort($team_2_out, function($first, $second) {
                return $first->rbi < $second->rbi;
            });
            foreach($team_2_out as $tr) {
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

        // Team LOB
        echo '<br>';
        echo 'Team LOB: ' . $gameState->visTeamLOB;

        // Runners in scoring position hits per at-bat
        if($gameState->risp_vis_ab > 0) {
            echo '<br>';
            echo 'With RISP: ' . $gameState->risp_vis_h . ' for ' . $gameState->risp_vis_ab . '.';
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