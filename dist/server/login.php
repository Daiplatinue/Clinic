<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        echo "Please fill in all the fields.";
        exit;
    }

    $conn = new mysqli('localhost', 'root', '', 'clinic_db');
    if ($conn->connect_error) {
        die('Connection Failed: ' . $conn->connect_error);
    }

    $stmt = $conn->prepare("SELECT u_id, u_password, u_type, u_fn, u_bt, u_grade, u_hs, u_h, u_gender, u_allergy, u_age , u_pc, u_sc, u_pcn, u_scn FROM user WHERE u_email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($user_id, $hashed_password, $u_type, $u_fn, $u_bt, $u_grade, $u_health_status, $u_height, $u_gender, $u_allergy, $u_age, $primaryContact, $primaryNumber, $secondaryContact, $secondaryNumber);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $user_id;
            $_SESSION['user_name'] = $u_fn;
            $_SESSION['blood_type'] = $u_bt;
            $_SESSION['grade'] = $u_grade;
            $_SESSION['health_status'] = $u_health_status;
            $_SESSION['height'] = $u_height;
            $_SESSION['gender'] = $u_gender;
            $_SESSION['allergy'] = $u_allergy;
            $_SESSION['age'] = $u_age;
            $_SESSION['pc'] = $primaryContact;
            $_SESSION['pn'] = $primaryNumber;
            $_SESSION['sn'] = $secondaryContact;
            $_SESSION['sc'] = $secondaryNumber;

            if ($u_type === 'doctor') {
                header("Location: ../doctor/doctor.php");
            } elseif ($u_type === 'student') {
                header("Location: ../student/student.php");
            } else {
                echo "User type not recognized.";
            }
            exit;
        } else {
            echo "Invalid credentials!";
        }
    } else {
        echo "No user found with that email!";
    }

    $stmt->close();
    $conn->close();
}
