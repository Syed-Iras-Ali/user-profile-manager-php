<?php include 'config.php'; ?>
<?php include 'auth.php'; ?>
<?php $isAdmin = $_SESSION['user']['role'] === 'admin'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>User Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">PHP CRUD App</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout (<?= htmlspecialchars($_SESSION['user']['name']) ?>)</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">User Management System</h2>
            <div>
                <?php if (in_array($_SESSION['user']['role'], ['admin', 'superadmin'])): ?>
                    <a href="create.php" class="btn btn-success me-2">Add Employee</a>
                <?php endif; ?>
                <a href="export.php" class="btn btn-info">Export to CSV</a>
            </div>
        </div>

        
        <div class="table-responsive shadow-sm">
            <table class="table table-hover table-bordered align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th style="width: 180px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = mysqli_query($conn, "SELECT * FROM users");
                    while ($row = mysqli_fetch_assoc($result)):
                    ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= htmlspecialchars($row['phone']) ?></td>
                            <td>
                                <span class="badge bg-<?= 
                                    $row['role'] === 'superadmin' ? 'danger' : 
                                    ($row['role'] === 'admin' ? 'primary' : 'secondary') ?>">
                                    <?= ucfirst($row['role']) ?>
                                </span>
                            </td>
                            <td>
                                <?php
                                $role = $_SESSION['user']['role'];
                                $loggedInUserId = $_SESSION['user']['id'];

                                if (in_array($role, ['admin', 'superadmin'])) {
                                    if ($row['role'] === 'superadmin') {
                                        echo "<span class='text-muted'>Protected</span>";
                                    } else {
                                        echo "<a href='edit.php?id={$row['id']}' class='btn btn-warning btn-sm me-1'>Edit</a>";
                                        echo "<a href='delete.php?id={$row['id']}' onclick='return confirm(\"Delete this user?\")' class='btn btn-danger btn-sm'>Delete</a>";
                                    }
                                } else {
                                    if ($row['id'] == $loggedInUserId) {
                                        echo "<a href='edit.php?id={$row['id']}' class='btn btn-warning btn-sm'>Edit Your Info</a>";
                                    } else {
                                        echo "<span class='text-muted'>Not Allowed</span>";
                                    }
                                }
                                ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <footer class="bg-dark text-white text-center py-3 mt-5">
        <div class="container">
            <p class="mb-0">© <?= date('Y') ?> PHP CRUD App. All rights reserved.</p>
        </div>
    </footer>

    <!-- SweetAlert Success Popup for Delete -->
    <?php if (isset($_SESSION['delete_success'])): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'User Deleted',
            html: `<?= $_SESSION['delete_success'] ?>`,
            confirmButtonText: 'OK',
            customClass: {
                popup: 'rounded-4'
            }
        });
    </script>
    <?php unset($_SESSION['delete_success']); ?>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
