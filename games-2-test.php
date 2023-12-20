<?php
    // Include config file
    // include "configtest.php";
    include("$_SERVER[DOCUMENT_ROOT]/../configtest.php");
    include "functions.php";
?>
<!DOCTYPE html>
<html class="no-js">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Games</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous"/>
        <link href="https://fonts.googleapis.com/css2?family=Abril+Fatface&family=Roboto:wght@900&display=swap" rel="stylesheet">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script>
            $(document).ready(function() {
                let selectedDate = $("#mydate").val();
                let month = 0;

                $('.month-container').on('click', function() {
                    if($('.month-dropdown-content').hasClass('show-dropdown-content')) {
                        $('.month-dropdown-content').removeClass('show-dropdown-content');
                    }
                    else {
                        $('.month-dropdown-content').addClass('show-dropdown-content');
                    }
                });

                $('.year-container').on('click', function() {
                    if($('.year-dropdown-content').hasClass('show-dropdown-content')) {
                        $('.year-dropdown-content').removeClass('show-dropdown-content');
                    }
                    else {
                        $('.year-dropdown-content').addClass('show-dropdown-content');
                    }
                });

                $('.btn-scoresheet').on('click', function(e) {
                    thisGameId = $("#mydate").val();
                    thisTeam = $(e.target).data("team");
                    console.log(e.target);
                    thisGameNum = $(e.target).data("gamenum");

                    $('#gridgames').css('filter', 'blur(4px)');
                    // show lineups
                    $("#mylineups").load("load-lineups.php", {
                        gameid: thisGameId,
                        team: thisTeam,
                        gamenum: thisGameNum
                    }, function() {
                        $('.date-selector').css('display', 'none');
                        document.documentElement.style.overflow = "hidden";
                        $(window).scrollTop(0);
                        $("#mylineups").css('display', 'grid');

                        // populate scoresheet
                        $('#footer-date').text($('.current-date').text());

                        // get teams and replace headers
                        $('#vis-team-name').text($('.lineup-visitor-team-button').text());
                        $('#home-team-name').text($('.lineup-home-team-button').text());

                        const visLineupNames = document.querySelectorAll('.lineup-visitors .lineup-name');
                        const homeLineupNames = document.querySelectorAll('.lineup-home .lineup-name');

                        const visPos = document.querySelectorAll('.lineup-visitors .lineup-pos');
                        const homePos = document.querySelectorAll('.lineup-home .lineup-pos');

                        for(let i = 0; i < 9; i++) {
                            $('#vBatter' + (i+1) + 'Name').text(visLineupNames[i].textContent);
                            $('#hBatter' + (i+1) + 'Name').text(homeLineupNames[i].textContent);

                            $('#vPos' + (i+1)).text(visPos[i].textContent);
                            $('#hPos' + (i+1)).text(homePos[i].textContent);
                        }

                        $('#vPitcherName').text($('#visitors-starting-pitcher').text());
                        $('#hPitcherName').text($('#home-starting-pitcher').text());

                        $('.scoreboard-team1').text($('.lineup-visitor-team-button').text());
                        $('.scoreboard-team2').text($('.lineup-home-team-button').text());
                    });
                });

                $('.year-dropdown-content a').on('click', function() {
                    elem = $(this);

                    // if element is not already active
                    if(!$(this).hasClass("active-year")) {

                        // remove active class
                        const elems = document.querySelectorAll('.year-dropdown-content a');

                        elems.forEach(name => {
                            if(name.classList.contains('active-year')) {
                                name.classList.remove('active-year');
                            }
                        });

                        $(this).addClass("active-year");

                        // change month-name text
                        const newYear = parseInt($(elem).text());
                        $('.year-name').html(newYear);

                        // add new months to dropdown
                        $("#master-container").load("load-date-selector-days.php", {
                            newDate: newYear
                        },function() {
                            const months = document.querySelectorAll('.day-grid-container');

                            let monthArr = [];

                            months.forEach(e => {
                                monthArr.push(parseInt(e.dataset.monthDiv));
                            });

                            $(".month-dropdown-content").load("load-new-months.php", {
                                months: monthArr
                            }, function() {
                                monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
                                let monthName = $('.month-name');
                                monthName.text(monthNames[monthArr[0]-1]);

                                addDayGridContainers();

                                tempMonth = monthArr[0] < 10 ? '0' + monthArr[0] : monthArr[0];

                                thisDay = $('#change-day').val();
                                tempDay = thisDay < 10 ? '0' + thisDay : thisDay;

                                updateMonthDayYear(newYear + tempMonth + tempDay);
                            });
                        });
                    }
                    
                });

                $("#mydate").on('change', function() {
                    // setup new date
                    selectedDate = $("#mydate").val();

                    $("#gridgames").load("load-games.php", {
                        newDate: selectedDate
                    },function() {
                        $(window).scrollTop(0);
                    });
                });

                $("#gridgames").load("load-games.php", {
                        newDate: selectedDate
                    },function() {
                        $(window).scrollTop(0);
                });
            });
        </script>
        <link rel="stylesheet" href="css/style-test-main.css">
        <link rel="stylesheet" href="css/games-2-test-scoresheet.css">
    </head>
    <body>
        <?php
            $newArray = array();
            $sql = "SELECT DISTINCT DATE FROM GAMELOGS_MAIN WHERE SUBSTRING(DATE,1,4) = (SELECT MAX(SUBSTRING(DATE,1,4)) FROM GAMELOGS_MAIN)";
            $result = mysqli_query($link, $sql);
            $resultCheck = mysqli_num_rows($result);

            if($resultCheck > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $newArray[] = $row['DATE'];
                }
            }

            $curDate = min($newArray);

            $year= substr($curDate,0,4);
            $month = substr($curDate,4,2);
            $maxMonth = substr(max(...$newArray),4,2);
            $day = (int)substr($curDate,6,2);
            if(strlen($day) == 1) {
                $day = "0" . $day;
            }

        ?>

        <div id="myboxscore" class="boxscore"></div>

        <div id="mylineups">

        </div>

        <div class="date-selector">
            <div class="current-date-container"><div class="current-date"><?php echo getCurrentMonthFull($month - 1) . " " . $day . ", " . $year ?></div></div>
            <div class="date-selector-container">
                <div class="month-year-container">
                    <div class="month-container"><p class="month-name"><?php echo getCurrentMonthFull($month - 1); ?></p>
                        <div class="month-dropdown-content">
                            <?php
                                for($i = (int)$month; $i <= $maxMonth; $i++) {
                                    ?><a href="#" data-month="<?php echo $i; ?>" class="<?php if($i === (int)$month) { echo 'active-month'; };?>" > <?php echo getCurrentMonthFull($i - 1); ?></a><div class="a-underline"></div>
                                <?php
                                }
                            ?>
                        </div>
                    </div>
                    <div class="year-container"><p class="year-name"><?php echo $year; ?></p>
                        <div class="year-dropdown-content">
                            <?php 
                                for($i = $year; $i >= 1906; $i--) {
                                    ?>
                                    <a href="#" class="<?php if($i === $year) { echo 'active-year'; };?>" > <?php echo $i; ?></a><div class="a-underline"></div>
                                <?php
                                }
                            ?>
                        </div>
                    </div>
                </div>

                <div id="change-day" class="day-current"><input type="hidden" value="<?php echo $day; ?>"></input></div>
                    <?php
                        $daysInMonth = [0, 0, 0, 31, 30, 31, 30, 31, 31, 30, 31, 30];
                        $curDay = 0;
                        $curMonth = (int)$month;
                        $curMaxMonth = (int)$maxMonth;

                        ?>
                        <div id="master-container" class="master-day-grid-container">
                        <?php

                        while($curMonth <= $curMaxMonth) {
                            ?>
                            <div data-month-div="<?php echo $curMonth; ?>" class="day-grid-container
                            <?php
                                if($curMonth > (int)$month) { echo "hidden-days";}; ?>">
                            <?php

                            for($j = 1; $j <= $daysInMonth[$curMonth]; $j++) {

                                    // add day to grid
                                    ?>
                                    <div data-grid-item="<?php echo $j; ?>" class="grid-item
                                        <?php
                                            if($j != (int)substr($newArray[$curDay], 6, 2)) {
                                                echo " not-available";
                                            }
                                            else {
                                                // if day is first day of month, make active
                                                if($j === (int)$day) {
                                                    echo " active";
                                                }

                                                $curDay++;
                                            }
                                        ?>
                                        ">
                                        <?php
                                            echo $j;
                                        ?>
                                    </div>
                                    <?php
                                    
                            }
                            // increment curMonth
                            $curMonth++;
                            ?>
                            </div>
                            <?php
                        }
                    ?>
                </div>
                </div>
                <div class="button-container">

                    <div class="ok-container">
                        <div class="ok-button">OK</div>
                    </div>
                    <div class="cancel-container">
                        <div class="cancel-button">Cancel</div>
                    </div>
                </div>
            </div>
        </div>
            <div class="main">
			<div class="container-scoreboard">
				<!-- SCOREBOARD -->
				<div id="footer-date" class="headGrid scoreboard-team-header"></div>
				<div class="headGrid grid-border-left-thick">1</div>
				<div class="headGrid grid-border-left">2</div>
				<div class="headGrid grid-border-left">3</div>
				<div class="headGrid grid-border-left">4</div>
				<div class="headGrid grid-border-left">5</div>
				<div class="headGrid grid-border-left">6</div>
				<div class="headGrid grid-border-left">7</div>
				<div class="headGrid grid-border-left">8</div>
				<div class="headGrid grid-border-left">9</div>
				<div class="headGrid grid-border-left">10</div>
				<div class="headGrid grid-border-left">11</div>
				<div class="headGrid grid-border-left">12</div>
				<div class="headGrid grid-border-left-thick">R</div>
				<div class="headGrid grid-border-left">H</div>
				<div class="headGrid grid-border-left grid-border-right-thick">E</div>

				<div id="visTeam" class="scoreboard-team1"></div>
				<div
					id="vLine1"
					class="grid-border-left-thick grid-border-bottom center-text2"
				></div>
				<div
					id="vLine2"
					class="grid-border-left grid-border-bottom center-text2"
				></div>
				<div
					id="vLine3"
					class="grid-border-left grid-border-bottom center-text2"
				></div>
				<div
					id="vLine4"
					class="grid-border-left grid-border-bottom center-text2"
				></div>
				<div
					id="vLine5"
					class="grid-border-left grid-border-bottom center-text2"
				></div>
				<div
					id="vLine7"
					class="grid-border-left grid-border-bottom center-text2"
				></div>
				<div
					id="vLine6"
					class="grid-border-left grid-border-bottom center-text2"
				></div>
				<div
					id="vLine8"
					class="grid-border-left grid-border-bottom center-text2"
				></div>
				<div
					id="vLine9"
					class="grid-border-left grid-border-bottom center-text2"
				></div>
				<div
					id="vLine10"
					class="grid-border-left grid-border-bottom center-text2"
				></div>
				<div
					id="vLine11"
					class="grid-border-left grid-border-bottom center-text2"
				></div>
				<div
					id="vLine12"
					class="grid-border-left grid-border-bottom center-text2"
				></div>
				<div
					id="vLineR"
					class="grid-border-left-thick grid-border-bottom center-text2"
				></div>
				<div
					id="vLineH"
					class="grid-border-left grid-border-bottom center-text2"
				></div>
				<div
					id="vLineE"
					class="grid-border-left grid-border-right-thick grid-border-bottom center-text2"
				></div>

				<div id="homeTeam" class="scoreboard-team2 grey-cell last-row"></div>
				<div
					id="hLine1"
					class="grid-border-left-thick grey-cell center-text2 faded-color-bottom last-row"
				></div>
				<div
					id="hLine2"
					class="grid-border-left grey-cell center-text2 faded-color-bottom last-row"
				></div>
				<div
					id="hLine3"
					class="grid-border-left grey-cell center-text2 faded-color-bottom last-row"
				></div>
				<div
					id="hLine4"
					class="grid-border-left grey-cell center-text2 faded-color-bottom last-row"
				></div>
				<div
					id="hLine5"
					class="grid-border-left grey-cell center-text2 faded-color-bottom last-row"
				></div>
				<div
					id="hLine6"
					class="grid-border-left grey-cell center-text2 faded-color-bottom last-row"
				></div>
				<div
					id="hLine7"
					class="grid-border-left grey-cell center-text2 faded-color-bottom last-row"
				></div>
				<div
					id="hLine8"
					class="grid-border-left grey-cell center-text2 faded-color-bottom last-row"
				></div>
				<div
					id="hLine9"
					class="grid-border-left grey-cell center-text2 faded-color-bottom last-row"
				></div>
				<div
					id="hLine10"
					class="grid-border-left grey-cell center-text2 faded-color-bottom last-row"
				></div>
				<div
					id="hLine11"
					class="grid-border-left grey-cell center-text2 faded-color-bottom last-row"
				></div>
				<div
					id="hLine12"
					class="grid-border-left grey-cell center-text2 faded-color-botto last-row"
				></div>
				<div
					id="hLineR"
					class="grid-border-left-thick grey-cell center-text2 faded-color-bottom last-row"
				></div>
				<div
					id="hLineH"
					class="grid-border-left grey-cell center-text2 faded-color-bottom last-row"
				></div>
				<div
					id="hLineE"
					class="grid-border-left grid-border-right-thick grey-cell center-text2 faded-color-bottom last-row"
				></div>
			</div>
			<div class="container-lineup">
				<!-- HEADER -->
				<div id="vis-team-name" class="headGrid grid-border-left-thick">
					2023 Los Angeles (A)
				</div>
				<div class="headGrid grid-border-left-thick">POS</div>
				<div class="headGrid grid-border-left">RNG</div>
				<div class="headGrid grid-border-left">ERR</div>
				<div class="headGrid grid-border-left">ARM</div>
				<div class="headGrid grid-border-left">RUN</div>
				<div class="headGrid grid-border-left-thick">1</div>
				<div class="headGrid grid-border-left">2</div>
				<div class="headGrid grid-border-left">3</div>
				<div class="headGrid grid-border-left">4</div>
				<div class="headGrid grid-border-left">5</div>
				<div class="headGrid grid-border-left">6</div>
				<div class="headGrid grid-border-left">7</div>
				<div class="headGrid grid-border-left">8</div>
				<div class="headGrid grid-border-left">9</div>
				<div class="headGrid grid-border-left">10</div>
				<div class="headGrid grid-border-left">11</div>
				<div class="headGrid grid-border-left">12</div>
				<div class="headGrid grid-border-left">13</div>
				<div class="headGrid grid-border-left">14</div>
				<div class="headGrid grid-border-left grid-border-right-thick">15</div>

				<!-- 1ST ROW -->
				<div class="vBatter normal-cell-left">
					<p class="lineupNumbersLeft">1</p>
					<p id="vBatter1Name" class="adjustName">Player 1</p>
				</div>
				<div class="vBatter normal-cell-left center-text">
					<p id="vPos1" class="adjustPos">CF</p>
				</div>
				<div class="normal-cell"></div>
				<div class="normal-cell"></div>
				<div class="normal-cell"></div>
				<div class="normal-cell-before-batter-result"></div>
				<div class="batter-result1 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result2 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result3 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result4 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result5 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result6 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result7 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result8 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result9 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result10 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result11 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result12 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result13 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result14 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result15 grid-border-right-thick batter-box">
					<p class="diamond-test"></p>
				</div>
				<!-- BACKUP 1ST ROW -->
				<div class="normal-cell-left grey-cell last-row"></div>
				<div class="normal-cell-left grey-cell last-row"></div>
				<div class="normal-cell grey-cell last-row"></div>
				<div class="normal-cell grey-cell last-row"></div>
				<div class="normal-cell grey-cell last-row"></div>
				<div class="normal-cell-before-batter-result grey-cell last-row"></div>
				<!-- 2ND ROW -->
				<div class="vBatter normal-cell-left">
					<p class="lineupNumbersLeft">2</p>
					<p id="vBatter2Name" class="adjustName">Player 2</p>
				</div>
				<div class="vBatter normal-cell-left center-text">
					<p id="vPos2" class="adjustPos">RF</p>
				</div>
				<div class="normal-cell"></div>
				<div class="normal-cell"></div>
				<div class="normal-cell"></div>
				<div class="normal-cell-before-batter-result"></div>
				<div class="batter-result16 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result17 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result18 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result19 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result20 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result21 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result22 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result23 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result24 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result25 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result26 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result27 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result28 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result29 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result30 batter-box grid-border-right-thick">
					<p class="diamond-test"></p>
				</div>

				<!-- BACKUP 2ND ROW -->
				<div class="normal-cell-left grey-cell last-row"></div>
				<div class="normal-cell-left grey-cell last-row"></div>
				<div class="normal-cell grey-cell last-row"></div>
				<div class="normal-cell grey-cell last-row"></div>
				<div class="normal-cell grey-cell last-row"></div>
				<div class="normal-cell-before-batter-result grey-cell last-row"></div>
				<!-- 3RD ROW -->
				<div class="vBatter normal-cell-left">
					<p class="lineupNumbersLeft">3</p>
					<p id="vBatter3Name" class="adjustName">Player 3</p>
				</div>
				<div class="vBatter normal-cell-left center-text">
					<p id="vPos3" class="adjustPos">LF</p>
				</div>
				<div class="normal-cell"></div>
				<div class="normal-cell"></div>
				<div class="normal-cell"></div>
				<div class="normal-cell-before-batter-result"></div>
				<div class="batter-result31 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result32 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result33 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result34 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result35 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result36 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result37 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result38 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result39 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result40 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result41 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result42 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result43 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result44 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result45 batter-box grid-border-right-thick">
					<p class="diamond-test"></p>
				</div>

				<!-- BACKUP 3RD ROW -->
				<div class="normal-cell-left grey-cell last-row"></div>
				<div class="normal-cell-left grey-cell last-row"></div>
				<div class="normal-cell grey-cell last-row"></div>
				<div class="normal-cell grey-cell last-row"></div>
				<div class="normal-cell grey-cell last-row"></div>
				<div class="normal-cell-before-batter-result grey-cell last-row"></div>
				<!-- 4TH ROW -->
				<div class="vBatter normal-cell-left">
					<p class="lineupNumbersLeft">4</p>
					<p id="vBatter4Name" class="adjustName">Player 4</p>
				</div>
				<div class="vBatter normal-cell-left center-text">
					<p id="vPos4" class="adjustPos">1B</p>
				</div>
				<div class="normal-cell"></div>
				<div class="normal-cell"></div>
				<div class="normal-cell"></div>
				<div class="normal-cell-before-batter-result"></div>
				<div class="batter-result46 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result47 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result48 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result49 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result50 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result51 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result52 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result53 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result54 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result55 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result56 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result57 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result58 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result59 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result60 batter-box grid-border-right-thick">
					<p class="diamond-test"></p>
				</div>

				<!-- BACKUP 4TH ROW -->
				<div class="normal-cell-left grey-cell last-row"></div>
				<div class="normal-cell-left grey-cell last-row"></div>
				<div class="normal-cell grey-cell last-row"></div>
				<div class="normal-cell grey-cell last-row"></div>
				<div class="normal-cell grey-cell last-row"></div>
				<div class="normal-cell-before-batter-result grey-cell last-row"></div>
				<!-- 5th ROW -->
				<div class="vBatter normal-cell-left">
					<p class="lineupNumbersLeft">5</p>
					<p id="vBatter5Name" class="adjustName">Player 5</p>
				</div>
				<div class="vBatter normal-cell-left center-text">
					<p id="vPos5" class="adjustPos">2B</p>
				</div>
				<div class="normal-cell"></div>
				<div class="normal-cell"></div>
				<div class="normal-cell"></div>
				<div class="normal-cell-before-batter-result"></div>
				<div class="batter-result61 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result62 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result63 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result64 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result65 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result66 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result67 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result68 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result69 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result70 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result71 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result72 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result73 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result74 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result75 batter-box grid-border-right-thick">
					<p class="diamond-test"></p>
				</div>

				<!-- BACKUP 5TH ROW -->
				<div class="normal-cell-left grey-cell last-row"></div>
				<div class="normal-cell-left grey-cell last-row"></div>
				<div class="normal-cell grey-cell last-row"></div>
				<div class="normal-cell grey-cell last-row"></div>
				<div class="normal-cell grey-cell last-row"></div>
				<div class="normal-cell-before-batter-result grey-cell last-row"></div>
				<!-- 6TH ROW -->
				<div class="vBatter normal-cell-left">
					<p class="lineupNumbersLeft">6</p>
					<p id="vBatter6Name" class="adjustName">Player 6</p>
				</div>
				<div class="vBatter normal-cell-left center-text">
					<p id="vPos6" class="adjustPos">3B</p>
				</div>
				<div class="normal-cell"></div>
				<div class="normal-cell"></div>
				<div class="normal-cell"></div>
				<div class="normal-cell-before-batter-result"></div>
				<div class="batter-result76 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result77 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result78 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result79 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result80 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result81 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result82 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result83 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result84 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result85 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result86 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result87 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result88 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result89 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result90 batter-box grid-border-right-thick">
					<p class="diamond-test"></p>
				</div>

				<!-- BACKUP 6TH ROW -->
				<div class="normal-cell-left grey-cell last-row"></div>
				<div class="normal-cell-left grey-cell last-row"></div>
				<div class="normal-cell grey-cell last-row"></div>
				<div class="normal-cell grey-cell last-row"></div>
				<div class="normal-cell grey-cell last-row"></div>
				<div class="normal-cell-before-batter-result grey-cell last-row"></div>
				<!-- 7TH ROW -->
				<div class="vBatter normal-cell-left">
					<p class="lineupNumbersLeft">7</p>
					<p id="vBatter7Name" class="adjustName">Player 7</p>
				</div>
				<div class="vBatter normal-cell-left center-text">
					<p id="vPos7" class="adjustPos">SS</p>
				</div>
				<div class="normal-cell"></div>
				<div class="normal-cell"></div>
				<div class="normal-cell"></div>
				<div class="normal-cell-before-batter-result"></div>
				<div class="batter-result91 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result92 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result93 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result94 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result95 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result96 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result97 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result98 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result99 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result100 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result101 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result102 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result103 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result104 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result105 batter-box grid-border-right-thick">
					<p class="diamond-test"></p>
				</div>

				<!-- BACKUP 7TH ROW -->
				<div class="normal-cell-left grey-cell last-row"></div>
				<div class="normal-cell-left grey-cell last-row"></div>
				<div class="normal-cell grey-cell last-row"></div>
				<div class="normal-cell grey-cell last-row"></div>
				<div class="normal-cell grey-cell last-row"></div>
				<div class="normal-cell-before-batter-result grey-cell last-row"></div>
				<!-- 8TH ROW -->
				<div class="vBatter normal-cell-left">
					<p class="lineupNumbersLeft">8</p>
					<p id="vBatter8Name" class="adjustName">Player 8</p>
				</div>
				<div class="vBatter normal-cell-left center-text">
					<p id="vPos8" class="adjustPos">C</p>
				</div>
				<div class="normal-cell"></div>
				<div class="normal-cell"></div>
				<div class="normal-cell"></div>
				<div class="normal-cell-before-batter-result"></div>
				<div class="batter-result106 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result107 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result108 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result109 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result110 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result111 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result112 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result113 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result114 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result115 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result116 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result117 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result118 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result119 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result120 batter-box grid-border-right-thick">
					<p class="diamond-test"></p>
				</div>

				<!-- BACKUP 8TH ROW -->
				<div class="normal-cell-left grey-cell last-row"></div>
				<div class="normal-cell-left grey-cell last-row"></div>
				<div class="normal-cell grey-cell last-row"></div>
				<div class="normal-cell grey-cell last-row"></div>
				<div class="normal-cell grey-cell last-row"></div>
				<div class="normal-cell-before-batter-result grey-cell last-row"></div>
				<!-- 9TH ROW -->
				<div class="vBatter normal-cell-left">
					<p class="lineupNumbersLeft">9</p>
					<p id="vBatter9Name" class="adjustName">Player 9</p>
				</div>
				<div class="vBatter normal-cell-left center-text">
					<p id="vPos9" class="adjustPos">DH</p>
				</div>
				<div class="normal-cell"></div>
				<div class="normal-cell"></div>
				<div class="normal-cell"></div>
				<div class="normal-cell-before-batter-result"></div>
				<div class="batter-result121 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result122 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result123 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result124 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result125 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result126 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result127 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result128 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result129 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result130 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result131 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result132 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result133 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result134 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result135 batter-box grid-border-right-thick">
					<p class="diamond-test"></p>
				</div>

				<!-- BACKUP 9TH ROW -->
				<div class="normal-cell-left grey-cell last-row"></div>
				<div class="normal-cell-left grey-cell last-row"></div>
				<div class="normal-cell grey-cell last-row"></div>
				<div class="normal-cell grey-cell last-row"></div>
				<div class="normal-cell grey-cell last-row"></div>
				<div class="normal-cell-before-batter-result grey-cell last-row"></div>
			</div>
			<div class="container-pitching">
				<!-- VISITOR PITCHING SECTION -->
				<div class="visitor-pitching headGrid">VISITOR PITCHERS</div>
				<div class="headGrid grid-border-left-thick">IP</div>
				<div class="headGrid grid-border-left">H</div>
				<div class="headGrid grid-border-left">R</div>
				<div class="headGrid grid-border-left">ER</div>
				<div class="headGrid grid-border-left">BB</div>
				<div class="headGrid grid-border-left">SO</div>
				<div class="headGrid grid-border-left">HBP</div>
				<div class="headGrid grid-border-left">BF</div>
				<div class="headGrid grid-border-left">WP</div>
				<!-- HOME PITCHING SECTION -->
				<div class="home-pitching headGrid">HOME PITCHERS</div>
				<div class="headGrid grid-border-left">IP</div>
				<div class="headGrid grid-border-left">H</div>
				<div class="headGrid grid-border-left">R</div>
				<div class="headGrid grid-border-left">ER</div>
				<div class="headGrid grid-border-left">BB</div>
				<div class="headGrid grid-border-left">SO</div>
				<div class="headGrid grid-border-left">HBP</div>
				<div class="headGrid grid-border-left">BF</div>
				<div class="headGrid grid-border-left grid-border-right-thick">WP</div>
				<!-- 1ST PITCHER ROW -->
				<div id="vPitcherContainer" class="normal-cell grid-border-left-thick"><span id="vPitcherName"></span></div>
				<div class="normal-cell"></div>
				<div class="normal-cell"></div>
				<div class="normal-cell"></div>
				<div class="normal-cell"></div>
				<div class="normal-cell"></div>
				<div class="normal-cell"></div>
				<div class="normal-cell"></div>
				<div class="normal-cell"></div>
				<div class="normal-cell"></div>
				<div id="hPitcherContainer" class="normal-cell grid-border-left-thick"><span id="hPitcherName"></span></div>
				<div class="normal-cell"></div>
				<div class="normal-cell"></div>
				<div class="normal-cell"></div>
				<div class="normal-cell"></div>
				<div class="normal-cell"></div>
				<div class="normal-cell"></div>
				<div class="normal-cell"></div>
				<div class="normal-cell"></div>
				<div class="normal-cell grid-border-right-thick"></div>
				<!-- 2nd PITCHER ROW -->
				<div class="normal-cell grid-border-left-thick grey-cell"></div>
				<div class="normal-cell grey-cell"></div>
				<div class="normal-cell grey-cell"></div>
				<div class="normal-cell grey-cell"></div>
				<div class="normal-cell grey-cell"></div>
				<div class="normal-cell grey-cell"></div>
				<div class="normal-cell grey-cell"></div>
				<div class="normal-cell grey-cell"></div>
				<div class="normal-cell grey-cell"></div>
				<div class="normal-cell grey-cell"></div>
				<div class="normal-cell grid-border-left-thick grey-cell"></div>
				<div class="normal-cell grey-cell"></div>
				<div class="normal-cell grey-cell"></div>
				<div class="normal-cell grey-cell"></div>
				<div class="normal-cell grey-cell"></div>
				<div class="normal-cell grey-cell"></div>
				<div class="normal-cell grey-cell"></div>
				<div class="normal-cell grey-cell"></div>
				<div class="normal-cell grey-cell"></div>
				<div class="normal-cell grid-border-right-thick grey-cell"></div>
				<!-- 3rd PITCHER ROW -->
				<div class="normal-cell grid-border-left-thick"></div>
				<div class="normal-cell"></div>
				<div class="normal-cell"></div>
				<div class="normal-cell"></div>
				<div class="normal-cell"></div>
				<div class="normal-cell"></div>
				<div class="normal-cell"></div>
				<div class="normal-cell"></div>
				<div class="normal-cell"></div>
				<div class="normal-cell"></div>
				<div class="normal-cell grid-border-left-thick"></div>
				<div class="normal-cell"></div>
				<div class="normal-cell"></div>
				<div class="normal-cell"></div>
				<div class="normal-cell"></div>
				<div class="normal-cell"></div>
				<div class="normal-cell"></div>
				<div class="normal-cell"></div>
				<div class="normal-cell"></div>
				<div class="normal-cell grid-border-right-thick"></div>
				<!-- 4th PITCHER ROW -->
				<div class="normal-cell grid-border-left-thick grey-cell"></div>
				<div class="normal-cell grey-cell"></div>
				<div class="normal-cell grey-cell"></div>
				<div class="normal-cell grey-cell"></div>
				<div class="normal-cell grey-cell"></div>
				<div class="normal-cell grey-cell"></div>
				<div class="normal-cell grey-cell"></div>
				<div class="normal-cell grey-cell"></div>
				<div class="normal-cell grey-cell"></div>
				<div class="normal-cell grey-cell"></div>
				<div class="normal-cell grid-border-left-thick grey-cell"></div>
				<div class="normal-cell grey-cell"></div>
				<div class="normal-cell grey-cell"></div>
				<div class="normal-cell grey-cell"></div>
				<div class="normal-cell grey-cell"></div>
				<div class="normal-cell grey-cell"></div>
				<div class="normal-cell grey-cell"></div>
				<div class="normal-cell grey-cell"></div>
				<div class="normal-cell grey-cell"></div>
				<div class="normal-cell grid-border-right-thick grey-cell"></div>
				<!-- 5th PITCHER ROW -->
				<div class="normal-cell grid-border-left-thick"></div>
				<div class="normal-cell"></div>
				<div class="normal-cell"></div>
				<div class="normal-cell"></div>
				<div class="normal-cell"></div>
				<div class="normal-cell"></div>
				<div class="normal-cell"></div>
				<div class="normal-cell"></div>
				<div class="normal-cell"></div>
				<div class="normal-cell"></div>
				<div class="normal-cell grid-border-left-thick"></div>
				<div class="normal-cell"></div>
				<div class="normal-cell"></div>
				<div class="normal-cell"></div>
				<div class="normal-cell"></div>
				<div class="normal-cell"></div>
				<div class="normal-cell"></div>
				<div class="normal-cell"></div>
				<div class="normal-cell"></div>
				<div class="normal-cell grid-border-right-thick"></div>
				<!-- 6th PITCHER ROW -->
				<div
					class="normal-cell grid-border-left-thick last-row grey-cell"
				></div>
				<div class="normal-cell last-row grey-cell"></div>
				<div class="normal-cell last-row grey-cell"></div>
				<div class="normal-cell last-row grey-cell"></div>
				<div class="normal-cell last-row grey-cell"></div>
				<div class="normal-cell last-row grey-cell"></div>
				<div class="normal-cell last-row grey-cell"></div>
				<div class="normal-cell last-row grey-cell"></div>
				<div class="normal-cell last-row grey-cell"></div>
				<div class="normal-cell last-row grey-cell"></div>
				<div
					class="normal-cell grid-border-left-thick last-row grey-cell"
				></div>
				<div class="normal-cell last-row grey-cell"></div>
				<div class="normal-cell last-row grey-cell"></div>
				<div class="normal-cell last-row grey-cell"></div>
				<div class="normal-cell last-row grey-cell"></div>
				<div class="normal-cell last-row grey-cell"></div>
				<div class="normal-cell last-row grey-cell"></div>
				<div class="normal-cell last-row grey-cell"></div>
				<div class="normal-cell last-row grey-cell"></div>
				<div
					class="normal-cell grid-border-right-thick last-row grey-cell"
				></div>
			</div>
			<div class="container-lineup">
				<!-- HEADER -->
				<div id="home-team-name" class="headGrid grid-border-left-thick">
					2023 Los Angeles (A)
				</div>
				<div class="headGrid grid-border-left-thick">POS</div>
				<div class="headGrid grid-border-left">RNG</div>
				<div class="headGrid grid-border-left">ERR</div>
				<div class="headGrid grid-border-left">ARM</div>
				<div class="headGrid grid-border-left">RUN</div>
				<div class="headGrid grid-border-left-thick">1</div>
				<div class="headGrid grid-border-left">2</div>
				<div class="headGrid grid-border-left">3</div>
				<div class="headGrid grid-border-left">4</div>
				<div class="headGrid grid-border-left">5</div>
				<div class="headGrid grid-border-left">6</div>
				<div class="headGrid grid-border-left">7</div>
				<div class="headGrid grid-border-left">8</div>
				<div class="headGrid grid-border-left">9</div>
				<div class="headGrid grid-border-left">10</div>
				<div class="headGrid grid-border-left">11</div>
				<div class="headGrid grid-border-left">12</div>
				<div class="headGrid grid-border-left">13</div>
				<div class="headGrid grid-border-left">14</div>
				<div class="headGrid grid-border-left grid-border-right-thick">15</div>

				<!-- 1ST ROW -->
				<div class="vBatter normal-cell-left">
					<p class="lineupNumbersLeft">1</p>
					<p id="hBatter1Name" class="adjustName">Player 1</p>
				</div>
				<div class="vBatter normal-cell-left center-text">
					<p id="hPos1" class="adjustPos">CF</p>
				</div>
				<div class="normal-cell"></div>
				<div class="normal-cell"></div>
				<div class="normal-cell"></div>
				<div class="normal-cell-before-batter-result"></div>
				<div class="batter-result1 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result2 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result3 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result4 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result5 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result6 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result7 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result8 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result9 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result10 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result11 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result12 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result13 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result14 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result15 batter-box grid-border-right-thick">
					<p class="diamond-test"></p>
				</div>
				<!-- BACKUP 1ST ROW -->
				<div class="normal-cell-left grey-cell last-row"></div>
				<div class="normal-cell-left grey-cell last-row"></div>
				<div class="normal-cell grey-cell last-row"></div>
				<div class="normal-cell grey-cell last-row"></div>
				<div class="normal-cell grey-cell last-row"></div>
				<div class="normal-cell-before-batter-result grey-cell last-row"></div>
				<!-- 2ND ROW -->
				<div class="vBatter normal-cell-left">
					<p class="lineupNumbersLeft">2</p>
					<p id="hBatter2Name" class="adjustName">Player 2</p>
				</div>
				<div class="vBatter normal-cell-left center-text">
					<p id="hPos2" class="adjustPos">RF</p>
				</div>
				<div class="normal-cell"></div>
				<div class="normal-cell"></div>
				<div class="normal-cell"></div>
				<div class="normal-cell-before-batter-result"></div>
				<div class="batter-result16 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result17 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result18 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result19 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result20 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result21 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result22 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result23 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result24 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result25 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result26 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result27 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result28 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result29 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result30 batter-box grid-border-right-thick">
					<p class="diamond-test"></p>
				</div>

				<!-- BACKUP 2ND ROW -->
				<div class="normal-cell-left grey-cell last-row"></div>
				<div class="normal-cell-left grey-cell last-row"></div>
				<div class="normal-cell grey-cell last-row"></div>
				<div class="normal-cell grey-cell last-row"></div>
				<div class="normal-cell grey-cell last-row"></div>
				<div class="normal-cell-before-batter-result grey-cell last-row"></div>
				<!-- 3RD ROW -->
				<div class="vBatter normal-cell-left">
					<p class="lineupNumbersLeft">3</p>
					<p id="hBatter3Name" class="adjustName">Player 3</p>
				</div>
				<div class="vBatter normal-cell-left center-text">
					<p id="hPos3" class="adjustPos">LF</p>
				</div>
				<div class="normal-cell"></div>
				<div class="normal-cell"></div>
				<div class="normal-cell"></div>
				<div class="normal-cell-before-batter-result"></div>
				<div class="batter-result31 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result32 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result33 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result34 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result35 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result36 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result37 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result38 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result39 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result40 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result41 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result42 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result43 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result44 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result45 batter-box grid-border-right-thick">
					<p class="diamond-test"></p>
				</div>

				<!-- BACKUP 3RD ROW -->
				<div class="normal-cell-left grey-cell last-row"></div>
				<div class="normal-cell-left grey-cell last-row"></div>
				<div class="normal-cell grey-cell last-row"></div>
				<div class="normal-cell grey-cell last-row"></div>
				<div class="normal-cell grey-cell last-row"></div>
				<div class="normal-cell-before-batter-result grey-cell last-row"></div>
				<!-- 4TH ROW -->
				<div class="vBatter normal-cell-left">
					<p class="lineupNumbersLeft">4</p>
					<p id="hBatter4Name" class="adjustName">Player 4</p>
				</div>
				<div class="vBatter normal-cell-left center-text">
					<p id="hPos4" class="adjustPos">1B</p>
				</div>
				<div class="normal-cell"></div>
				<div class="normal-cell"></div>
				<div class="normal-cell"></div>
				<div class="normal-cell-before-batter-result"></div>
				<div class="batter-result46 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result47 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result48 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result49 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result50 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result51 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result52 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result53 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result54 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result55 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result56 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result57 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result58 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result59 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result60 batter-box grid-border-right-thick">
					<p class="diamond-test"></p>
				</div>

				<!-- BACKUP 4TH ROW -->
				<div class="normal-cell-left grey-cell last-row"></div>
				<div class="normal-cell-left grey-cell last-row"></div>
				<div class="normal-cell grey-cell last-row"></div>
				<div class="normal-cell grey-cell last-row"></div>
				<div class="normal-cell grey-cell last-row"></div>
				<div class="normal-cell-before-batter-result grey-cell last-row"></div>
				<!-- 5th ROW -->
				<div class="vBatter normal-cell-left">
					<p class="lineupNumbersLeft">5</p>
					<p id="hBatter5Name" class="adjustName">Player 5</p>
				</div>
				<div class="vBatter normal-cell-left center-text">
					<p id="hPos5" class="adjustPos">2B</p>
				</div>
				<div class="normal-cell"></div>
				<div class="normal-cell"></div>
				<div class="normal-cell"></div>
				<div class="normal-cell-before-batter-result"></div>
				<div class="batter-result61 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result62 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result63 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result64 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result65 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result66 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result67 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result68 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result69 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result70 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result71 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result72 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result73 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result74 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result75 batter-box grid-border-right-thick">
					<p class="diamond-test"></p>
				</div>

				<!-- BACKUP 5TH ROW -->
				<div class="normal-cell-left grey-cell last-row"></div>
				<div class="normal-cell-left grey-cell last-row"></div>
				<div class="normal-cell grey-cell last-row"></div>
				<div class="normal-cell grey-cell last-row"></div>
				<div class="normal-cell grey-cell last-row"></div>
				<div class="normal-cell-before-batter-result grey-cell last-row"></div>
				<!-- 6TH ROW -->
				<div class="vBatter normal-cell-left">
					<p class="lineupNumbersLeft">6</p>
					<p id="hBatter6Name" class="adjustName">Player 6</p>
				</div>
				<div class="vBatter normal-cell-left center-text">
					<p id="hPos6" class="adjustPos">3B</p>
				</div>
				<div class="normal-cell"></div>
				<div class="normal-cell"></div>
				<div class="normal-cell"></div>
				<div class="normal-cell-before-batter-result"></div>
				<div class="batter-result76 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result77 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result78 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result79 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result80 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result81 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result82 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result83 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result84 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result85 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result86 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result87 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result88 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result89 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result90 batter-box grid-border-right-thick">
					<p class="diamond-test"></p>
				</div>

				<!-- BACKUP 6TH ROW -->
				<div class="normal-cell-left grey-cell last-row"></div>
				<div class="normal-cell-left grey-cell last-row"></div>
				<div class="normal-cell grey-cell last-row"></div>
				<div class="normal-cell grey-cell last-row"></div>
				<div class="normal-cell grey-cell last-row"></div>
				<div class="normal-cell-before-batter-result grey-cell last-row"></div>
				<!-- 7TH ROW -->
				<div class="vBatter normal-cell-left">
					<p class="lineupNumbersLeft">7</p>
					<p id="hBatter7Name" class="adjustName">Player 7</p>
				</div>
				<div class="vBatter normal-cell-left center-text">
					<p id="hPos7" class="adjustPos">SS</p>
				</div>
				<div class="normal-cell"></div>
				<div class="normal-cell"></div>
				<div class="normal-cell"></div>
				<div class="normal-cell-before-batter-result"></div>
				<div class="batter-result91 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result92 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result93 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result94 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result95 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result96 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result97 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result98 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result99 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result100 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result101 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result102 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result103 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result104 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result105 batter-box grid-border-right-thick">
					<p class="diamond-test"></p>
				</div>

				<!-- BACKUP 7TH ROW -->
				<div class="normal-cell-left grey-cell last-row"></div>
				<div class="normal-cell-left grey-cell last-row"></div>
				<div class="normal-cell grey-cell last-row"></div>
				<div class="normal-cell grey-cell last-row"></div>
				<div class="normal-cell grey-cell last-row"></div>
				<div class="normal-cell-before-batter-result grey-cell last-row"></div>
				<!-- 8TH ROW -->
				<div class="vBatter normal-cell-left">
					<p class="lineupNumbersLeft">8</p>
					<p id="hBatter8Name" class="adjustName">Player 8</p>
				</div>
				<div class="vBatter normal-cell-left center-text">
					<p id="hPos8" class="adjustPos">C</p>
				</div>
				<div class="normal-cell"></div>
				<div class="normal-cell"></div>
				<div class="normal-cell"></div>
				<div class="normal-cell-before-batter-result"></div>
				<div class="batter-result106 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result107 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result108 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result109 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result110 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result111 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result112 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result113 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result114 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result115 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result116 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result117 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result118 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result119 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result120 batter-box grid-border-right-thick">
					<p class="diamond-test"></p>
				</div>

				<!-- BACKUP 8TH ROW -->
				<div class="normal-cell-left grey-cell last-row"></div>
				<div class="normal-cell-left grey-cell last-row"></div>
				<div class="normal-cell grey-cell last-row"></div>
				<div class="normal-cell grey-cell last-row"></div>
				<div class="normal-cell grey-cell last-row"></div>
				<div class="normal-cell-before-batter-result grey-cell last-row"></div>
				<!-- 9TH ROW -->
				<div class="vBatter normal-cell-left">
					<p class="lineupNumbersLeft">9</p>
					<p id="hBatter9Name" class="adjustName">Player 9</p>
				</div>
				<div class="vBatter normal-cell-left center-text">
					<p id="hPos9" class="adjustPos">DH</p>
				</div>
				<div class="normal-cell"></div>
				<div class="normal-cell"></div>
				<div class="normal-cell"></div>
				<div class="normal-cell-before-batter-result"></div>
				<div class="batter-result121 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result122 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result123 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result124 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result125 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result126 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result127 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result128 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result129 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result130 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result131 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result132 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result133 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result134 batter-box">
					<p class="diamond-test"></p>
				</div>
				<div class="batter-result135 batter-box grid-border-right-thick">
					<p class="diamond-test"></p>
				</div>

				<!-- BACKUP 9TH ROW -->
				<div class="normal-cell-left grey-cell last-row"></div>
				<div class="normal-cell-left grey-cell last-row"></div>
				<div class="normal-cell grey-cell last-row"></div>
				<div class="normal-cell grey-cell last-row"></div>
				<div class="normal-cell grey-cell last-row"></div>
				<div class="normal-cell-before-batter-result grey-cell last-row"></div>
			</div>
			<div class="container-notes">
				<h3>Notes</h3>
			</div>
		</div>

            <div class="grid-container">
                <section id="gridgames" class="grid-of-games">

                <?php

                    $sql = "SELECT * FROM GAMELOGS_MAIN WHERE date = " . $year . $month . $day;
                    $result = mysqli_query($link, $sql);
                    $resultCheck = mysqli_num_rows($result);

                    if($resultCheck > 0) {
                        while ($row = mysqli_fetch_assoc($result)) { ?>
                            <div class="game-container">
                                <div class="game">
                                    <div class="cell top-row-empty">
                                    </div>
                                    <div class="cell top-row-header">R</div>
                                    <div class="cell top-row-header">H</div>
                                    <div class="cell top-row-header">E</div>
                                    <div class="cell-name vis-teamname" style="color: <?php echo getTeamFontColor($row['visteam']); ?>; background-color: <?php echo getTeamColor($row['visteam']); ?>">
                                        <?php echo getTeamName($row['visteam'], $year); ?>
                                    </div>
                                    <div class="cell cell-name vis-runs-scored"  style="color: <?php echo getTeamFontColor($row['visteam']); ?>; background-color: <?php echo getTeamColor($row['visteam']); ?>">
                                        <?php echo $row['visscore']; ?>
                                    </div>
                                    <div class="cell cell-name vis-hits"  style="color: <?php echo getTeamFontColor($row['visteam']); ?>; background-color: <?php echo getTeamColor($row['visteam']); ?>">
                                        <?php echo $row['vish']; ?>
                                    </div>
                                    <div class="cell cell-name vis-errors"  style="color: <?php echo getTeamFontColor($row['visteam']); ?>; background-color: <?php echo getTeamColor($row['visteam']); ?>">
                                        <?php echo $row['vise']; ?>
                                    </div>
                                    <div class="cell-name home-teamname" style="color: <?php echo getTeamFontColor($row['hometeam']); ?>; background-color: <?php echo getTeamColor($row['hometeam']); ?>">
                                        <?php echo getTeamName($row['hometeam'], $year); ?>
                                    </div>
                                    <div class="cell home-runs-scored" style="color: <?php echo getTeamFontColor($row['hometeam']); ?>; background-color: <?php echo getTeamColor($row['hometeam']); ?>">
                                        <?php echo $row['homescore']; ?>
                                    </div>
                                    <div class="cell home-hits" style="color: <?php echo getTeamFontColor($row['hometeam']); ?>; background-color: <?php echo getTeamColor($row['hometeam']); ?>">
                                        <?php echo $row['homeh']; ?>
                                    </div>
                                    <div class="cell home-errors" style="color: <?php echo getTeamFontColor($row['hometeam']); ?>; background-color: <?php echo getTeamColor($row['hometeam']); ?>">
                                        <?php echo $row['homee']; ?>
                                    </div>
                                </div>
                                <!-- <div class="separator"></div> -->
                                <div class="pitchers-of-record">
                                    <p class="pitcher-right-align">Winning Pitcher:</p>
                                    <p id="winning-pitcher"><?php echo $row['winningpitchername']; ?></p>
                                    <p class="pitcher-right-align">Losing Pitcher:</p>
                                    <p id="losing-pitcher"><?php echo $row['losingpitchername']; ?></p>
                                    <p class="pitcher-right-align">Save:</p>
                                    <p><?php
                                        echo $row['savingpitchername']; ?>
                                    </p>
                                </div>
                                <div class="button-options">
                                    <div class="btn-scoresheet button-choice" data-team="<?php echo $row['hometeam']; ?>" data-gamenum="<?php echo $row['game']; ?>">
                                        VIEW LINEUPS
                                    </div>
                                    <!-- <div data-boxscore="<?php /* echo $row['hometeam'] . $year . $month . $day . $row['game']; */ ?>" class="btn-box-score button-choice btn-disabled">
                                        <i class="fas fa-list-ol fa-2x"></i>
                                        <p>Box Score</p>
                                    </div>
                                    <div class="btn-recap button-choice btn-disabled">
                                        <img src="./images/recap.png" alt="Watch game unfold">
                                        <p>Recap</p>
                                    </div> -->
                                </div>
                            </div>
                    <?php }
                    }
                ?>
            </section>
        </div>

        <input id="mydate" type="hidden" value="<?php echo $year . $month . $day; ?>">

        <script src="js/date-picker.js" async defer></script>
    </body>
</html>