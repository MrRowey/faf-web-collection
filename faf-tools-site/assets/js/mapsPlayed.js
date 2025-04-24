function escapeHtml(unsafe) {
    return unsafe
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");
  }
  
  function displayMap(rootElement, map) {
    const html = `
      <div class="card m-2 position-relative shadow" style="width: 200px">
        <img src="${encodeURI(map.imageUrl)}" alt="map image" class="card-img-top" style="width: 200px; height: 200px"/>
        <div class="card-body">
          <h4 class="card-text position-absolute shadow-lg" style="top: 10px; left: 10px">
            <span class="badge badge-info">${map.count}</span>
          </h4>
          <h4 class="card-title">${escapeHtml(map.name)}</h4>
        </div>
      </div>
    `;
    rootElement.innerHTML += html;
  }
  
  function displayJson(jsonData) {
    const rootElement = document.getElementById("receiverZone");
    rootElement.innerHTML = ""; // clear previous results
    jsonData.forEach(map => displayMap(rootElement, map));
  }
  
  function displayLoadingState(isLoading, playerName = "") {
    document.getElementById("description").style.display = isLoading ? "none" : "block";
    document.getElementById("loadingZone").style.display = isLoading ? "block" : "none";
  
    if (isLoading) {
      document.getElementById("loadingZoneDescription").textContent = `Loading ${playerName}'s maps played...`;
    } else {
      document.getElementById("playerNameTextContainer").style.display = "flex";
      document.getElementById("playerNameText").textContent = `Showing ${playerName}'s maps played...`;
    }
  }
  
  async function getStatsFromDocument() {
    const playerName = document.getElementById("playerName").value;
    if (!playerName) return;
  
    displayLoadingState(true, playerName);
  
    const maps = await getMapsPlayed(playerName);
    displayJson(maps);
  
    displayLoadingState(false, playerName);
  }
  
  async function getMapsPlayed(playerName) {
    const url = `https://api.faforever.com/data/game?sort=-endTime&filter=playerStats.player.login==${encodeURIComponent(playerName)}&include=mapVersion,mapVersion.map&fields[game]=mapVersion&fields[mapVersion]=map,thumbnailUrlLarge&fields[map]=displayName&page[limit]=10000`;
  
    try {
      const response = await fetch(url);
      if (!response.ok) throw new Error(`FAF API error: ${response.status}`);
  
      const json = await response.json();
      const mapVersions = {};
      const maps = {};
  
      json.data.forEach(game => {
        const mapVersionId = game.relationships?.mapVersion?.data?.id;
        if (mapVersionId) mapVersions[mapVersionId] = (mapVersions[mapVersionId] || 0) + 1;
      });
  
      json.included.forEach(include => {
        const { id, type, attributes, relationships } = include;
  
        if (type === 'mapVersion') {
          const mapId = relationships?.map?.data?.id;
          const imageUrl = attributes?.thumbnailUrlLarge;
          if (!mapId) return;
  
          if (!maps[mapId]) {
            maps[mapId] = { count: mapVersions[id] || 0, imageUrl };
          } else {
            maps[mapId].count += mapVersions[id] || 0;
          }
        }
  
        if (type === 'map') {
          if (!maps[id]) maps[id] = {};
          maps[id].name = attributes?.displayName;
        }
      });
  
      return Object.values(maps).sort((a, b) => b.count - a.count);
    } catch (err) {
      alert("Error loading maps: " + err.message);
      console.error(err);
      return [];
    }
  }
  