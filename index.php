<?php
include 'config.php';
session_start();

$msg = "";

// Retain input data values across POST cycles
$f_name_val = isset($_POST['f_name']) ? htmlspecialchars($_POST['f_name']) : '';
$f_phone_val = isset($_POST['f_phone']) ? htmlspecialchars($_POST['f_phone']) : '';
$f_region_val = isset($_POST['f_region']) ? $_POST['f_region'] : '';
$f_district_val = isset($_POST['f_district']) ? htmlspecialchars($_POST['f_district']) : '';
$f_waste_val = isset($_POST['f_waste']) ? $_POST['f_waste'] : '';
$f_desc_val = isset($_POST['f_desc']) ? htmlspecialchars($_POST['f_desc']) : '';

$p_name_val = isset($_POST['p_name']) ? htmlspecialchars($_POST['p_name']) : '';
$p_phone_val = isset($_POST['p_phone']) ? htmlspecialchars($_POST['p_phone']) : '';
$p_region_val = isset($_POST['p_region']) ? $_POST['p_region'] : '';
$p_addr_val = isset($_POST['p_addr']) ? htmlspecialchars($_POST['p_addr']) : '';
$p_date_val = isset($_POST['p_date']) ? $_POST['p_date'] : '';
$p_waste_val = isset($_POST['p_waste']) ? $_POST['p_waste'] : '';

// Mock logged-in user setup
$user_phone = "0241234567"; 
$user_query = mysqli_query($conn, "SELECT * FROM users WHERE phone='$user_phone'");
if (mysqli_num_rows($user_query) == 0) {
    mysqli_query($conn, "INSERT INTO users (phone, full_name, points) VALUES ('$user_phone', 'Demo Citizen', 150)");
    $points = 150;
} else {
    $user_data = mysqli_fetch_assoc($user_query);
    $points = $user_data['points'];
}

// Interactive Rewards Redemption Processor
if (isset($_POST['redeem_rewards'])) {
    $redeem_type = $_POST['redeem_type'];
    $provider = $_POST['provider'];
    $target_phone = mysqli_real_escape_string($conn, $_POST['target_phone']);
    $points_to_burn = intval($_POST['points_amount']);
    
    if ($points_to_burn <= 0) {
        $msg = "<div class='bg-rose-950/80 border-l-4 border-rose-500 text-rose-300 p-4 mb-4 rounded shadow-sm'>⚠️ Please specify a valid points total.</div>";
    } elseif ($points >= $points_to_burn) {
        $cash_value = $points_to_burn * 0.10; 
        $new_points = $points - $points_to_burn;
        
        if (mysqli_query($conn, "UPDATE users SET points = $new_points WHERE phone='$user_phone'")) {
            $points = $new_points;
            $msg = "<div class='bg-emerald-950/80 border-l-4 border-emerald-500 text-emerald-300 p-4 mb-4 rounded shadow-sm'>🎉 **Success!** Transferred **GHS " . number_format($cash_value, 2) . "** via $provider $redeem_type to $target_phone.</div>";
        }
    } else {
        $msg = "<div class='bg-rose-950/80 border-l-4 border-rose-500 text-rose-300 p-4 mb-4 rounded shadow-sm'>❌ **Insufficient Balance:** You cannot redeem $points_to_burn points.</div>";
    }
}

// Report Dumping Forms Handling with Image Upload Processing
if (isset($_POST['submit_report'])) {
    $f_name = mysqli_real_escape_string($conn, $_POST['f_name']);
    $f_phone = mysqli_real_escape_string($conn, $_POST['f_phone']);
    $f_region = mysqli_real_escape_string($conn, $_POST['f_region']);
    $f_district = mysqli_real_escape_string($conn, $_POST['f_district']);
    $f_waste = mysqli_real_escape_string($conn, $_POST['f_waste']);
    $f_desc = mysqli_real_escape_string($conn, $_POST['f_desc']);
    
    $image_path = "";
    if (isset($_FILES['f_image']) && $_FILES['f_image']['error'] == 0) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $filename = time() . "_" . basename($_FILES["f_image"]["name"]);
        $target_file = $target_dir . $filename;
        if (move_uploaded_file($_FILES["f_image"]["tmp_name"], $target_file)) {
            $image_path = $target_file;
        }
    }
    
    $sql = "INSERT INTO reports (full_name, phone, region, district, waste_type, description, image_path, status) 
            VALUES ('$f_name', '$f_phone', '$f_region', '$f_district', '$f_waste', '$f_desc', '$image_path', 'Pending')";
    if (mysqli_query($conn, $sql)) {
        mysqli_query($conn, "UPDATE users SET points = points + 20 WHERE phone='$user_phone'");
        $points += 20;
        $msg = "<div class='bg-emerald-950/80 border-l-4 border-emerald-500 text-emerald-300 p-4 mb-4 rounded shadow-sm'>🎉 Field dumping alert logged successfully! (+20 Points)</div>";
        $f_name_val = $f_phone_val = $f_region_val = $f_district_val = $f_waste_val = $f_desc_val = "";
    }
}

