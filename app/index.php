<?php
require_once 'db.php';

$thong_bao = '';

/* =========================
   XỬ LÝ THÊM / SỬA / XÓA
   ========================= */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = $_POST['action'] ?? '';

    // THÊM LỚP
    if ($action === 'them_lop') {
        $ten_lop = trim($_POST['ten_lop'] ?? '');

        if ($ten_lop !== '') {
            $stmt = $conn->prepare("INSERT INTO classes (class_name) VALUES (?)");
            $stmt->bind_param("s", $ten_lop);
            $stmt->execute();
            $stmt->close();

            header("Location: index.php?page=lop");
            exit;
        }
    }

    // SỬA LỚP
    if ($action === 'sua_lop') {
        $id = (int)($_POST['id'] ?? 0);
        $ten_lop = trim($_POST['ten_lop'] ?? '');

        if ($id > 0 && $ten_lop !== '') {
            $stmt = $conn->prepare(
                "UPDATE classes SET class_name = ? WHERE id = ?"
            );
            $stmt->bind_param("si", $ten_lop, $id);
            $stmt->execute();
            $stmt->close();

            header("Location: index.php?page=lop");
            exit;
        }
    }

    // XÓA LỚP
    if ($action === 'xoa_lop') {
        $id = (int)($_POST['id'] ?? 0);

        if ($id > 0) {
            $stmt = $conn->prepare("DELETE FROM classes WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();

            header("Location: index.php?page=lop");
            exit;
        }
    }

    // THÊM SINH VIÊN
    if ($action === 'them_sv') {
        $ma_sv = trim($_POST['ma_sv'] ?? '');
        $ho_ten = trim($_POST['ho_ten'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $sdt = trim($_POST['sdt'] ?? '');
        $class_id = (int)($_POST['class_id'] ?? 0);

        if ($ma_sv !== '' && $ho_ten !== '') {
            $stmt = $conn->prepare(
                "INSERT INTO students
                (student_code, full_name, email, phone, class_id)
                VALUES (?, ?, ?, ?, ?)"
            );

            $stmt->bind_param(
                "ssssi",
                $ma_sv,
                $ho_ten,
                $email,
                $sdt,
                $class_id
            );

            $stmt->execute();
            $stmt->close();

            header("Location: index.php?page=sinhvien");
            exit;
        }
    }

    // SỬA SINH VIÊN
    if ($action === 'sua_sv') {
        $id = (int)($_POST['id'] ?? 0);
        $ma_sv = trim($_POST['ma_sv'] ?? '');
        $ho_ten = trim($_POST['ho_ten'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $sdt = trim($_POST['sdt'] ?? '');
        $class_id = (int)($_POST['class_id'] ?? 0);

        if ($id > 0 && $ma_sv !== '' && $ho_ten !== '') {
            $stmt = $conn->prepare(
                "UPDATE students
                 SET student_code = ?,
                     full_name = ?,
                     email = ?,
                     phone = ?,
                     class_id = ?
                 WHERE id = ?"
            );

            $stmt->bind_param(
                "ssssii",
                $ma_sv,
                $ho_ten,
                $email,
                $sdt,
                $class_id,
                $id
            );

            $stmt->execute();
            $stmt->close();

            header("Location: index.php?page=sinhvien");
            exit;
        }
    }

    // XÓA SINH VIÊN
    if ($action === 'xoa_sv') {
        $id = (int)($_POST['id'] ?? 0);

        if ($id > 0) {
            $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();

            header("Location: index.php?page=sinhvien");
            exit;
        }
    }

    // THÊM ĐIỂM
    if ($action === 'them_diem') {
        $student_id = (int)($_POST['student_id'] ?? 0);
        $mon_hoc = trim($_POST['mon_hoc'] ?? '');
        $diem = (float)($_POST['diem'] ?? 0);

        if ($student_id > 0 && $mon_hoc !== '' && $diem >= 0 && $diem <= 10) {
            $stmt = $conn->prepare(
                "INSERT INTO scores (student_id, subject, score)
                 VALUES (?, ?, ?)"
            );

            $stmt->bind_param(
                "isd",
                $student_id,
                $mon_hoc,
                $diem
            );

            $stmt->execute();
            $stmt->close();

            header("Location: index.php?page=diem");
            exit;
        }
    }

    // SỬA ĐIỂM
    if ($action === 'sua_diem') {
        $id = (int)($_POST['id'] ?? 0);
        $mon_hoc = trim($_POST['mon_hoc'] ?? '');
        $diem = (float)($_POST['diem'] ?? 0);

        if ($id > 0 && $mon_hoc !== '' && $diem >= 0 && $diem <= 10) {
            $stmt = $conn->prepare(
                "UPDATE scores
                 SET subject = ?, score = ?
                 WHERE id = ?"
            );

            $stmt->bind_param(
                "sdi",
                $mon_hoc,
                $diem,
                $id
            );

            $stmt->execute();
            $stmt->close();

            header("Location: index.php?page=diem");
            exit;
        }
    }

    // XÓA ĐIỂM
    if ($action === 'xoa_diem') {
        $id = (int)($_POST['id'] ?? 0);

        if ($id > 0) {
            $stmt = $conn->prepare("DELETE FROM scores WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();

            header("Location: index.php?page=diem");
            exit;
        }
    }
}


/* =========================
   THỐNG KÊ
   ========================= */

$so_lop = $conn->query("SELECT COUNT(*) AS total FROM classes")
              ->fetch_assoc()['total'];

$so_sv = $conn->query("SELECT COUNT(*) AS total FROM students")
             ->fetch_assoc()['total'];

$so_diem = $conn->query("SELECT COUNT(*) AS total FROM scores")
               ->fetch_assoc()['total'];


/* =========================
   TRANG HIỆN TẠI
   ========================= */

$page = $_GET['page'] ?? 'dashboard';


/* =========================
   DỮ LIỆU
   ========================= */

$classes = $conn->query(
    "SELECT * FROM classes ORDER BY id DESC"
);

$students = $conn->query(
    "SELECT
        students.*,
        classes.class_name
     FROM students
     LEFT JOIN classes
        ON students.class_id = classes.id
     ORDER BY students.id DESC"
);

$students_for_grade = $conn->query(
    "SELECT id, student_code, full_name
     FROM students
     ORDER BY full_name"
);

$scores = $conn->query(
    "SELECT
        scores.id,
        scores.subject,
        scores.score,
        students.student_code,
        students.full_name
     FROM scores
     INNER JOIN students
        ON scores.student_id = students.id
     ORDER BY scores.id DESC"
);


/* =========================
   HỖ TRỢ HTML
   ========================= */

function e($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

?>

<!DOCTYPE html>
<html lang="vi">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>QLHTSV - Quản lý học tập sinh viên</title>

<style>

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: Arial, sans-serif;
    background: #f3f6fb;
    color: #333;
}

.header {
    height: 64px;
    background: #1769e0;
    color: white;
    display: flex;
    align-items: center;
    padding: 0 28px;
    font-size: 21px;
    font-weight: bold;
}

.logo {
    margin-right: 45px;
}

.container {
    display: flex;
    min-height: calc(100vh - 64px);
}

.sidebar {
    width: 230px;
    background: white;
    border-right: 1px solid #ddd;
    padding-top: 20px;
}

.sidebar a {
    display: block;
    text-decoration: none;
    color: #444;
    padding: 15px 25px;
    font-size: 15px;
}

.sidebar a:hover,
.sidebar a.active {
    background: #e8f1ff;
    color: #1769e0;
    font-weight: bold;
}

.content {
    flex: 1;
    padding: 30px;
}

h1 {
    margin-top: 0;
}

.card-container {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    margin: 25px 0;
}

.card {
    background: white;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,.08);
}

.card-title {
    color: #777;
    font-size: 15px;
}

.card-number {
    color: #1769e0;
    font-size: 32px;
    font-weight: bold;
    margin-top: 10px;
}

.panel {
    background: white;
    border-radius: 10px;
    padding: 22px;
    margin-bottom: 25px;
    box-shadow: 0 2px 8px rgba(0,0,0,.08);
}

.panel-title {
    font-size: 19px;
    font-weight: bold;
    margin-bottom: 20px;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 15px;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-group label {
    margin-bottom: 7px;
    font-weight: bold;
}

input,
select {
    padding: 11px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 14px;
}

button,
.btn {
    border: none;
    border-radius: 6px;
    padding: 10px 15px;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
    font-size: 14px;
}

.btn-primary {
    background: #1769e0;
    color: white;
}

.btn-warning {
    background: #f0ad4e;
    color: white;
}

.btn-danger {
    background: #dc3545;
    color: white;
}

.btn-secondary {
    background: #777;
    color: white;
}

table {
    width: 100%;
    border-collapse: collapse;
}

th {
    background: #f0f4f8;
    font-weight: bold;
}

th,
td {
    border: 1px solid #ddd;
    padding: 12px;
    text-align: left;
}

tr:hover {
    background: #f8fbff;
}

.actions {
    display: flex;
    gap: 6px;
}

.inline-form {
    display: inline;
}

.search-box {
    margin-bottom: 20px;
}

.search-box input {
    width: 300px;
}

.badge {
    padding: 5px 9px;
    border-radius: 15px;
    background: #e8f1ff;
    color: #1769e0;
}

@media(max-width: 800px) {

    .sidebar {
        width: 180px;
    }

    .card-container,
    .form-grid {
        grid-template-columns: 1fr;
    }

    .content {
        padding: 15px;
    }
}

</style>

</head>

<body>

<div class="header">

    <div class="logo">
        🎓 QUẢN LÝ HỌC TẬP SINH VIÊN
    </div>

</div>

<div class="container">

    <!-- SIDEBAR -->

    <div class="sidebar">

        <a href="index.php?page=dashboard"
           class="<?= $page === 'dashboard' ? 'active' : '' ?>">
            🏠 Tổng quan
        </a>

        <a href="index.php?page=lop"
           class="<?= $page === 'lop' ? 'active' : '' ?>">
            🏫 Quản lý lớp
        </a>

        <a href="index.php?page=sinhvien"
           class="<?= $page === 'sinhvien' ? 'active' : '' ?>">
            👨‍🎓 Quản lý sinh viên
        </a>

        <a href="index.php?page=diem"
           class="<?= $page === 'diem' ? 'active' : '' ?>">
            📊 Quản lý điểm số
        </a>

    </div>


    <!-- CONTENT -->

    <div class="content">


<?php if ($page === 'dashboard'): ?>

    <h1>Tổng quan hệ thống</h1>

    <div class="card-container">

        <div class="card">
            <div class="card-title">
                Tổng số sinh viên
            </div>

            <div class="card-number">
                <?= $so_sv ?>
            </div>
        </div>


        <div class="card">
            <div class="card-title">
                Tổng số lớp
            </div>

            <div class="card-number">
                <?= $so_lop ?>
            </div>
        </div>


        <div class="card">
            <div class="card-title">
                Tổng số điểm
            </div>

            <div class="card-number">
                <?= $so_diem ?>
            </div>
        </div>

    </div>


    <div class="panel">

        <div class="panel-title">
            Chức năng hệ thống
        </div>

        <p>
            Hệ thống hỗ trợ quản lý lớp, sinh viên và điểm số.
        </p>

        <p>
            <a class="btn btn-primary"
               href="index.php?page=lop">
                Quản lý lớp
            </a>

            <a class="btn btn-primary"
               href="index.php?page=sinhvien">
                Quản lý sinh viên
            </a>

            <a class="btn btn-primary"
               href="index.php?page=diem">
                Quản lý điểm
            </a>
        </p>

    </div>


<?php elseif ($page === 'lop'): ?>

    <h1>Quản lý lớp</h1>


    <!-- THÊM LỚP -->

    <div class="panel">

        <div class="panel-title">
            ➕ Thêm lớp
        </div>

        <form method="POST">

            <input type="hidden"
                   name="action"
                   value="them_lop">

            <div class="form-grid">

                <div class="form-group">

                    <label>Tên lớp</label>

                    <input type="text"
                           name="ten_lop"
                           placeholder="VD: CNTT K23G"
                           required>

                </div>

            </div>

            <br>

            <button class="btn btn-primary">
                Thêm lớp
            </button>

        </form>

    </div>


    <!-- DANH SÁCH LỚP -->

    <div class="panel">

        <div class="panel-title">
            📋 Danh sách lớp
        </div>

        <table>

            <tr>
                <th>ID</th>
                <th>Tên lớp</th>
                <th>Thao tác</th>
            </tr>

            <?php while ($lop = $classes->fetch_assoc()): ?>

            <tr>

                <td><?= $lop['id'] ?></td>

                <td>
                    <span class="badge">
                        <?= e($lop['class_name']) ?>
                    </span>
                </td>

                <td>

                    <div class="actions">

                        <a class="btn btn-warning"
                           href="index.php?page=sua_lop&id=<?= $lop['id'] ?>">
                            Sửa
                        </a>

                        <form method="POST"
                              class="inline-form">

                            <input type="hidden"
                                   name="action"
                                   value="xoa_lop">

                            <input type="hidden"
                                   name="id"
                                   value="<?= $lop['id'] ?>">

                            <button class="btn btn-danger"
                                    onclick="return confirm('Bạn có chắc muốn xóa lớp này?')">
                                Xóa
                            </button>

                        </form>

                    </div>

                </td>

            </tr>

            <?php endwhile; ?>

        </table>

    </div>


<?php elseif ($page === 'sua_lop'): ?>

    <?php

    $id = (int)($_GET['id'] ?? 0);

    $stmt = $conn->prepare(
        "SELECT * FROM classes WHERE id = ?"
    );

    $stmt->bind_param("i", $id);
    $stmt->execute();

    $lop_edit = $stmt->get_result()->fetch_assoc();

    $stmt->close();

    ?>

    <h1>Sửa thông tin lớp</h1>

    <div class="panel">

        <form method="POST">

            <input type="hidden"
                   name="action"
                   value="sua_lop">

            <input type="hidden"
                   name="id"
                   value="<?= $lop_edit['id'] ?>">

            <div class="form-group">

                <label>Tên lớp</label>

                <input type="text"
                       name="ten_lop"
                       value="<?= e($lop_edit['class_name']) ?>"
                       required>

            </div>

            <br>

            <button class="btn btn-primary">
                Lưu thay đổi
            </button>

            <a class="btn btn-secondary"
               href="index.php?page=lop">
                Hủy
            </a>

        </form>

    </div>


<?php elseif ($page === 'sinhvien'): ?>

    <h1>Quản lý sinh viên</h1>


    <!-- THÊM SINH VIÊN -->

    <div class="panel">

        <div class="panel-title">
            ➕ Thêm sinh viên
        </div>

        <form method="POST">

            <input type="hidden"
                   name="action"
                   value="them_sv">

            <div class="form-grid">

                <div class="form-group">

                    <label>Mã sinh viên</label>

                    <input type="text"
                           name="ma_sv"
                           placeholder="VD: SV004"
                           required>

                </div>


                <div class="form-group">

                    <label>Họ và tên</label>

                    <input type="text"
                           name="ho_ten"
                           placeholder="Nguyễn Văn A"
                           required>

                </div>


                <div class="form-group">

                    <label>Email</label>

                    <input type="email"
                           name="email"
                           placeholder="email@gmail.com">

                </div>


                <div class="form-group">

                    <label>Số điện thoại</label>

                    <input type="text"
                           name="sdt"
                           placeholder="09xxxxxxxx">

                </div>


                <div class="form-group">

                    <label>Lớp</label>

                    <select name="class_id">

                        <option value="0">
                            -- Chọn lớp --
                        </option>

                        <?php

                        $ds_lop_form = $conn->query(
                            "SELECT * FROM classes ORDER BY class_name"
                        );

                        while ($l = $ds_lop_form->fetch_assoc()):

                        ?>

                        <option value="<?= $l['id'] ?>">
                            <?= e($l['class_name']) ?>
                        </option>

                        <?php endwhile; ?>

                    </select>

                </div>

            </div>

            <br>

            <button class="btn btn-primary">
                Thêm sinh viên
            </button>

        </form>

    </div>


    <!-- DANH SÁCH SINH VIÊN -->

    <div class="panel">

        <div class="panel-title">
            📋 Danh sách sinh viên
        </div>

        <div class="search-box">

            <input type="text"
                   id="timKiem"
                   placeholder="Tìm mã SV hoặc họ tên..."
                   onkeyup="timSinhVien()">

        </div>


        <table id="bangSinhVien">

            <tr>

                <th>ID</th>
                <th>Mã SV</th>
                <th>Họ và tên</th>
                <th>Email</th>
                <th>Số điện thoại</th>
                <th>Lớp</th>
                <th>Thao tác</th>

            </tr>

            <?php while ($sv = $students->fetch_assoc()): ?>

            <tr>

                <td><?= $sv['id'] ?></td>

                <td><?= e($sv['student_code']) ?></td>

                <td><?= e($sv['full_name']) ?></td>

                <td><?= e($sv['email']) ?></td>

                <td><?= e($sv['phone']) ?></td>

                <td><?= e($sv['class_name'] ?? '') ?></td>

                <td>

                    <div class="actions">

                        <a class="btn btn-warning"
                           href="index.php?page=sua_sv&id=<?= $sv['id'] ?>">
                            Sửa
                        </a>

                        <form method="POST"
                              class="inline-form">

                            <input type="hidden"
                                   name="action"
                                   value="xoa_sv">

                            <input type="hidden"
                                   name="id"
                                   value="<?= $sv['id'] ?>">

                            <button class="btn btn-danger"
                                    onclick="return confirm('Bạn có chắc muốn xóa sinh viên này?')">
                                Xóa
                            </button>

                        </form>

                    </div>

                </td>

            </tr>

            <?php endwhile; ?>

        </table>

    </div>


<?php elseif ($page === 'sua_sv'): ?>

    <?php

    $id = (int)($_GET['id'] ?? 0);

    $stmt = $conn->prepare(
        "SELECT * FROM students WHERE id = ?"
    );

    $stmt->bind_param("i", $id);
    $stmt->execute();

    $sv_edit = $stmt->get_result()->fetch_assoc();

    $stmt->close();

    ?>

    <h1>Sửa thông tin sinh viên</h1>

    <div class="panel">

        <form method="POST">

            <input type="hidden"
                   name="action"
                   value="sua_sv">

            <input type="hidden"
                   name="id"
                   value="<?= $sv_edit['id'] ?>">

            <div class="form-grid">

                <div class="form-group">

                    <label>Mã sinh viên</label>

                    <input type="text"
                           name="ma_sv"
                           value="<?= e($sv_edit['student_code']) ?>"
                           required>

                </div>


                <div class="form-group">

                    <label>Họ và tên</label>

                    <input type="text"
                           name="ho_ten"
                           value="<?= e($sv_edit['full_name']) ?>"
                           required>

                </div>


                <div class="form-group">

                    <label>Email</label>

                    <input type="email"
                           name="email"
                           value="<?= e($sv_edit['email']) ?>">

                </div>


                <div class="form-group">

                    <label>Số điện thoại</label>

                    <input type="text"
                           name="sdt"
                           value="<?= e($sv_edit['phone']) ?>">

                </div>


                <div class="form-group">

                    <label>Lớp</label>

                    <select name="class_id">

                        <?php

                        $ds_lop_edit = $conn->query(
                            "SELECT * FROM classes ORDER BY class_name"
                        );

                        while ($l = $ds_lop_edit->fetch_assoc()):

                        ?>

                        <option value="<?= $l['id'] ?>"
                            <?= $l['id'] == $sv_edit['class_id'] ? 'selected' : '' ?>>

                            <?= e($l['class_name']) ?>

                        </option>

                        <?php endwhile; ?>

                    </select>

                </div>

            </div>

            <br>

            <button class="btn btn-primary">
                Lưu thay đổi
            </button>

            <a class="btn btn-secondary"
               href="index.php?page=sinhvien">
                Hủy
            </a>

        </form>

    </div>


<?php elseif ($page === 'diem'): ?>

    <h1>Quản lý điểm số</h1>


    <!-- THÊM ĐIỂM -->

    <div class="panel">

        <div class="panel-title">
            ➕ Nhập điểm
        </div>

        <form method="POST">

            <input type="hidden"
                   name="action"
                   value="them_diem">

            <div class="form-grid">

                <div class="form-group">

                    <label>Chọn sinh viên</label>

                    <select name="student_id" required>

                        <option value="">
                            -- Chọn sinh viên --
                        </option>

                        <?php while ($svg = $students_for_grade->fetch_assoc()): ?>

                        <option value="<?= $svg['id'] ?>">

                            <?= e($svg['student_code']) ?>
                            -
                            <?= e($svg['full_name']) ?>

                        </option>

                        <?php endwhile; ?>

                    </select>

                </div>


                <div class="form-group">

                    <label>Tên môn học</label>

                    <input type="text"
                           name="mon_hoc"
                           placeholder="VD: Lập trình Web"
                           required>

                </div>


                <div class="form-group">

                    <label>Điểm số</label>

                    <input type="number"
                           name="diem"
                           min="0"
                           max="10"
                           step="0.1"
                           placeholder="VD: 8.5"
                           required>

                </div>

            </div>

            <br>

            <button class="btn btn-primary">
                Lưu điểm
            </button>

        </form>

    </div>


    <!-- BẢNG ĐIỂM -->

    <div class="panel">

        <div class="panel-title">
            📋 Bảng điểm tổng hợp
        </div>

        <table>

            <tr>

                <th>Mã SV</th>
                <th>Họ và tên</th>
                <th>Môn học</th>
                <th>Điểm số</th>
                <th>Thao tác</th>

            </tr>

            <?php while ($diem = $scores->fetch_assoc()): ?>

            <tr>

                <td>
                    <?= e($diem['student_code']) ?>
                </td>

                <td>
                    <?= e($diem['full_name']) ?>
                </td>

                <td>
                    <?= e($diem['subject']) ?>
                </td>

                <td>
                    <strong>
                        <?= e($diem['score']) ?>
                    </strong>
                </td>

                <td>

                    <div class="actions">

                        <a class="btn btn-warning"
                           href="index.php?page=sua_diem&id=<?= $diem['id'] ?>">
                            Sửa
                        </a>

                        <form method="POST"
                              class="inline-form">

                            <input type="hidden"
                                   name="action"
                                   value="xoa_diem">

                            <input type="hidden"
                                   name="id"
                                   value="<?= $diem['id'] ?>">

                            <button class="btn btn-danger"
                                    onclick="return confirm('Bạn có chắc muốn xóa điểm này?')">
                                Xóa
                            </button>

                        </form>

                    </div>

                </td>

            </tr>

            <?php endwhile; ?>

        </table>

    </div>


<?php elseif ($page === 'sua_diem'): ?>

    <?php

    $id = (int)($_GET['id'] ?? 0);

    $stmt = $conn->prepare(
        "SELECT * FROM scores WHERE id = ?"
    );

    $stmt->bind_param("i", $id);
    $stmt->execute();

    $diem_edit = $stmt->get_result()->fetch_assoc();

    $stmt->close();

    ?>

    <h1>Sửa điểm số</h1>

    <div class="panel">

        <form method="POST">

            <input type="hidden"
                   name="action"
                   value="sua_diem">

            <input type="hidden"
                   name="id"
                   value="<?= $diem_edit['id'] ?>">

            <div class="form-grid">

                <div class="form-group">

                    <label>Môn học</label>

                    <input type="text"
                           name="mon_hoc"
                           value="<?= e($diem_edit['subject']) ?>"
                           required>

                </div>


                <div class="form-group">

                    <label>Điểm số</label>

                    <input type="number"
                           name="diem"
                           min="0"
                           max="10"
                           step="0.1"
                           value="<?= e($diem_edit['score']) ?>"
                           required>

                </div>

            </div>

            <br>

            <button class="btn btn-primary">
                Lưu thay đổi
            </button>

            <a class="btn btn-secondary"
               href="index.php?page=diem">
                Hủy
            </a>

        </form>

    </div>


<?php endif; ?>


    </div>

</div>


<script>

function timSinhVien() {

    let input = document
        .getElementById("timKiem")
        .value
        .toLowerCase();

    let table = document
        .getElementById("bangSinhVien");

    let rows = table.getElementsByTagName("tr");

    for (let i = 1; i < rows.length; i++) {

        let text = rows[i]
            .innerText
            .toLowerCase();

        rows[i].style.display =
            text.includes(input) ? "" : "none";
    }
}

</script>

</body>

</html>
