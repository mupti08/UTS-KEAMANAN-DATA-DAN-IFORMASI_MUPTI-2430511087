<?php
require_once 'config.php';

$msgSuccess = '';
$msgError   = '';

// PROSES 1: SIMPAN & ENKRIPSI DATA SERVIS BARU
if (isset($_POST['submit_servis'])) {
    $kode     = "FX-" . strtoupper(substr(md5(uniqid()), 0, 6));
    $nama     = trim($_POST['nama_pelanggan']);
    $tipe     = trim($_POST['tipe_laptop']);
    $keluhan  = trim($_POST['keluhan']);
    $passLap  = trim($_POST['password_laptop']);

    $namaEnc    = encryptSymmetric($nama, SYSTEM_SYMMETRIC_KEY);
    $tipeEnc    = encryptSymmetric($tipe, SYSTEM_SYMMETRIC_KEY);
    $keluhanEnc = encryptSymmetric($keluhan, SYSTEM_SYMMETRIC_KEY);
    $passLapEnc = encryptAsymmetric($passLap, SYSTEM_PUBLIC_KEY);

    $stmt = mysqli_prepare($conn, "INSERT INTO servis_laptop (kode_servis, nama_pelanggan_enc, tipe_laptop_enc, keluhan_enc, password_laptop_enc) VALUES (?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "sssss", $kode, $namaEnc, $tipeEnc, $keluhanEnc, $passLapEnc);

    if (mysqli_stmt_execute($stmt)) {
        $msgSuccess = "Unit <b>$kode</b> berhasil dienkripsi dan disimpan ke dalam antrean database.";
    } else {
        $msgError = "Gagal menyimpan data: " . mysqli_error($conn);
    }
}

// PROSES 2: LOGIN ADMIN DENGAN KUNCI SIMETRIS & ASIMETRIS
if (isset($_POST['login_kunci'])) {
    $symKey  = trim($_POST['symmetric_key']);
    $privKey = trim($_POST['asymmetric_private_key']);

    if (verifyAdminKeys($symKey, $privKey)) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['active_sym_key']  = $symKey;
        $_SESSION['active_priv_key'] = $privKey;
        header("Location: admin.php");
        exit;
    } else {
        $msgError = "Otorisasi Ditolak: Kunci Simetris atau Private Key Asimetris tidak valid.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FixLapBot.ID — CryptoVault Service System</title>
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
        .glass-input {
            background: rgba(5, 38, 89, 0.55);
            border: 1px solid rgba(125, 160, 202, 0.25);
            transition: all 0.2s ease;
        }
        .glass-input:focus {
            border-color: #7DA0CA;
            box-shadow: 0 0 0 3px rgba(125, 160, 202, 0.25);
            outline: none;
        }
    </style>
</head>
<body class="min-h-screen relative overflow-x-hidden selection:bg-[#7DA0CA] selection:text-[#021024]">

    <!-- Ambient Moonish Glow Orbs -->
    <div class="fixed -top-32 -left-32 w-96 h-96 rounded-full bg-[#C1E8FF]/25 blur-[140px] pointer-events-none"></div>
    <div class="fixed top-1/3 -right-32 w-96 h-96 rounded-full bg-[#7DA0CA]/20 blur-[140px] pointer-events-none"></div>
    <div class="fixed bottom-0 left-1/4 w-96 h-96 rounded-full bg-[#5483B3]/20 blur-[150px] pointer-events-none"></div>

    <!-- Floating Pill Navbar (FixLapBot Signature) -->
    <header class="fixed top-5 left-1/2 -translate-x-1/2 z-50 w-[92%] max-w-4xl">
        <div class="glass-pill rounded-full px-5 py-2.5 flex items-center justify-between shadow-2xl">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-[#5483B3] to-[#C1E8FF] flex items-center justify-center text-[#021024] font-extrabold text-xs shadow-md">
                    FX
                </div>
                <div class="flex items-center space-x-2">
                    <span class="font-extrabold tracking-tight text-sm text-white">FIXLAPBOT<span class="text-[#7DA0CA]">.ID</span></span>
                    <span class="text-[10px] font-mono-code uppercase px-2 py-0.5 rounded-full bg-white/5 text-slate-300 border border-white/10">
                        CryptoVault
                    </span>
                </div>
            </div>

            <div class="flex items-center space-x-2">
                <span class="px-3.5 py-1.5 rounded-full text-xs font-semibold bg-white/10 text-[#C1E8FF] border border-white/10">
                    Landing Portal
                </span>
                <?php if (isset($_SESSION['admin_logged_in'])): ?>
                    <a href="admin.php" class="px-3.5 py-1.5 rounded-full text-xs font-bold bg-[#C1E8FF] text-[#021024] hover:bg-[#aee0ff] transition">
                        Vault Admin
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="max-w-6xl mx-auto px-4 pt-28 pb-16 relative z-10">

        <!-- Editorial Header -->
        <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-[#021024]/10 border border-[#021024]/20 mb-3 backdrop-blur-md">
                    <span class="w-2 h-2 rounded-full bg-[#052659]"></span>
                    <span class="text-[11px] font-mono-code uppercase tracking-widest text-[#021024] font-bold">Hybrid Cryptography Architecture</span>
                </div>
                <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight text-[#021024]">
                    Service Privacy <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#052659] to-[#5483B3]">Vault</span>
                </h1>
            </div>
            <p class="text-xs text-[#021024]/80 font-medium max-w-sm leading-relaxed">
                Sistem informasi manajemen servis laptop dengan enkripsi database penuh dan autentikasi berbasis kunci simetris serta asimetris.
            </p>
        </div>

        <!-- Alert Notifications -->
        <?php if ($msgSuccess): ?>
            <div class="mb-6 glass-card rounded-2xl p-4 border-l-4 border-l-[#C1E8FF] text-sm text-slate-200">
                <?= $msgSuccess ?>
            </div>
        <?php endif; ?>
        <?php if ($msgError): ?>
            <div class="mb-6 glass-card rounded-2xl p-4 border-l-4 border-l-rose-500 text-sm text-rose-300">
                <?= $msgError ?>
            </div>
        <?php endif; ?>

        <!-- Bento Grid Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            <!-- BENTO 1: REGISTRASI & ENKRIPSI DATA SERVIS (7 Cols) -->
            <div class="lg:col-span-7 glass-card rounded-3xl p-6 md:p-8 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <span class="text-[10px] font-mono-code uppercase tracking-widest text-slate-400">Bento Module 01</span>
                            <h2 class="text-xl font-bold text-white mt-0.5">Input Data Servis Laptop</h2>
                        </div>
                        <span class="text-[11px] font-mono-code px-3 py-1 rounded-full bg-[#7DA0CA]/15 text-[#C1E8FF] border border-[#7DA0CA]/30">
                            AES-256 + RSA-2048
                        </span>
                    </div>

                    <form method="POST" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[11px] font-mono-code uppercase tracking-wider text-slate-300 mb-1.5">
                                    Nama Pelanggan & Kontak <span class="text-[#7DA0CA]">[AES]</span>
                                </label>
                                <input type="text" name="nama_pelanggan" required
                                    class="w-full glass-input rounded-xl px-4 py-3 text-sm text-white">
                            </div>

                            <div>
                                <label class="block text-[11px] font-mono-code uppercase tracking-wider text-slate-300 mb-1.5">
                                    Tipe & Seri Laptop <span class="text-[#7DA0CA]">[AES]</span>
                                </label>
                                <input type="text" name="tipe_laptop" required
                                    class="w-full glass-input rounded-xl px-4 py-3 text-sm text-white">
                            </div>
                        </div>

                        <div>
                            <label class="block text-[11px] font-mono-code uppercase tracking-wider text-slate-300 mb-1.5">
                                Password Windows / PIN BIOS <span class="text-[#C1E8FF]">[RSA Public Key]</span>
                            </label>
                            <input type="text" name="password_laptop" required
                                class="w-full glass-input rounded-xl px-4 py-3 text-sm text-white font-mono-code">
                        </div>

                        <div>
                            <label class="block text-[11px] font-mono-code uppercase tracking-wider text-slate-300 mb-1.5">
                                Deskripsi Kerusakan / Keluhan <span class="text-[#7DA0CA]">[AES]</span>
                            </label>
                            <textarea name="keluhan" rows="4" required
                                class="w-full glass-input rounded-xl px-4 py-3 text-sm text-white"></textarea>
                        </div>

                        <div class="pt-2">
                            <button type="submit" name="submit_servis"
                                class="w-full rounded-2xl bg-gradient-to-r from-[#052659] via-[#5483B3] to-[#7DA0CA] hover:opacity-95 text-white font-extrabold py-3.5 text-sm tracking-wide transition shadow-lg shadow-[#052659]/30">
                                Enkripsi & Simpan Data Servis
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- BENTO 2: PORTAL LOGIN ADMIN KUNCI SIMETRIS & ASIMETRIS (5 Cols) -->
            <div class="lg:col-span-5 glass-card rounded-3xl p-6 md:p-8 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <span class="text-[10px] font-mono-code uppercase tracking-widest text-slate-400">Bento Module 02</span>
                            <h2 class="text-xl font-bold text-white mt-0.5">Autentikasi Kunci Admin</h2>
                        </div>
                        <span class="text-[11px] font-mono-code px-3 py-1 rounded-full bg-[#C1E8FF]/15 text-[#C1E8FF] border border-[#C1E8FF]/30">
                            Key Paradigm
                        </span>
                    </div>

                    <form method="POST" class="space-y-4">
                        <div>
                            <label class="block text-[11px] font-mono-code uppercase tracking-wider text-[#7DA0CA] mb-1.5">
                                Kunci Simetris (Secret Key)
                            </label>
                            <input type="password" name="symmetric_key" required
                                class="w-full glass-input rounded-xl px-4 py-3 text-sm font-mono-code text-[#C1E8FF]">
                        </div>

                        <div>
                            <label class="block text-[11px] font-mono-code uppercase tracking-wider text-[#C1E8FF] mb-1.5">
                                Kunci Asimetris (RSA Private Key)
                            </label>
                            <textarea name="asymmetric_private_key" rows="8" required
                                class="w-full glass-input rounded-xl px-4 py-3 text-xs font-mono-code text-slate-200 leading-relaxed"></textarea>
                        </div>

                        <div class="pt-2">
                            <button type="submit" name="login_kunci"
                                class="w-full rounded-2xl bg-[#C1E8FF] hover:bg-[#aee0ff] text-[#021024] font-extrabold py-3.5 text-sm tracking-wide transition shadow-lg shadow-[#C1E8FF]/20">
                                Buka Brankas Halaman Admin
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </main>

</body>
</html>