<?php
session_start();

// ✅ Ensure user is logged in and is a recipient
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'recipient') {
    header("Location: ../login.html");
    exit();
}

include "db_connect.php";

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// ✅ Update expired listings dynamically
$conn->query("
    UPDATE food_listings 
    SET status = 'Expired' 
    WHERE status = 'Active' AND available_until <= NOW()
");

// ✅ Fetch ALL food items (recipients should see everything)
$stmt = $conn->prepare("SELECT * FROM food_listings WHERE status = 'Active' ORDER BY created_at DESC");
$stmt->execute();
$result = $stmt->get_result();
$food_items = $result->fetch_all(MYSQLI_ASSOC);
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Recipient Dashboard | CampusFoodShare</title>
  <link href="../src/output.css" rel="stylesheet">
</head>
<body class="bg-white min-h-screen font-sans">
    
<div class="bg-green-700 text-white px-6 py-4 flex justify-between items-center">
  <!-- Left: Logo -->
  <h1 class="text-xl font-bold">CampusFoodShare - Recipient Dashboard</h1>

  <!-- Right: Welcome + Notifications + Logout -->
  <div class="flex items-center gap-6">
    <span>Welcome, <b><?php echo htmlspecialchars($username); ?></b></span>

    <!-- Notification Bell -->
    <div class="relative" id="notifWrapper">
      <button id="notifBell" class="relative p-2 rounded-full hover:bg-green-600 focus:outline-none">
        <span class="sr-only">Open notifications</span>
        <span>🔔</span>
        <span id="notifCount" class="hidden absolute -top-1 -right-1 text-xs px-1 rounded-full bg-red-600 text-white">0</span>
      </button>

      <!-- Dropdown -->
        <div id="notifDropdown" class="hidden fixed right-0 mt-2 w-80 bg-green-50 border border-green-200 rounded-2xl shadow-lg overflow-hidden">
        <div class="px-4 py-2 font-semibold bg-green-100 text-green-900">Notifications</div>
        <ul id="notifList" class="max-h-96 overflow-y-auto divide-y divide-green-200 text-green-900"></ul>
        <div class="px-4 py-2 bg-green-100 text-right">
          <button id="markAllBtn" class="text-sm underline text-green-800 hover:text-green-900">Mark all as read</button>
        </div>
        </div>
      </div>

    <a href="logout.php" class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded-lg">Logout</a>
  </div>
</div>

        
<!-- Browse Listings Section -->
<div id="browseSection" class="max-w-4xl mx-auto mt-8">

<h2 class="text-2xl font-bold text-green-700 mb-6">Available Food Listings</h2>

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
            
        <form method="POST" action="claim_food.php" class="mt-4 flex items-center gap-3">
        <input type="hidden" name="food_id" value="<?php echo $item['id']; ?>">

        <div class="flex items-center border rounded-lg overflow-hidden">
            <button type="button" class="px-3 bg-gray-200 hover:bg-gray-300" onclick="decrementQty(<?php echo $item['id']; ?>)">-</button>
            <input 
                type="number" 
                name="quantity" 
                value="1" 
                min="1" 
                max="<?php echo $item['quantity']; ?>" 
                id="qty-<?php echo $item['id']; ?>" 
                class="w-12 text-center border-none outline-none"
                required
            >
            <button type="button" class="px-3 bg-gray-200 hover:bg-gray-300" onclick="incrementQty(<?php echo $item['id']; ?>)">+</button>
            </div>
            <div>
                <button type="submit" 
                    class="bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-lg shadow-md">
                        Claim This Food
                </button>
            </div> 
        </div>
        </form>

        
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
function incrementQty(foodId) {
    const input = document.getElementById(`qty-${foodId}`);
    const max = parseInt(input.max);
    let current = parseInt(input.value);
    if (current < max) input.value = current + 1;
}

function decrementQty(foodId) {
    const input = document.getElementById(`qty-${foodId}`);
    const min = parseInt(input.min);
    let current = parseInt(input.value);
    if (current > min) input.value = current - 1;
}
</script>
<script>
// Basic polling for notifications
async function fetchNotifications() {
  try {
    const res = await fetch('notifications.php', { credentials: 'same-origin' });
    const data = await res.json();
    if (!data.success) return;

    const countEl = document.getElementById('notifCount');
    const listEl = document.getElementById('notifList');
    listEl.innerHTML = '';

    // Badge
    if (data.unread > 0) {
      countEl.textContent = data.unread;
      countEl.classList.remove('hidden');
    } else {
      countEl.classList.add('hidden');
    }

    // List
    if (data.notifications.length === 0) {
      const li = document.createElement('li');
      li.className = 'px-4 py-3 text-sm text-gray-500';
      li.textContent = 'No notifications yet';
      listEl.appendChild(li);
    } else {
      data.notifications.forEach(n => {
        const li = document.createElement('li');
        li.className = 'px-4 py-3 text-sm hover:bg-gray-50';
        const time = new Date(n.created_at.replace(' ', 'T')); // naive parse
        li.innerHTML = `
          <div class="flex items-start gap-3">
            <div class="mt-0.5">${n.is_read == 0 ? '🟢' : '⚪'}</div>
            <div class="flex-1">
              <div class="font-medium">${n.message}</div>
              <div class="text-xs text-gray-500">${time.toLocaleString()}</div>
            </div>
          </div>
        `;
        listEl.appendChild(li);
      });
    }
  } catch (e) {
    console.error('notif fetch failed', e);
  }
}

// Toggle dropdown & mark as read
document.getElementById('notifBell').addEventListener('click', async () => {
  const dd = document.getElementById('notifDropdown');
  dd.classList.toggle('hidden');
  if (!dd.classList.contains('hidden')) {
    await fetch('notifications_mark_read.php', { method: 'POST', credentials: 'same-origin' });
    await fetchNotifications();
  }
});

document.getElementById('markAllBtn').addEventListener('click', async () => {
  await fetch('notifications_mark_read.php', { method: 'POST', credentials: 'same-origin' });
  await fetchNotifications();
});

// Poll every 10s
fetchNotifications();
setInterval(fetchNotifications, 10000);
</script>


</body>
</html>
