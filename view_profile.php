<?php
session_start();
require_once('dbconfig/config.php');

if (!isset($_SESSION['email'])) {
    echo '<script>window.location.href = "login.php";</script>';
    exit;
}

$profileUpdated = false;
$passwordChanged = false;
$passwordAlert = '';

$email = $_SESSION['email'];
$query = "SELECT * FROM user WHERE email = '$email'";
$result = mysqli_query($con, $query);

if ($result) {
    $row = mysqli_fetch_assoc($result);
} else {
    echo "Error fetching user details: " . mysqli_error($con);
}

if (isset($_POST['name']) && isset($_POST['class']) && isset($_POST['rollno'])) {
    $newName = mysqli_real_escape_string($con, $_POST['name']);
    $newClass = mysqli_real_escape_string($con, $_POST['class']);
    $newRollno = mysqli_real_escape_string($con, $_POST['rollno']);

    $updateQuery = "UPDATE user SET name = '$newName', class = '$newClass', rollno = '$newRollno' WHERE email = '$email'";
    
    if (mysqli_query($con, $updateQuery)) {
        $row['name'] = $newName;
        $row['class'] = $newClass;
        $row['rollno'] = $newRollno;
        $profileUpdated = true;
    } else {
        echo "Error updating user information: " . mysqli_error($con);
    }
}

if (isset($_POST['new_password'])) {
    $newPassword = mysqli_real_escape_string($con, $_POST['new_password']);

    if (validatePassword($newPassword)) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT); // Hash the new password
    
        $updateQuery = "UPDATE user SET password = '$hashedPassword' WHERE email = '$email'";
        
        if (mysqli_query($con, $updateQuery)) {
            $passwordChanged = true;
        } else {
            echo "Error updating password: " . mysqli_error($con);
        }
    } else {
        $passwordAlert = "Password should be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, one number, and one special symbol.";
    }
}

function validatePassword($password) {
   
    $pattern = "/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_])[A-Za-z\d\W_]{8,}$/";
    return preg_match($pattern, $password);
}


// Fetch user's records
$recordsQuery = "SELECT * FROM records WHERE email = '$email'";
$recordsResult = mysqli_query($con, $recordsQuery);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <title>View / Edit Profile</title>
    <style>
        body {
            font-family: Georgia, serif;
            margin: 0;
            padding: 0;
            background-color: #f2f2f2;
        }
        .container {
            width: 100%;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            font-weight: bold;
            margin-bottom: 5px;
        }
        input {
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
        }
        input[type="submit"] {
            background-color: #007BFF;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }
        .password-change-btn {
            background-color: #4CAF50;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Profile</h1>

        <form action="view_profile.php" method="POST">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?php echo $row['name']; ?>">
            
            <label for="email">Username:</label>
            <input type="email" id="email" name="email" value="<?php echo $row['email']; ?>" disabled>
            
            <label for="class">Class:</label>
            <input type="text" id="class" name="class" value="<?php echo $row['class']; ?>">
            
            <label for="rollno">Roll No:</label>
            <input type="text" id="rollno" name="rollno" value="<?php echo $row['rollno']; ?>">
            
            <input type="submit" value="Update">
        </form>

        <?php if ($profileUpdated): ?>
            <script>
                alert("Profile changed successfully!");
            </script>
        <?php endif; ?>

        <?php if (!empty($passwordAlert)): ?>
            <script>
                alert("<?php echo $passwordAlert; ?>");
            </script>
        <?php endif; ?>

        <?php if ($passwordChanged): ?>
            <script>
                alert("Password changed successfully!");
            </script>
        <?php endif; ?>
<center>
        <button class="password-change-btn" id="changePasswordBtn">Change Password</button>
        <div id="passwordChangeForm" style="display: none;">
            <form action="view_profile.php" method="POST">
                <label for="new_password">New Password:</label>
                <input type="password" id="new_password" name="new_password" required>
                <input type="submit" value="Change Password">
            </form>
        </div>
    </div>
    </center>
        </div>

        <div class="container mt-3">
        <center><h2>Records</h2></center>
        <br>
        &nbsp;
        &nbsp;
        <label for="operationFilter">Filter Operation:</label>
        <select id="operationFilter" onchange="filterRecords()">
            <option value="All">All</option>
            <option value="Addition">Addition</option>
            <option value="Subtraction">Subtraction</option>
            <option value="Multiplication">Multiplication</option>
        </select>
        &nbsp;
        &nbsp;
        <label for="levelFilter">Filter Level:</label>
        <select id="levelFilter" onchange="filterRecords()">
            <option value="All">All</option>
            <option value="1">Level 1</option>
            <option value="2">Level 2</option>
            <option value="3">Level 3</option>
            <option value="4">Level 4</option>
            <option value="5">Level 5</option>
        </select>
        <table class="table table-hover" id="recordsTable">
            <thead>
                <tr class="text-center">
                    <th>Operation</th>
                    <th>Level</th>
                    <th>Time</th>
                    <th>Score</th>
                    <th>T-Score</th>
                </tr>
            </thead>
            <center>
            <tbody>
                
                <?php while ($record = mysqli_fetch_assoc($recordsResult)) : ?>
                    <tr>
                        <td class="text-center"><?php echo $record['operation']; ?></td>
                        <td class="text-center"><?php echo $record['level']; ?></td>
                        <td class="text-center"><?php echo $record['time']; ?></td>
                        <td class="text-center"><?php echo $record['score']; ?></td>
                        <td class="text-center"><?php echo $record['tscore']; ?></td>
                    </tr>
                <?php endwhile; ?>
                
            </tbody>
            </center>
        </table>
    </div>

    <script>
        document.getElementById('changePasswordBtn').addEventListener('click', function () {
            document.getElementById('passwordChangeForm').style.display = 'block';
        });
    </script>
    <script>
        function filterRecords() {
            var operationFilter = document.getElementById('operationFilter').value;
            var levelFilter = document.getElementById('levelFilter').value;
            var table = document.getElementById('recordsTable');
            var rows = table.getElementsByTagName('tr');

            for (var i = 1; i < rows.length; i++) {
                var operationCell = rows[i].cells[0];
                var levelCell = rows[i].cells[1];
                if ((operationFilter === 'All' || operationCell.textContent === operationFilter) &&
                    (levelFilter === 'All' || levelCell.textContent === levelFilter)) {
                    rows[i].style.display = '';
                } else {
                    rows[i].style.display = 'none';
                }
            }
        }
    </script>
</body>
</html>
