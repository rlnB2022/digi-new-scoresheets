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

    $visLineup = [];
    $visDefense = [];
    $homeLineup = [];
    $homeDefense = [];
    $visSubCount = 0; // used for placement of players substituted into box score
    $homeSubCount = 0; // used for placement of players substituted into box score

    $outs_in_the_inning = 0;

    $total_runners_on_base = 0;
    $visTeamLOB = 0;
    $homeTeamLOB = 0;

    class TotalBases {
        public $playerid;
        public $bases;
    }

    class TotalRBI {
        public $playerid;
        public $rbi;
    }

     class GameStatus {
        public $runneron1st = 'None';
        public $runneron2nd = 'None';
        public $runneron3rd = 'None';

        public function clearRunners() {
            $this->runneron1st = 'None';
            $this->runneron2nd = 'None';
            $this->runneron3rd = 'None';
        }
    };

    $runnersOnBase = new GameStatus();

    $gameTeamAtBat = 0;

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
        array_push($visDefense, $row['pos' . ($i + 1)]);
        $batterS = new playerStat();
        $batterS->playerid = $visLineup[$i];
        $batterS->playerpos = $visDefense[$i];
        $batterS->status = 'starter';
        array_push($visBoxScoreStats, $batterS);
    }
  }

    // GET STARTING PITCHERS
    $sql = "SELECT * FROM GAMELOGS WHERE GAMEID = '" . $gameid . "'";

    $result = mysqli_query($link, $sql);

    $resultCheck = mysqli_num_rows($result);

    if($resultCheck > 0) {
        $row = mysqli_fetch_assoc($result);
        array_push($visPitcherBoxScoreStats, $row['visstarterid']);
        array_push($homePitcherBoxScoreStats, $row['homestarterid']);
    }

    // get home lineup next
    $sql = "SELECT * FROM HOMELINEUPS WHERE GAMEID = '" . $gameid . "'";

    $result = mysqli_query($link, $sql);

    $resultCheck = mysqli_num_rows($result);

    if($resultCheck > 0) {
      $row = mysqli_fetch_assoc($result);

      for($i = 0; $i < 9; $i++) {
        array_push($homeLineup, $row['batter' . ($i + 1)]);
        array_push($homeDefense, $row['pos' . ($i + 1)]);
        $batterS = new playerStat();
        $batterS->playerid = $homeLineup[$i];
        $batterS->playerpos = $homeDefense[$i];
        $batterS->status = 'starter';
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
            // clear baserunners if new team batting and assign team LOB
            showRunners();
            checkEndOfInning($row);

            // strikeouts
            checkForStrikeouts($row);

            // // walks, not wild pitches
            checkForWalks($row);

            // // intentional walks
            checkForIntentionalWalks($row);

            // // singles, not stolen bases
            checkForSingles($row);

            // // doubles, not defensive interference
            checkForDoubles($row);

            // // triples
            checkForTriples($row);

            // // homeruns
            checkForHomeruns($row);

            // // wild pitch
            checkForWildPitch($row);

            // // hit by pitch
            checkForHitByPitch($row);

            // // get steals
            checkForStolenBase($row);

            // // get caught stealing
            checkForCaughtStealing($row);

            // // ERRORS
            checkForErrors($row);

            // // POPUPS, NOT ERRORS
            checkForOuts($row);

            // // check for SUB
            checkForSub($row);
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
        <div class="boxscore-cell boxscore-center"><?php echo $vs->stat_ab ?></div>
        <div class="boxscore-cell boxscore-center"><?php echo $vs->stat_r ?></div>
        <div class="boxscore-cell boxscore-center"><?php echo $vs->getHits() ?></div>
        <div class="boxscore-cell boxscore-center"><?php echo $vs->stat_rbi ?></div>
        <div class="boxscore-cell boxscore-center"><?php echo $vs->stat_bb ?></div>
        <div class="boxscore-cell boxscore-center"><?php echo $vs->stat_k ?></div>
        <div class="boxscore-cell boxscore-cell-end boxscore-center"><?php echo $vs->stat_lob ?></div>
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
            $total_doubles += $vs->stat_2b;
        }

        foreach($visBoxScoreStats as $vs) {
            // add up all triples
            $total_triples += $vs->stat_3b;
        }

        foreach($visBoxScoreStats as $vs) {
            // add up all homeruns
            $total_homeruns += $vs->stat_hr;
        }

        foreach($visBoxScoreStats as $vs) {
            // add up all sac flies
            $total_sacflies += $vs->stat_sf;
        }

        foreach($visBoxScoreStats as $vs) {
            // add up all sac hits
            $total_sachits += $vs->stat_sh;
        }

        foreach($visBoxScoreStats as $vs) {
            // add up all hbp
            $total_hbp += $vs->stat_hbp;
        }

        foreach($visBoxScoreStats as $vs) {
            // total bases
            $bases = $vs->stat_1b + ($vs->stat_2b * 2) + ($vs->stat_3b * 3) + ($vs->stat_hr * 4);
            $team_total_bases += $bases;

            $newTotalBases = new TotalBases();

            $newTotalBases->playerid = $vs->playerid;
            $newTotalBases->bases = $bases;

            array_push($total_bases, $newTotalBases);
        }

        foreach($visBoxScoreStats as $vs) {
            // RBI
            $team_total_rbi += $vs->stat_rbi;
            $newTotalRBI = new TotalRBI();
            $newTotalRBI->playerid = $vs->playerid;
            $newTotalRBI->rbi = $vs->stat_rbi;

            array_push($total_rbi, $newTotalRBI);
        }

        foreach($visBoxScoreStats as $vs) {
            // 2-out RBI
            $team_total_rbi_2_out += $vs->stat_rbi_2_out;
            $newTotalRBI = new TotalRBI();
            $newTotalRBI->playerid = $vs->playerid;
            $newTotalRBI->rbi = $vs->stat_rbi_2_out;

            array_push($team_2_out, $newTotalRBI);
        }

        // doubles
        if($total_doubles > 0) {
            echo '2B: ';
            $found = false;
            foreach($visBoxScoreStats as $vs) {
                if($vs->stat_2b > 0) {
                    if($found) {
                        echo '; ';
                    }
                    echo getPlayerName($link, $vs->playerid) . '(' . $vs->stat_2b . ')';
                    $found = true;
                }
            }
        }
        // triples
        if($total_triples > 0) {
            echo '3B: ';
            $found = false;
            foreach($visBoxScoreStats as $vs) {
                if($vs->stat_3b > 0) {
                    if($found) {
                        echo '; ';
                    }
                    echo getPlayerName($link, $vs->playerid) . '(' . $vs->stat_3b . ')';
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
                if($vs->stat_hr > 0) {
                    if($found) {
                        echo '; ';
                    }
                    echo getPlayerName($link, $vs->playerid) . '(' . $vs->stat_hr . ')';
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
                if($vs->stat_sf > 0) {
                    if($found) {
                        echo '; ';
                    }
                    echo getPlayerName($link, $vs->playerid) . '(' . $vs->stat_sf . ')';
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
                if($vs->stat_sh > 0) {
                    if($found) {
                        echo '; ';
                    }
                    echo getPlayerName($link, $vs->playerid) . '(' . $vs->stat_sh . ')';
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
                if($vs->stat_hbp > 0) {
                    if($found) {
                        echo '; ';
                    }
                    echo getPlayerName($link, $vs->playerid) . '(' . $vs->stat_hbp . ')';
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
        echo 'Team LOB: ' . $visTeamLOB;
    ?>
</div>