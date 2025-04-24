---
layout: default
title: Hours Wasted Calculator
permalink: /hours-wasted/
---

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hours Wasted Calculator</title>
    <style>
        body {
            font-family: Calibri, sans-serif;
            text-align: center;
            padding: 20px;
        }

        #loadingZone {
            visibility: hidden;
        }

        #receiverZone {
            margin-top: 20px;
        }

        .input-field {
            padding: 8px;
            width: 200px;
            margin: 10px;
            border: 1px solid #ccc;
        }

        .btn {
            padding: 8px 15px;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <h1>FAF Time Wasted Calculator</h1>
    <p>Enter your FAForever player name:</p>
    <input type="text" id="playerName" class="input-field" placeholder="Enter player name">
    <button class="btn" onclick="checkPlayerName()">Check Time Wasted</button>
    
    <div id="loadingZone">
        Loading...<br>
        <img src="loading.gif" alt="loading">
    </div>
    
    <div id="receiverZone"></div>

    <!-- Link to the external JavaScript file -->
    <script src="time-wasted-calculator.js"></script>
</body>
</html>
