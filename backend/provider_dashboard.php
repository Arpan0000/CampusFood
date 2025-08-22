<?php
session_start();

// ✅ Ensure user is logged in and is a provider
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'provider') {
    header("Location: ../login.html");
    exit();
}

include "db_connect.php";

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Fetch food items for this provider
$stmt = $conn->prepare("SELECT * FROM food_listings WHERE provider_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$food_items = $result->fetch_all(MYSQLI_ASSOC);
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Provider Dashboard | CampusFoodShare</title>
  <link href="../src/output.css" rel="stylesheet">
  <script>
    function showTab(tab) {
      document.getElementById("addFormSection").classList.add("hidden");
      document.getElementById("browseSection").classList.add("hidden");

      document.getElementById(tab).classList.remove("hidden");

      // Highlight active tab
      document.getElementById("addTab").classList.remove("bg-green-900");
      document.getElementById("browseTab").classList.remove("bg-green-900");

      document.getElementById(tab + "Tab").classList.add("bg-green-900");
    }
  </script>
</head>
<body class="bg-white min-h-screen font-sans">

  <!-- Navbar -->
  <div class="bg-green-700 text-white px-6 py-4 flex justify-between items-center">
    <h1 class="text-xl font-bold">CampusFoodShare - Provider Dashboard</h1>
    <div class="flex gap-4">
      <button id="browseTab" onclick="showTab('browseSection')" class="px-3 py-2 rounded-lg hover:bg-green-900">Browse Listings</button>
      <button id="addTab" onclick="showTab('addFormSection')" class="px-3 py-2 rounded-lg hover:bg-green-800">Add Listing</button>
    </div>
    <div>
      <span class="mr-4">Welcome, <b><?php echo htmlspecialchars($username); ?></b></span>
      <a href="logout.php" class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded-lg">Logout</a>
    </div>
  </div>
        
    <!--Add Listing Section-->
  <div id="addFormSection" class="max-w-2xl mx-auto mt-8 hidden">
  <div class="bg-white rounded-xl shadow-lg border border-gray-300 p-6">
    <h2 class="text-2xl font-bold text-green-700 mb-6">Add Food Listing</h2>
    <form action="save_listing.php" method="POST" class="space-y-4">

      <!-- Food Title -->
      <input type="text" name="food_title" placeholder="Food Title"
             class="w-full border rounded-lg px-4 py-2" required>

      <!-- Food Description -->
      <textarea name="food_description" placeholder="Food Description"
                class="w-full border rounded-lg px-4 py-2" rows="3" required></textarea>

      <!-- Food Type (Veg/Non-Veg) -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Food Type</label>
        <div class="flex gap-6">
          <label class="flex items-center gap-2">
            <input type="radio" name="food_type" value="Veg" required class="text-green-600">
            <span class="text-gray-800">Veg</span>
          </label>
          <label class="flex items-center gap-2">
            <input type="radio" name="food_type" value="Non-Veg" required class="text-red-600">
            <span class="text-gray-800">Non-Veg</span>
          </label>
        </div>
      </div>

      <!-- Quantity -->
      <input type="number" name="quantity" placeholder="Quantity"
             class="w-full border rounded-lg px-4 py-2" required>

      <!-- Freshness -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Freshness Status</label>
        <div class="flex gap-6">
          <label class="flex items-center gap-2">
            <input type="radio" name="freshness_status" value="Fresh" required class="text-green-600">
            <span class="text-gray-800">Fresh</span>
          </label>
          <label class="flex items-center gap-2">
            <input type="radio" name="freshness_status" value="Good" required class=" text-green-600">
            <span class="text-gray-800">Good</span>
          </label>
          <label class="flex items-center gap-2">
            <input type="radio" name="freshness_status" value="Near Expiry" required class="text-red-600">
            <span class="text-gray-800">Near Expiry</span>
          </label>
        </div>
      </div>

      <!-- Available Until -->
      <label class="block text-sm font-medium text-gray-700 mb-1">Available Until</label>
      <input type="datetime-local" name="available_until"
             class="w-full border rounded-lg px-4 py-2" required>

      <!-- Pickup Location -->
      <input type="text" name="pickup_location" placeholder="Pickup Location"
             class="w-full border rounded-lg px-4 py-2" required>

      <!-- Buttons -->
      <div class="flex justify-end gap-3">
        <button type="reset" class="px-4 py-2 bg-gray-300 rounded-lg">Clear</button>
        <button type="submit"
                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
          Save
        </button>
      </div>
    </form>
  </div>
</div>


  <!-- Browse Listings Section -->
<div id="browseSection" class="max-w-4xl mx-auto mt-8">
  <h2 class="text-2xl font-bold text-green-700 mb-6">Your Food Listings</h2>

  <div class="bg-white rounded-xl shadow-lg border border-gray-300 p-6">
    <?php if (count($food_items) > 0): ?>
      <div class="space-y-5">
        <?php foreach ($food_items as $item): ?>
          <div class="bg-green-50 rounded-lg p-5 border border-gray-200 hover:bg-gray-100 transition">
            <!-- Food Title -->
            <h3 class="text-xl font-semibold text-gray-900 mb-2">
              <?php echo htmlspecialchars($item['food_title']); ?>
            </h3>

            <!-- Food Description -->
            <p class="text-sm text-gray-700 mb-2">
              📝 <?php echo htmlspecialchars($item['food_description'] ?? 'No description provided'); ?>
            </p>

            <!-- Food Type -->
            <p class="text-sm text-gray-700 mb-2">
              🥗 Type: 
              <span class="<?php echo ($item['food_type'] === 'Veg') ? 'text-green-700 font-medium' : 'text-red-700 font-medium'; ?>">
                <?php echo htmlspecialchars($item['food_type'] ?? 'N/A'); ?>
              </span>
            </p>

            <!-- Quantity -->
            <p class="text-sm text-gray-700 mb-1">🍽️ Quantity: 
              <span class="font-medium text-gray-800"><?php echo htmlspecialchars($item['quantity']); ?></span>
            </p>

            <!-- Freshness -->
            <p class="text-sm text-gray-700 mb-1">🟢 Freshness: 
              <span class="font-medium text-gray-800"><?php echo htmlspecialchars($item['freshness_status']); ?></span>
            </p>

            <!-- Pickup Window -->
            <p class="text-sm text-gray-700 mb-1">📍 Pickup Location: 
              <span class="font-medium text-gray-800">
                <?php echo htmlspecialchars($item['pickup_location'] ?? 'Not specified'); ?>
              </span>
            </p>

            <!-- Expiry Timer -->
            <p class="text-sm text-gray-700">⏳ Expires In: 
              <span id="timer-<?php echo $item['id']; ?>" 
                    data-expiry="<?php echo htmlspecialchars($item['available_until']); ?>" 
                    class="font-medium text-red-600"></span>
            </p>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <p class="text-gray-600">No food items added yet.</p>
    <?php endif; ?>
  </div>
</div>

<script>
// Expiry Countdown Timer
function updateTimers() {
  document.querySelectorAll("[id^='timer-']").forEach(el => {
    const expiry = new Date(el.dataset.expiry).getTime();
    const now = new Date().getTime();
    const distance = expiry - now;

    if (distance <= 0) {
      el.innerHTML = "Expired";
      el.classList.remove("text-red-600");
      el.classList.add("text-gray-500");
    } else {
      const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
      const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
      const seconds = Math.floor((distance % (1000 * 60)) / 1000);
      el.innerHTML = `${hours}h ${minutes}m ${seconds}s`;
    }
  });
}
setInterval(updateTimers, 1000);
</script>



  <script>
    // Default: Show Browse Listings
    if (window.location.hash === "#browseSection") {
        showTab('browseSection');
    } else if (window.location.hash === "#addFormSection") {
        showTab('addFormSection');
    } else {
        showTab('browseSection'); // fallback default
    }


  </script>

</body>
</html>


