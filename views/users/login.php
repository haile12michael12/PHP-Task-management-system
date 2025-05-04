<?php include_once 'views/layout/header.php'; ?>

<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white text-center py-3">
                    <h4 class="mb-0">Login to Task Manager</h4>
                </div>
                <div class="card-body p-4">
                    <form action="/login" method="POST">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo isset($_SESSION['flash_old']['email']) ? htmlspecialchars($_SESSION['flash_old']['email']) : ''; ?>"
                                   required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Login</button>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center py-3">
                    <p class="mb-0">Don't have an account? <a href="/register">Register here</a></p>
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