// Pickup Scheduling Form Handling
if (isset($_POST['submit_pickup'])) {
    $p_name = mysqli_real_escape_string($conn, $_POST['p_name']);
    $p_phone = mysqli_real_escape_string($conn, $_POST['p_phone']);
    $p_region = mysqli_real_escape_string($conn, $_POST['p_region']);
    $p_addr = mysqli_real_escape_string($conn, $_POST['p_addr']);
    $p_date = mysqli_real_escape_string($conn, $_POST['p_date']);
    $p_waste = mysqli_real_escape_string($conn, $_POST['p_waste']);
    
    $sql = "INSERT INTO pickups (full_name, phone, region, location, pickup_date, waste_type, status) 
            VALUES ('$p_name', '$p_phone', '$p_region', '$p_addr', '$p_date', '$p_waste', 'Pending')";
    if (mysqli_query($conn, $sql)) {
        mysqli_query($conn, "UPDATE users SET points = points + 10 WHERE phone='$user_phone'");
        $points += 10;
        $msg = "<div class='bg-emerald-950/80 border-l-4 border-emerald-500 text-emerald-300 p-4 mb-4 rounded shadow-sm'>🚛 Domestic pickup scheduled! (+10 Points)</div>";
        $p_name_val = $p_phone_val = $p_region_val = $p_addr_val = $p_date_val = $p_waste_val = "";
    }
}

