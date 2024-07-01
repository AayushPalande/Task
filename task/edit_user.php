<?php
require_once('db_config.php');

// Fetch user details for editing
if (isset($_GET['id'])) {
    $edit_id = $_GET['id'];

    // Fetch user details
    $stmt_user = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt_user->bind_param("i", $edit_id);
    $stmt_user->execute();
    $result_user = $stmt_user->get_result();
    $user = $result_user->fetch_assoc();
    $stmt_user->close();

    // Fetch user experiences
    $stmt_exp = $conn->prepare("SELECT * FROM experiences WHERE user_id = ?");
    $stmt_exp->bind_param("i", $edit_id);
    $stmt_exp->execute();
    $result_exp = $stmt_exp->get_result();
    $experiences = [];
    while ($row = $result_exp->fetch_assoc()) {
        $experiences[] = $row;
    }
    $stmt_exp->close();
}

// Handle form submission to update user details
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_id'])) {
    $edit_id = $_POST['edit_id'];
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
        // Check if mobile number already exists for another user
        $stmt_check_mobile = $conn->prepare("SELECT id FROM users WHERE mobile = ? AND id != ?");
        $stmt_check_mobile->bind_param("si", $mobile, $edit_id);
        $stmt_check_mobile->execute();
        $stmt_check_mobile->store_result();

        if ($stmt_check_mobile->num_rows > 0) {
            echo "Error: Mobile number already exists for another user.";
        } else {
            // Update user details
            $stmt_update_user = $conn->prepare("UPDATE users SET name=?, email=?, mobile=?, gender=? WHERE id=?");
            $stmt_update_user->bind_param("ssssi", $name, $email, $mobile, $gender, $edit_id);

            if ($stmt_update_user->execute()) {
                // Delete existing experiences for the user
                $stmt_delete_exp = $conn->prepare("DELETE FROM experiences WHERE user_id = ?");
                $stmt_delete_exp->bind_param("i", $edit_id);
                $stmt_delete_exp->execute();
                $stmt_delete_exp->close();

                // Insert updated experiences
                $stmt_insert_exp = $conn->prepare("INSERT INTO experiences (user_id, company, years, months) VALUES (?, ?, ?, ?)");
                $stmt_insert_exp->bind_param("isii", $edit_id, $company, $year, $month);

                for ($i = 0; $i < count($companies); $i++) {
                    $company = $companies[$i];
                    $year = $years[$i];
                    $month = $months[$i];
                    if (!empty($company) && !empty($year) && !empty($month)) {
                        $stmt_insert_exp->execute();
                    }
                }
                $stmt_insert_exp->close();

                // Redirect to main page after update
                header("Location: index.php?action=update");
                exit();
            } else {
                echo "Error updating user details: " . $stmt_update_user->error;
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
    <title>Edit User</title>
</head>
<body>

<h2>Edit User</h2>

<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" id="editUserForm">
    <input type="hidden" name="edit_id" value="<?php echo $edit_id; ?>">
    Name: <input type="text" name="name" value="<?php echo $user['name']; ?>"><br><br>
    Email: <input type="email" name="email" value="<?php echo $user['email']; ?>"><br><br>
    Mobile: <input type="text" name="mobile" value="<?php echo $user['mobile']; ?>"><br><br>
    Gender:
    <select name="gender">
        <option value="Male" <?php if ($user['gender'] === 'Male') echo 'selected'; ?>>Male</option>
        <option value="Female" <?php if ($user['gender'] === 'Female') echo 'selected'; ?>>Female</option>
        <option value="Other" <?php if ($user['gender'] === 'Other') echo 'selected'; ?>>Other</option>
    </select><br><br>
    Experiences:<br>
    <div id="experiences">
        <?php foreach ($experiences as $exp): ?>
            <div class="exp-item">
                Company: <input type="text" name="company[]" value="<?php echo $exp['company']; ?>"><br>
                No of Years: <input type="number" name="years[]" value="<?php echo $exp['years']; ?>"><br>
                No of Months: <input type="number" name="months[]" value="<?php echo $exp['months']; ?>"><br><br>
            </div>
        <?php endforeach; ?>
    </div>
    <button type="button" onclick="addExperience()">Add Another Experience</button><br><br>
    <input type="submit" value="Update">
</form>

<br>
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
    document.getElementById('editUserForm').addEventListener('submit', function(event) {
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
