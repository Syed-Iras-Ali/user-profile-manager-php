<?php
session_start();
include 'config.php';

$pageTitle = "Create User";

if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin', 'superadmin'])) {
    header("Location: login.php");
    exit;
}

$showPopup = false;
$newEmail = "";
$newPassword = "";

if (isset($_POST['create'])) {
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $phone    = trim($_POST['phone']);
    $password = trim($_POST['password']);
    $role     = trim($_POST['role']);

    if (empty($name) || empty($email) || empty($phone)) {
        $error = "Please fill in all required fields.";
    } else {
        if (empty($password)) {
            $password = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"), 0, 10);
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $query = "INSERT INTO users (name, email, phone, password, role)
                  VALUES ('$name', '$email', '$phone', '$hashedPassword', '$role')";

        if (mysqli_query($conn, $query)) {
            $showPopup = true;
            $newEmail = $email;
            $newPassword = $password;
        } else {
            $error = "Database error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Create User</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body {
      background: #f1f4f6;
    }
    .card {
      border-radius: 20px;
      box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
    }
    .form-control, .form-select {
      border-radius: 10px;
    }
    .btn-custom {
      border-radius: 10px;
    }
    .form-label {
      font-weight: 500;
    }
  </style>
</head>
<body>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
      <div class="card p-4 bg-white">
        <h3 class="mb-4 text-center text-success"><i class="bi bi-person-plus-fill"></i> Create New User</h3>

        <?php if (!empty($error)): ?>
          <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="post">
          <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" name="name" class="form-control" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Email Address</label>
            <input type="email" name="email" class="form-control" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Phone Number</label>
            <input type="text" name="phone" class="form-control" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Password <small class="text-muted">(Leave blank to auto-generate)</small></label>
            <input type="text" name="password" class="form-control">
          </div>

          <div class="mb-3">
            <label class="form-label">Role</label>
            <select name="role" class="form-select" required>
              <option value="user">User</option>
              <option value="admin">Admin</option>
            </select>
          </div>

          <div class="d-flex justify-content-between mt-4">
            <a href="index.php" class="btn btn-outline-secondary btn-custom"><i class="bi bi-arrow-left"></i> Back</a>
            <button type="submit" name="create" class="btn btn-success btn-custom"><i class="bi bi-check2-circle"></i> Create</button>
          </div>
        </form>

      </div>
    </div>
  </div>
</div>

<?php if ($showPopup): ?>
<script>
  Swal.fire({
    title: 'User Created!',
    html: `<p><strong>Email:</strong> <?= $newEmail ?></p><p><strong>Password:</strong> <?= $newPassword ?></p>`,
    icon: 'success',
    confirmButtonText: 'OK'
  });
</script>
<?php endif; ?>

</body>
</html>
