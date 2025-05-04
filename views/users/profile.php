<?php include_once 'views/layout/header.php'; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">User Profile</h1>
</div>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Update Profile Information</h5>
            </div>
            <div class="card-body">
                <form action="/profile/update" method="POST">
                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="<?php echo isset($_SESSION['flash_old']['name']) ? htmlspecialchars($_SESSION['flash_old']['name']) : htmlspecialchars($user->name); ?>"
                               required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?php echo isset($_SESSION['flash_old']['email']) ? htmlspecialchars($_SESSION['flash_old']['email']) : htmlspecialchars($user->email); ?>"
                               required>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="submit" class="btn btn-primary">Update Profile</button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title">Account Security</h5>
            </div>
            <div class="card-body">
                <p>Manage your account security settings and password.</p>
                
                <div class="d-grid gap-2 d-md-flex">
                    <a href="/change-password" class="btn btn-outline-primary">
                        <span data-feather="lock"></span> Change Password
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
// Clear old form data
if (isset($_SESSION['flash_old'])) {
    unset($_SESSION['flash_old']);
}
?>

<?php include_once 'views/layout/footer.php'; ?>
