<?php
include 'config.php';
session_start();

$auth_error = "";

// Process logout action explicitly
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    unset($_SESSION['admin_logged_in']);
    session_destroy();
    header("Location: index.php");
    exit();
}

// Handle administrative login gate processing
if (isset($_POST['login_submit'])) {
    $admin_user = trim($_POST['admin_user']);
    $admin_pass = trim($_POST['admin_pass']);
    
    // Explicit requested credential checks
    if ($admin_user === "GROUP37" && $admin_pass === "2026") {
        $_SESSION['admin_logged_in'] = true;
    } else {
        $auth_error = "Invalid administrator user key or system password constraint.";
    }
}

// RENDER LOGIN GATEWAY IF SESSION NOT VALIDATED
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>EPA Environmental Console - Secure Login Gate</title>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="bg-slate-950 text-slate-100 font-sans min-h-screen flex items-center justify-center p-4">
        <div class="w-full max-w-md bg-slate-900 border border-slate-800 p-6 sm:p-8 rounded-2xl shadow-2xl space-y-6">
            <div class="text-center">
                <span class="text-3xl">🛡️</span>
                <h2 class="text-xl font-black text-white tracking-tight mt-2">EPA Infrastructure Node</h2>
                <p class="text-xs text-slate-400 mt-1">Authorized Node Controller Entry Gateway</p>
                <div class="text-[10px] inline-block bg-slate-950 px-3 py-1 rounded-full border border-slate-800 text-emerald-400 font-bold mt-2">CTPSI GROUP_37</div>
            </div>

            <?php if(!empty($auth_error)) { ?>
                <div class="p-3 text-xs bg-rose-950/80 border border-rose-800 text-rose-300 rounded-xl text-center">
                    ❌ <?php echo $auth_error; ?>
                </div>
            <?php } ?>

            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-[11px] uppercase tracking-wider text-slate-400 font-bold mb-1">Admin Name</label>
                    <input type="text" name="admin_user" class="w-full p-2.5 rounded-xl bg-slate-950 text-white border border-slate-800 text-xs focus:ring-1 focus:ring-emerald-500 focus:outline-none" required placeholder="Enter administration identity">
                </div>
                <div>
                    <label class="block text-[11px] uppercase tracking-wider text-slate-400 font-bold mb-1">System Password</label>
                    <input type="password" name="admin_pass" class="w-full p-2.5 rounded-xl bg-slate-950 text-white border border-slate-800 text-xs focus:ring-1 focus:ring-emerald-500 focus:outline-none" required placeholder="••••••••">
                </div>
                <div class="pt-2 flex space-x-2">
                    <button type="submit" name="login_submit" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs p-2.5 rounded-xl transition shadow-md">Authenticate Portal Node</button>
                    <a href="index.php" class="bg-slate-880 hover:bg-slate-700 text-slate-400 text-xs font-bold p-2.5 rounded-xl transition flex items-center justify-center">Cancel</a>
                </div>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit();
}

// Handle Interactive Status Pipeline Shifts
if (isset($_POST['update_status'])) {
    $table = $_POST['target_table'];
    $row_id = intval($_POST['row_id']);
    $next_status = $_POST['update_status'];
    
    if (in_array($next_status, ['Pending', 'Approved', 'Deployed']) && in_array($table, ['reports', 'pickups'])) {
        mysqli_query($conn, "UPDATE $table SET status='$next_status' WHERE id=$row_id");
    }
}

