<?php
    // Include config file
    include "configtest.php";
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
                let selectedDate = '';
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

                                console.log("Day: " + tempDay);

                                console.log("Month: " + tempMonth);

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
            });
        </script>
        <link rel="stylesheet" href="css/style-test.css">
        <link rel="stylesheet" href="css/scoresheet.css">
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

        ?>

        <div class="head">
            <a href="https://www.digidugout.com/index.html"><img src="/images/main_digidugout_logo.png" alt="logo"></a>
        </div>
        
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
                                                if($j === $day) {
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

            <div class="test">
                <div>
                    <h6 id="footer-date"></h6>
                </div>
                <div class="container">
                    <!-- HEADER -->
                    <div id="vis-team-name" class="headGrid grid-border-left-thick">VISITING TEAMNAME</div>
                    <div class="headGrid grid-border-left-thick">POS</div>
                    <div class="headGrid grid-border-left">RNG</div>
                    <div class="headGrid grid-border-left">ERROR</div>
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
                    <div class="headGrid bench-header1 grid-border-right-thick grid-border-left-thick">BENCH</div>

                    <!-- 1ST ROW -->
                    <div class="vBatter normal-cell-left">
                    <p class="lineupNumbersLeft">1</p>
                    <p id="vBatter1Name" class="adjustName"></p>
                    </div>
                    <div class="vBatter normal-cell-left center-text">
                    <p id="vPos1" class="adjustPos"></p>
                    </div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell-before-batter-result"></div>
                    <div class="batter-result1">
                        <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result2">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result3">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result4">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result5">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result6">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result7">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result8">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result9">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result10">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result11">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result12">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="bench1"></div>

                    <!-- BACKUP 1ST ROW -->
                    <div class="normal-cell-left grey-cell last-row"></div>
                    <div class="normal-cell-left grey-cell last-row"></div>
                    <div class="normal-cell grey-cell last-row"></div>
                    <div class="normal-cell grey-cell last-row"></div>
                    <div class="normal-cell grey-cell last-row"></div>
                    <div class="normal-cell-before-batter-result grey-cell last-row"></div>
                    <div class="bench2 grey-cell"></div>

                    <!-- 2ND ROW -->
                    <div class="vBatter normal-cell-left">
                    <p class="lineupNumbersLeft">2</p>
                    <p id="vBatter2Name" class="adjustName"></p>
                    </div>
                    <div class="vBatter normal-cell-left center-text">
                    <p id="vPos2" class="adjustPos"></p>
                    </div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell-before-batter-result"></div>
                    <div class="batter-result13">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result14">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result15">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result16">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result17">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result18">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result19">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result20">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result21">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result22">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result23">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result24">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="bench3"></div>

                    <!-- BACKUP 2ND ROW -->
                    <div class="normal-cell-left grey-cell last-row"></div>
                    <div class="normal-cell-left grey-cell last-row"></div>
                    <div class="normal-cell grey-cell last-row"></div>
                    <div class="normal-cell grey-cell last-row"></div>
                    <div class="normal-cell grey-cell last-row"></div>
                    <div class="normal-cell-before-batter-result grey-cell last-row"></div>
                    <div class="bench4 grey-cell"></div>

                    <!-- 3RD ROW -->

                    <div class="vBatter normal-cell-left">
                    <p class="lineupNumbersLeft">3</p>
                    <p id="vBatter3Name" class="adjustName"></p>
                    </div>
                    <div class="vBatter normal-cell-left center-text">
                    <p id="vPos3" class="adjustPos"></p>
                    </div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell-before-batter-result"></div>
                    <div class="batter-result25">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result26">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result27">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result28">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result29">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result30">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result31">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result32">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result33">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result34">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result35">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result36">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="bench5"></div>

                    <!-- BACKUP 3RD ROW -->
                    <div class="normal-cell-left grey-cell last-row"></div>
                    <div class="normal-cell-left grey-cell last-row"></div>
                    <div class="normal-cell grey-cell last-row"></div>
                    <div class="normal-cell grey-cell last-row"></div>
                    <div class="normal-cell grey-cell last-row"></div>
                    <div class="normal-cell-before-batter-result grey-cell last-row"></div>
                    <div class="bench6 grey-cell"></div>

                    <!-- 4TH ROW -->
                    <div class="vBatter normal-cell-left">
                    <p class="lineupNumbersLeft">4</p>
                    <p id="vBatter4Name" class="adjustName"></p>
                    </div>
                    <div class="vBatter normal-cell-left center-text">
                    <p id="vPos4" class="adjustPos"></p>
                    </div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell-before-batter-result"></div>
                    <div class="batter-result37">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result38">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result39">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result40">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result41">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result42">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result43">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result44">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result45">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result46">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result47">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result48">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="bench7"></div>

                    <!-- BACKUP 4TH ROW -->
                    <div class="normal-cell-left grey-cell last-row"></div>
                    <div class="normal-cell-left grey-cell last-row"></div>
                    <div class="normal-cell grey-cell last-row"></div>
                    <div class="normal-cell grey-cell last-row"></div>
                    <div class="normal-cell grey-cell last-row"></div>
                    <div class="normal-cell-before-batter-result grey-cell last-row"></div>
                    <div class="bench8 grey-cell"></div>

                    <!-- 5TH ROW -->
                    <div class="vBatter normal-cell-left">
                    <p class="lineupNumbersLeft">5</p>
                    <p id="vBatter5Name" class="adjustName"></p>
                    </div>
                    <div class="vBatter normal-cell-left center-text">
                    <p id="vPos5" class="adjustPos"></p>
                    </div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell-before-batter-result"></div>
                    <div class="batter-result49">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result50">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result51">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result52">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result53">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result54">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result55">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result56">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result57">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result58">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result59">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result60">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="bullpen headGrid bench9">BULLPEN</div>

                    <!-- BACKUP 5TH ROW -->
                    <div class="normal-cell-left grey-cell last-row"></div>
                    <div class="normal-cell-left grey-cell last-row"></div>
                    <div class="normal-cell grey-cell last-row"></div>
                    <div class="normal-cell grey-cell last-row"></div>
                    <div class="normal-cell grey-cell last-row"></div>
                    <div class="normal-cell-before-batter-result grey-cell last-row"></div>
                    <div class="bench10 grey-cell"></div>

                    <!-- 6TH ROW -->

                    <div class="vBatter normal-cell-left">
                    <p class="lineupNumbersLeft">6</p>
                    <p id="vBatter6Name" class="adjustName"></p>
                    </div>
                    <div class="vBatter normal-cell-left center-text">
                    <p id="vPos6" class="adjustPos"></p>
                    </div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell-before-batter-result"></div>
                    <div class="batter-result61">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result62">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result63">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result64">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result65">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result66">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result67">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result68">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result69">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result70">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result71">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result72">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="bench11"></div>

                    <!-- BACKUP 6TH ROW -->
                    <div class="normal-cell-left grey-cell last-row"></div>
                    <div class="normal-cell-left grey-cell last-row"></div>
                    <div class="normal-cell grey-cell last-row"></div>
                    <div class="normal-cell grey-cell last-row"></div>
                    <div class="normal-cell grey-cell last-row"></div>
                    <div class="normal-cell-before-batter-result grey-cell last-row"></div>
                    <div class="bench12 grey-cell"></div>

                    <!-- 7TH ROW -->

                    <div class="vBatter normal-cell-left">
                    <p class="lineupNumbersLeft">7</p>
                    <p id="vBatter7Name" class="adjustName"></p>
                    </div>
                    <div class="vBatter normal-cell-left center-text">
                    <p id="vPos7" class="adjustPos"></p>
                    </div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell-before-batter-result"></div>
                    <div class="batter-result73">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result74">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result75">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result76">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result77">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result78">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result79">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result80">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result81">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result82">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result83">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result84">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="bench13"></div>

                    <!-- BACKUP 7TH ROW -->
                    <div class="normal-cell-left grey-cell last-row"></div>
                    <div class="normal-cell-left grey-cell last-row"></div>
                    <div class="normal-cell grey-cell last-row"></div>
                    <div class="normal-cell grey-cell last-row"></div>
                    <div class="normal-cell grey-cell last-row"></div>
                    <div class="normal-cell-before-batter-result grey-cell last-row"></div>
                    <div class="bench14 grey-cell"></div>

                    <!-- 8TH ROW -->

                    <div class="vBatter normal-cell-left">
                    <p class="lineupNumbersLeft">8</p>
                    <p id="vBatter8Name" class="adjustName"></p>
                    </div>
                    <div class="vBatter normal-cell-left center-text">
                    <p id="vPos8" class="adjustPos"></p>
                    </div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell-before-batter-result"></div>
                    <div class="batter-result85">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result86">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result87">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result88">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result89">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result90">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result91">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result92">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result93">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result94">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result95">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result96">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="bench15"></div>

                    <!-- BACKUP 8TH ROW -->
                    <div class="normal-cell-left grey-cell last-row"></div>
                    <div class="normal-cell-left grey-cell last-row"></div>
                    <div class="normal-cell grey-cell last-row"></div>
                    <div class="normal-cell grey-cell last-row"></div>
                    <div class="normal-cell grey-cell last-row"></div>
                    <div class="normal-cell-before-batter-result grey-cell last-row"></div>
                    <div class="bench16 grey-cell"></div>

                    <!-- 9TH ROW -->

                    <div class="vBatter normal-cell-left">
                    <p class="lineupNumbersLeft">9</p>
                    <p id="vBatter9Name" class="adjustName"></p>
                    </div>
                    <div class="vBatter normal-cell-left center-text">
                    <p id="vPos9" class="adjustPos"></p>
                    </div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell-before-batter-result"></div>
                    <div class="batter-result97">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result98">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result99">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result100">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result101">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result102">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result103">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result104">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result105">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result106">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result107">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="batter-result108">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="bench17"></div>

                    <!-- BACKUP 9TH ROW -->
                    <div class="grey-cell grid-border-left-thick"></div>
                    <div class="grey-cell grid-border-left-thick"></div>
                    <div class="grey-cell grid-border-left"></div>
                    <div class="grey-cell grid-border-left"></div>
                    <div class="grey-cell grid-border-left"></div>
                    <div class="grey-cell grid-border-left"></div>
                    <div class="grey-cell bench18"></div>

                    <!-- VISITOR PITCHING SECTION -->
                    <div class="visitor-pitching headGrid">VISITOR PITCHERS</div>
                    <div class="notes headGrid">NOTES</div>
                    <div class="headGrid grid-border-left-thick">IP</div>
                    <div class="headGrid grid-border-left">H</div>
                    <div class="headGrid grid-border-left">R</div>
                    <div class="headGrid grid-border-left">ER</div>
                    <div class="headGrid grid-border-left">BB</div>
                    <div class="headGrid grid-border-left">SO</div>
                    <div class="headGrid grid-border-left">HBP</div>
                    <div class="headGrid grid-border-left">BF</div>
                    <div class="headGrid grid-border-left">WP</div>
                    <div class="visiting-team-notes-header headGrid">VISITING TEAM NOTES</div>

                    <!-- ROW 1 -->
                    <div class="vPitcher normal-cell-left">
                    <p id="vPitcherName" class="adjustName"></p>
                    </div>
                    <div class="notes-left1"></div>
                    <div class="normal-cell-left"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="visiting-team-notes"></div>

                    <!-- ROW 2 -->
                    <div class="normal-cell-left grey-cell"></div>
                    <div class="notes-left2 grey-cell"></div>
                    <div class="normal-cell-left grey-cell"></div>
                    <div class="normal-cell grey-cell"></div>
                    <div class="normal-cell grey-cell"></div>
                    <div class="normal-cell grey-cell"></div>
                    <div class="normal-cell grey-cell"></div>
                    <div class="normal-cell grey-cell"></div>
                    <div class="normal-cell grey-cell"></div>
                    <div class="normal-cell grey-cell"></div>
                    <div class="normal-cell grey-cell"></div>

                    <!-- ROW 3 -->
                    <div class="normal-cell-left"></div>
                    <div class="notes-left3"></div>
                    <div class="normal-cell-left"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>

                    <!-- ROW 4 -->
                    <div class="normal-cell-left grey-cell"></div>
                    <div class="notes-left4 grey-cell"></div>
                    <div class="normal-cell-left grey-cell"></div>
                    <div class="normal-cell grey-cell"></div>
                    <div class="normal-cell grey-cell"></div>
                    <div class="normal-cell grey-cell"></div>
                    <div class="normal-cell grey-cell"></div>
                    <div class="normal-cell grey-cell"></div>
                    <div class="normal-cell grey-cell"></div>
                    <div class="normal-cell grey-cell"></div>
                    <div class="normal-cell grey-cell"></div>

                    <!-- ROW 5 -->
                    <div class="normal-cell-left"></div>
                    <div class="notes-left5"></div>
                    <div class="normal-cell-left"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>

                    <!-- ROW 6 -->
                    <div class="normal-cell-left grey-cell"></div>
                    <div class="notes-left6 grey-cell"></div>
                    <div class="normal-cell-left grey-cell"></div>
                    <div class="normal-cell grey-cell"></div>
                    <div class="normal-cell grey-cell"></div>
                    <div class="normal-cell grey-cell"></div>
                    <div class="normal-cell grey-cell"></div>
                    <div class="normal-cell grey-cell"></div>
                    <div class="normal-cell grey-cell"></div>
                    <div class="normal-cell grey-cell"></div>
                    <div class="normal-cell grey-cell"></div>

                    <!-- ROW 7 -->
                    <div class="normal-cell-left"></div>
                    <div class="notes-left7"></div>
                    <div class="normal-cell-left"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>

                    <!-- ROW 8 -->
                    <div class="grey-cell grid-border-left-thick"></div>
                    <div class="notes-left8 grey-cell"></div>
                    <div class="grey-cell grid-border-left-thick"></div>
                    <div class="grey-cell grid-border-left"></div>
                    <div class="grey-cell grid-border-left"></div>
                    <div class="grey-cell grid-border-left"></div>
                    <div class="grey-cell grid-border-left"></div>
                    <div class="grey-cell grid-border-left"></div>
                    <div class="grey-cell grid-border-left"></div>
                    <div class="grey-cell grid-border-left"></div>
                    <div class="grey-cell grid-border-left"></div>

                    <div class="headGrid scoreboard-team-header">TEAM</div>
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
                    <div id="vLine1" class="grid-border-left-thick grid-border-bottom center-text2"></div>
                    <div id="vLine2" class="grid-border-left grid-border-bottom center-text2"></div>
                    <div id="vLine3" class="grid-border-left grid-border-bottom center-text2"></div>
                    <div id="vLine4" class="grid-border-left grid-border-bottom center-text2"></div>
                    <div id="vLine5" class="grid-border-left grid-border-bottom center-text2"></div>
                    <div id="vLine7" class="grid-border-left grid-border-bottom center-text2"></div>
                    <div id="vLine6" class="grid-border-left grid-border-bottom center-text2"></div>
                    <div id="vLine8" class="grid-border-left grid-border-bottom center-text2"></div>
                    <div id="vLine9" class="grid-border-left grid-border-bottom center-text2"></div>
                    <div id="vLine10" class="grid-border-left grid-border-bottom center-text2"></div>
                    <div id="vLine11" class="grid-border-left grid-border-bottom center-text2"></div>
                    <div id="vLine12" class="grid-border-left grid-border-bottom center-text2"></div>
                    <div id="vLineR" class="grid-border-left-thick grid-border-bottom center-text2"></div>
                    <div id="vLineH" class="grid-border-left grid-border-bottom center-text2"></div>
                    <div id="vLineE" class="grid-border-left grid-border-right-thick grid-border-bottom center-text2"></div>

                    <div id="homeTeam" class="scoreboard-team2 grey-cell"></div>
                    <div id="hLine1" class="grid-border-left-thick grey-cell center-text2 faded-color-bottom"></div>
                    <div id="hLine2" class="grid-border-left grey-cell center-text2 faded-color-bottom"></div>
                    <div id="hLine3" class="grid-border-left grey-cell center-text2 faded-color-bottom"></div>
                    <div id="hLine4" class="grid-border-left grey-cell center-text2 faded-color-bottom"></div>
                    <div id="hLine5" class="grid-border-left grey-cell center-text2 faded-color-bottom"></div>
                    <div id="hLine6" class="grid-border-left grey-cell center-text2 faded-color-bottom"></div>
                    <div id="hLine7" class="grid-border-left grey-cell center-text2 faded-color-bottom"></div>
                    <div id="hLine8" class="grid-border-left grey-cell center-text2 faded-color-bottom"></div>
                    <div id="hLine9" class="grid-border-left grey-cell center-text2 faded-color-bottom"></div>
                    <div id="hLine10" class="grid-border-left grey-cell center-text2 faded-color-bottom"></div>
                    <div id="hLine11" class="grid-border-left grey-cell center-text2 faded-color-bottom"></div>
                    <div id="hLine12" class="grid-border-left grey-cell center-text2 faded-color-bottom"></div>
                    <div id="hLineR" class="grid-border-left-thick grey-cell center-text2 faded-color-bottom"></div>
                    <div id="hLineH" class="grid-border-left grey-cell center-text2 faded-color-bottom"></div>
                    <div id="hLineE" class="grid-border-left grid-border-right-thick grey-cell center-text2 faded-color-bottom"></div>

                    <!-- HOME SECTION -->
                    <!-- HEADER -->
                    <div id="home-team-name" class="headGrid grid-border-left-thick">HOME TEAMNAME</div>
                    <div class="headGrid grid-border-left-thick">POS</div>
                    <div class="headGrid grid-border-left">RNG</div>
                    <div class="headGrid grid-border-left">ERROR</div>
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
                    <div class="headGrid bench-header2 grid-border-right-thick grid-border-left-thick">BENCH</div>

                    <!-- 1ST ROW -->
                    <div class="vBatter normal-cell-left">
                    <p class="lineupNumbersLeft">1</p>
                    <p id="hBatter1Name" class="adjustName"></p>
                    </div>
                    <div class="vBatter normal-cell-left center-text">
                    <p id="hPos1" class="adjustPos"></p>
                    </div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell-before-batter-result"></div>
                    <div class="hbatter-result1">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result2">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result3">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result4">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result5">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result6">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result7">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result8">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result9">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result10">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result11">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result12">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbench1"></div>

                    <!-- BACKUP 1ST ROW -->
                    <div class="normal-cell-left grey-cell last-row"></div>
                    <div class="normal-cell-left grey-cell last-row"></div>
                    <div class="normal-cell grey-cell last-row"></div>
                    <div class="normal-cell grey-cell last-row"></div>
                    <div class="normal-cell grey-cell last-row"></div>
                    <div class="normal-cell-before-batter-result grey-cell last-row"></div>
                    <div class="hbench2 grey-cell"></div>

                    <!-- 2ND ROW -->
                    <div class="vBatter normal-cell-left">
                    <p class="lineupNumbersLeft">2</p>
                    <p id="hBatter2Name" class="adjustName"></p>
                    </div>
                    <div class="vBatter normal-cell-left center-text">
                    <p id="hPos2" class="adjustPos"></p>
                    </div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell-before-batter-result"></div>
                    <div class="hbatter-result13">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result14">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result15">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result16">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result17">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result18">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result19">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result20">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result21">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result22">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result23">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result24">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbench3"></div>

                    <!-- BACKUP 2ND ROW -->
                    <div class="normal-cell-left grey-cell last-row"></div>
                    <div class="normal-cell-left grey-cell last-row"></div>
                    <div class="normal-cell grey-cell last-row"></div>
                    <div class="normal-cell grey-cell last-row"></div>
                    <div class="normal-cell grey-cell last-row"></div>
                    <div class="normal-cell-before-batter-result grey-cell last-row"></div>
                    <div class="hbench4 grey-cell"></div>

                    <!-- 3RD ROW -->

                    <div class="vBatter normal-cell-left">
                    <p class="lineupNumbersLeft">3</p>
                    <p id="hBatter3Name" class="adjustName"></p>
                    </div>
                    <div class="vBatter normal-cell-left center-text">
                    <p id="hPos3" class="adjustPos"></p>
                    </div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell-before-batter-result"></div>
                    <div class="hbatter-result25">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result26">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result27">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result28">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result29">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result30">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result31">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result32">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result33">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result34">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result35">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result36">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbench5"></div>

                    <!-- BACKUP 3RD ROW -->
                    <div class="normal-cell-left grey-cell last-row"></div>
                    <div class="normal-cell-left grey-cell last-row"></div>
                    <div class="normal-cell grey-cell last-row"></div>
                    <div class="normal-cell grey-cell last-row"></div>
                    <div class="normal-cell grey-cell last-row"></div>
                    <div class="normal-cell-before-batter-result grey-cell last-row"></div>
                    <div class="hbench6 grey-cell"></div>

                    <!-- 4TH ROW -->
                    <div class="vBatter normal-cell-left">
                    <p class="lineupNumbersLeft">4</p>
                    <p id="hBatter4Name" class="adjustName"></p>
                    </div>
                    <div class="vBatter normal-cell-left center-text">
                    <p id="hPos4" class="adjustPos"></p>
                    </div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell-before-batter-result"></div>
                    <div class="hbatter-result37">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result38">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result39">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result40">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result41">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result42">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result43">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result44">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result45">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result46">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result47">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result48">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbench7"></div>

                    <!-- BACKUP 4TH ROW -->
                    <div class="normal-cell-left grey-cell last-row"></div>
                    <div class="normal-cell-left grey-cell last-row"></div>
                    <div class="normal-cell grey-cell last-row"></div>
                    <div class="normal-cell grey-cell last-row"></div>
                    <div class="normal-cell grey-cell last-row"></div>
                    <div class="normal-cell-before-batter-result grey-cell last-row"></div>
                    <div class="hbench8 grey-cell"></div>

                    <!-- 5TH ROW -->
                    <div class="vBatter normal-cell-left">
                    <p class="lineupNumbersLeft">5</p>
                    <p id="hBatter5Name" class="adjustName"></p>
                    </div>
                    <div class="vBatter normal-cell-left center-text">
                    <p id="hPos5" class="adjustPos"></p>
                    </div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell-before-batter-result"></div>
                    <div class="hbatter-result49">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result50">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result51">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result52">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result53">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result54">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result55">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result56">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result57">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result58">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result59">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result60">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="bullpen headGrid hbench9">BULLPEN</div>

                    <!-- BACKUP 5TH ROW -->
                    <div class="normal-cell-left grey-cell last-row"></div>
                    <div class="normal-cell-left grey-cell last-row"></div>
                    <div class="normal-cell grey-cell last-row"></div>
                    <div class="normal-cell grey-cell last-row"></div>
                    <div class="normal-cell grey-cell last-row"></div>
                    <div class="normal-cell-before-batter-result grey-cell last-row"></div>
                    <div class="hbench10 grey-cell"></div>

                    <!-- 6TH ROW -->
                    <div class="vBatter normal-cell-left">
                    <p class="lineupNumbersLeft">6</p>
                    <p id="hBatter6Name" class="adjustName"></p>
                    </div>
                    <div class="vBatter normal-cell-left center-text">
                    <p id="hPos6" class="adjustPos"></p>
                    </div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell-before-batter-result"></div>
                    <div class="hbatter-result61">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result62">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result63">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result64">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result65">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result66">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result67">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result68">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result69">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result70">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result71">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result72">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbench11"></div>

                    <!-- BACKUP 6TH ROW -->
                    <div class="normal-cell-left grey-cell last-row"></div>
                    <div class="normal-cell-left grey-cell last-row"></div>
                    <div class="normal-cell grey-cell last-row"></div>
                    <div class="normal-cell grey-cell last-row"></div>
                    <div class="normal-cell grey-cell last-row"></div>
                    <div class="normal-cell-before-batter-result grey-cell last-row"></div>
                    <div class="hbench12 grey-cell"></div>

                    <!-- 7TH ROW -->

                    <div class="vBatter normal-cell-left">
                    <p class="lineupNumbersLeft">7</p>
                    <p id="hBatter7Name" class="adjustName"></p>
                    </div>
                    <div class="vBatter normal-cell-left center-text">
                    <p id="hPos7" class="adjustPos"></p>
                    </div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell-before-batter-result"></div>
                    <div class="hbatter-result73">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result74">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result75">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result76">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result77">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result78">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result79">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result80">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result81">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result82">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result83">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result84">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbench13"></div>

                    <!-- BACKUP 7TH ROW -->
                    <div class="normal-cell-left grey-cell last-row"></div>
                    <div class="normal-cell-left grey-cell last-row"></div>
                    <div class="normal-cell grey-cell last-row"></div>
                    <div class="normal-cell grey-cell last-row"></div>
                    <div class="normal-cell grey-cell last-row"></div>
                    <div class="normal-cell-before-batter-result grey-cell last-row"></div>
                    <div class="hbench14 grey-cell"></div>

                    <!-- 8TH ROW -->

                    <div class="vBatter normal-cell-left">
                    <p class="lineupNumbersLeft">8</p>
                    <p id="hBatter8Name" class="adjustName"></p>
                    </div>
                    <div class="vBatter normal-cell-left center-text">
                    <p id="hPos8" class="adjustPos"></p>
                    </div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell-before-batter-result"></div>
                    <div class="hbatter-result85">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result86">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result87">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result88">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result89">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result90">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result91">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result92">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result93">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result94">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result95">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result96">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbench15"></div>

                    <!-- BACKUP 8TH ROW -->
                    <div class="normal-cell-left grey-cell last-row"></div>
                    <div class="normal-cell-left grey-cell last-row"></div>
                    <div class="normal-cell grey-cell last-row"></div>
                    <div class="normal-cell grey-cell last-row"></div>
                    <div class="normal-cell grey-cell last-row"></div>
                    <div class="normal-cell-before-batter-result grey-cell last-row"></div>
                    <div class="hbench16 grey-cell"></div>

                    <!-- 9TH ROW -->

                    <div class="vBatter normal-cell-left">
                    <p class="lineupNumbersLeft">9</p>
                    <p id="hBatter9Name" class="adjustName"></p>
                    </div>
                    <div class="vBatter normal-cell-left center-text">
                    <p id="hPos9" class="adjustPos"></p>
                    </div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell-before-batter-result"></div>
                    <div class="hbatter-result97">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result98">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result99">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result100">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result101">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result102">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result103">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result104">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result105">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result106">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result107">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbatter-result108">
                    <p class="diamond-test"></p>
                    </div>
                    <div class="hbench17"></div>

                    <!-- BACKUP 9TH ROW -->
                    <div class="grey-cell grid-border-left-thick"></div>
                    <div class="grey-cell grid-border-left-thick"></div>
                    <div class="grey-cell grid-border-left"></div>
                    <div class="grey-cell grid-border-left"></div>
                    <div class="grey-cell grid-border-left"></div>
                    <div class="grey-cell grid-border-left"></div>
                    <div class="grey-cell hbench18"></div>

                    <!-- HOME PITCHING SECTION -->
                    <div class="home-pitching headGrid">HOME PITCHERS</div>
                    <div class="home-notes headGrid">NOTES</div>
                    <div class="headGrid grid-border-left-thick">IP</div>
                    <div class="headGrid grid-border-left">H</div>
                    <div class="headGrid grid-border-left">R</div>
                    <div class="headGrid grid-border-left">ER</div>
                    <div class="headGrid grid-border-left">BB</div>
                    <div class="headGrid grid-border-left">SO</div>
                    <div class="headGrid grid-border-left">HBP</div>
                    <div class="headGrid grid-border-left">BF</div>
                    <div class="headGrid grid-border-left">WP</div>
                    <div class="home-team-notes-header headGrid">HOME TEAM NOTES</div>

                    <!-- ROW 1 -->
                    <div class="vPitcher normal-cell-left">
                    <p id="hPitcherName" class="adjustName"></p>
                    </div>
                    <div class="hnotes-left1"></div>
                    <div class="normal-cell-left"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="home-team-notes grid-border-bottom-thick"></div>


                    <!-- ROW 2 -->
                    <div class="normal-cell-left grey-cell"></div>
                    <div class="hnotes-left2 grey-cell"></div>
                    <div class="normal-cell-left grey-cell"></div>
                    <div class="normal-cell grey-cell"></div>
                    <div class="normal-cell grey-cell"></div>
                    <div class="normal-cell grey-cell"></div>
                    <div class="normal-cell grey-cell"></div>
                    <div class="normal-cell grey-cell"></div>
                    <div class="normal-cell grey-cell"></div>
                    <div class="normal-cell grey-cell"></div>
                    <div class="normal-cell grey-cell"></div>

                    <!-- ROW 3 -->
                    <div class="normal-cell-left"></div>
                    <div class="hnotes-left3"></div>
                    <div class="normal-cell-left"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>

                    <!-- ROW 4 -->
                    <div class="normal-cell-left grey-cell"></div>
                    <div class="hnotes-left4 grey-cell"></div>
                    <div class="normal-cell-left grey-cell"></div>
                    <div class="normal-cell grey-cell"></div>
                    <div class="normal-cell grey-cell"></div>
                    <div class="normal-cell grey-cell"></div>
                    <div class="normal-cell grey-cell"></div>
                    <div class="normal-cell grey-cell"></div>
                    <div class="normal-cell grey-cell"></div>
                    <div class="normal-cell grey-cell"></div>
                    <div class="normal-cell grey-cell"></div>

                    <!-- ROW 5 -->
                    <div class="normal-cell-left"></div>
                    <div class="hnotes-left5"></div>
                    <div class="normal-cell-left"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>

                    <!-- ROW 6 -->
                    <div class="normal-cell-left grey-cell"></div>
                    <div class="hnotes-left6 grey-cell"></div>
                    <div class="normal-cell-left grey-cell"></div>
                    <div class="normal-cell grey-cell"></div>
                    <div class="normal-cell grey-cell"></div>
                    <div class="normal-cell grey-cell"></div>
                    <div class="normal-cell grey-cell"></div>
                    <div class="normal-cell grey-cell"></div>
                    <div class="normal-cell grey-cell"></div>
                    <div class="normal-cell grey-cell"></div>
                    <div class="normal-cell grey-cell"></div>

                    <!-- ROW 7 -->
                    <div class="normal-cell-left"></div>
                    <div class="hnotes-left7"></div>
                    <div class="normal-cell-left"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>
                    <div class="normal-cell"></div>

                    <!-- ROW 8 -->
                    <div class="grey-cell grid-border-left-thick grid-border-bottom-thick"></div>
                    <div class="hnotes-left8 grey-cell grid-border-bottom-thick"></div>
                    <div class="grey-cell grid-border-left-thick grid-border-bottom-thick"></div>
                    <div class="grey-cell grid-border-left grid-border-bottom-thick"></div>
                    <div class="grey-cell grid-border-left grid-border-bottom-thick"></div>
                    <div class="grey-cell grid-border-left grid-border-bottom-thick"></div>
                    <div class="grey-cell grid-border-left grid-border-bottom-thick"></div>
                    <div class="grey-cell grid-border-left grid-border-bottom-thick"></div>
                    <div class="grey-cell grid-border-left grid-border-bottom-thick"></div>
                    <div class="grey-cell grid-border-left grid-border-bottom-thick"></div>
                    <div class="grey-cell grid-border-left grid-border-bottom-thick"></div>
                    <div class="copyright copyright-place center-text">
                        <p id="footer-copyright">&copy; 2021 DigiDugout &nbsp; &nbsp; https://www.digidugout.com</p>
                    </div>
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
                                    <p><?php echo $row['winningpitchername']; ?></p>
                                    <p class="pitcher-right-align">Losing Pitcher:</p>
                                    <p><?php echo $row['losingpitchername']; ?></p>
                                    <p class="pitcher-right-align">Save:</p>
                                    <p><?php
                                        echo $row['savingpitchername']; ?>
                                    </p>
                                </div>
                                <div class="button-options">
                                    <div class="btn-scoresheet button-choice" data-team="<?php echo $row['hometeam']; ?>" data-gamenum="<?php echo $row['game']; ?>">
                                        VIEW LINEUPS
                                    </div>
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