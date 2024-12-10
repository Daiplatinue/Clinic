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
    <title>Student Health Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="../style.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#EFF6FF',
                            100: '#DBEAFE',
                            500: '#3B82F6',
                            600: '#2563EB',
                            700: '#1D4ED8'
                        },
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
</head>

<body class="bg-dark-200 text-gray-100">
    <div class="flex min-h-screen">
        <!-- Animated Sidebar -->
        <aside class="w-full lg:w-72 bg-dark-100/95 backdrop-blur-md border-r border-gray-800 lg:fixed h-auto lg:h-full animate-slide-in">
            <div class="p-4 lg:p-6">
                <div class="text-center mb-6 lg:mb-8">
                    <div class="relative inline-block group">
                        <img src="<?php echo !empty($user_data['u_image']) ? htmlspecialchars($user_data['u_image']) : '/uploads/profiles/'; ?>"
                            class="w-32 h-32 lg:w-40 lg:h-40 rounded-full object-cover border-4 border-blue-500 transition-transform duration-300 group-hover:scale-105">
                    </div>

                    <h4 class="text-lg lg:text-xl font-bold mt-3">
                        <?php echo htmlspecialchars($user_name); ?>
                    </h4>
                    <p class="text-gray-400">
                        <?php echo htmlspecialchars($grade); ?>
                    </p>
                    <div class="mt-3 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-500/20 text-green-400 backdrop-blur-sm border border-green-500/30">
                        <span class="w-2 h-2 mr-2 rounded-full bg-green-400"></span>
                        <?php echo htmlspecialchars($health_status); ?>
                    </div>
                </div>

                <nav class="space-y-2">
                    <a href="student.php"
                        class="flex items-center space-x-3 px-4 py-3 rounded-lg bg-gradient-to-r from-blue-600 to-blue-700 text-white hover:from-blue-700 hover:to-blue-800 transition-all duration-300 transform hover:translate-x-1">
                        <i class="fas fa-newspaper"></i>
                        <span>News Feed</span>
                    </a>
                    <a href="myprofile.php"
                        class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-dark-400 text-gray-400 hover:text-white transition-all duration-300 transform hover:translate-x-1">
                        <i class="fas fa-user"></i>
                        <span>My Profile</span>
                    </a>
                    <a href="health-history.php"
                        class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-dark-400 text-gray-400 hover:text-white transition-all duration-300 transform hover:translate-x-1">
                        <i class="fas fa-history"></i>
                        <span>Notifications</span>
                    </a>
                    <a href="../auth/login.php"
                        class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-dark-400 text-gray-400 hover:text-white transition-all duration-300 transform hover:translate-x-1">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </a>
                </nav>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 ml-72">
            <!-- Animated Header -->
            <header class="bg-dark-100 border-b border-gray-800 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h1 class="text-2xl font-bold">My Account</h1>
                    <div class="flex items-center space-x-4">
                        <button onclick="openCheckupModal()"
                            class="bg-primary-500 hover:bg-primary-600 text-white px-4 py-2 rounded-lg flex items-center transition-colors">
                            <i class="fas fa-calendar-plus mr-2"></i>
                            Request Check-up
                        </button>
                    </div>
                </div>
            </header>

            <div class="container mx-auto px-6 py-8">
                <!-- Animated News Feed -->
                <div class="max-w-3xl mx-auto space-y-6">
                    <!-- Health Alert Card -->
                    <div
                        class="health-card bg-dark-100 rounded-xl p-6 transform hover:scale-[1.02] transition-all duration-300">
                        <div class="flex items-center mb-4">
                            <div class="relative">
                                <img src="https://images.unsplash.com/photo-1550831107-1553da8c8464?ixlib=rb-4.0.3&auto=format&fit=crop&w=50&q=80"
                                    alt="School Nurse" class="w-12 h-12 rounded-full border-2 border-primary-500">
                                <span
                                    class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-500 rounded-full border-2 border-dark-100"></span>
                            </div>
                            <div class="ml-4">
                                <h4 class="font-bold text-lg">School Nurse</h4>
                                <p class="text-sm text-gray-400">2 hours ago</p>
                            </div>
                        </div>
                        <div class="mb-4">
                            <h3 class="text-xl font-bold mb-3 text-primary-500">Flu Season Alert 🚨</h3>
                            <p class="text-gray-300 leading-relaxed">
                                We're seeing an increase in flu cases. Remember to:
                            <ul class="list-disc list-inside mt-2 space-y-1">
                                <li>Wash hands frequently</li>
                                <li>Use hand sanitizers</li>
                                <li>Wear masks if feeling unwell</li>
                            </ul>
                            </p>
                        </div>
                        <div class="flex items-center justify-between text-gray-400">
                            <div class="flex space-x-6">
                                <button class="flex items-center space-x-2 hover:text-primary-500 transition-colors">
                                    <i class="far fa-thumbs-up"></i>
                                    <span class="animate-number" data-target="24">0</span>
                                </button>
                            </div>
                            <button class="hover:text-primary-500 transition-colors">
                                <i class="far fa-bookmark"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Health Tip Card -->
                    <div class="health-card bg-dark-100 rounded-xl p-6">
                        <div class="flex items-center mb-4">
                            <div class="relative">
                                <img src="https://images.unsplash.com/photo-1559839734-2b71ea197ec2?ixlib=rb-4.0.3&auto=format&fit=crop&w=50&q=80"
                                    alt="Nutritionist" class="w-12 h-12 rounded-full border-2 border-primary-500">
                                <span
                                    class="absolute -bottom-1 -right-1  w-4 h-4 bg-green-500 rounded-full border-2 border-dark-100"></span>
                            </div>
                            <div class="ml-4">
                                <h4 class="font-bold text-lg">School Nutritionist</h4>
                                <p class="text-sm text-gray-400">5 hours ago</p>
                            </div>
                        </div>
                        <div class="mb-4">
                            <h3 class="text-xl font-bold mb-3 text-primary-500">Healthy Snack Ideas 🍎</h3>
                            <p class="text-gray-300 leading-relaxed mb-4">
                                Boost your energy with these healthy options:
                            </p>
                            <div class="relative rounded-xl overflow-hidden group">
                                <img src="https://images.unsplash.com/photo-1490818387583-1baba5e638af?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80"
                                    alt="Healthy Snacks"
                                    class="w-full h-64 object-cover transform group-hover:scale-105 transition-transform duration-300">
                                <div
                                    class="absolute inset-0 bg-gradient-to-t from-dark-200 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center justify-between text-gray-400">
                            <div class="flex space-x-6">
                                <button class="flex items-center space-x-2 hover:text-primary-500 transition-colors">
                                    <i class="far fa-thumbs-up"></i>
                                    <span class="animate-number" data-target="42">0</span>
                                </button>
                            </div>
                            <button class="hover:text-primary-500 transition-colors">
                                <i class="far fa-bookmark"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Animated Profile Sidebar -->
        <aside class="w-80 bg-dark-100 border-l border-gray-800 hidden lg:block">
            <div class="p-6 sticky top-0">
                <h2
                    class="text-xl font-bold mb-6 bg-gradient-to-r from-primary-500 to-primary-600 bg-clip-text text-transparent">
                    My Health Profile
                </h2>
                <div class="space-y-4">
                    <div class="health-card bg-dark-300 rounded-lg p-4 hover:bg-dark-400 transition-colors">
                        <h3 class="text-sm text-gray-400 mb-2">Blood Type</h3>
                        <p class="text-3xl font-bold text-primary-500">
                            <?php echo htmlspecialchars($blood_type); ?>
                        </p>
                    </div>
                    <div class="health-card bg-dark-300 rounded-lg p-4 hover:bg-dark-400 transition-colors">
                        <h3 class="text-sm text-gray-400 mb-2">Age</h3>
                        <p class="text-3xl font-bold text-white">
                            <?php echo htmlspecialchars($age); ?>
                        </p>
                    </div>
                    <div class="health-card bg-dark-300 rounded-lg p-4 hover:bg-dark-400 transition-colors">
                        <h3 class="text-sm text-gray-400 mb-2">Height</h3>
                        <p class="text-3xl font-bold text-white">
                            <?php echo htmlspecialchars($height); ?>
                        </p>
                    </div>
                </div>
            </div>
        </aside>
    </div>

    <!-- Check-up Request Modal -->
    <div id="checkupModal"
        class="hidden fixed inset-0 bg-dark-200 bg-opacity-75 flex items-center justify-center transition-opacity duration-300 ease-out">
        <div
            class="m-auto bg-dark-100 rounded-xl p-8 w-full max-w-2xl transform scale-90 transition-transform duration-300 ease-out">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-primary-500">Request Check-up</h3>
                <button onclick="closeCheckupModal()" class="text-gray-400 hover:text-white transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <form action="check-up.php" method="POST" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-gray-400 mb-2">Preferred Date</label>
                        <input type="date" name="date" required min=""
                            class="w-full bg-dark-400 border border-gray-700 rounded-lg px-4 py-2 text-white">
                    </div>
                    <div>
                        <label class="block text-gray-400 mb-2">Preferred Time</label>
                        <input type="time" name="time" required
                            class="w-full bg-dark-400 border border-gray-700 rounded-lg px-4 py-2 text-white">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-gray-400 mb-2">Reason for Check-up</label>
                        <textarea name="reason" required rows="3"
                            placeholder="Please describe your symptoms or reason for the check-up"
                            class="w-full bg-dark-400 border border-gray-700 rounded-lg px-4 py-2 text-white resize-none"></textarea>
                    </div>
                    <div class="md:col-span-2">
                        <label class="flex items-center space-x-3 text-gray-400">
                            <input type="checkbox" name="urgent"
                                class="form-checkbox h-5 w-5 text-primary-500 rounded border-gray-700 bg-dark-400">
                            <span>This is an urgent request</span>
                        </label>
                    </div>
                </div>
                <div class="flex justify-end space-x-4 mt-8">
                    <button type="button" onclick="closeCheckupModal()"
                        class="px-6 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-6 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors">
                        Submit Request
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Success Dialog -->
    <div id="successDialog"
        class="hidden fixed inset-0 bg-dark-200 bg-opacity-75 flex items-center justify-center transition-opacity duration-300 ease-out">
        <div
            class="m-auto bg-dark-100 rounded-xl p-8 max-w-md text-center transform scale-90 transition-transform duration-300 ease-out">
            <div class="mb-4 text-green-500">
                <i class="fas fa-check-circle text-6xl"></i>
            </div>
            <h3 class="text-2xl font-bold mb-2">Request Submitted!</h3>
            <p class="text-gray-400 mb-6">Your check-up request has been successfully submitted. We'll notify
                you once it's confirmed.</p>
            <button onclick="closeSuccessDialog()"
                class="px-6 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors">
                OK
            </button>
        </div>
    </div>

    <script src="student.js"></script>
</body>

</html>