$regions = ["Greater Accra", "Ashanti", "Western", "Central", "Eastern", "Volta", "Northern", "Upper East", "Upper West", "Bono", "Bono East", "Ahafo", "Oti", "Savannah", "North East", "Western North"];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoClean Ghana - Portal Control</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-950 text-slate-100 font-sans antialiased">

    <header class="bg-slate-900 border-b border-slate-800 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <span class="text-2xl">♻️</span>
                <h1 class="text-xl font-bold tracking-tight text-emerald-400">EcoClean Ghana</h1>
            </div>
            
            <div class="text-xs font-black tracking-widest text-slate-400 bg-slate-950 px-4 py-1.5 rounded-full border border-slate-800 animate-pulse">
                🚀 CTPSI GROUP_37
            </div>

            <a href="admin.php" class="bg-slate-880 hover:bg-slate-700 text-slate-300 px-4 py-2 rounded-lg font-bold text-sm transition border border-slate-700">Admin Dashboard →</a>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 py-8 space-y-8">
        
        <?php echo $msg; ?>

        <!-- TOP GRID AREA: ECO-REWARDS EXCHANGE & INFRASTRUCTURE MAP -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <!-- REWARDS CONVERSION CONSOLE -->
            <div class="bg-gradient-to-br from-slate-900 to-slate-950 p-5 rounded-2xl shadow-xl border border-emerald-800/40 flex flex-col justify-between">
                <div class="text-center">
                    <h3 class="text-[10px] uppercase tracking-widest font-bold text-emerald-400">Citizen Eco-Reward Balance</h3>
                    <div class="text-4xl font-black my-2 text-white"><?php echo $points; ?> <span class="text-xs text-slate-400 font-normal">Pts</span></div>
                    <p class="text-[10px] text-slate-400">10 Points = GHS 1.00 Cashout Value</p>
                </div>
                
                <form action="index.php" method="POST" class="mt-4 pt-4 border-t border-slate-800 space-y-2">
                    <div class="grid grid-cols-2 gap-2">
                        <select name="redeem_type" class="p-1.5 bg-slate-800 border border-slate-700 rounded-lg text-[11px] text-white focus:outline-none" required>
                            <option value="Mobile Money">Mobile Money</option>
                            <option value="Airtime">Airtime Payment</option>
                        </select>
                        <select name="provider" class="p-1.5 bg-slate-800 border border-slate-700 rounded-lg text-[11px] text-white focus:outline-none" required>
                            <option value="MTN">MTN MoMo</option>
                            <option value="Telecel">Telecel Cash</option>
                            <option value="AT">AT Money</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <input type="text" name="target_phone" placeholder="Recipient Number" class="p-1.5 bg-slate-800 border border-slate-700 rounded-lg text-[11px] text-white placeholder-slate-500 focus:outline-none" required>
                        <input type="number" name="points_amount" max="<?php echo $points; ?>" placeholder="Points to Spend" class="p-1.5 bg-slate-800 border border-slate-700 rounded-lg text-[11px] text-white placeholder-slate-500 focus:outline-none" required>
                    </div>
                    <button type="submit" name="redeem_rewards" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] p-2 rounded-xl transition shadow-md">Execute Point Transfer</button>
                </form>
            </div>

            <!-- SMART BINS GPS UTILITY BOARD -->
            <div id="centers" class="md:col-span-2 bg-slate-900 p-6 rounded-2xl shadow-xl border border-slate-800">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center border-b border-slate-800 pb-3 mb-4">
                    <div>
                        <h3 class="text-md font-bold text-white">📍 Regional Smart Infrastructure Locator</h3>
                        <p class="text-xs text-slate-400">Track distribution nodes across all 16 Ghanaian regions.</p>
                    </div>
                    <select id="regionFilter" onchange="filterCenters()" class="mt-2 sm:mt-0 p-2 border border-slate-700 rounded-xl bg-slate-800 text-white text-xs font-semibold focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        <option value="all">Display All Regions</option>
                        <?php foreach($regions as $r) { echo "<option value='$r'>$r</option>"; } ?>
                    </select>
                </div>
                <div id="centers-list" class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-[140px] overflow-y-auto pr-1">
                    <?php
                    $centers = mysqli_query($conn, "SELECT * FROM recycling_centers");
                    if(mysqli_num_rows($centers) == 0) {
                        echo "<div class='col-span-2 p-4 text-center text-xs text-slate-500 border border-dashed border-slate-800 rounded-xl'>No infrastructure nodes seeded yet.</div>";
                    }
                    while($c = mysqli_fetch_assoc($centers)) {
                        echo "<div class='center-item border-l-4 border-emerald-500 bg-slate-800 p-2.5 rounded-r-xl text-xs' data-region='".$c['region']."'>
                                <h4 class='font-bold text-white'>".htmlspecialchars($c['center_name'])."</h4>
                                <p class='text-slate-400 mt-0.5'>📍 ".htmlspecialchars($c['location'])."</p>
                              </div>";
                    }
                    ?>
                </div>
            </div>
        </div>

        <!-- MIDDLE GRID AREA: SIDE-BY-SIDE INTERFACES WITH FLUSH CONTROLS -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <!-- LEFT SECTION: REPORT ILLEGAL DUMPING -->
            <section class="bg-slate-900 p-6 rounded-2xl shadow-xl border border-slate-800 flex flex-col justify-between">
                <div>
                    <div class="border-b border-slate-800 pb-3 mb-4">
                        <h2 class="text-lg font-bold text-white">1. Report Illegal Dumping Hotspots</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Flag immediate drainage constraints or waste clusters.</p>
                    </div>
                    <form action="index.php" method="POST" enctype="multipart/form-data" class="space-y-3.5">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Full Name</label>
                                <input type="text" name="f_name" value="<?php echo $f_name_val; ?>" class="w-full p-2 rounded-xl bg-slate-800 text-white border border-slate-700 text-xs focus:outline-none" required>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Phone Contact</label>
                                <input type="text" name="f_phone" value="<?php echo $f_phone_val; ?>" class="w-full p-2 rounded-xl bg-slate-800 text-white border border-slate-700 text-xs focus:outline-none" required>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Region Bound</label>
                                <select name="f_region" class="w-full p-2 rounded-xl bg-slate-800 text-white border border-slate-700 text-xs focus:outline-none" required>
                                    <option value="">Select</option>
                                    <?php foreach($regions as $r) { 
                                        $sel = ($f_region_val === $r) ? 'selected' : '';
                                        echo "<option value='$r' $sel>$r</option>"; 
                                    } ?>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">District / Sub-Metro</label>
                                <input type="text" name="f_district" value="<?php echo $f_district_val; ?>" class="w-full p-2 rounded-xl bg-slate-800 text-white border border-slate-700 text-xs focus:outline-none" required>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Material Composition Stream</label>
                                <select name="f_waste" class="w-full p-2 rounded-xl bg-slate-800 text-white border border-slate-700 text-xs focus:outline-none" required>
                                    <option value="PET Bottles" <?php echo ($f_waste_val === 'PET Bottles') ? 'selected' : ''; ?>>PET Plastic Bottles</option>
                                    <option value="HDPE Sachet Bags" <?php echo ($f_waste_val === 'HDPE Sachet Bags') ? 'selected' : ''; ?>>HDPE Sachet Bags (Pure Water)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Evidence Photo Asset</label>
                                <input type="file" name="f_image" accept="image/*" class="w-full p-1.5 rounded-xl bg-slate-800 text-white border border-slate-700 text-xs focus:outline-none">
                            </div>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Incident Profile Context</label>
                            <textarea name="f_desc" rows="2" class="w-full p-2 rounded-xl bg-slate-800 text-white border border-slate-700 text-xs focus:outline-none"><?php echo $f_desc_val; ?></textarea>
                        </div>
                        
                        <div class="pt-2 flex space-x-2">
                            <button type="submit" name="submit_report" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white p-2 rounded-xl font-bold text-xs transition">
                                Dispatch Threat Alert
                            </button>
                            <button type="button" onclick="window.location.href=window.location.pathname;" class="bg-slate-800 hover:bg-slate-700 text-slate-400 font-bold text-xs px-4 py-2 rounded-xl transition border border-slate-700">
                                Clear Form
                            </button>
                        </div>
                    </form>
                </div>
            </section>

            <!-- RIGHT SECTION: SCHEDULE WASTE PICKUPS -->
            <section class="bg-slate-900 p-6 rounded-2xl shadow-xl border border-slate-800 flex flex-col justify-between">
                <div>
                    <div class="border-b border-slate-800 pb-3 mb-4">
                        <h2 class="text-lg font-bold text-white">2. Domestic & Business Waste Collection</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Book scheduled logistics pickups at your residence doorstep.</p>
                    </div>
                    <form action="index.php" method="POST" class="space-y-3.5">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Corporate / Owner Name</label>
                                <input type="text" name="p_name" value="<?php echo $p_name_val; ?>" class="w-full p-2 rounded-xl bg-slate-800 text-white border border-slate-700 text-xs focus:outline-none" required>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Phone Number</label>
                                <input type="text" name="p_phone" value="<?php echo $p_phone_val; ?>" class="w-full p-2 rounded-xl bg-slate-800 text-white border border-slate-700 text-xs focus:outline-none" required>
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Region</label>
                                <select name="p_region" class="w-full p-2 rounded-xl bg-slate-800 text-white border border-slate-700 text-xs focus:outline-none" required>
                                    <option value="">Select</option>
                                    <?php foreach($regions as $r) { 
                                        $sel = ($p_region_val === $r) ? 'selected' : '';
                                        echo "<option value='$r' $sel>$r</option>"; 
                                    } ?>
                                </select>
                            </div>
                            <div class="col-span-2">
                                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Digital/Physical Address</label>
                                <input type="text" name="p_addr" value="<?php echo $p_addr_val; ?>" class="w-full p-2 rounded-xl bg-slate-800 text-white border border-slate-700 text-xs focus:outline-none" required placeholder="e.g. GA-124-5231">
                            </div>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Target Appointment Date</label>
                            <input type="date" name="p_date" value="<?php echo $p_date_val; ?>" class="w-full p-2 rounded-xl bg-slate-800 text-white border border-slate-700 text-xs focus:outline-none" required>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Aggregated Stockpile Scale</label>
                            <select name="p_waste" class="w-full p-2 rounded-xl bg-slate-800 text-white border border-slate-700 text-xs focus:outline-none" required>
                                <option value="Sachet Scrap Sorting" <?php echo ($p_waste_val === 'Sachet Scrap Sorting') ? 'selected' : ''; ?>>Sachet Scrap Sorting</option>
                                <option value="Crushed Plastic Flakes/Bottles" <?php echo ($p_waste_val === 'Crushed Plastic Flakes/Bottles') ? 'selected' : ''; ?>>Crushed Plastic Flakes/Bottles</option>
                            </select>
                        </div>
                        
                        <div class="pt-2 flex space-x-2">
                            <button type="submit" name="submit_pickup" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white p-2 rounded-xl font-bold text-xs transition">
                                Confirm Appointment Frame
                            </button>
                            <button type="button" onclick="window.location.href=window.location.pathname;" class="bg-slate-800 hover:bg-slate-700 text-slate-400 font-bold text-xs px-4 py-2 rounded-xl transition border border-slate-700">
                                Clear Form
                            </button>
                        </div>
                    </form>
                </div>
            </section>
        </div>

        <!-- BOTTOM AREA: LIVE SUBMISSION RECORDS -->
        <section class="bg-slate-900 p-6 rounded-2xl shadow-xl border border-slate-800">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-sm font-bold text-white">📋 Live Consolidated Platform Execution Telemetry</h3>
                <span class="text-[10px] bg-slate-800 text-slate-400 px-2.5 py-1 rounded-full font-bold border border-slate-700/60">Updated Live</span>
            </div>
            <div class="overflow-x-auto rounded-xl border border-slate-800">
                <table class="w-full border-collapse text-left text-xs text-slate-300">
                    <thead class="bg-slate-800 text-slate-200 font-bold uppercase border-b border-slate-700">
                        <tr>
                            <th class="p-3">Source Pipeline</th>
                            <th class="p-3">Initiator Profile</th>
                            <th class="p-3">Jurisdiction Region</th>
                            <th class="p-3">Asset Classification Group</th>
                            <th class="p-3">State Vector</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        <?php
                        $union_query = "(SELECT 'Illegal Dumping' AS type, full_name AS fname, region AS rgn, waste_type AS wtype, IFNULL(status, 'Pending') AS stat FROM reports)
                                        UNION ALL
                                        (SELECT 'Domestic Pickup' AS type, full_name AS fname, region AS rgn, waste_type AS wtype, IFNULL(status, 'Pending') AS stat FROM pickups)
                                        ORDER BY stat ASC LIMIT 10";
                        $logs = mysqli_query($conn, $union_query);
                        
                        if(!$logs || mysqli_num_rows($logs) == 0) {
                            echo "<tr><td colspan='5' class='p-4 text-center text-slate-500 italic'>No live telemetry data found. Submit a form above to view live state updates.</td></tr>";
                        } else {
                            while($row = mysqli_fetch_assoc($logs)) {
                                $badge = ($row['type'] === "Domestic Pickup") ? "bg-blue-950/70 text-blue-400 border border-blue-900/40" : "bg-purple-950/70 text-purple-400 border border-purple-900/40";
                                
                                $status_color = "text-amber-400";
                                if($row['stat'] == 'Approved') $status_color = "text-blue-400";
                                if($row['stat'] == 'Deployed' || $row['stat'] == 'Completed') $status_color = "text-emerald-400";

                                echo "<tr class='hover:bg-slate-850/40 transition'>
                                        <td class='p-3'><span class='px-2 py-0.5 rounded text-[10px] font-bold tracking-wide uppercase $badge'>".$row['type']."</span></td>
                                        <td class='p-3 font-semibold text-white'>".htmlspecialchars($row['fname'])."</td>
                                        <td class='p-3 text-slate-300'>".htmlspecialchars($row['rgn'])."</td>
                                        <td class='p-3 text-slate-400'>".htmlspecialchars($row['wtype'])."</td>
                                        <td class='p-3 font-black $status_color'>".htmlspecialchars($row['stat'])."</td>
                                      </tr>";
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <script>
    function filterCenters() {
        let val = document.getElementById('regionFilter').value;
        document.querySelectorAll('.center-item').forEach(item => {
            item.style.display = (val === 'all' || item.getAttribute('data-region') === val) ? 'block' : 'none';
        });
    }
    </script>
</body>
</html>