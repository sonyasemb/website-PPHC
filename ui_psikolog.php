<div class="profil-page">
    <?php if ($profile_saved): ?>
        <div class="profil-alert success">Profil berhasil diperbarui.</div>
    <?php endif; ?>
    <?php if ($profil_alert_error !== ""): ?>
        <div class="profil-alert error"><?php echo htmlspecialchars($profil_alert_error); ?></div>
    <?php endif; ?>
    <?php if ($profil_alert_success !== ""): ?>
        <div class="profil-alert success"><?php echo htmlspecialchars($profil_alert_success); ?></div>
    <?php endif; ?>

    <div class="profil-identity-card">
        <img src="ikon/psikolog1.jpeg" alt="Foto Psikolog">
        <div>
            <h4><?php echo htmlspecialchars($psikolog_profile["nama_lengkap"]); ?>, Psikolog</h4>
            <p><?php echo htmlspecialchars($psikolog_profile["spesialisasi"]); ?></p>
            <span>STR: <?php echo htmlspecialchars($psikolog_profile["no_str"]); ?></span>
        </div>
    </div>

    <div class="profil-stat-grid">
        <div class="profil-stat-card">
            <small>Total Klien</small>
            <strong><?php echo number_format((int)$profile_stats["total_klien"], 0, ",", "."); ?></strong>
        </div>
        <div class="profil-stat-card">
            <small>Total Sesi</small>
            <strong><?php echo number_format((int)$profile_stats["total_sesi"], 0, ",", "."); ?></strong>
        </div>
        <div class="profil-stat-card">
            <small>Sesi Bulan Ini</small>
            <strong><?php echo number_format((int)$profile_stats["sesi_bulan_ini"], 0, ",", "."); ?></strong>
        </div>
        <div class="profil-stat-card">
            <small>Follow-up Aktif</small>
            <strong><?php echo number_format((int)$profile_stats["follow_up_aktif"], 0, ",", "."); ?></strong>
        </div>
    </div>

    <form class="profil-form" method="post" action="profile_psikolog.php">
        <input type="hidden" name="action" value="save_profile">
        <div class="profil-grid">
            <section class="profil-card">
                <h4>Informasi Pribadi</h4>
                <div class="profil-field-grid">
                    <label>Nama Lengkap</label>
                    <?php if ($profile_edit_mode): ?>
                        <input type="text" name="nama_lengkap" value="<?php echo htmlspecialchars($psikolog_profile["nama_lengkap"]); ?>" required>
                    <?php else: ?>
                        <div class="profil-value"><?php echo htmlspecialchars($psikolog_profile["nama_lengkap"]); ?></div>
                    <?php endif; ?>

                    <label>Jenis Kelamin</label>
                    <?php if ($profile_edit_mode): ?>
                        <select name="jenis_kelamin">
                            <option value="Laki-laki" <?php echo $psikolog_profile["jenis_kelamin"] === "Laki-laki" ? "selected" : ""; ?>>Laki-laki</option>
                            <option value="Perempuan" <?php echo $psikolog_profile["jenis_kelamin"] === "Perempuan" ? "selected" : ""; ?>>Perempuan</option>
                        </select>
                    <?php else: ?>
                        <div class="profil-value"><?php echo htmlspecialchars($psikolog_profile["jenis_kelamin"]); ?></div>
                    <?php endif; ?>

                    <label>Tanggal Lahir</label>
                    <?php if ($profile_edit_mode): ?>
                        <input type="date" name="tanggal_lahir" value="<?php echo htmlspecialchars($psikolog_profile["tanggal_lahir"]); ?>">
                    <?php else: ?>
                        <div class="profil-value"><?php echo htmlspecialchars(date("d-m-Y", strtotime($psikolog_profile["tanggal_lahir"]))); ?></div>
                    <?php endif; ?>

                    <label>Email</label>
                    <?php if ($profile_edit_mode): ?>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($psikolog_profile["email"]); ?>">
                    <?php else: ?>
                        <div class="profil-value"><?php echo htmlspecialchars($psikolog_profile["email"]); ?></div>
                    <?php endif; ?>

                    <label>Telepon</label>
                    <?php if ($profile_edit_mode): ?>
                        <input type="text" name="telepon" value="<?php echo htmlspecialchars($psikolog_profile["telepon"]); ?>">
                    <?php else: ?>
                        <div class="profil-value"><?php echo htmlspecialchars($psikolog_profile["telepon"]); ?></div>
                    <?php endif; ?>
                </div>
            </section>

            <section class="profil-card">
                <h4>Informasi Profesional</h4>
                <div class="profil-field-grid">
                    <label>Spesialisasi</label>
                    <?php if ($profile_edit_mode): ?>
                        <input type="text" name="spesialisasi" value="<?php echo htmlspecialchars($psikolog_profile["spesialisasi"]); ?>">
                    <?php else: ?>
                        <div class="profil-value"><?php echo htmlspecialchars($psikolog_profile["spesialisasi"]); ?></div>
                    <?php endif; ?>

                    <label>No STR</label>
                    <?php if ($profile_edit_mode): ?>
                        <input type="text" name="no_str" value="<?php echo htmlspecialchars($psikolog_profile["no_str"]); ?>">
                    <?php else: ?>
                        <div class="profil-value"><?php echo htmlspecialchars($psikolog_profile["no_str"]); ?></div>
                    <?php endif; ?>

                    <label>Pendidikan</label>
                    <?php if ($profile_edit_mode): ?>
                        <input type="text" name="pendidikan" value="<?php echo htmlspecialchars($psikolog_profile["pendidikan"]); ?>">
                    <?php else: ?>
                        <div class="profil-value"><?php echo htmlspecialchars($psikolog_profile["pendidikan"]); ?></div>
                    <?php endif; ?>

                    <label>Pengalaman</label>
                    <?php if ($profile_edit_mode): ?>
                        <input type="text" name="pengalaman" value="<?php echo htmlspecialchars($psikolog_profile["pengalaman"]); ?>">
                    <?php else: ?>
                        <div class="profil-value"><?php echo htmlspecialchars($psikolog_profile["pengalaman"]); ?></div>
                    <?php endif; ?>

                    <label>Metode Terapi</label>
                    <?php if ($profile_edit_mode): ?>
                        <input type="text" name="metode_terapi" value="<?php echo htmlspecialchars($psikolog_profile["metode_terapi"]); ?>">
                    <?php else: ?>
                        <div class="profil-value"><?php echo htmlspecialchars($psikolog_profile["metode_terapi"]); ?></div>
                    <?php endif; ?>
                </div>
            </section>
        </div>

        <div class="profil-grid">
            <section class="profil-card">
                <h4>Informasi Praktik</h4>
                <div class="profil-field-grid">
                    <label>Lokasi Praktik</label>
                    <?php if ($profile_edit_mode): ?>
                        <input type="text" name="lokasi_praktik" value="<?php echo htmlspecialchars($psikolog_profile["lokasi_praktik"]); ?>">
                    <?php else: ?>
                        <div class="profil-value"><?php echo htmlspecialchars($psikolog_profile["lokasi_praktik"]); ?></div>
                    <?php endif; ?>

                    <label>Hari Praktik</label>
                    <?php if ($profile_edit_mode): ?>
                        <input type="text" name="hari_praktik" value="<?php echo htmlspecialchars($psikolog_profile["hari_praktik"]); ?>">
                    <?php else: ?>
                        <div class="profil-value"><?php echo htmlspecialchars($psikolog_profile["hari_praktik"]); ?></div>
                    <?php endif; ?>

                    <label>Jam Praktik</label>
                    <?php if ($profile_edit_mode): ?>
                        <input type="text" name="jam_praktik" value="<?php echo htmlspecialchars($psikolog_profile["jam_praktik"]); ?>">
                    <?php else: ?>
                        <div class="profil-value"><?php echo htmlspecialchars($psikolog_profile["jam_praktik"]); ?></div>
                    <?php endif; ?>

                    <label>Durasi Sesi</label>
                    <?php if ($profile_edit_mode): ?>
                        <input type="text" name="durasi_sesi" value="<?php echo htmlspecialchars($psikolog_profile["durasi_sesi"]); ?>">
                    <?php else: ?>
                        <div class="profil-value"><?php echo htmlspecialchars($psikolog_profile["durasi_sesi"]); ?></div>
                    <?php endif; ?>
                </div>
            </section>

            <section class="profil-card">
                <h4>Layanan</h4>
                <div class="profil-field-grid">
                    <label>Jenis Layanan</label>
                    <?php if ($profile_edit_mode): ?>
                        <input type="text" name="layanan" value="<?php echo htmlspecialchars($psikolog_profile["layanan"]); ?>">
                    <?php else: ?>
                        <div class="profil-value"><?php echo htmlspecialchars($psikolog_profile["layanan"]); ?></div>
                    <?php endif; ?>
                </div>
            </section>
        </div>

        <div class="profil-form-actions">
            <?php if ($profile_edit_mode): ?>
                <button type="submit" class="profil-btn">Simpan Perubahan</button>
                <a class="profil-btn secondary" href="profile_psikolog.php">Batal</a>
            <?php else: ?>
                <a class="profil-btn" href="profile_psikolog.php?edit=1">Edit Profil</a>
            <?php endif; ?>
        </div>
    </form>
</div>
