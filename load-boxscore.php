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

<h1 class="section-separator">VISITORS - BATTING</h1>

<?php
    getLineup($link, $gameid, $gameState, $visBoxScoreStats, 'vis');
    getLineup($link, $gameid, $gameState, $homeBoxScoreStats, 'home');
    getStartingPitchers($link, $gamedate, $hometeam, $gamenum, $visPitcherBoxScoreStats, $homePitcherBoxScoreStats);
?>
