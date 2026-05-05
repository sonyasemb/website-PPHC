<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

require_once "koneksi.php";

function has_column(mysqli $conn, string $table, string $column): bool
{
    static $cache = [];
    $key = $table . "." . $column;
    if (isset($cache[$key])) {
        return $cache[$key];
    }
    $safeTable = preg_replace('/[^a-zA-Z0-9_]/', '', $table);
    $safeColumn = preg_replace('/[^a-zA-Z0-9_]/', '', $column);
    $res = mysqli_query($conn, "SHOW COLUMNS FROM {$safeTable} LIKE '{$safeColumn}'");
    $cache[$key] = ($res && mysqli_num_rows($res) > 0);
    return $cache[$key];
}

function bind_params(mysqli_stmt $stmt, string $types, array &$params): void
{
    if ($types === "" || count($params) === 0) {
        return;
    }
    $refs = [];
    $refs[] = $stmt;
    $refs[] = &$types;
    foreach ($params as $k => $v) {
        $refs[] = &$params[$k];
    }
    call_user_func_array('mysqli_stmt_bind_param', $refs);
}

function human_time(?string $datetime): string
{
    if (!$datetime) {
        return "-";
    }
    $ts = strtotime($datetime);
    if ($ts === false) {
        return "-";
    }
    $diff = time() - $ts;
    if ($diff < 60) {
        return "baru saja";
    }
    if ($diff < 3600) {
        $m = (int)floor($diff / 60);
        return $m . " menit lalu";
    }
    if ($diff < 86400) {
        $h = (int)floor($diff / 3600);
        return $h . " jam lalu";
    }
    if ($diff < 2592000) {
        $d = (int)floor($diff / 86400);
        return $d . " hari lalu";
    }
    if ($diff < 31536000) {
        $mo = (int)floor($diff / 2592000);
        return $mo . " bulan lalu";
    }
    $y = (int)floor($diff / 31536000);
    return $y . " tahun lalu";
}

function format_date(?string $datetime): string
{
    if (!$datetime) {
        return "-";
    }
    $ts = strtotime($datetime);
    if ($ts === false) {
        return "-";
    }
    return date("d M Y", $ts);
}

$has_email = has_column($conn, "users", "email");
$has_status = has_column($conn, "users", "status");
$has_created = has_column($conn, "users", "created_at");
$has_last_login = has_column($conn, "users", "last_login_at");

