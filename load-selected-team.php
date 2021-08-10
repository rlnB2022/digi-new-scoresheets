<?php
    session_start();

    include "keepers_config.php";

    // Check if the user is already logged in, if yes then redirect him to welcome page
    if(!isset($_SESSION["loggedin"]) && !$_SESSION["loggedin"] === true){
        header("location: keepers.php");
        exit;
    }

    $teamid = $_POST['team'];

    $sql = "SELECT teamname, manager_name FROM teams WHERE team_id='" . $teamid . "' LIMIT 1";

    $result = mysqli_query($link, $sql);
    $resultCheck = mysqli_num_rows($result);
    
    if($resultCheck > 0) {
        $row = mysqli_fetch_assoc($result);

        $teamname = $row['teamname'];
        $managername = $row['manager_name'];

    }
?>
<div class="team-viewer-top">
    <div class="team-info">
        <div>Team Name: <input id="user-teamname" type="text" value="<?php echo $teamname; ?>"></div>
        <div>Manager Name: <input id="user-managername" type="text" value="<?php echo $managername; ?>"></div>
    </div>
    <div class="team-info-save-button"><input id="save-team-info" type="submit" value="Save"></div>
</div>
<hr>
<div class="team-viewer-players">
    <?php
        $sql = "SELECT users_players.player_id, PLAYERS.firstname, PLAYERS.lastname FROM users_players INNER JOIN PLAYERS ON users_players.player_id = PLAYERS.playerid where team_id='" . $teamid . "'";

        $result = mysqli_query($link, $sql);
        $resultCheck = mysqli_num_rows($result);
        
        if($resultCheck > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<div class='roster-player'>" . $row['firstname'] . " " . $row['lastname'] . "</div>";
            }
        }
        else {
            echo "<p class='center-message'>No players found. Click the Add Player button below to add players.</p>";
        }
    ?>
</div>

<div class="team-viewer-buttons">
    <div id="add-player" class="admin-button">
        Add Player
    </div>
</div>

<div id="add-player-overlay" class="overlay">
    <div class="add-player-search">
        <input id="last-name" type="text" name="name" placeholder="Enter a last name">
        <div id="search-player-button" class="btn">
            Search
        </div>
    </div>

    <div class="player-list-outer">
        <div class="player-list-inner">
        </div>
    </div>

    <div class="add-player-buttons">
        <div class="btn btn-add" id="add-player-cancel">
            Cancel
        </div>
        <div class="btn" id="add-player-to-team">
            Add Player
        </div>
    </div>
</div>

<script>
    $('#add-player').on('click', function() {
        $('#add-player-overlay').toggle('fast');
        $('.dark-bg').toggle();
    });
    $('#add-player-cancel').on('click', function() {
        // clear player list
        $('#player-list').remove();
        $('#add-player-overlay').toggle('fast');
        $('.dark-bg').toggle();
    });

    $('.dark-bg').on('click', function()  {
        $('#add-player-overlay').toggle('fast');
        $('.dark-bg').toggle();
    });
    $('#search-player-button').on('click', function() {
        name_search = $("#last-name").val();

        $(".player-list-outer").load("get_player_names.php", {
            name: name_search
        }, function() {

        });
    });

    $('#add-player-to-team').on('click', function() {
        const li_count = $('.player-list-outer ul li').length;
        
        if(li_count == 0) {
            return;
        }

        const selected_li = $('.player-list-outer ul li.active > div');
        const team_id = $('.team-selection .selected-team').data('team-id');

        if(selected_li.is(':empty')) {
            // add player to team
            const player_id = $('.player-list-outer ul li.active').data('item');

            $('.team-viewer-players').load('store_player_on_team.php', {
                playerid: player_id,
                teamid: team_id
            });
        }
        else {
            alert('Player already assigned.');
        }
    });

    $('#save-team-info').on('click', function() {
        team = $('.team-selection .selected-team').data('team-id');
        teamname = $('#user-teamname').val();
        managername = $('#user-managername').val();

        $('.team-info').load('store_team_info.php', {
            team_id: team,
            tname: teamname,
            mname: managername
        }, function() {
            newteam = $('.team-selection .selected-team').data('team-id');

            $('.team-selection').load('show_all_teams.php', {
                selection_team_id: newteam
            }, function() {

            });
        });
    });
</script>