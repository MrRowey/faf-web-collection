---
layout: default
title: Maps Played
permalink: /maps-played/
---

<div id="description" style="text-align:center;" class="container-fluid">
  <h2>See my played maps</h2>
  <div class="row justify-content-md-center">
    <div class="form-group col-2">
      <label for="playerName">Player:</label>
      <input type="text" class="form-control" placeholder="player name" id="playerName" />
      <button onclick="getStatsFromDocument()" type="button" class="btn btn-primary m-3">
        Show me!
      </button>
    </div>
  </div>
</div>

<div id="loadingZone" style="text-align:center;display:none;">
  <p id="loadingZoneDescription">Loading maps played...</p>
  <img src="{{ '/assets/images/loading.gif' | relative_url }}" alt="loading gif" />
  <p>This can take up to one minute!</p>
</div>

<div id="receiverZone" class="d-flex flex-row flex-wrap">
  <nav id="playerNameTextContainer" class="navbar navbar-expand-sm bg-light justify-content-center w-100 sticky-top" style="display: none">
    <ul class="navbar-nav">
      <li class="nav-item">
        <h4 id="playerNameText">Link 1</h4>
      </li>
    </ul>
  </nav>
</div>

<script src="{{ '/assets/js/maps-played.js' | relative_url }}"></script>