$allowed_roles = ["admin", "psikolog", "user"];
$allowed_status = ["active", "inactive", "pending", "suspended", "banned"];
$alert_error = "";
$alert_success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = trim((string)($_POST["action"] ?? ""));

    if ($action === "add") {
        $nama = trim((string)($_POST["nama"] ?? ""));
        $username = trim((string)($_POST["username"] ?? ""));
        $email = trim((string)($_POST["email"] ?? ""));
        $role = strtolower(trim((string)($_POST["role"] ?? "user")));
        $status = strtolower(trim((string)($_POST["status"] ?? "active")));
        $password = (string)($_POST["password"] ?? "");

        if ($nama === "" || $username === "" || $password === "") {
            $alert_error = "Nama, username, dan password wajib diisi.";
        } elseif (!in_array($role, $allowed_roles, true)) {
            $alert_error = "Role tidak valid.";
        } elseif ($has_status && !in_array($status, $allowed_status, true)) {
            $alert_error = "Status tidak valid.";
        } else {
            $stmt = mysqli_prepare($conn, "SELECT id_user FROM users WHERE username=? LIMIT 1");
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "s", $username);
                mysqli_stmt_execute($stmt);
                $res = mysqli_stmt_get_result($stmt);
                $exists = $res ? mysqli_fetch_assoc($res) : null;
                mysqli_stmt_close($stmt);

                if ($exists) {
                    $alert_error = "Username sudah digunakan.";
                }
            }

            if ($alert_error === "") {
                $fields = ["nama", "username", "password", "role"];
                $marks = ["?", "?", "?", "?"];
                $types = "ssss";
                $params = [$nama, $username, password_hash($password, PASSWORD_DEFAULT), $role];

                if ($has_email) {
                    $fields[] = "email";
                    $marks[] = "?";
                    $types .= "s";
                    $params[] = $email;
                }
                if ($has_status) {
                    $fields[] = "status";
                    $marks[] = "?";
                    $types .= "s";
                    $params[] = $status;
                }

                $sql = "INSERT INTO users (" . implode(", ", $fields) . ") VALUES (" . implode(", ", $marks) . ")";
                $stmt = mysqli_prepare($conn, $sql);
                if ($stmt) {
                    bind_params($stmt, $types, $params);
                    mysqli_stmt_execute($stmt);
                    if (mysqli_stmt_errno($stmt) === 0) {
                        $alert_success = "User berhasil ditambahkan.";
                    } else {
                        $alert_error = "Gagal menambahkan user.";
                    }
                    mysqli_stmt_close($stmt);
                } else {
                    $alert_error = "Gagal menyiapkan query tambah user.";
                }
            }
        }
    } elseif ($action === "edit") {
        $id = (int)($_POST["id_user"] ?? 0);
        $nama = trim((string)($_POST["nama"] ?? ""));
        $username = trim((string)($_POST["username"] ?? ""));
        $email = trim((string)($_POST["email"] ?? ""));
        $role = strtolower(trim((string)($_POST["role"] ?? "user")));
        $status = strtolower(trim((string)($_POST["status"] ?? "active")));
        $password = (string)($_POST["password"] ?? "");

        if ($id <= 0 || $nama === "" || $username === "") {
            $alert_error = "Data edit tidak lengkap.";
        } elseif (!in_array($role, $allowed_roles, true)) {
            $alert_error = "Role tidak valid.";
        } elseif ($has_status && !in_array($status, $allowed_status, true)) {
            $alert_error = "Status tidak valid.";
        } else {
            $stmt = mysqli_prepare($conn, "SELECT id_user FROM users WHERE username=? AND id_user<>? LIMIT 1");
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "si", $username, $id);
                mysqli_stmt_execute($stmt);
                $res = mysqli_stmt_get_result($stmt);
                $exists = $res ? mysqli_fetch_assoc($res) : null;
                mysqli_stmt_close($stmt);
                if ($exists) {
                    $alert_error = "Username sudah digunakan user lain.";
                }
            }

            if ($alert_error === "") {
                $set = ["nama=?", "username=?", "role=?"];
                $types = "sss";
                $params = [$nama, $username, $role];

                if ($has_email) {
                    $set[] = "email=?";
                    $types .= "s";
                    $params[] = $email;
                }
                if ($has_status) {
                    $set[] = "status=?";
                    $types .= "s";
                    $params[] = $status;
                }
                if ($password !== "") {
                    $set[] = "password=?";
                    $types .= "s";
                    $params[] = password_hash($password, PASSWORD_DEFAULT);
                }

                $types .= "i";
                $params[] = $id;
                $sql = "UPDATE users SET " . implode(", ", $set) . " WHERE id_user=?";
                $stmt = mysqli_prepare($conn, $sql);
                if ($stmt) {
                    bind_params($stmt, $types, $params);
                    mysqli_stmt_execute($stmt);
                    if (mysqli_stmt_errno($stmt) === 0) {
                        $alert_success = "Data user berhasil diperbarui.";
                    } else {
                        $alert_error = "Gagal memperbarui user.";
                    }
                    mysqli_stmt_close($stmt);
                } else {
                    $alert_error = "Gagal menyiapkan query edit user.";
                }
            }
        }
    } elseif ($action === "delete") {
        $id = (int)($_POST["id_user"] ?? 0);
        $selfId = (int)($_SESSION["id_user"] ?? 0);
        if ($id <= 0) {
            $alert_error = "ID user tidak valid.";
        } elseif ($id === $selfId) {
            $alert_error = "Akun admin yang sedang login tidak bisa dihapus.";
        } else {
            $stmt = mysqli_prepare($conn, "DELETE FROM users WHERE id_user=?");
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "i", $id);
                mysqli_stmt_execute($stmt);
                if (mysqli_stmt_errno($stmt) === 0) {
                    $alert_success = "User berhasil dihapus.";
                } else {
                    $alert_error = "Gagal menghapus user.";
                }
                mysqli_stmt_close($stmt);
            } else {
                $alert_error = "Gagal menyiapkan query hapus user.";
            }
        }
    }
}

