<?php 
session_start();
require_once('dbconfig/config.php');
if (!isset($_SESSION['email'])) {
    echo '<script>window.location.href = "login.php";</script>';
  exit;
}
?>

<?php
if(isset($_POST['logout']))
{
session_destroy();
echo '<script>window.location.href = "login.php";</script>';
}
?>



</html>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
   
    <title>Math Game</title>
  </head>

    <style>
       
       body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            text-align: center;
        
        }

        .container {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            padding: 20px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            max-width: 100%; 
        }

        .left-column {
            padding: 20px;
        }

        .right-column {
            padding: 20px;
            background-color: #007bff;
            color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        h1 {
            font-size: 24px;
            margin-bottom: 20px;
        }

        label {
            font-weight: bold;
            display: block;
            margin-bottom: 10px;
        }

        select, input[type="number"] {
            width: 80%;
            padding: 10px;
            margin-bottom: 20px;
        }

        button {
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            cursor: pointer;
        }

        #question {
            font-size: 24px;
            margin-bottom: 20px;
        }

        #userAnswer {
            font-size: 18px;
            padding: 10px;
        }

        #feedback {
            font-size: 18px;
            margin-top: 10px;
        }

        #score {
            font-size: 20px;
            margin-top: 20px;
        }

        #timer {
            font-size: 20px;
            margin-top: 10px;
        }

        .correct {
            color: green;
        }

        .wrong {
            color: red;
        }

        .user-info {
            text-align: center;
        }

        .logout_button{
             border: 2px solid black;
             background-color: white;
             color: black;
             padding: 10px 20px;
             cursor: pointer;     
        }
        .edit_btn{
             border: 2px solid black;
             background-color: white;
             color: black;
             padding: 10px 25px;
             cursor: pointer;     
        }

        .info {
  border-color: black;
  color: black;
}

.info:hover {
  background: #007bff;
  color: white;
}

.scrolling-table {
    max-height: 213px; 
    overflow-y: scroll;
    border: 1px solid #ccc;
    padding: 10px;
}

table {
        font-family: arial, sans-serif;
        border-collapse: collapse;
        width: 100%;
        background-color: white;
        text-align: center; 
    }

    td, th {
        border: 1px solid white;
        text-align: center; 
        padding: 8px;
    }

    tr:nth-child(even) {
        background-color: #dddddd;
    }


    </style>
 

  <body>
  
  <div class="container" >
        <div class="left-column">
            <h1>Maths Game</h1>
            <label for="operator">Select an operation:</label>
            <select id="operator">
                <option value="Addition">Addition</option>
                <option value="Subtraction">Subtraction</option>
                <option value="Multiplication">Multiplication</option>
            </select>
            <label for="level">Select a level:</label>
            <select id="level">
                <option value="1">Level 1 (0-10)</option>
                <option value="2">Level 2 (0-20)</option>
                <option value="3">Level 3 (0-50)</option>
                <option value="4">Level 4 (0-100)</option>
                <option value="5">Level 5 (0-150)</option>
            </select>
            <label for="timerSelect">Select a timer (minutes):</label>
            <select id="timerSelect">
                <option value="1">1 Minute</option>
                <option value="3">3 Minutes</option>
                <option value="5">5 Minutes</option>
                <option value="10">10 Minutes</option>
                <option value="0">Untimed</option>
            </select>
            <button onclick="startGame()">Start Game</button>
            <br />
            &nbsp;
            <br />
            <div id="question"></div>
            <input type="text" id="userAnswer" placeholder="Your Answer" disabled />
            <button onclick="checkAnswer()" disabled>Check</button>
            <br />
            &nbsp;
            <br />
            <button onclick="resetGame()">Reset</button>
            <div id="feedback"></div>
            <div id="score">Score: 0</div>
            <div id="timer">Time Left: 00:00</div>
        </div>
        <div class="right-column">
    <div class="user-info">
        <center><h2>Profile</h2></center>
        <center><h3>Welcome, <?php echo $_SESSION['email']; ?></h3></center>

        <form action="index.php" method="post">
            <div class="inner_container">
                <button name="logout" class="logout_button info" type="submit">Log Out</button>
                &nbsp;
                &nbsp;
                <button class="edit_btn info" id="editProfileButton" onclick="openProfilePopup()">Profile</button>
            </div>
        </form>
    </div>
