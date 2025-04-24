async function getStatsFromDocument() {
    const playerName = document.getElementById('playerName').value;
    await getStats(playerName);
}

async function getStats(playerName) {
    const description = document.getElementById('description');
    const loadingZone = document.getElementById('loadingZone');
    const loadingZoneDescription = document.getElementById('loadingZoneDescription');
    const receiverZone = document.getElementById('receiverZone');

    description.style.display = "none";
    loadingZone.style.display = "block";
    loadingZoneDescription.innerHTML = `Loading ${playerName}'s achievements...`;

    try {
        const achievements = await fetchPlayerAchievements(playerName);
        const displayString = buildAchievementDisplay(achievements);
        receiverZone.innerHTML = displayString;
    } catch (error) {
        receiverZone.innerHTML = `Error: ${error.message} - Please try again later.`;
    } finally {
        loadingZone.style.display = "none";
    }
}

// Fetch player achievements and merge with data
async function fetchPlayerAchievements(playerName) {
    const achievements = {};
    
    const playerAchievementsURL = `https://api.faforever.com/data/playerAchievement?filter=player.login==${playerName}&include=achievement&fields[playerAchievement]=achievement,state,updateTime,currentSteps&fields[achievement]=description,name,unlockedIconUrl,totalSteps`;
    const achievementURL = 'https://api.faforever.com/data/achievement?fields[achievement]=description,name,unlockedIconUrl,totalSteps';

    try {
        const [playerResponse, achievementResponse] = await Promise.all([
            fetch(playerAchievementsURL),
            fetch(achievementURL)
        ]);

        const playerData = await playerResponse.json();
        const achievementData = await achievementResponse.json();

        // Merge player achievements
        playerData.data.forEach(item => {
            const achId = item.relationships.achievement.data.id;
            const { state, currentSteps, updateTime } = item.attributes;
            achievements[achId] = { state, currentSteps, updateTime };
        });

        // Add achievement details
        achievementData.data.forEach(item => {
            const achId = item.id;
            const { name, description, totalSteps, unlockedIconUrl } = item.attributes;

            if (!achievements[achId]) {
                achievements[achId] = {
                    state: 'LOCKED',
                    currentSteps: 0,
                    updateTime: 0,
                    name,
                    description,
                    totalSteps,
                    icon: unlockedIconUrl
                };
            } else {
                achievements[achId] = {
                    ...achievements[achId],
                    name,
                    description,
                    totalSteps,
                    icon: unlockedIconUrl
                };
            }
        });

        return achievements;
    } catch (error) {
        throw new Error('Error fetching player or achievement data');
    }
}

// Build the display string for achievements
function buildAchievementDisplay(achievements) {
    const unlocked = Object.values(achievements).filter(ach => ach.state === 'UNLOCKED').length;
    const totalAchievements = Object.keys(achievements).length;
    const completionAmount = Math.floor((unlocked / totalAchievements) * 100);

    const achievementList = Object.values(achievements).map(ach => {
        const completion = ach.totalSteps > 0 ? `${ach.currentSteps}/${ach.totalSteps}` : '';
        return `
            <div class="achievementContainer">
                <div class="tooltip">
                    <img src="${ach.icon}" alt="X" class="achievementIcon achievement${ach.state}">
                    <span class="tooltiptext"><b>${ach.name}</b><br>${ach.description}</span>
                    <span class="achievementCompletion">${completion}</span>
                </div>
            </div>`;
    }).join('');

    return `
        <div class="metaInfo">
            <b>Found ${totalAchievements} achievements for player ${achievements[0].playerName}</b> (${completionAmount}% completion)
        </div>
        <div class="achievementsList">
            ${achievementList}
        </div>`;
}