$roleFilter = strtolower(trim((string)($_GET["role"] ?? "")));
$statusFilter = strtolower(trim((string)($_GET["status"] ?? "")));
$dateFilter = trim((string)($_GET["date"] ?? ""));
$where = " FROM users WHERE 1=1";
$types = "";
$params = [];

if ($roleFilter !== "" && in_array($roleFilter, $allowed_roles, true)) {
    $where .= " AND LOWER(role)=?";
    $types .= "s";
    $params[] = $roleFilter;
}

if ($has_status && $statusFilter !== "" && in_array($statusFilter, $allowed_status, true)) {
    $where .= " AND LOWER(status)=?";
    $types .= "s";
    $params[] = $statusFilter;
}

if ($has_created && $dateFilter !== "") {
    $where .= " AND DATE(created_at)=?";
    $types .= "s";
    $params[] = $dateFilter;
}

$selectFields = "id_user, nama, username, role";
if ($has_email) {
    $selectFields .= ", email";
}
if ($has_status) {
    $selectFields .= ", status";
}
if ($has_created) {
    $selectFields .= ", created_at";
}
if ($has_last_login) {
    $selectFields .= ", last_login_at";
}

$dataSql = "SELECT {$selectFields}" . $where . " ORDER BY id_user DESC";
$stmt = mysqli_prepare($conn, $dataSql);
$users = [];
if ($stmt) {
    $dataParams = $params;
    bind_params($stmt, $types, $dataParams);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $users[] = $row;
        }
    }
    mysqli_stmt_close($stmt);
}

