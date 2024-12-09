<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/auth.php");
    exit;
}

$conn = new mysqli('localhost', 'root', '', 'clinic_db');
if ($conn->connect_error) {
    die('Connection Failed: ' . $conn->connect_error);
}

$urgent_query = "SELECT c.*, u.u_fn as fullname 
                FROM check_up c 
                JOIN user u ON c.u_id = u.u_id 
                WHERE c.c_urgent = 'urgent' AND c.c_status = 'pending' 
                ORDER BY c.c_pd ASC, c.c_pt ASC";
$urgent_result = $conn->query($urgent_query);

$regular_query = "SELECT c.*, u.u_fn as fullname 
                 FROM check_up c 
                 JOIN user u ON c.u_id = u.u_id 
                 WHERE c.c_urgent = 'unurgent' AND c.c_status = 'pending' 
                 ORDER BY c.c_pd ASC, c.c_pt ASC";
$regular_result = $conn->query($regular_query);

function formatTime($time)
{
    return date('g:i A', strtotime($time));
}

function formatDate($date)
{
    return date('F j, Y', strtotime($date));
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
    <style>
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        .slide-in {
            animation: slideIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes slideIn {
            from {
                transform: translateY(20px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .hover-scale {
            transition: transform 0.2s ease;
        }

        .hover-scale:hover {
            transform: scale(1.01);
        }
    </style>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            100: '#E6F3FF',
                            500: '#0066CC',
                            600: '#0052A3',
                            700: '#003D7A'
                        },
                        dark: {
                            800: '#1F2937',
                            900: '#111827'
                        }
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-gray-50 dark:bg-gray-900">
    <div class="flex h-screen overflow-hidden">
        <aside class="hidden lg:flex lg:flex-col w-64 border-r border-gray-700 bg-gray-800">
            <div class="flex-1 flex flex-col min-h-0">
                <div class="flex items-center h-16 flex-shrink-0 px-4 border-b border-gray-700">
                    <span class="text-2xl font-bold bg-gradient-to-r from-blue-400 to-teal-400 text-transparent bg-clip-text">MediTrack</span>
                </div>
                <div class="flex-1 flex flex-col pt-5 pb-4 overflow-y-auto">
                    <div class="flex-1 px-3 space-y-1">
                        <a href="doctor.php" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg text-gray-300 hover:bg-gray-700">
                            <i class="fas fa-home w-6 h-6 mr-3"></i>
                            <span>Dashboard</span>
                        </a>
                        <a href="checkup.php" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg bg-blue-900/50 text-blue-100">
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

            <main class="flex-1 overflow-y-auto">
                <div class="p-8">
                    <div class="bg-dark-800 rounded-xl shadow-sm p-6 mb-6 fade-in">
                        <div class="flex flex-wrap gap-4 items-center justify-between">
                            <div class="flex-1 min-w-[200px]">
                                <div class="relative">
                                    <input type="text"
                                        placeholder="Search patients..."
                                        class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-700 bg-dark-900 text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                                </div>
                            </div>
                            <div class="flex flex-wrap gap-4">
                                <select class="px-4 py-2 rounded-lg border border-gray-700 bg-dark-900 text-white">
                                    <option value="">All Categories</option>
                                    <option value="urgent">Urgent</option>
                                    <option value="routine">Routine</option>
                                    <option value="follow-up">Follow-up</option>
                                </select>
                                <select class="px-4 py-2 rounded-lg border border-gray-700 bg-dark-900 text-white">
                                    <option value="">All Times</option>
                                    <option value="morning">Morning</option>
                                    <option value="afternoon">Afternoon</option>
                                    <option value="evening">Evening</option>
                                </select>
                                <button class="px-4 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600">
                                    <i class="fas fa-filter mr-2"></i>Apply Filters
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="bg-dark-800 rounded-xl shadow-sm p-6 mb-6 fade-in">
                            <h2 class="text-xl font-bold text-red-500 mb-4">Urgent Cases</h2>
                            <div class="space-y-4">
                                <?php while ($urgent = $urgent_result->fetch_assoc()): ?>
                                    <div class="border border-red-900 rounded-lg p-4 hover-scale bg-red-900/20">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-4">
                                                <div class="bg-red-900 p-3 rounded-lg">
                                                    <i class="fas fa-heartbeat text-red-500"></i>
                                                </div>
                                                <div>
                                                    <h3 class="font-semibold text-white"><?php echo htmlspecialchars($urgent['fullname']); ?></h3>
                                                    <p class="text-sm text-gray-400"><?php echo htmlspecialchars($urgent['c_rc']); ?></p>
                                                    <div class="flex items-center mt-2 space-x-4 text-sm">
                                                        <span class="text-red-500">
                                                            <i class="fas fa-calendar mr-1"></i><?php echo formatDate($urgent['c_pd']); ?>
                                                        </span>
                                                        <span class="text-red-500">
                                                            <i class="fas fa-clock mr-1"></i><?php echo formatTime($urgent['c_pt']); ?>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex space-x-2">
                                                <button onclick="showUpdateModal(<?php echo intval($urgent['c_id']); ?>)"
                                                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition duration-200">
                                                    Start Check-up
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        </div>

                        <!-- Regular Checkups Section -->
                        <div class="bg-dark-800 rounded-xl shadow-sm p-6 fade-in">
                            <h2 class="text-xl font-bold text-white mb-4">Regular Checkups</h2>
                            <div class="space-y-4">
                                <?php while ($regular = $regular_result->fetch_assoc()): ?>
                                    <div class="border border-gray-700 rounded-lg p-4 hover-scale">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-4">
                                                <div class="bg-blue-900 p-3 rounded-lg">
                                                    <i class="fas fa-user-md text-blue-500"></i>
                                                </div>
                                                <div>
                                                    <h3 class="font-semibold text-white"><?php echo htmlspecialchars($regular['fullname']); ?></h3>
                                                    <p class="text-sm text-gray-400"><?php echo htmlspecialchars($regular['c_rc']); ?></p>
                                                    <div class="flex items-center mt-2 space-x-4 text-sm">
                                                        <span class="text-gray-400">
                                                            <i class="fas fa-calendar mr-1"></i><?php echo formatDate($regular['c_pd']); ?>
                                                        </span>
                                                        <span class="text-gray-400">
                                                            <i class="fas fa-clock mr-1"></i><?php echo formatTime($regular['c_pt']); ?>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex space-x-2">
                                                <button onclick="showUpdateModal(<?php echo intval($regular['c_id']); ?>)"
                                                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200">
                                                    Start Check-up
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <div id="updateModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center">
        <div class="bg-gray-800 rounded-lg p-8 max-w-md w-full">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-white">Update Check-up Status</h3>
                <button class="modal-close text-gray-400 hover:text-white">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="updateForm" method="POST">
                <input type="hidden" id="checkup_id" name="checkup_id">
                <input type="hidden" name="update_checkup" value="1">

                <div class="mb-4">
                    <label for="health_status" class="block text-gray-400 mb-2">Health Status</label>
                    <textarea id="health_status" name="health_status" rows="3"
                        class="w-full bg-gray-700 text-white rounded-lg p-2 border border-gray-600 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                        required></textarea>
                </div>

                <div class="mb-6">
                    <label for="allergies" class="block text-gray-400 mb-2">Allergies</label>
                    <textarea id="allergies" name="allergies" rows="2"
                        class="w-full bg-gray-700 text-white rounded-lg p-2 border border-gray-600 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"></textarea>
                </div>

                <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg transition duration-200">
                    Update Status
                </button>
            </form>
        </div>
    </div>

    <script type="module" src="checkup.js"></script>
</body>

</html>