<br>
&nbsp;
<br>
<br>
&nbsp;
<br>
<br>
&nbsp;
<br>
    <h1>Leader-Board</h1>
    <div class="scrolling-table">
    
    <Center>
    <table id="leaderboard-table">
    <thead>
        <tr>
            <th>No.</th>
            <th>Username</th>
            <th>T-Score</th>
        </tr>
    </thead>
    <tbody id="leaderboard-body"></tbody>
</table>
<Center>
</div>

        

  </div>
   
  </body>



  <script>
    function openProfilePopup() {
   
    
    const popupWidth = 650;
    const popupHeight = 650;
    const left = window.innerWidth / 2 - popupWidth / 2;
    const top = window.innerHeight / 2 - popupHeight / 2;
    
    const profilePopup = window.open('view_profile.php', 'Profile', `width=${popupWidth}, height=${popupHeight}, top=${top}, left=${left}`);
    
    if (profilePopup) {
        profilePopup.focus();
    } else {
        alert('Please enable pop-ups to view/edit your profile.');
    }
}
     
     let selectedOperator = "Addition";
        let level = 1;
        let numberRange = [0, 10];
        let correctAnswer;
        let score = 0;
        let timerMinutes = 0;
        let timerInterval;
        let gameActive = false;
        let originalTimerMinutes = 0; // Store the original timer value

        function setNumberRange() {
            switch (level) {
                case 1:
                    numberRange = [0, 10];
                    break;
                case 2:
                    numberRange = [0, 20];
                    break;
                case 3:
                    numberRange = [0, 50];
                    break;
                case 4:
                    numberRange = [0, 100];
                    break;
                case 5:
                    numberRange = [0, 150];
                    break;
                default:
                    numberRange = [0, 10];
            }
        }

        function generateQuestion() {
            const num1 = Math.floor(Math.random() * (numberRange[1] - numberRange[0] + 1)) + numberRange[0];
            const num2 = Math.floor(Math.random() * (numberRange[1] - numberRange[0] + 1)) + numberRange[0];

            let question;

            switch (selectedOperator) {
                case "Addition":
                    question = `${num1} + ${num2}`;
                    correctAnswer = num1 + num2;
                    break;
                case "Subtraction":
                    question = `${num1} - ${num2}`;
                    correctAnswer = num1 - num2;
                    break;
                case "Multiplication":
                    question = `${num1} * ${num2}`;
                    correctAnswer = num1 * num2;
                    break;
            }

            document.getElementById(
                "question"
            ).textContent = `What is ${question}?`;
        }

        function startGame() {
            console.log("game started")
            if (gameActive) {
                timerMinutes = originalTimerMinutes;
                clearInterval(timerInterval);
            } else {
                originalTimerMinutes = timerMinutes;
            }

            selectedOperator = document.getElementById("operator").value;
            level = parseInt(document.getElementById("level").value);
            setNumberRange(); 
            const timerSelect = document.getElementById("timerSelect");
            timerMinutes = parseInt(timerSelect.value);
            disableInputs(true);
            startTimer();


            if (!gameActive) {
                score = 0;
            }

            generateQuestion();

            document.getElementById("userAnswer").value = "";
            document.getElementById("feedback").textContent = "";
            document.getElementById("score").textContent = `Score: ${score}`;
            gameActive = true;
        }

        function disableInputs(disabled) {
            document.getElementById("operator").disabled = disabled;
            document.getElementById("level").disabled = disabled;
            document.getElementById("timerSelect").disabled = disabled;
            document.getElementById("userAnswer").disabled = !disabled;

            const startButton = document.querySelector(
                'button[onclick="startGame()"]'
            );
            startButton.disabled = disabled;

            const checkButton = document.querySelector(
                'button[onclick="checkAnswer()"]'
            );
            checkButton.disabled = !disabled;

            const resetButton = document.querySelector(
                'button[onclick="resetGame()"]'
            );
            resetButton.disabled = !disabled;
        }

        function startTimer() {
            if (timerMinutes > 0) {
                console.log("Time stared")
                let timeLeft = timerMinutes * 60;
                document.getElementById(
                    "timer"
                ).textContent = `Time Left: ${formatTime(timeLeft)}`;
                timerInterval = setInterval(function () {
                    timeLeft--;
                    document.getElementById(
                        "timer"
                    ).textContent = `Time Left: ${formatTime(timeLeft)}`;

                    if (timeLeft === 0) {
                        clearInterval(timerInterval);
                        disableInputs(false);
                        document.getElementById("timer").textContent = "Time Left: 00:00";
                        document.getElementById("timer").style.color = "red";
                        gameActive = false;

                        // Display an alert with the score
                        const alertMessage = `Time's up! Your final score is: ${score}`;
                        alert(alertMessage);
                        tscore = (score * level) / timerMinutes
        // Send the data to save_data.php using AJAX
        const xhr = new XMLHttpRequest();
        console.log("data saved")
        xhr.open("POST", "save_data.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                console.log(xhr.responseText);
            }
        };
        const data = new URLSearchParams();
        data.append("operator", selectedOperator);
        data.append("level", level);
        data.append("time", timerMinutes);
        data.append("score", score);
        data.append("tscore", tscore);
        xhr.send(data);

                        resetGame();
                    }
                }, 1000);
            }
        }

        function formatTime(seconds) {
            const minutes = Math.floor(seconds / 60);
            const remainingSeconds = seconds % 60;
            const formattedMinutes = String(minutes).padStart(2, "0");
            const formattedSeconds = String(remainingSeconds).padStart(2, "0");
            return `${formattedMinutes}:${formattedSeconds}`;
        }

        function checkAnswer() {
            if (!gameActive) return;

            const userAnswer = parseInt(
                document.getElementById("userAnswer").value
            );

            if (userAnswer === correctAnswer) {
                document.getElementById("feedback").textContent =
                    "Correct! Well done.";
                document.getElementById("feedback").classList.remove("wrong");
                document.getElementById("feedback").classList.add("correct");
                score++;
            } else {
                document.getElementById("feedback").textContent = "Oops! Try again.";
                document.getElementById("feedback").classList.remove("correct");
                document.getElementById("feedback").classList.add("wrong");
                if (score > 0) {
                    score--;
                }
            }

            document.getElementById("score").textContent = `Score: ${score}`;
            generateQuestion();
            document.getElementById("userAnswer").value = "";
        }      
  

   


        function resetGame() {
            console.log("endgame")
            clearInterval(timerInterval);
            disableInputs(false);
            score = 0;
            document.getElementById("score").textContent = `Score: ${score}`;
            document.getElementById("timer").style.color = "black";
            gameActive = false;

            timerMinutes = originalTimerMinutes;
            document.getElementById("timer").textContent = `Time Left: ${formatTime(
                timerMinutes * 60
            )}`;

            const feedback = document.getElementById("feedback");
            feedback.textContent = "";
            feedback.classList.remove("correct", "wrong");
        }

        document
            .getElementById("userAnswer")
            .addEventListener("keyup", function (event) {
                if (event.key === "Enter") {
                    checkAnswer();
                }
            });

        generateQuestion();


    function updateLeaderboard() {
        const xhr = new XMLHttpRequest();
        xhr.open("GET", "leaderboard_data.php", true);

        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                const leaderboardData = JSON.parse(xhr.responseText);
                populateLeaderboardTable(leaderboardData);
            }
        };

        xhr.send();
    }

    function populateLeaderboardTable(leaderboardData) {
    const leaderboardBody = document.getElementById("leaderboard-body");
    leaderboardBody.innerHTML = ""; 

    for (let i = 0; i < leaderboardData.length; i++) {
        const user = leaderboardData[i];
        const row = document.createElement("tr");
        const numberCell = document.createElement("td");
        numberCell.textContent = i + 1; 
        row.appendChild(numberCell);
        const usernameCell = document.createElement("td");
        const usernameLink = document.createElement("a");
        usernameLink.href = "javascript:void(0);";
        usernameLink.textContent = user.username;
        usernameLink.addEventListener("click", function () {
            openUserProfile(user.username);
        });
        usernameCell.appendChild(usernameLink);
        row.appendChild(usernameCell);
        const tscoreCell = document.createElement("td");
        tscoreCell.textContent = user.highest_tscore;
        row.appendChild(tscoreCell);
        leaderboardBody.appendChild(row);
    }
}


    updateLeaderboard();

    setInterval(updateLeaderboard, 5000); 


    function openUserProfile(username) {
    const popupWidth = 275;
    const popupHeight = 150;
    const left = window.innerWidth / 2 - popupWidth / 2;
    const top = window.innerHeight / 2 - popupHeight / 2;

    const userProfileURL = 'user_profile.php?username=' + username;
    const userProfilePopup = window.open(userProfileURL, 'User Profile', `width=${popupWidth}, height=${popupHeight}, top=${top}, left=${left}`);

    if (userProfilePopup) {
        userProfilePopup.focus();
    } else {
        alert('Please enable pop-ups to view user profiles.');
    }
}


  </script>

</html>
