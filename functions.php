<?php
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
?>