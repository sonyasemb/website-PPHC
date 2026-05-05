<?php
$catatan_pasien = trim((string)($_GET["pasien"] ?? "Budi Santoso"));
$catatan_tanggal = trim((string)($_GET["tanggal"] ?? date("d-m-Y")));
$catatan_waktu = trim((string)($_GET["waktu"] ?? "10:00 - 11:00"));
$catatan_jenis = trim((string)($_GET["jenis"] ?? "Konseling Individu"));
$catatan_id = trim((string)($_GET["id_sesi"] ?? ("SESI-" . date("YmdHis"))));
$catatan_edit_id = (int)($_GET["catatan_id"] ?? 0);
$catatan_keluhan = "";
$catatan_observasi = "";
$catatan_ringkasan = "";
$catatan_diagnosis = "";
$catatan_rencana = "";
$catatan_rekomendasi = "";
$catatan_status = "selesai";
$status_label_map = [
    "selesai" => "Selesai",
    "lanjut" => "Perlu Sesi Lanjutan",
    "ditunda" => "Ditunda"
];
$status_class_map = [
    "selesai" => "status-selesai",
    "lanjut" => "status-lanjut",
    "ditunda" => "status-ditunda"
];

if (isset($conn)) {
    $catatan_row = null;
    if ($catatan_edit_id > 0) {
        $stmt = mysqli_prepare($conn, "SELECT * FROM catatan_klien WHERE id = ? LIMIT 1");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $catatan_edit_id);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            if ($res) {
                $catatan_row = mysqli_fetch_assoc($res);
            }
            mysqli_stmt_close($stmt);
        }
    } elseif ($catatan_id !== "") {
        $stmt = mysqli_prepare($conn, "SELECT * FROM catatan_klien WHERE id_sesi = ? LIMIT 1");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $catatan_id);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            if ($res) {
                $catatan_row = mysqli_fetch_assoc($res);
            }
            mysqli_stmt_close($stmt);
        }
    }

    if ($catatan_row) {
        $catatan_edit_id = (int)($catatan_row["id"] ?? $catatan_edit_id);
        $catatan_pasien = trim((string)($catatan_row["pasien"] ?? $catatan_pasien));
        $catatan_id = trim((string)($catatan_row["id_sesi"] ?? $catatan_id));
        $catatan_waktu = trim((string)($catatan_row["waktu"] ?? $catatan_waktu));
        $catatan_jenis = trim((string)($catatan_row["jenis"] ?? $catatan_jenis));
        $catatan_keluhan = (string)($catatan_row["keluhan"] ?? "");
        $catatan_observasi = (string)($catatan_row["observasi"] ?? "");
        $catatan_ringkasan = (string)($catatan_row["ringkasan"] ?? "");
        $catatan_diagnosis = (string)($catatan_row["diagnosis"] ?? "");
        $catatan_rencana = (string)($catatan_row["rencana"] ?? "");
        $catatan_rekomendasi = (string)($catatan_row["rekomendasi"] ?? "");
        $catatan_status = (string)($catatan_row["status_sesi"] ?? "selesai");

        $tanggal_db = $catatan_row["tanggal"] ?? "";
        if (function_exists("format_catatan_date")) {
            $catatan_tanggal = format_catatan_date($tanggal_db);
        } elseif ($tanggal_db !== "") {
            $catatan_tanggal = date("d-m-Y", strtotime((string)$tanggal_db));
        }
    }
}
$print_status_label = $status_label_map[$catatan_status] ?? "Selesai";
$print_status_class = $status_class_map[$catatan_status] ?? "status-selesai";
$psikolog_nama = trim((string)($_SESSION["nama"] ?? "Psikolog"));
$psikolog_jabatan = trim((string)($_SESSION["jabatan"] ?? "Psikolog Klinis"));
if ($psikolog_jabatan === "") {
    $psikolog_jabatan = "Psikolog Klinis";
}
$psikolog_initials = "PS";
if ($psikolog_nama !== "") {
    $parts = preg_split("/\s+/", $psikolog_nama);
    $initials = "";
    foreach ($parts as $part) {
        if ($part === "") {
            continue;
        }
        $initials .= strtoupper(substr($part, 0, 1));
    }
    if ($initials !== "") {
        $psikolog_initials = substr($initials, 0, 2);
    }
}
?>
<link rel="stylesheet" href="assets/css/catatan_sesi.css?v=<?php echo filemtime(__DIR__ . '/assets/css/catatan_sesi.css'); ?>">
<div class="catatan-page">
    <div class="catatan-topbar">
        <div class="catatan-pasien">Pasien: <strong><?php echo htmlspecialchars($catatan_pasien); ?></strong></div>
    </div>

    <div class="catatan-meta">
        <div class="meta-item"><strong>Tanggal:</strong> <?php echo htmlspecialchars($catatan_tanggal); ?></div>
        <div class="meta-item"><strong>Waktu:</strong> <?php echo htmlspecialchars($catatan_waktu); ?></div>
        <div class="meta-item"><strong>Jenis:</strong> <?php echo htmlspecialchars($catatan_jenis); ?></div>
        <div class="meta-item"><strong>ID Sesi:</strong> #<?php echo htmlspecialchars($catatan_id); ?></div>
    </div>

    <form class="catatan-layout" method="post" action="psikolog.php?page=catatan">
        <input type="hidden" name="action" value="save_catatan">
        <input type="hidden" name="pasien" value="<?php echo htmlspecialchars($catatan_pasien); ?>">
        <input type="hidden" name="tanggal" value="<?php echo htmlspecialchars($catatan_tanggal); ?>">
        <input type="hidden" name="waktu" value="<?php echo htmlspecialchars($catatan_waktu); ?>">
        <input type="hidden" name="jenis" value="<?php echo htmlspecialchars($catatan_jenis); ?>">
        <input type="hidden" name="id_sesi" value="<?php echo htmlspecialchars($catatan_id); ?>">
        <?php if ($catatan_edit_id > 0): ?>
            <input type="hidden" name="catatan_id" value="<?php echo (int)$catatan_edit_id; ?>">
        <?php endif; ?>
        <div class="catatan-main">
            <div class="catatan-card">
                <label for="keluhan">Keluhan Utama</label>
                <textarea id="keluhan" name="keluhan" rows="3" placeholder="Tulis keluhan utama pasien"><?php echo htmlspecialchars($catatan_keluhan); ?></textarea>

                <label for="observasi">Observasi Psikolog</label>
                <textarea id="observasi" name="observasi" rows="4" placeholder="Tulis hasil observasi selama sesi"><?php echo htmlspecialchars($catatan_observasi); ?></textarea>

                <label for="ringkasan">Ringkasan Pembicaraan</label>
                <textarea id="ringkasan" name="ringkasan" rows="4" placeholder="Ringkas topik pembicaraan utama"><?php echo htmlspecialchars($catatan_ringkasan); ?></textarea>

                <label for="diagnosis">Diagnosis / Temuan <span>(Opsional)</span></label>
                <textarea id="diagnosis" name="diagnosis" rows="3" placeholder="Masukkan diagnosis atau temuan klinis"><?php echo htmlspecialchars($catatan_diagnosis); ?></textarea>

                <label for="rencana">Rencana Tindak Lanjut</label>
                <textarea id="rencana" name="rencana" rows="3" placeholder="Rencana sesi lanjutan atau tindakan berikutnya"><?php echo htmlspecialchars($catatan_rencana); ?></textarea>

                <label for="rekomendasi">Rekomendasi / Terapi</label>
                <textarea id="rekomendasi" name="rekomendasi" rows="3" placeholder="Masukkan rekomendasi terapi"><?php echo htmlspecialchars($catatan_rekomendasi); ?></textarea>
            </div>
        </div>

        <aside class="catatan-side">
            <div class="catatan-card">
                <h4>Status Sesi</h4>
                <select name="status_sesi">
                    <option value="selesai" <?php echo $catatan_status === "selesai" ? "selected" : ""; ?>>Selesai</option>
                    <option value="lanjut" <?php echo $catatan_status === "lanjut" ? "selected" : ""; ?>>Perlu Sesi Lanjutan</option>
                    <option value="ditunda" <?php echo $catatan_status === "ditunda" ? "selected" : ""; ?>>Ditunda</option>
                </select>
            </div>
        </aside>

        <div class="catatan-actions">
            <button type="button" class="btn-cetak-inline" id="btn-cetak-catatan">Cetak PDF</button>
            <button type="submit" class="btn-simpan">Simpan Catatan</button>
            <a class="btn-batal" href="psikolog.php?page=dashboard">Batal</a>
        </div>
    </form>

    <div class="catatan-print" id="catatan-print">
        <div class="print-topline"></div>
        <div class="print-header">
            <div class="print-title">Catatan Konsultasi</div>
            <div class="print-doctor">
                <div class="print-doctor-meta">
                    <div class="print-doctor-name" id="print-psikolog-nama"><?php echo htmlspecialchars($psikolog_nama); ?></div>
                    <div class="print-doctor-role" id="print-psikolog-role"><?php echo htmlspecialchars($psikolog_jabatan); ?></div>
                </div>
                <div class="print-doctor-avatar" aria-hidden="true"><?php echo htmlspecialchars($psikolog_initials); ?></div>
            </div>
        </div>

        <div class="print-divider"></div>

        <div class="print-pasien">Pasien: <span id="print-pasien"><?php echo htmlspecialchars($catatan_pasien); ?></span></div>

        <div class="print-meta-grid">
            <div class="print-meta-item">
                <span class="print-meta-label">Tanggal:</span>
                <span id="print-tanggal"><?php echo htmlspecialchars($catatan_tanggal); ?></span>
            </div>
            <div class="print-meta-item">
                <span class="print-meta-label">Waktu:</span>
                <span id="print-waktu"><?php echo htmlspecialchars($catatan_waktu); ?></span>
            </div>
            <div class="print-meta-item">
                <span class="print-meta-label">Jenis:</span>
                <span id="print-jenis"><?php echo htmlspecialchars($catatan_jenis); ?></span>
            </div>
            <div class="print-meta-item print-meta-item-status">
                <span class="print-badge <?php echo htmlspecialchars($print_status_class); ?>" id="print-status-badge"><?php echo htmlspecialchars($print_status_label); ?></span>
            </div>
            <div class="print-meta-item span-2">
                <span class="print-meta-label">ID Sesi:</span>
                <span id="print-id">#<?php echo htmlspecialchars($catatan_id); ?></span>
            </div>
            <div class="print-meta-item span-2">
                <span class="print-meta-label">Psikolog:</span>
                <span id="print-psikolog"><?php echo htmlspecialchars($psikolog_nama); ?></span>
            </div>
        </div>

        <div class="print-section">
            <h4>Keluhan Utama</h4>
            <p id="print-keluhan" class="print-text">-</p>
        </div>

        <div class="print-section">
            <h4>Observasi Psikolog</h4>
            <p id="print-observasi" class="print-text">-</p>
        </div>

        <div class="print-section">
            <h4>Diagnosis / Temuan <span class="print-optional">(Opsional)</span></h4>
            <p id="print-diagnosis" class="print-text">-</p>
        </div>

        <div class="print-section">
            <h4>Ringkasan Tindak Lanjut</h4>
            <div class="print-pill" id="print-ringkasan">-</div>
        </div>

        <div class="print-section">
            <h4>Rencana Tindak Lanjut</h4>
            <ol class="print-list" id="print-rencana-list"></ol>
            <p class="print-text" id="print-rencana-text">-</p>
        </div>

        <div class="print-section">
            <h4>Rekomendasi / Terapi</h4>
            <ol class="print-list" id="print-rekomendasi-list"></ol>
            <p class="print-text" id="print-rekomendasi-text">-</p>
        </div>

        <div class="print-footer">
            <div class="print-footer-info">
                <div class="print-footer-line">Status Sesi: <span id="print-status"><?php echo htmlspecialchars($print_status_label); ?></span></div>
            </div>
            <div class="print-signature">
                <img id="print-ttd-img" class="print-sign-image" src="" alt="Tanda tangan psikolog">
                <div class="print-sign-name" id="print-ttd-name"><?php echo htmlspecialchars($psikolog_nama); ?></div>
                <div class="print-sign-role" id="print-ttd-role"><?php echo htmlspecialchars($psikolog_jabatan); ?></div>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const statusMap = {
                selesai: { label: "Selesai", className: "status-selesai" },
                lanjut: { label: "Perlu Sesi Lanjutan", className: "status-lanjut" },
                ditunda: { label: "Ditunda", className: "status-ditunda" }
            };

            const getValue = (id) => {
                const el = document.getElementById(id);
                return el ? el.value.trim() : "";
            };

            const setText = (id, value) => {
                const el = document.getElementById(id);
                if (!el) {
                    return;
                }
                const clean = value && value.trim() ? value.trim() : "-";
                el.textContent = clean;
            };

            const fillList = (listId, textId, rawValue) => {
                const listEl = document.getElementById(listId);
                const textEl = document.getElementById(textId);
                if (!listEl || !textEl) {
                    return;
                }
                const lines = rawValue
                    .split(/\r?\n/)
                    .map((line) => line.trim())
                    .filter(Boolean);
                if (lines.length > 1) {
                    listEl.innerHTML = "";
                    lines.forEach((line) => {
                        const li = document.createElement("li");
                        li.textContent = line;
                        listEl.appendChild(li);
                    });
                    listEl.style.display = "block";
                    textEl.style.display = "none";
                } else {
                    listEl.innerHTML = "";
                    listEl.style.display = "none";
                    textEl.style.display = "block";
                    textEl.textContent = lines[0] || "-";
                }
            };

            const updateStatus = () => {
                const statusSelect = document.querySelector('select[name="status_sesi"]');
                const statusValue = statusSelect ? statusSelect.value : "selesai";
                const statusInfo = statusMap[statusValue] || statusMap.selesai;
                const badge = document.getElementById("print-status-badge");
                if (badge) {
                    badge.textContent = statusInfo.label;
                    badge.classList.remove("status-selesai", "status-lanjut", "status-ditunda");
                    badge.classList.add(statusInfo.className);
                }
                setText("print-status", statusInfo.label);
            };

            const prepareCatatanPrint = () => {
                setText("print-keluhan", getValue("keluhan"));
                setText("print-observasi", getValue("observasi"));
                setText("print-diagnosis", getValue("diagnosis"));
                setText("print-ringkasan", getValue("ringkasan"));
                fillList("print-rencana-list", "print-rencana-text", getValue("rencana"));
                fillList("print-rekomendasi-list", "print-rekomendasi-text", getValue("rekomendasi"));
                updateStatus();
            };

            window.prepareCatatanPrint = prepareCatatanPrint;

            const printButton = document.getElementById("btn-cetak-catatan");
            if (printButton) {
                printButton.addEventListener("click", function () {
                    prepareCatatanPrint();
                    window.print();
                });
            }
            window.addEventListener("beforeprint", prepareCatatanPrint);
        })();
    </script>
</div>
