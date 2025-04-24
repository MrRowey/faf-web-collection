function getStatsFromDocument() {
    const records = parseFloat(document.getElementById('records').value);
    getStats(records);
  }
  
  async function getStats(records) {
    const loadingZone = document.getElementById('loadingZone');
    const description = document.getElementById('description');
    const loadingZoneDescription = document.getElementById('loadingZoneDescription');
    const receiverZone = document.getElementById('receiverZone');
  
    description.style.display = "none";
    loadingZone.style.display = "block";
    loadingZoneDescription.innerHTML = `Loading ${records} records...`;
  
    try {
      const response = await fetch(`/calculator?records=${records}`);
      const jsonData = await response.json();
      loadingZone.style.display = "none";
      displayResults(jsonData, receiverZone);
    } catch (error) {
      loadingZone.style.display = "none";
      receiverZone.innerHTML = `Error: ${error.message}`;
      console.error(error);
    }
  }
  
  function displayResults(jsonData, receiverZone) {
    const { time, bounds, players, meta } = jsonData;
    const lowTimestamp = time.oldest;
    const highTimestamp = time.newest;
  
    const lowTime = new Date(0).setUTCSeconds(lowTimestamp).toDateString();
    const highTime = new Date(0).setUTCSeconds(highTimestamp).toDateString();
  
    const headerString = `
      <div class="metaInfo">
        <span class="gameAmount">Latest ${meta.records} new player records</span> - 
        The <span class="gameAmount">FIRST DOT</span> and the <span class="gameAmount">LAST DOT</span> might be generated from truncated data and therefore erroneous. 
        <br><span class="timeRange">(From ${lowTime} to ${highTime})</span>
      </div>
    `;
  
    receiverZone.innerHTML = headerString;
    
    if (meta.records <= 0) {
      receiverZone.innerHTML += "Empty records or invalid parameters";
      console.log(jsonData);
      return;
    }
  
    // Graph init
    const canvas = document.createElement('canvas');
    canvas.id = 'graphCanvas';
    canvas.width = 1800;
    canvas.height = 256;
    canvas.style.border = '1px solid white';
    canvas.style.backgroundColor = 'rgba(0,0,0,0)';
    receiverZone.appendChild(canvas);
  
    const ctx = canvas.getContext("2d");
    ctx.textBaseline = "middle";
    const margin = { x: 50, y: canvas.height / 2 };
  
    const timeBounds = {
      lowest: Math.floor(lowTimestamp / 86400) * 86400,
      highest: Math.ceil(highTimestamp / 86400) * 86400
    };
  
    let lastPoint = { x: -1, y: -1 };
    let i = 0;
    const guidesEvery = 86400;
    const guidesRange = [lowTimestamp, highTimestamp];
    const guideColor = "#FFCF52";
  
    // Graph plotting
    for (let j = guidesRange[0]; j < guidesRange[1]; j += guidesEvery) {
      const value = (j - timeBounds.lowest) / (timeBounds.highest - timeBounds.lowest);
      const playerAmount = players[Math.floor(j / 86400)].length;
      const data = (playerAmount - bounds.lowest) / (bounds.highest - bounds.lowest);
  
      const currentPoint = {
        x: canvas.width - (value * (canvas.width - margin.x)) - margin.x / 2,
        y: canvas.height - (data * (canvas.height - margin.y)) - margin.y / 2
      };
  
      if (currentPoint.x > canvas.width - margin.x / 2) continue;
  
      const line = {
        x1: currentPoint.x,
        y1: currentPoint.y,
        x2: currentPoint.x,
        y2: canvas.height - margin.y / 4
      };
  
      // Draw guide line and point
      ctx.strokeStyle = guideColor;
      ctx.beginPath();
      ctx.moveTo(line.x1, line.y1);
      ctx.lineTo(line.x2, line.y2);
      ctx.stroke();
  
      ctx.fillStyle = guideColor;
      ctx.beginPath();
      ctx.arc(currentPoint.x, currentPoint.y, 2, 0, 2 * Math.PI);
      ctx.fill();
  
      // Draw time and playerAmount
      ctx.font = "12px Calibri";
      const date = new Date(0);
      date.setUTCSeconds(j);
      const time = `${date.getDate()}/${date.getMonth() + 1}`;
      ctx.strokeText(time, line.x1, canvas.height - 7 - 14 * (i % 2));
      ctx.strokeText(playerAmount, line.x1, currentPoint.y - 10);
  
      // Draw line connecting points
      if (lastPoint.x > -1) {
        ctx.strokeStyle = guideColor;
        ctx.beginPath();
        ctx.moveTo(lastPoint.x, lastPoint.y);
        ctx.lineTo(currentPoint.x, currentPoint.y);
        ctx.stroke();
      }
  
      i++;
      lastPoint = currentPoint;
    }
  
    // Display client stats
    const clientStats = calculateClientStats(players);
    receiverZone.appendChild(createStatsElement("Client stats", clientStats));
  
    // Display other stats
    const otherStats = [
      `Best day: ${clientStats.bestDay} (${bounds.highest} new users)`,
      `Worst day: ${clientStats.worstDay} (${bounds.lowest} new users)`
    ];
    receiverZone.appendChild(createStatsElement("Other stats", otherStats));
  }
  
  function calculateClientStats(players) {
    const clients = {};
    let bestDay = '';
    let worstDay = '';
  
    players.forEach(playerRecords => {
      playerRecords.forEach(player => {
        if (!clients[player.userAgent]) clients[player.userAgent] = 0;
        clients[player.userAgent]++;
      });
    });
  
    return { clients, bestDay, worstDay };
  }
  
  function createStatsElement(title, stats) {
    const p = document.createElement('p');
    p.textContent = title;
    const list = document.createElement('ul');
  
    stats.forEach(stat => {
      const li = document.createElement('li');
      li.textContent = stat;
      list.appendChild(li);
    });
  
    p.appendChild(list);
    return p;
  }
  