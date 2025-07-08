<?php
session_start();
include 'config.php';

$pageTitle = "Edit User";

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$loggedInUser = $_SESSION['user'];

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid user ID.");
}
$id = intval($_GET['id']);

if (!in_array($loggedInUser['role'], ['admin', 'superadmin']) && $loggedInUser['id'] != $id) {
    die("Access denied.");
}

$result = mysqli_query($conn, "SELECT * FROM users WHERE id = $id");
if (mysqli_num_rows($result) === 0) {
    die("User not found.");
}
$user = mysqli_fetch_assoc($result);

if ($user['role'] === 'superadmin') {
    die("You cannot edit a Super Admin account.");
}

$error = "";
$success = "";

if (isset($_POST['update'])) {
    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'] ?? '';

    $updateQuery = "UPDATE users SET name = '$name', email = '$email', phone = '$phone'";

    if ($loggedInUser['id'] == $id && !empty($password)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $updateQuery .= ", password = '$hashedPassword'";
    }

    $updateQuery .= " WHERE id = $id";

    if (mysqli_query($conn, $updateQuery)) {
        $success = "User updated successfully!";
        $user['name'] = $name;
        $user['email'] = $email;
        $user['phone'] = $phone;
    } else {
        $error = "Failed to update user.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit User</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body {
      background: #f8f9fa;
    }
    .card {
      border: none;
      border-radius: 15px;
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
    }
    .form-control {
      border-radius: 10px;
    }
    .form-label {
      font-weight: 500;
    }
    .btn-custom {
      border-radius: 10px;
      padding: 10px 25px;
    }
  </style>
</head>
<body>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
      <div class="card p-4">
        <h3 class="mb-4 text-center text-primary"><i class="bi bi-pencil-square"></i> Edit User</h3>

        <form method="post">
          <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" class="form-control" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" class="form-control" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Phone</label>
            <input type="text" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" class="form-control" required>
          </div>

          <?php if ($loggedInUser['id'] == $id): ?>
          <div class="mb-3">
            <label class="form-label">New Password <small>(Leave blank to keep current)</small></label>
            <input type="password" name="password" id="password" class="form-control">
            <div class="form-check mt-2">
              <input class="form-check-input" type="checkbox" onclick="togglePassword()">
              <label class="form-check-label">Show Password</label>
            </div>
          </div>
          <?php endif; ?>

          <div class="d-flex justify-content-between">
            <a href="index.php" class="btn btn-outline-secondary btn-custom"><i class="bi bi-arrow-left"></i> Back</a>
            <button type="submit" name="update" class="btn btn-primary btn-custom"><i class="bi bi-check-circle"></i> Update</button>
          </div>
        </form>

      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  function togglePassword() {
    const pwd = document.getElementById("password");
    pwd.type = pwd.type === "password" ? "text" : "password";
  }
</script>

<?php if (!empty($success)): ?>
<script>
  Swal.fire({
    icon: 'success',
    title: 'Success',
    text: '<?= addslashes($success) ?>',
    confirmButtonColor: '#3085d6',
    timer: 3000
  });
</script>
<?php endif; ?>

<?php if (!empty($error)): ?>
<script>
  Swal.fire({
    icon: 'error',
    title: 'Oops...',
    text: '<?= addslashes($error) ?>',
    confirmButtonColor: '#d33'
  });
</script>
<?php endif; ?>

</body>
</html>
