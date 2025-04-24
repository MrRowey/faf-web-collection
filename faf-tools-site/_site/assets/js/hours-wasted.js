function displayResults(str_result) {
    const number = parseInt(str_result);
    let text = "";

    if (isNaN(number)) {
        text = "Internal error - please report this issue.";
    } else if (number <= 0) {
        text = "Could not find the given player name, or this player didn't play any games.";
    } else {
        const hours = Math.floor(number / 3600);
        const minutes = Math.floor(number / 60) % 60;
        const seconds = number % 60;
        text = `You wasted a total of ${hours} hours, ${minutes} minutes, and ${seconds} seconds in-game.`;
    }

    document.getElementById('receiverZone').innerHTML = text;
}

async function checkPlayerName() {
    const playerName = document.getElementById("playerName").value;

    if (playerName.trim() === "") {
        document.getElementById('receiverZone').innerHTML = "Please enter a valid player name.";
        return;
    }

    // Show loading message
    document.getElementById('loadingZone').style.visibility = "visible";

    try {
        let totalSeconds = 0;
        let page = 1;
        let hasMoreData = true;

        while (hasMoreData) {
            // API request for game data related to the player
            const response = await fetch(`https://api.faforever.com/data/game?filter=playerStats.player.login=="${playerName}"&sort=-endTime&fields[game]=startTime,endTime&page[number]=${page}`);
            const data = await response.json();

            if (data.data.length === 0) {
                hasMoreData = false; // No more games found
            } else {
                // Calculate time for each game
                data.data.forEach(game => {
                    const startTime = new Date(game.attributes.startTime).getTime();
                    const endTime = new Date(game.attributes.endTime).getTime();
                    const timeSpent = Math.max((endTime - startTime) / 1000, 0); // In seconds
                    totalSeconds += timeSpent;
                });
            }
            page++; // Move to the next page of results
        }

        // Convert seconds to hours, minutes, and seconds
        const hours = Math.floor(totalSeconds / 3600);
        const minutes = Math.floor((totalSeconds % 3600) / 60);
        const seconds = totalSeconds % 60;

        // Hide loading and display result
        document.getElementById('loadingZone').style.visibility = "hidden";

        const resultText = `You wasted a total of ${hours} hours, ${minutes} minutes, and ${Math.round(seconds)} seconds in-game.`;
        document.getElementById('receiverZone').innerHTML = resultText;

    } catch (error) {
        document.getElementById('loadingZone').style.visibility = "hidden";
        document.getElementById('receiverZone').innerHTML = "There was an error fetching the data. Please try again later.";
        console.error('Error fetching data:', error);
    }
}