$total_reports = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM reports"))['count'];
$total_pickups = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM pickups"))['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Environmental Protection Agency - Multi-Agency Master Node</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-950 font-sans antialiased text-slate-100 min-h-screen block lg:flex">

    <!-- MOBILE NAVIGATION HEADER -->
    <header class="bg-slate-900 border-b border-slate-800 w-full p-4 flex justify-between items-center lg:hidden sticky top-0 z-40">
        <div>
            <h2 class="text-white text-sm font-black tracking-wide">🇬🇭 EPA Console</h2>
            <p class="text-[9px] text-emerald-400 font-bold uppercase tracking-wider">Group 37 Node</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="index.php" class="bg-slate-800 hover:bg-slate-700 text-slate-300 px-2.5 py-1 rounded-lg font-bold text-[10px] border border-slate-700">Client Portal</a>
            <button onclick="toggleMobileMenu()" class="text-slate-400 hover:text-white focus:outline-none p-1 bg-slate-800 rounded-lg border border-slate-700">
                🎛️ Menu
            </button>
        </div>
    </header>

    <!-- SIDEBAR CONTAINER -->
    <aside id="sidebarMenu" class="hidden lg:flex w-64 bg-slate-900 text-slate-300 p-5 space-y-6 fixed inset-y-0 left-0 z-50 lg:z-auto border-r border-slate-800 flex-col justify-between">
        <div class="space-y-6">
            <div class="flex justify-between items-center lg:block">
                <div>
                    <h2 class="text-white text-md font-black tracking-wide">🇬🇭 EPA Environment Console</h2>
                    <p class="text-[10px] text-emerald-400 font-bold uppercase tracking-wider mt-1">Multi-Agency Master Node</p>
                </div>
                <button onclick="toggleMobileMenu()" class="lg:hidden text-slate-400 text-lg">&times;</button>
            </div>
            <div class="text-[11px] bg-slate-950 p-2.5 rounded-xl border border-slate-800 text-center text-slate-400 font-bold">
                ⚡ Node Operator Mode
            </div>
            <nav class="space-y-1 text-xs">
                <a href="#" class="block bg-emerald-800 text-white p-3 rounded-xl font-bold">📊 Consolidated Overview</a>
                <a href="index.php" class="block hover:bg-slate-800 p-3 rounded-xl transition text-slate-400">🌐 Public Portal Client</a>
            </nav>
        </div>
        <div>
            <a href="admin.php?action=logout" class="block w-full text-center text-xs font-bold text-rose-400 bg-slate-950 hover:bg-rose-950 border border-slate-800 p-2.5 rounded-xl transition">Session Terminate 🔒</a>
        </div>
    </aside>

    <!-- WORKSPACE CONSOLE VIEWPORT -->
    <main class="w-full lg:ml-64 p-4 sm:p-6 lg:p-8 space-y-6 sm:space-y-8 min-w-0">
        
        <header class="hidden lg:flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-black text-white tracking-tight">National Waste Stream Analytics Dashboard</h2>
                <p class="text-xs text-slate-400">Government oversight tracker for municipal plastic collection optimization.</p>
            </div>
            <div class="flex items-center space-x-3">
                <span class="text-xs font-black text-slate-400 tracking-widest">CTPSI GROUP_37</span>
                <span class="bg-emerald-950 text-emerald-400 border border-emerald-800 text-xs px-3 py-1 rounded-full font-bold">📡 Systems Consolidated</span>
            </div>
        </header>

        <!-- OVERVIEW STATS BOARD -->
        <section class="p-4 sm:p-6 bg-slate-900 rounded-2xl border border-slate-800 space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="bg-slate-850 p-4 sm:p-5 rounded-xl border border-slate-700/50">
                    <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Unmanaged Pollution Threats</h4>
                    <p class="text-3xl sm:text-4xl font-black text-rose-500 mt-1"><?php echo $total_reports; ?></p>
                </div>
                <div class="bg-slate-850 p-4 sm:p-5 rounded-xl border border-slate-700/50">
                    <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Corporate Scheduled Pickup Volume</h4>
                    <p class="text-3xl sm:text-4xl font-black text-blue-500 mt-1"><?php echo $total_pickups; ?></p>
                </div>
            </div>
        </section>

        <!-- ILLEGAL DUMPING HOTSPOTS DATA INTERFACE -->
        <section class="bg-slate-900 rounded-2xl border border-slate-800 overflow-hidden">
            <div class="p-4 bg-slate-850 border-b border-slate-800 flex flex-col sm:flex-row justify-between sm:items-center gap-2">
                <h3 class="text-xs sm:text-sm font-black text-white text-emerald-400">Government Oversight & User Dumping Hotspots</h3>
                <span class="self-start sm:self-auto text-[9px] sm:text-[10px] bg-purple-950 text-purple-400 px-2.5 py-0.5 rounded font-bold border border-purple-900">Field Telemetry</span>
            </div>
            
            <div class="w-full overflow-x-auto block">
                <table class="w-full text-left text-slate-300 min-w-[850px] sm:min-w-full">
                    <thead class="bg-slate-800 text-slate-200 uppercase font-bold text-[10px] sm:text-[11px]">
                        <tr>
                            <th class="p-3">Reporter</th>
                            <th class="p-3">Contact</th>
                            <th class="p-3">Region Bound</th>
                            <th class="p-3">District/Sector Location</th>
                            <th class="p-3">Stream Type</th>
                            <th class="p-3">Context Description</th>
                            <th class="p-3">Asset</th>
                            <th class="p-3">State Vector</th>
                            <th class="p-3 text-center">Interactive Switch</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800 text-xs">
                        <?php
                        $reports = mysqli_query($conn, "SELECT * FROM reports ORDER BY id DESC");
                        if(mysqli_num_rows($reports) == 0) echo "<tr><td colspan='9' class='p-4 text-center text-slate-500 italic'>No material threat reports logged in system database.</td></tr>";
                        while($r = mysqli_fetch_assoc($reports)) {
                            $st = $r['status'];
                            $badge_color = ($st == 'Pending') ? 'bg-amber-950 text-amber-400 border-amber-900' : (($st == 'Approved') ? 'bg-blue-950 text-blue-400 border-blue-900' : 'bg-emerald-950 text-emerald-400 border-emerald-900');
                            
                            $img_cell = "<span class='text-slate-600 italic text-[10px]'>None</span>";
                            if (!empty($r['image_path']) && file_exists($r['image_path'])) {
                                $img_cell = "<button type='button' onclick=\"openAssetModal('".$r['image_path']."')\" class='text-emerald-400 hover:underline font-bold text-[10px] bg-slate-950 px-2 py-0.5 border border-slate-800 rounded'>View Asset 🖼️</button>";
                            }

                            echo "<tr class='hover:bg-slate-850/30 transition'>
                                    <td class='p-3 font-semibold text-white'>".htmlspecialchars($r['full_name'])."</td>
                                    <td class='p-3 font-mono text-slate-400'>".htmlspecialchars($r['phone'])."</td>
                                    <td class='p-3'>".htmlspecialchars($r['region'])."</td>
                                    <td class='p-3 font-medium text-slate-300'>".htmlspecialchars($r['district'])."</td>
                                    <td class='p-3'><span class='bg-slate-800 text-slate-300 px-1.5 py-0.5 border border-slate-700 rounded text-[10px]'>".htmlspecialchars($r['waste_type'])."</span></td>
                                    <td class='p-3 max-w-[180px] truncate text-slate-400' title='".htmlspecialchars($r['description'])."'>".htmlspecialchars($r['description'])."</td>
                                    <td class='p-3'>".$img_cell."</td>
                                    <td class='p-3'><span class='px-2 py-0.5 rounded-full font-bold border text-[10px] $badge_color'>$st</span></td>
                                    <td class='p-3'>
                                        <form method='POST' class='flex justify-center space-x-1'>
                                            <input type='hidden' name='row_id' value='".$r['id']."'>
                                            <input type='hidden' name='target_table' value='reports'>
                                            <button type='submit' name='update_status' value='Pending' class='px-1.5 py-0.5 bg-amber-700 text-white rounded font-bold text-[10px] hover:bg-amber-600 transition'>Pending</button>
                                            <button type='submit' name='update_status' value='Approved' class='px-1.5 py-0.5 bg-blue-700 text-white rounded font-bold text-[10px] hover:bg-blue-600 transition'>Approve</button>
                                            <button type='submit' name='update_status' value='Deployed' class='px-1.5 py-0.5 bg-emerald-700 text-white rounded font-bold text-[10px] hover:bg-emerald-600 transition'>Deploy</button>
                                        </form>
                                    </td>
                                  </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- DOMESTIC WASTE PICKUP INTERFACE -->
        <section class="bg-slate-900 rounded-2xl border border-slate-800 overflow-hidden">
            <div class="p-4 bg-slate-850 border-b border-slate-800 flex flex-col sm:flex-row justify-between sm:items-center gap-2">
                <h3 class="text-xs sm:text-sm font-black text-white text-emerald-400">Scheduled Municipal Waste & Domestic Pickup Zones</h3>
                <span class="self-start sm:self-auto text-[9px] sm:text-[10px] bg-blue-950 text-blue-400 px-2.5 py-0.5 rounded font-bold border border-blue-900">Logistics Processing</span>
            </div>
            
            <div class="w-full overflow-x-auto block">
                <table class="w-full text-left text-slate-300 min-w-[850px] sm:min-w-full">
                    <thead class="bg-slate-800 text-slate-200 uppercase font-bold text-[10px] sm:text-[11px]">
                        <tr>
                            <th class="p-3">Client Entity</th>
                            <th class="p-3">Contact Key</th>
                            <th class="p-3">Jurisdiction Region</th>
                            <th class="p-3">Digital / Full Address</th>
                            <th class="p-3">Target Date</th>
                            <th class="p-3">Material Stream</th>
                            <th class="p-3">Current Pipeline State</th>
                            <th class="p-3 text-center">Interactive Switch</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800 text-xs">
                        <?php
                        $pickups = mysqli_query($conn, "SELECT * FROM pickups ORDER BY id DESC");
                        if(mysqli_num_rows($pickups) == 0) echo "<tr><td colspan='8' class='p-4 text-center text-slate-500 italic'>No collection logs or scheduled appointments found.</td></tr>";
                        while($p = mysqli_fetch_assoc($pickups)) {
                            $st = $p['status'];
                            $badge_color = ($st == 'Pending') ? 'bg-amber-950 text-amber-400 border-amber-900' : (($st == 'Approved') ? 'bg-blue-950 text-blue-400 border-blue-900' : 'bg-emerald-950 text-emerald-400 border-emerald-900');
                            echo "<tr class='hover:bg-slate-850/30 transition'>
                                    <td class='p-3 font-semibold text-white'>".htmlspecialchars($p['full_name'])."</td>
                                    <td class='p-3 text-slate-400 font-mono'>".htmlspecialchars($p['phone'])."</td>
                                    <td class='p-3'>".htmlspecialchars($p['region'])."</td>
                                    <td class='p-3 text-slate-300 font-medium max-w-[220px] truncate' title='".htmlspecialchars($p['location'])."'>".htmlspecialchars($p['location'])."</td>
                                    <td class='p-3 text-slate-300'>".$p['pickup_date']."</td>
                                    <td class='p-3'><span class='bg-slate-800 text-slate-300 px-1.5 py-0.5 border border-slate-700 rounded text-[10px]'>".htmlspecialchars($p['waste_type'])."</span></td>
                                    <td class='p-3'><span class='px-2 py-0.5 rounded-full font-bold border text-[10px] $badge_color'>$st</span></td>
                                    <td class='p-3'>
                                        <form method='POST' class='flex justify-center space-x-1'>
                                            <input type='hidden' name='row_id' value='".$p['id']."'>
                                            <input type='hidden' name='target_table' value='pickups'>
                                            <button type='submit' name='update_status' value='Pending' class='px-1.5 py-0.5 bg-amber-700 text-white rounded font-bold text-[10px] hover:bg-amber-600 transition'>Pending</button>
                                            <button type='submit' name='update_status' value='Approved' class='px-1.5 py-0.5 bg-blue-700 text-white rounded font-bold text-[10px] hover:bg-blue-600 transition'>Approve</button>
                                            <button type='submit' name='update_status' value='Deployed' class='px-1.5 py-0.5 bg-emerald-700 text-white rounded font-bold text-[10px] hover:bg-emerald-600 transition'>Deploy</button>
                                        </form>
                                    </td>
                                  </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <!-- INTERACTIVE ASSET MODAL PREVIEW -->
    <div id="assetOverlayModal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
        <div class="bg-slate-900 border border-slate-800 rounded-2xl max-w-md w-full overflow-hidden shadow-2xl relative">
            <div class="p-4 border-b border-slate-800 flex justify-between items-center bg-slate-850">
                <h4 class="text-xs font-black uppercase tracking-wider text-slate-300">EPA Threat Field Asset Preview</h4>
                <button type="button" onclick="closeAssetModal()" class="text-slate-400 hover:text-white text-sm font-bold bg-slate-800 hover:bg-slate-700 rounded-full h-6 w-6 flex items-center justify-center">&times;</button>
            </div>
            <div class="p-4 flex justify-center bg-slate-950">
                <img id="modalAssetSource" src="" alt="Threat Evidence" class="max-h-[300px] w-auto object-contain rounded-lg border border-slate-800 shadow-md">
            </div>
        </div>
    </div>

    <script>
    function toggleMobileMenu() {
        const menu = document.getElementById('sidebarMenu');
        if (menu.classList.contains('hidden')) {
            menu.classList.remove('hidden');
            menu.classList.add('flex');
        } else {
            menu.classList.remove('flex');
            menu.classList.add('hidden');
        }
    }

    function openAssetModal(srcPath) {
        document.getElementById('modalAssetSource').src = srcPath;
        document.getElementById('assetOverlayModal').classList.remove('hidden');
        document.getElementById('assetOverlayModal').classList.add('flex');
    }
    
    function closeAssetModal() {
        document.getElementById('assetOverlayModal').classList.remove('flex');
        document.getElementById('assetOverlayModal').classList.add('hidden');
        document.getElementById('modalAssetSource').src = '';
    }
    </script>
</body>
</html>