<?php
require_once 'config.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: index.php");
    exit;
}

$activeSymKey  = $_SESSION['active_sym_key'];
$activePrivKey = $_SESSION['active_priv_key'];

if (isset($_GET['hapus'])) {
    $idHapus = (int) $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM servis_laptop WHERE id = $idHapus");
    header("Location: admin.php");
    exit;
}

$result = mysqli_query($conn, "SELECT * FROM servis_laptop ORDER BY id DESC");
$totalData = mysqli_num_rows($result);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Vault — FixLapBot.ID</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;600&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --color-midnight: #021024;
            --color-deep-navy: #052659;
            --color-steel-blue: #5483B3;
            --color-soft-blue: #7DA0CA;
            --color-ice-blue: #C1E8FF;
        }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(180deg, #C1E8FF 0%, #7DA0CA 20%, #5483B3 45%, #052659 75%, #021024 100%);
            background-attachment: fixed;
            min-height: 100vh;
            color: #F1F5F9;
        }
        .font-mono-code {
            font-family: 'JetBrains Mono', monospace;
        }
        .glass-card {
            background: rgba(2, 16, 36, 0.85);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(193, 232, 255, 0.2);
            box-shadow: 0 20px 50px rgba(2, 16, 36, 0.4), inset 0 1px 0 rgba(193, 232, 255, 0.15);
        }
        .glass-pill {
            background: rgba(2, 16, 36, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(193, 232, 255, 0.25);
            box-shadow: 0 10px 30px rgba(2, 16, 36, 0.3);
        }
        .cipher-box {
            background: rgba(5, 38, 89, 0.45);
            border: 1px solid rgba(125, 160, 202, 0.2);
        }
    </style>
</head>
<body class="min-h-screen relative overflow-x-hidden selection:bg-[#7DA0CA] selection:text-[#021024]">

    <!-- Ambient Glow Orbs -->
    <div class="fixed -top-32 left-1/4 w-96 h-96 rounded-full bg-[#C1E8FF]/25 blur-[140px] pointer-events-none"></div>
    <div class="fixed bottom-0 right-10 w-96 h-96 rounded-full bg-[#7DA0CA]/20 blur-[150px] pointer-events-none"></div>

    <!-- Floating Pill Navbar -->
    <header class="fixed top-5 left-1/2 -translate-x-1/2 z-50 w-[92%] max-w-5xl">
        <div class="glass-pill rounded-full px-5 py-2.5 flex items-center justify-between shadow-2xl">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-[#5483B3] to-[#C1E8FF] flex items-center justify-center text-[#021024] font-extrabold text-xs shadow-md">
                    FX
                </div>
                <div class="flex items-center space-x-2">
                    <span class="font-extrabold tracking-tight text-sm text-white">FIXLAPBOT<span class="text-[#7DA0CA]">.ID</span></span>
                    <span class="text-[10px] font-mono-code uppercase px-2 py-0.5 rounded-full bg-[#C1E8FF]/15 text-[#C1E8FF] border border-[#C1E8FF]/30">
                        Admin Unlocked
                    </span>
                </div>
            </div>

            <div class="flex items-center space-x-2">
                <a href="index.php" class="px-3.5 py-1.5 rounded-full text-xs font-medium text-slate-200 hover:text-white bg-white/10 border border-white/15 transition">
                    + Input Data Baru
                </a>
                <a href="logout.php" class="px-3.5 py-1.5 rounded-full text-xs font-bold bg-rose-500/20 text-rose-300 border border-rose-500/30 hover:bg-rose-500/30 transition">
                    Kunci Vault
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-6xl mx-auto px-4 pt-28 pb-16 relative z-10">

        <!-- Bento Metrics Summary -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
            <div class="glass-card rounded-3xl p-5">
                <span class="text-[10px] font-mono-code uppercase tracking-widest text-[#7DA0CA]">Total Konten Terenkripsi</span>
                <div class="text-3xl font-extrabold text-white mt-1"><?= $totalData ?> <span class="text-sm font-normal text-slate-300">Unit</span></div>
                <div class="text-[11px] text-[#C1E8FF] mt-2 font-mono-code">Tersimpan sebagai Ciphertext di MySQL</div>
            </div>

            <div class="glass-card rounded-3xl p-5">
                <span class="text-[10px] font-mono-code uppercase tracking-widest text-[#7DA0CA]">Kunci Simetris Aktif (AES-256)</span>
                <div class="text-sm font-mono-code font-semibold text-white mt-2 truncate"><?= htmlspecialchars($activeSymKey) ?></div>
                <div class="text-[11px] text-slate-300 mt-2">Membuka: Pelanggan, Unit, & Keluhan</div>
            </div>

            <div class="glass-card rounded-3xl p-5">
                <span class="text-[10px] font-mono-code uppercase tracking-widest text-[#C1E8FF]">Kunci Asimetris Aktif (RSA-2048)</span>
                <div class="text-xs font-mono-code text-slate-200 mt-2 truncate"><?= htmlspecialchars(substr($activePrivKey, 0, 48)) ?>...</div>
                <div class="text-[11px] text-slate-300 mt-2">Membuka: Password / PIN Perangkat</div>
            </div>
        </div>

        <!-- Section Heading -->
        <div class="flex items-center justify-between mb-5">
            <div>
                <h2 class="text-xl font-bold text-[#021024]">Brankas Konten Hasil Enkripsi & Dekripsi</h2>
                <p class="text-xs text-[#021024]/80 font-medium">Menampilkan wujud asli Ciphertext dari database beserta hasil dekripsi menggunakan kunci sesi aktif.</p>
            </div>
        </div>

        <!-- Bento Cards Queue List -->
        <?php if ($totalData === 0): ?>
            <div class="glass-card rounded-3xl p-12 text-center">
                <p class="text-sm text-slate-300">Belum ada data servis terenkripsi di dalam database.</p>
                <a href="index.php" class="inline-block mt-4 px-5 py-2.5 rounded-full text-xs font-bold bg-[#C1E8FF] text-[#021024] hover:bg-[#aee0ff] transition">
                    Input Data di Landing Page
                </a>
            </div>
        <?php else: ?>
            <div class="space-y-5">
                <?php while ($row = mysqli_fetch_assoc($result)):
                    $namaPlain    = decryptSymmetric($row['nama_pelanggan_enc'], $activeSymKey);
                    $tipePlain    = decryptSymmetric($row['tipe_laptop_enc'], $activeSymKey);
                    $keluhanPlain = decryptSymmetric($row['keluhan_enc'], $activeSymKey);
                    $passPlain    = decryptAsymmetric($row['password_laptop_enc'], $activePrivKey);
                ?>
                <div class="glass-card rounded-3xl p-6">
                    <!-- Top Card Bar -->
                    <div class="flex flex-wrap items-center justify-between gap-2 pb-4 mb-5 border-b border-white/10">
                        <div class="flex items-center space-x-3">
                            <span class="px-3 py-1 rounded-full text-xs font-mono-code font-bold bg-[#052659] text-[#C1E8FF] border border-[#7DA0CA]/30">
                                <?= htmlspecialchars($row['kode_servis']) ?>
                            </span>
                            <span class="text-xs font-mono-code text-slate-300"><?= $row['created_at'] ?></span>
                        </div>
                        <a href="admin.php?hapus=<?= $row['id'] ?>" onclick="return confirm('Hapus rekaman terenkripsi ini?')"
                           class="px-3 py-1 rounded-full text-xs font-medium bg-rose-500/10 text-rose-300 border border-rose-500/20 hover:bg-rose-500/20 transition">
                            Hapus Rekaman
                        </a>
                    </div>

                    <!-- 3-Column Bento Data Inside Each Record -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

                        <!-- Kolom 1: Pelanggan & Unit (Simetris) -->
                        <div class="cipher-box rounded-2xl p-4 flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-[10px] font-mono-code uppercase tracking-wider text-slate-300">Pelanggan & Unit</span>
                                    <span class="text-[10px] font-mono-code text-[#7DA0CA]">AES-256</span>
                                </div>
                                <div class="text-[10px] font-mono-code text-rose-300/90 uppercase mb-1">Ciphertext Database:</div>
                                <div class="font-mono-code text-[11px] text-slate-300 break-all bg-black/40 p-2.5 rounded-xl border border-white/5 mb-3">
                                    <span class="text-slate-400">Nama:</span> <?= htmlspecialchars($row['nama_pelanggan_enc']) ?><br>
                                    <span class="text-slate-400">Unit:</span> <?= htmlspecialchars($row['tipe_laptop_enc']) ?>
                                </div>
                            </div>
                            <div class="pt-2 border-t border-white/5">
                                <div class="text-[10px] font-mono-code text-[#C1E8FF] uppercase mb-1">Plaintext Hasil Dekripsi:</div>
                                <div class="text-sm font-bold text-white"><?= htmlspecialchars($namaPlain) ?></div>
                                <div class="text-xs text-[#7DA0CA] font-medium mt-0.5"><?= htmlspecialchars($tipePlain) ?></div>
                            </div>
                        </div>

                        <!-- Kolom 2: Keluhan Servis (Simetris) -->
                        <div class="cipher-box rounded-2xl p-4 flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-[10px] font-mono-code uppercase tracking-wider text-slate-300">Detail Keluhan</span>
                                    <span class="text-[10px] font-mono-code text-[#7DA0CA]">AES-256</span>
                                </div>
                                <div class="text-[10px] font-mono-code text-rose-300/90 uppercase mb-1">Ciphertext Database:</div>
                                <div class="font-mono-code text-[11px] text-slate-300 break-all bg-black/40 p-2.5 rounded-xl border border-white/5 mb-3">
                                    <?= htmlspecialchars($row['keluhan_enc']) ?>
                                </div>
                            </div>
                            <div class="pt-2 border-t border-white/5">
                                <div class="text-[10px] font-mono-code text-[#C1E8FF] uppercase mb-1">Plaintext Hasil Dekripsi:</div>
                                <div class="text-xs text-slate-200 leading-relaxed"><?= nl2br(htmlspecialchars($keluhanPlain)) ?></div>
                            </div>
                        </div>

                        <!-- Kolom 3: Kredensial Perangkat (Asimetris RSA) -->
                        <div class="cipher-box rounded-2xl p-4 flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-[10px] font-mono-code uppercase tracking-wider text-slate-300">Kredensial Laptop</span>
                                    <span class="text-[10px] font-mono-code text-[#C1E8FF]">RSA-2048</span>
                                </div>
                                <div class="text-[10px] font-mono-code text-rose-300/90 uppercase mb-1">Ciphertext RSA:</div>
                                <div class="font-mono-code text-[11px] text-slate-300 break-all bg-black/40 p-2.5 rounded-xl border border-white/5 mb-3 max-h-24 overflow-y-auto">
                                    <?= htmlspecialchars($row['password_laptop_enc']) ?>
                                </div>
                            </div>
                            <div class="pt-2 border-t border-white/5">
                                <div class="text-[10px] font-mono-code text-[#7DA0CA] uppercase mb-1">Dekripsi Private Key:</div>
                                <span class="inline-block font-mono-code text-xs font-bold text-[#021024] bg-[#C1E8FF] px-3 py-1 rounded-lg">
                                    <?= htmlspecialchars($passPlain) ?>
                                </span>
                            </div>
                        </div>

                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>

    </main>

</body>
</html>