if (isset($_GET["export"]) && $_GET["export"] === "1") {
    header("Content-Type: text/csv; charset=UTF-8");
    header("Content-Disposition: attachment; filename=manajemen_staff.csv");
    $out = fopen("php://output", "w");
    fputcsv($out, ["Nama", "Email", "Username", "Status", "Role", "Tanggal Gabung", "Terakhir Aktif"]);

    $exportSql = "SELECT {$selectFields}" . $where . " ORDER BY id_user DESC";
    $stmtExport = mysqli_prepare($conn, $exportSql);
    if ($stmtExport) {
        $exportParams = $params;
        bind_params($stmtExport, $types, $exportParams);
        mysqli_stmt_execute($stmtExport);
        $resExport = mysqli_stmt_get_result($stmtExport);
        if ($resExport) {
            while ($row = mysqli_fetch_assoc($resExport)) {
                $st = $has_status ? ((string)($row["status"] ?? "active")) : "active";
                fputcsv($out, [
                    (string)($row["nama"] ?? ""),
                    (string)($row["email"] ?? "-"),
                    (string)($row["username"] ?? ""),
                    ucfirst($st),
                    ucfirst((string)($row["role"] ?? "user")),
                    format_date($row["created_at"] ?? null),
                    human_time($row["last_login_at"] ?? null)
                ]);
            }
        }
        mysqli_stmt_close($stmtExport);
    }
    fclose($out);
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Staff</title>
    <link rel="stylesheet" href="assets/css/admincss.css?v=<?php echo filemtime(__DIR__ . '/assets/css/admincss.css'); ?>">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/manajemen_staff.css">
</head>
<body>
    <?php
        require "admin_sidebar.php";
    ?>

    <div class="content">
        <div class="header">
            <h1>User Management</h1>
        </div>

        <?php if ($alert_error !== ""): ?>
            <div class="staff-alert error"><?php echo htmlspecialchars($alert_error); ?></div>
        <?php endif; ?>
        <?php if ($alert_success !== ""): ?>
            <div class="staff-alert success"><?php echo htmlspecialchars($alert_success); ?></div>
        <?php endif; ?>

        <?php if (!$has_email || !$has_status || !$has_created || !$has_last_login): ?>
            <div class="staff-alert warning">
                Beberapa kolom `users` belum lengkap. Jalankan file SQL `sql_archive/add_staff_columns.sql` agar fitur email, status, tanggal gabung, dan last active aktif penuh.
            </div>
        <?php endif; ?>

        <div class="filter-box">
            <h2>Filter Staff</h2>
            <form class="filter-grid" method="GET" action="manajemen_staff.php">
                <div class="filter-item">
                    <label>Role</label>
                    <select name="role">
                        <option value="">Semua</option>
                        <option value="admin" <?php echo $roleFilter === "admin" ? "selected" : ""; ?>>Admin</option>
                        <option value="psikolog" <?php echo $roleFilter === "psikolog" ? "selected" : ""; ?>>Psikolog</option>
                        <option value="user" <?php echo $roleFilter === "user" ? "selected" : ""; ?>>User</option>
                    </select>
                </div>
                <div class="filter-item">
                    <label>Status</label>
                    <select name="status" <?php echo $has_status ? "" : "disabled"; ?>>
                        <option value="">Semua</option>
                        <?php foreach ($allowed_status as $s): ?>
                            <option value="<?php echo $s; ?>" <?php echo $statusFilter === $s ? "selected" : ""; ?>><?php echo ucfirst($s); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filter-item">
                    <label>Tanggal Join</label>
                    <input type="date" name="date" value="<?php echo htmlspecialchars($dateFilter); ?>" <?php echo $has_created ? "" : "disabled"; ?>>
                </div>
                <div class="filter-item">
                    <label>&nbsp;</label>
                    <button type="submit">Filter</button>
                </div>
                <div class="filter-item">
                    <label>&nbsp;</label>
                    <button type="button" onclick="openAddModal()">+ Tambah User</button>
                </div>
                <div class="filter-item">
                    <label>&nbsp;</label>
                    <button type="submit" name="export" value="1" style="background:#6c757d;">Export</button>
                </div>
            </form>
        </div>

        <div class="table-card">
            <table class="table-reservasi">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Username</th>
                        <th>Status</th>
                        <th>Role</th>
                        <th>Tanggal Gabung</th>
                        <th>Terakhir Aktif</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($users) === 0): ?>
                        <tr><td colspan="8">Belum ada data user.</td></tr>
                    <?php else: ?>
                        <?php foreach ($users as $row): ?>
                            <?php
                                $role = strtolower((string)($row["role"] ?? "user"));
                                $status = $has_status ? strtolower((string)($row["status"] ?? "active")) : "active";
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars((string)($row["nama"] ?? "-")); ?></td>
                                <td><?php echo htmlspecialchars((string)($row["email"] ?? "-")); ?></td>
                                <td><?php echo htmlspecialchars((string)($row["username"] ?? "-")); ?></td>
                                <td><?php echo htmlspecialchars(ucfirst($status)); ?></td>
                                <td><?php echo htmlspecialchars(ucfirst($role)); ?></td>
                                <td><?php echo htmlspecialchars(format_date($row["created_at"] ?? null)); ?></td>
                                <td><?php echo htmlspecialchars(human_time($row["last_login_at"] ?? null)); ?></td>
                                <td>
                                    <div class="table-actions">
                                        <button
                                            type="button"
                                            class="btn-small"
                                            data-id="<?php echo (int)$row["id_user"]; ?>"
                                            data-nama="<?php echo htmlspecialchars((string)($row["nama"] ?? ""), ENT_QUOTES); ?>"
                                            data-email="<?php echo htmlspecialchars((string)($row["email"] ?? ""), ENT_QUOTES); ?>"
                                            data-username="<?php echo htmlspecialchars((string)($row["username"] ?? ""), ENT_QUOTES); ?>"
                                            data-role="<?php echo htmlspecialchars($role, ENT_QUOTES); ?>"
                                            data-status="<?php echo htmlspecialchars($status, ENT_QUOTES); ?>"
                                            onclick="openEditModal(this)"
                                        >Edit</button>
                                        <form method="POST" action="manajemen_staff.php" onsubmit="return confirm('Hapus user ini?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id_user" value="<?php echo (int)$row["id_user"]; ?>">
                                            <button type="submit" class="btn-small" style="background:#e53935;">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal" id="addModal">
        <div class="modal-card">
            <h3>Tambah User</h3>
            <form method="POST" action="manajemen_staff.php">
                <input type="hidden" name="action" value="add">
                <label>Nama</label>
                <input type="text" name="nama" required>
                <label>Email</label>
                <input type="email" name="email" <?php echo $has_email ? "" : "disabled"; ?>>
                <label>Username</label>
                <input type="text" name="username" required>
                <label>Password</label>
                <input type="password" name="password" required minlength="6">
                <label>Role</label>
                <select name="role" required>
                    <option value="user">User</option>
                    <option value="psikolog">Psikolog</option>
                    <option value="admin">Admin</option>
                </select>
                <label>Status</label>
                <select name="status" <?php echo $has_status ? "" : "disabled"; ?>>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="pending">Pending</option>
                    <option value="suspended">Suspended</option>
                    <option value="banned">Banned</option>
                </select>
                <div class="modal-actions">
                    <button type="button" class="ghost" onclick="closeModal('addModal')">Batal</button>
                    <button type="submit" class="primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal" id="editModal">
        <div class="modal-card">
            <h3>Edit User</h3>
            <form method="POST" action="manajemen_staff.php">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id_user" id="edit_id_user">
                <label>Nama</label>
                <input type="text" name="nama" id="edit_nama" required>
                <label>Email</label>
                <input type="email" name="email" id="edit_email" <?php echo $has_email ? "" : "disabled"; ?>>
                <label>Username</label>
                <input type="text" name="username" id="edit_username" required>
                <label>Password Baru (opsional)</label>
                <input type="password" name="password" minlength="6">
                <label>Role</label>
                <select name="role" id="edit_role" required>
                    <option value="user">User</option>
                    <option value="psikolog">Psikolog</option>
                    <option value="admin">Admin</option>
                </select>
                <label>Status</label>
                <select name="status" id="edit_status" <?php echo $has_status ? "" : "disabled"; ?>>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="pending">Pending</option>
                    <option value="suspended">Suspended</option>
                    <option value="banned">Banned</option>
                </select>
                <div class="modal-actions">
                    <button type="button" class="ghost" onclick="closeModal('editModal')">Batal</button>
                    <button type="submit" class="primary">Update</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openAddModal() {
            document.getElementById("addModal").classList.add("show");
        }
        function closeModal(id) {
            document.getElementById(id).classList.remove("show");
        }
        function openEditModal(btn) {
            document.getElementById("edit_id_user").value = btn.dataset.id || "";
            document.getElementById("edit_nama").value = btn.dataset.nama || "";
            document.getElementById("edit_email").value = btn.dataset.email || "";
            document.getElementById("edit_username").value = btn.dataset.username || "";
            document.getElementById("edit_role").value = btn.dataset.role || "user";
            document.getElementById("edit_status").value = btn.dataset.status || "active";
            document.getElementById("editModal").classList.add("show");
        }
        const filterForm = document.querySelector(".filter-grid");
        if (filterForm) {
            filterForm.querySelectorAll("select, input[type='date']").forEach(function (el) {
                if (!el.disabled) {
                    el.addEventListener("change", function () {
                        filterForm.submit();
                    });
                }
            });
        }
        window.addEventListener("click", function (event) {
            if (event.target.classList.contains("modal")) {
                event.target.classList.remove("show");
            }
        });
    </script>
</body>
</html>
