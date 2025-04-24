---
layout: default
title: FAF Growth
---

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <link href="{{ '/assets/css/style.css' | relative_url }}" rel="stylesheet" type="text/css">
    <link rel="icon" href="{{ '/assets/images/favicon.png' | relative_url }}" />
    <title>FAF Growth</title>
  </head>
  <body>
    <div id="description" style="text-align:center;">
      <h2>FAF Growth</h2>
      <p><b>Records:</b>
        <input type="text" placeholder="(optional)" id="records" value="3000">
      </p>
      <p>
        <button onclick="getStatsFromDocument()">Let's see</button>
      </p>
    </div>

    <div id="loadingZone" style="text-align:center;display:none;">
      <p id="loadingZoneDescription">Loading records...</p>
      <img src="{{ '/assets/images/loading.gif' | relative_url }}" alt="loading..."><br>
      <p>This should take less than a minute!</p>
    </div>

    <div id="receiverZone"></div>

    <!-- Link to external JS file -->
    <script src="{{ '/assets/js/scripts.js' | relative_url }}"></script>
  </body>
</html>
