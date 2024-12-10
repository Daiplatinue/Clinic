<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/auth.php");
    header("Location: ../student/check-up.php");
    exit;
}

$conn = new mysqli('localhost', 'root', '', 'clinic_db');
if ($conn->connect_error) {
    die('Connection Failed: ' . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT u_fn, u_bt, u_grade, u_hs, u_h, u_gender, u_allergy, u_age, u_image FROM user WHERE u_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();
$stmt->close();

$stmt2 = $conn->prepare("SELECT c_nc FROM check_up WHERE u_id = ? ORDER BY c_lc DESC LIMIT 1");
$stmt2->bind_param("i", $user_id);
$stmt2->execute();
$result2 = $stmt2->get_result();
$check_up_data = $result2->fetch_assoc();
$stmt2->close();

// Get total checkups
$stmt = $conn->prepare("SELECT COUNT(*) as total_checkups FROM check_up");
$stmt->execute();
$result = $stmt->get_result();
$total_checkups = $result->fetch_assoc()['total_checkups'];
$stmt->close();

// Get total completed check-ups
$stmt = $conn->prepare("SELECT COUNT(*) as completed_checkups FROM check_up WHERE c_status = 'completed'");
$stmt->execute();
$result = $stmt->get_result();
$completed_checkups = $result->fetch_assoc()['completed_checkups'];
$stmt->close();

// Get pending cases
$stmt = $conn->prepare("SELECT COUNT(*) as pending_cases FROM check_up WHERE c_status = ?");
$status = 'pending';
$stmt->bind_param("s", $status);
$stmt->execute();
$result = $stmt->get_result();
$pending_cases = $result->fetch_assoc()['pending_cases'];
$stmt->close();

// Get total student accounts
$stmt = $conn->prepare("SELECT COUNT(*) as total_students FROM user WHERE u_type = 'student'");
$stmt->execute();
$result = $stmt->get_result();
$total_students = $result->fetch_assoc()['total_students'];
$stmt->close();

// Get recent completed appointments
$stmt = $conn->prepare("SELECT c.*, u.u_fn, u.u_image 
                       FROM check_up c 
                       JOIN user u ON c.u_id = u.u_id 
                       WHERE c.c_status = 'completed' 
                       ORDER BY c.c_pd DESC 
                       LIMIT 5");
$stmt->execute();
$recent_appointments = $stmt->get_result();
$stmt->close();

$conn->close();

$next_checkup = $check_up_data['c_nc'] ?? 'N/A';
$last_checkup = $check_up_data['c_lc'] ?? 'N/A';
$user_name = $user_data['u_fn'] ?? 'User';
$blood_type = $user_data['u_bt'] ?? 'N/A';
$grade = $user_data['u_grade'] ?? 'N/A';
$health_status = $user_data['u_hs'] ?? 'N/A';
$height = $user_data['u_h'] ?? 'N/A';
$gender = $user_data['u_gender'] ?? 'N/A';
$allergy = $user_data['u_allergy'] ?? 'N/A';
$age = $user_data['u_age'] ?? '0';
$primaryContact = $user_data['u_pc'] ?? 'N/A';
$primaryNumber = $user_data['u_pcn'] ?? 'N/A';
$secondaryContact = $user_data['u_sc'] ?? 'N/A';
$secondaryNumber = $user_data['u_scn'] ?? 'N/A';
$userImage = $user_data['u_image'] ?? '';


if (isset($_POST['submit'])) {
    $file = $_FILES['u_image'];
    $file_name = $file['name'];
    $tempname = $file['tmp_name'];
    $folder = '../uploads/profiles/' . $file_name;

    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($file['type'], $allowed_types)) {
        echo "<h2>Invalid file type. Only JPG, PNG and GIF are allowed.</h2>";
        exit;
    }

    $query = $conn->prepare("UPDATE user SET u_image = ? WHERE u_id = ?");
    $query->bind_param("si", $file_name, $user_id);

    if ($query->execute() && move_uploaded_file($tempname, $folder)) {
        echo "<h2>Image Updated Successfully!</h2>";
    } else {
        echo "<h2>Upload Error!</h2>";
    }
    $query->close();
}

?>

<!DOCTYPE html>
<html lang="en" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediTrack - Doctor's Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="js/tailwind.config.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="styles/main.css">
</head>

<body class="bg-gray-50 dark:bg-gray-900">
    <div class="flex h-screen overflow-hidden">
        <aside class="hidden lg:flex lg:flex-col w-64 border-r border-gray-700 bg-gray-800">
            <div class="flex-1 flex flex-col min-h-0">
                <div class="flex items-center h-16 flex-shrink-0 px-4 border-b border-gray-700 justify-center">
                    <span class="text-2xl font-bold bg-gradient-to-r from-blue-400 to-teal-400 text-transparent bg-clip-text">Clinic Ni Sir Ares</span>
                </div>
                <div class="flex-1 flex flex-col pt-5 pb-4 overflow-y-auto">
                    <div class="flex-1 px-3 space-y-1">
                        <a href="doctor.php" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg bg-blue-900/50 text-blue-100">
                            <i class="fas fa-home w-6 h-6 mr-3"></i>
                            <span>Dashboard</span>
                        </a>
                        <a href="checkup.php" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg text-gray-300 hover:bg-gray-700">
                            <i class="fas fa-stethoscope w-6 h-6 mr-3"></i>
                            <span>Check-ups</span>
                        </a>
                        <a href="myaccount.php" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg text-gray-300 hover:bg-gray-700">
                            <i class="fas fa-user w-6 h-6 mr-3"></i>
                            <span>My Account</span>
                        </a>
                        <a href="../auth/login.php" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg text-gray-300 hover:bg-gray-700">
                            <i class="fas fa-sign-out-alt w-6 h-6 mr-3"></i>
                            <span>Logout</span>
                        </a>
                    </div>
                </div>
                <div class="flex-shrink-0 flex border-t justify-center border-gray-700 p-4">
                    <div class="flex items-center">
                        <div>
                            <img class="h-10 w-10 rounded-full object-cover ring-2 ring-green-400"
                                src="<?php echo !empty($user_data['u_image']) ? htmlspecialchars($user_data['u_image']) : '/uploads/profiles/'; ?>"
                                alt="Profile">
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-white">
                                <?php echo htmlspecialchars($user_name); ?>
                            </p>
                            <p class="text-xs text-gray-400">Doctor</p>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-gray-800 border-b border-gray-700">
                <div class="px-4 sm:px-6 lg:px-8 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 flex">
                            <button class="lg:hidden p-2 rounded-md text-gray-400 hover:text-gray-200 hover:bg-gray-700">
                                <i class="fas fa-bars"></i>
                            </button>
                            <h1 class="ml-3 text-2xl font-semibold text-white">Dashboard Overview</h1>
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto bg-gray-50 dark:bg-gray-900 p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                    <div class="relative overflow-hidden bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg">
                        <div class="absolute inset-0 bg-grid-white/5"></div>
                        <div class="p-6 relative z-10">
                            <div class="flex justify-between items-center mb-4">
                                <div class="bg-white/10 rounded-lg p-2">
                                    <i class="fas fa-calendar-check text-white text-xl"></i>
                                </div>
                                <span class="flex items-center text-white bg-white/10 rounded-full px-2 py-1 text-xs">
                                    <i class="fas fa-arrow-up text-xs mr-1"></i>8%
                                </span>
                            </div>
                            <h3 class="text-3xl font-bold text-white mb-1"><?php echo $total_checkups; ?></h3>
                            <p class="text-blue-100">Total Check Ups</p>
                        </div>
                    </div>

                    <div class="relative overflow-hidden bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl shadow-lg">
                        <div class="absolute inset-0 bg-grid-white/5"></div>
                        <div class="p-6 relative z-10">
                            <div class="flex justify-between items-center mb-4">
                                <div class="bg-white/10 rounded-lg p-2">
                                    <i class="fas fa-check-circle text-white text-xl"></i>
                                </div>
                                <span class="flex items-center text-white bg-white/10 rounded-full px-2 py-1 text-xs">
                                    <i class="fas fa-arrow-up text-xs mr-1"></i><?php echo $completed_checkups; ?>
                                </span>
                            </div>
                            <h3 class="text-3xl font-bold text-white mb-1"><?php echo $completed_checkups; ?></h3>
                            <p class="text-emerald-100">Completed Checkups</p>
                        </div>
                    </div>

                    <div class="relative overflow-hidden bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl shadow-lg">
                        <div class="absolute inset-0 bg-grid-white/5"></div>
                        <div class="p-6 relative z-10">
                            <div class="flex justify-between items-center mb-4">
                                <div class="bg-white/10 rounded-lg p-2">
                                    <i class="fas fa-clock text-white text-xl"></i>
                                </div>
                                <span class="flex items-center text-white bg-white/10 rounded-full px-2 py-1 text-xs">
                                    <i class="fas fa-arrow-down text-xs mr-1"></i><?php echo $pending_cases; ?>
                                </span>
                            </div>
                            <h3 class="text-3xl font-bold text-white mb-1"></i><?php echo $pending_cases; ?></h3>
                            <p class="text-amber-100">Pending Cases</p>
                        </div>
                    </div>

                    <div class="relative overflow-hidden bg-gradient-to-br from-violet-500 to-violet-600 rounded-xl shadow-lg">
                        <div class="absolute inset-0 bg-grid-white/5"></div>
                        <div class="p-6 relative z-10">
                            <div class="flex justify-between items-center mb-4">
                                <div class="bg-white/10 rounded-lg p-2">
                                    <i class="fas fa-users text-white text-xl"></i>
                                </div>
                                <span class="flex items-center text-white bg-white/10 rounded-full px-2 py-1 text-xs">
                                    <i class="fas fa-arrow-up text-xs mr-1"></i>3%
                                </span>
                            </div>
                            <h3 class="text-3xl font-bold text-white mb-1"><?php echo $total_students; ?></h3>
                            <p class="text-violet-100">Total Students</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                    <div class="lg:col-span-2 bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-700">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-white">Monthly Checkup Statistics</h3>
                        </div>
                        <div class="h-[300px]">
                            <canvas id="checkupStats"></canvas>
                        </div>
                    </div>

                    <div class="bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-700">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-white">Common Health Issues</h3>
                        </div>
                        <div class="h-[300px]">
                            <canvas id="healthIssues"></canvas>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-800 rounded-xl shadow-lg border border-gray-700">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-white">Recent Appointments</h3>
                            <button class="text-blue-400 hover:text-blue-300 text-sm font-medium">
                                View All
                            </button>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-700">
                                <thead>
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Patient</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Time</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-700">
                                    <?php while ($row = $recent_appointments->fetch_assoc()): ?>
                                        <tr class="hover:bg-gray-700/50 transition-colors duration-200">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <img class="h-8 w-8 rounded-full object-cover"
                                                        src="<?php echo !empty($row['u_image']) ? htmlspecialchars($row['u_image']) : 'default-profile.jpg'; ?>"
                                                        alt="">
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-white"><?php echo htmlspecialchars($row['u_fn']); ?></div>
                                                        <div class="text-sm text-gray-400">ID: <?php echo htmlspecialchars($row['u_id']); ?></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300"><?php echo date('Y-m-d', strtotime($row['c_pd'])); ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300"><?php echo date('h:i A', strtotime($row['c_pd'])); ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-900/50 text-green-300">
                                                    <span class="w-1.5 h-1.5 mr-1.5 rounded-full bg-green-400"></span>
                                                    Completed
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="chart.js"></script>
</body>

</html>