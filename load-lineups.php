<?php
    include("$_SERVER[DOCUMENT_ROOT]/../configtest.php");
    include "functions.php";

    $date = $_POST['gameid'];
    $team = $_POST['team'];
    $gamenum = $_POST['gamenum'];

    $sql = "SELECT * FROM GAMELOGS_MAIN WHERE DATE = '$date' AND HOMETEAM='$team' AND GAME='$gamenum'";
    
    $result = mysqli_query($link, $sql);
    $resultCheck = mysqli_num_rows($result);

    // separate the date
    $month = (int)substr($date,4,2) - 1;
    // echo "month: " . $month;
    $day = (int)substr($date,6,2);
    // echo "day: " . $day;
    $year = substr($date, 0, 4);
    // echo "year: " . $year;
    
    // store days with games in array
    if($resultCheck > 0) {
         while ($row = mysqli_fetch_assoc($result)) {
        ?>
        <script>
            $('.lineup-cancel-button').on('click', function(e) {
                e.stopPropagation();
                document.documentElement.style.overflow = "scroll";
                $('#mylineups').css('display', 'none');
                $('#gridgames').css('filter', 'blur(0px)');
                $('.date-selector').css('display', 'block');
            });

            $('.lineup-print-button').on('click', function() {
                $("#mylineups").css("display", "none");
                setTimeout(() => {
                    window.print();
                }, 100);
            })

            window.onafterprint = function() {
                $("#mylineups").css("display", "grid");
            }

            $('.lineup-home-team-button').on('click', function(e) {
                // show home team lineup
                $('.lineup-home').css('display','grid');
                $('.lineup-visitors').css('display','none');

                // show home pitcher
                $('#home-starting-pitcher-header').removeClass("hide-element");
                $('#home-starting-pitcher').removeClass("hide-element");

                // hide visitors pitcher
                $('#visitors-starting-pitcher-header').addClass("hide-element");
                $('#visitors-starting-pitcher').addClass("hide-element");
            });

            $('.lineup-visitor-team-button').on('click', function(e) {

                // show visitor team lineup
                $('.lineup-visitors').css('display','grid');
                $('.lineup-home').css('display','none');

                // show visitors pitcher header
                $('#visitors-starting-pitcher-header').removeClass("hide-element");
                $('#visitors-starting-pitcher').removeClass("hide-element");

                // hide visitors pitcher header
                $('#home-starting-pitcher-header').addClass("hide-element");
                $('#home-starting-pitcher').addClass("hide-element");
            });
            // $('.btn-scoresheet').on('click', function(e) {
            //         thisGameId = $("#mydate").val();
            //         thisTeam = $(e.target).data("team");
            //         thisGameNum = $(e.target).data("gamenum");
            //         $('#gridgames').css('filter', 'blur(4px)');
            //         // show lineups
            //         $("#mylineups").load("load-lineups.php", {
            //             gameid: thisGameId,
            //             team: thisTeam,
            //             gamenum: thisGameNum
            //         }, function() {
            //             $('.date-selector').css('display', 'none');
            //             document.documentElement.style.overflow = "hidden";
            //             $(window).scrollTop(0);
            //             $("#mylineups").css('display', 'grid');

            //             // populate scoresheet
            //             $('#footer-date').text($('.current-date').text());

            //             // get teams and replace headers
            //             $('#vis-team-name').text($('.lineup-visitor-team-button').text());
            //             $('#home-team-name').text($('.lineup-home-team-button').text());

            //             const visLineupNames = document.querySelectorAll('.lineup-visitors .lineup-name');
            //             const homeLineupNames = document.querySelectorAll('.lineup-home .lineup-name');

            //             const visPos = document.querySelectorAll('.lineup-visitors .lineup-pos');
            //             const homePos = document.querySelectorAll('.lineup-home .lineup-pos');
                        
            //             for(let i = 0; i < 9; i++) {
            //                 $('#vBatter' + (i+1) + 'Name').text(visLineupNames[i].textContent);
            //                 $('#hBatter' + (i+1) + 'Name').text(homeLineupNames[i].textContent);

            //                 $('#vPos' + (i+1)).text(visPos[i].textContent);
            //                 $('#hPos' + (i+1)).text(homePos[i].textContent);
            //             }

            //             $('#vPitcherName').text($('#visitors-starting-pitcher').text());
            //             $('#hPitcherName').text($('#home-starting-pitcher').text());

            //             $('.scoreboard-team1').text($('.lineup-visitor-team-button').text());
            //             $('.scoreboard-team2').text($('.lineup-home-team-button').text());
            //         });
            //     });
        </script>
            <div class="lineup-date"><?php echo getCurrentMonthFull($month) . " " . $day . ", " . $year; ?></div>
            <div class="lineup-team-buttons">
                <div class="lineup-visitor-team-button team-button" style="color: <?php echo getTeamFontColor($row['visteam']); ?>; background-color: <?php echo getTeamColor($row['visteam']); ?>"><?php echo getTeamName($row['visteam'], $year); ?></div>
                <div class="lineup-home-team-button team-button" style="color: <?php echo getTeamFontColor($row['hometeam']); ?>; background-color: <?php echo getTeamColor($row['hometeam']); ?>"><?php echo getTeamName($row['hometeam'], $year); ?></div></div>
                <div class="lineup-visitors lineup-grid-container" style="background-color: <?php echo getTeamColor($row['visteam']) . '44'; ?>">
                    <?php for($i = 1; $i < 10; $i++) {
                        ?>
                        <div class="lineup-num"><?php echo $i; ?></div>
                        <div class="lineup-name"><?php echo $row['visplayername' . $i]; ?></div>
                        <div class="lineup-pos"><?php echo getPositionByNumber($row['visplayerposition' . $i]); ?></div>
                        <?php
                    } ?>
                </div>
            <div id="visitors-starting-pitcher-header" class="starting-pitcher-header" style="background-color: <?php echo getTeamColor($row['visteam']); ?>; color: <?php echo getTeamFontColor($row['visteam']); ?>">Starting Pitcher</div>
            <div id="visitors-starting-pitcher" class="lineup-starting-pitcher" style="background-color: <?php echo getTeamColor($row['visteam']) . '44'; ?>;"><?php echo $row['vispitchername']; ?></div>

            <div class="lineup-home lineup-grid-container" style="background-color: <?php echo getTeamColor($row['hometeam']) . '44'; ?>">
                <?php for($i = 1; $i < 10; $i++) {
                ?>
                <div class="lineup-num"><?php echo $i; ?></div>
                <div class="lineup-name"><?php echo $row['homeplayername' . $i]; ?></div>
                <div class="lineup-pos"><?php echo getPositionByNumber($row['homeplayerposition' . $i]); ?></div>
                <?php } ?>
            </div>
            <div id="home-starting-pitcher-header" class="starting-pitcher-header hide-element" style="background-color: <?php echo getTeamColor($row['hometeam']); ?>; color: <?php echo getTeamFontColor($row['hometeam']); ?>">Starting Pitcher</div>
            <div id="home-starting-pitcher" class="lineup-starting-pitcher hide-element" style="background-color: <?php echo getTeamColor($row['hometeam']) . '44'; ?>;"><?php echo $row['homepitchername']; ?></div>
            <div class="lineup-action-buttons">
                <div class="lineup-cancel-button action-button">Cancel</div>
                <div class="lineup-print-button action-button">Print Scoresheet</div>
            </div>
        <?php
         }
     }
?>