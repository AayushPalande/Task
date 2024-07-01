<?php
require_once('db_config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $gender = $_POST['gender'];
    $companies = $_POST['company'];
    $years = $_POST['years'];
    $months = $_POST['months'];

    // Server-side validation
    $errors = [];
    if (empty($name)) {
        $errors[] = "Name is required.";
    }
    if (empty($email)) {
        $errors[] = "Email is required.";
    }
    if (empty($mobile)) {
        $errors[] = "Mobile is required.";
    }
    if (empty($gender)) {
        $errors[] = "Gender is required.";
    }
    if (!preg_match("/^\d{10}$/", $mobile)) {
        $errors[] = "Mobile number must be a 10-digit number.";
    }

    if (count($errors) > 0) {
        foreach ($errors as $error) {
            echo $error . "<br>";
        }
    } else {
        // Check if mobile number already exists
        $stmt_check_mobile = $conn->prepare("SELECT id FROM users WHERE mobile = ?");
        $stmt_check_mobile->bind_param("s", $mobile);
        $stmt_check_mobile->execute();
        $stmt_check_mobile->store_result();

        if ($stmt_check_mobile->num_rows > 0) {
            echo "Error: Mobile number already exists for another user.";
        } else {
            // Insert user details into users table
            $stmt_insert_user = $conn->prepare("INSERT INTO users (name, email, mobile, gender) VALUES (?, ?, ?, ?)");
            $stmt_insert_user->bind_param("ssss", $name, $email, $mobile, $gender);

            if ($stmt_insert_user->execute()) {
                // Get the user ID of the inserted user
                $user_id = $stmt_insert_user->insert_id;

                // Insert user experiences
                $stmt_insert_exp = $conn->prepare("INSERT INTO experiences (user_id, company, years, months) VALUES (?, ?, ?, ?)");
                $stmt_insert_exp->bind_param("isii", $user_id, $company, $year, $month);

                for ($i = 0; $i < count($companies); $i++) {
                    $company = $companies[$i];
                    $year = $years[$i];
                    $month = $months[$i];
                    if (!empty($company) && !empty($year) && !empty($month)) {
                        if (!$stmt_insert_exp->execute()) {
                            echo "Error adding experience: " . $stmt_insert_exp->error;
                        }
                    }
                }

                echo "User added successfully";
                echo "<script>setTimeout(function() {
                    window.location.href = 'index.php?action=add';
                }, 2000);</script>";
            } else {
                echo "Error adding user: " . $stmt_insert_user->error;
            }
        }

        $stmt_check_mobile->close();
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add User</title>
</head>
<body>

<h2>Add User</h2>

<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" id="addUserForm">
    Name: <input type="text" name="name"><br><br>
    Email: <input type="email" name="email"><br><br>
    Mobile: <input type="text" name="mobile"><br><br>
    Gender:
    <select name="gender">
        <option value="Male">Male</option>
        <option value="Female">Female</option>
        <option value="Other">Other</option>
    </select><br><br>
    Experiences:<br>
    <div id="experiences">
        <div class="exp-item">
            Company: <input type="text" name="company[]"><br>
            No of Years: <input type="number" name="years[]"><br>
            No of Months: <input type="number" name="months[]"><br><br>
        </div>
    </div>
    <button type="button" onclick="addExperience()">Add Another Experience</button><br><br>
    <input type="submit" value="Add User">
</form>

<a href="index.php">Back to User List</a>

<script>
    function addExperience() {
        var expDiv = document.createElement('div');
        expDiv.classList.add('exp-item');
        expDiv.innerHTML = `
            Company: <input type="text" name="company[]"><br>
            No of Years: <input type="number" name="years[]"><br>
            No of Months: <input type="number" name="months[]"><br><br>
        `;
        document.getElementById('experiences').appendChild(expDiv);
    }

    // Client-side validation
    document.getElementById('addUserForm').addEventListener('submit', function(event) {
        var name = this.elements['name'].value.trim();
        var email = this.elements['email'].value.trim();
        var mobile = this.elements['mobile'].value.trim();
        var gender = this.elements['gender'].value;

        var errors = [];
        if (name === '') {
            errors.push("Name is required.");
        }
        if (email === '') {
            errors.push("Email is required.");
        }
        if (mobile === '') {
            errors.push("Mobile is required.");
        }
        if (gender === '') {
            errors.push("Gender is required.");
        }
        if (!mobile.match(/^\d{10}$/)) {
            errors.push("Mobile number must be a 10-digit number.");
        }

        if (errors.length > 0) {
            event.preventDefault();
            var errorMessage = errors.join('<br>');
            document.getElementById('error').innerHTML = errorMessage;
        }
    });
</script>

</body>
</html>
