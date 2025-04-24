---
layout: default
title: Achievements
permalink: /achievements/
---

# Achievements

## See my achievements

Enter the FAForever player name to see their achievements.

### Player:
<input type="text" id="playerName" />

<button onclick="getStatsFromDocument()">Show me!</button>

### Loading Zone
<div id="loadingZone" style="text-align:center;display:none;">
  <p id="loadingZoneDescription">
    Loading achievements...
  </p>
  <img src="{{ "/assets/loading.gif" | relative_url }}" alt="loading..."><br>
  This can take up to one minute!
</div>

### Results
<div id="receiverZone"></div>

---

<script src="{{ "/assets/js/achievements.js" | relative_url }}"></script>
