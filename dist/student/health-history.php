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
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Health Portal - Health History</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        dark: {
                            100: '#1E293B',
                            200: '#0F172A',
                            300: '#0F1629',
                            400: '#1E2A4A'
                        }
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="styles.css">
</head>

<body class="bg-dark-200 text-gray-100">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="w-72 bg-dark-100 border-r border-gray-800 fixed h-full animate-slide-in">
            <div class="p-6">
                <div class="text-center mb-8">
                    <div class="relative inline-block">
                        <img src="<?php echo !empty($user_data['u_image']) ? htmlspecialchars($user_data['u_image']) : '/uploads/profiles/'; ?>"
                            class="w-40 h-40 rounded-full object-cover border-4 border-blue-500">
                    </div>
                    <h4 class="text-xl font-bold">
                        <?php echo htmlspecialchars($user_name); ?>
                    </h4>
                    <p class="text-gray-400">
                        <?php echo htmlspecialchars($grade); ?>
                    </p>
                    <div
                        class="mt-3 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-500/20 text-green-400">
                        <span class="w-2 h-2 mr-2 rounded-full bg-green-400"></span>
                        <?php echo htmlspecialchars($health_status); ?>
                    </div>
                </div>

                <nav class="space-y-2">
                    <a href="student.php"
                        class="flex items-center space-x-3 px-4 py-3 rounded-lg gradient-bg text-white gradient-hover">
                        <i class="fas fa-newspaper"></i>
                        <span>News Feed</span>
                    </a>
                    <a href="myprofile.php"
                        class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-dark-400 text-gray-400 hover:text-white transition-all duration-300">
                        <i class="fas fa-user"></i>
                        <span>My Profile</span>
                    </a>
                    <a href="health-history.php"
                        class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-dark-400 text-gray-400 hover:text-white transition-all duration-300">
                        <i class="fas fa-history"></i>
                        <span>Health History</span>
                        <span class="ml-auto bg-primary-500 text-white text-xs px-2 py-1 rounded-full">3</span>
                    </a>
                    <a href="../auth/login.php"
                        class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-dark-400 text-gray-400 hover:text-white transition-all duration-300">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </a>
                </nav>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 ml-64">
            <!-- Top Navigation -->
            <header class="bg-dark-100 border-b border-gray-800 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h1 class="text-xl font-semibold">Health History</h1>
                    <div class="flex space-x-4">
                        <div class="relative">
                            <input type="text"
                                placeholder="Search records..."
                                class="bg-dark-300 border border-gray-700 rounded-lg px-4 py-2 pl-10 focus:outline-none focus:border-blue-500">
                            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        </div>
                        <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">
                            <i class="fas fa-download mr-2"></i>Export Records
                        </button>
                    </div>
                </div>
            </header>

            <div class="container mx-auto px-6 py-8">
                <!-- Health Timeline -->
                <div class="max-w-4xl mx-auto">
                    <!-- Timeline Header -->
                    <div class="flex items-center justify-between mb-8">
                        <div class="flex items-center space-x-4">
                            <h2 class="text-2xl font-bold">Medical Timeline</h2>
                            <div class="flex items-center text-gray-400">
                                <i class="fas fa-filter mr-2"></i>
                                <select class="bg-dark-300 border border-gray-700 rounded-lg px-3 py-1">
                                    <option>All Records</option>
                                    <option>Check-ups</option>
                                    <option>Vaccinations</option>
                                    <option>Treatments</option>
                                </select>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2 text-sm">
                            <button class="px-3 py-1 bg-dark-300 rounded-lg hover:bg-dark-400">2023</button>
                            <button class="px-3 py-1 bg-dark-300 rounded-lg hover:bg-dark-400">2022</button>
                            <button class="px-3 py-1 bg-dark-300 rounded-lg hover:bg-dark-400">2021</button>
                        </div>
                    </div>

                    <!-- Timeline Content -->
                    <div class="space-y-6">
                        <!-- Timeline Item -->
                        <div class="relative pl-8 animate-fade-in">
                            <div class="absolute left-0 top-0 w-4 h-4 bg-blue-500 rounded-full"></div>
                            <div class="absolute left-2 top-4 bottom-0 w-0.5 bg-gray-700"></div>
                            <div class="bg-dark-100 rounded-xl p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <div>
                                        <span class="text-blue-400 text-sm">Regular Check-up</span>
                                        <h3 class="text-lg font-semibold">Annual Physical Examination</h3>
                                    </div>
                                    <span class="text-gray-400">Sept 15, 2023</span>
                                </div>
                                <div class="space-y-4">
                                    <p class="text-gray-300">Routine physical examination showed all vital signs within normal range. Height and weight measurements recorded.</p>
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                        <div class="bg-dark-300 p-3 rounded-lg">
                                            <span class="text-gray-400 text-sm">Blood Pressure</span>
                                            <p class="text-lg font-semibold">120/80</p>
                                        </div>
                                        <div class="bg-dark-300 p-3 rounded-lg">
                                            <span class="text-gray-400 text-sm">Heart Rate</span>
                                            <p class="text-lg font-semibold">72 bpm</p>
                                        </div>
                                        <div class="bg-dark-300 p-3 rounded-lg">
                                            <span class="text-gray-400 text-sm">Weight</span>
                                            <p class="text-lg font-semibold">65 kg</p>
                                        </div>
                                        <div class="bg-dark-300 p-3 rounded-lg">
                                            <span class="text-gray-400 text-sm">Height</span>
                                            <p class="text-lg font-semibold">175 cm</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Timeline Item -->
                        <div class="relative pl-8 animate-fade-in">
                            <div class="absolute left-0 top-0 w-4 h-4 bg-purple-500 rounded-full"></div>
                            <div class="absolute left-2 top-4 bottom-0 w-0.5 bg-gray-700"></div>
                            <div class="bg-dark-100 rounded-xl p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <div>
                                        <span class="text-purple-400 text-sm">Vaccination</span>
                                        <h3 class="text-lg font-semibold">Flu Vaccine</h3>
                                    </div>
                                    <span class="text-gray-400">Aug 20, 2023</span>
                                </div>
                                <p class="text-gray-300">Annual flu vaccination administered. No adverse reactions observed.</p>
                            </div>
                        </div>

                        <!-- Timeline Item -->
                        <div class="relative pl-8 animate-fade-in">
                            <div class="absolute left-0 top-0 w-4 h-4 bg-green-500 rounded-full"></div>
                            <div class="absolute left-2 top-4 bottom-0 w-0.5 bg-gray-700"></div>
                            <div class="bg-dark-100 rounded-xl p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <div>
                                        <span class="text-green-400 text-sm">Treatment</span>
                                        <h3 class="text-lg font-semibold">Minor Sports Injury</h3>
                                    </div>
                                    <span class="text-gray-400">July 5, 2023</span>
                                </div>
                                <p class="text-gray-300">Treated for a minor ankle sprain during basketball practice. RICE method recommended.</p>
                                <div class="mt-4 flex items-center space-x-4">
                                    <button class="text-blue-400 hover:text-blue-300">
                                        <i class="fas fa-file-medical mr-2"></i>View Report
                                    </button>
                                    <button class="text-blue-400 hover:text-blue-300">
                                        <i class="fas fa-image mr-2"></i>View X-Ray
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script src="history.js"></script>
</body>

</html>