<?php include_once 'views/layout/header.php'; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Create New Category</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="/categories" class="btn btn-sm btn-outline-secondary">
            <span data-feather="arrow-left"></span> Back to Categories
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Category Details</h5>
            </div>
            <div class="card-body">
                <form action="/categories/store" method="POST">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name *</label>
                        <input type="text" class="form-control" id="name" name="name" required 
                               value="<?php echo isset($_SESSION['flash_old']['name']) ? htmlspecialchars($_SESSION['flash_old']['name']) : ''; ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="color" class="form-label">Color *</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <input type="color" id="colorPicker" 
                                    value="<?php echo isset($_SESSION['flash_old']['color']) ? htmlspecialchars($_SESSION['flash_old']['color']) : '#3498db'; ?>" 
                                    onchange="document.getElementById('color').value = this.value;">
                            </span>
                            <input type="text" class="form-control" id="color" name="color" required 
                                   value="<?php echo isset($_SESSION['flash_old']['color']) ? htmlspecialchars($_SESSION['flash_old']['color']) : '#3498db'; ?>"
                                   pattern="^#([A-Fa-f0-9]{6})$" 
                                   title="Hexadecimal color code (e.g., #3498db)"
                                   onchange="document.getElementById('colorPicker').value = this.value;">
                        </div>
                        <div class="form-text">Choose a color for this category</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Preview</label>
                        <div class="p-3 border rounded d-flex align-items-center">
                            <div id="previewColor" style="width: 24px; height: 24px; border-radius: 50%; background-color: <?php echo isset($_SESSION['flash_old']['color']) ? htmlspecialchars($_SESSION['flash_old']['color']) : '#3498db'; ?>; margin-right: 10px;"></div>
                            <span id="previewName"><?php echo isset($_SESSION['flash_old']['name']) ? htmlspecialchars($_SESSION['flash_old']['name']) : 'Category Name'; ?></span>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="/categories" class="btn btn-secondary me-md-2">Cancel</a>
                        <button type="submit" class="btn btn-primary">Create Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Update preview when name changes
document.getElementById('name').addEventListener('input', function() {
    document.getElementById('previewName').textContent = this.value || 'Category Name';
});

// Update preview when color changes
document.getElementById('colorPicker').addEventListener('input', function() {
    document.getElementById('previewColor').style.backgroundColor = this.value;
    document.getElementById('color').value = this.value;
});

document.getElementById('color').addEventListener('input', function() {
    const colorInput = this.value;
    if (/^#([A-Fa-f0-9]{6})$/.test(colorInput)) {
        document.getElementById('previewColor').style.backgroundColor = colorInput;
        document.getElementById('colorPicker').value = colorInput;
    }
});
</script>

<?php 
// Clear old form data
if (isset($_SESSION['flash_old'])) {
    unset($_SESSION['flash_old']);
}
?>

<?php include_once 'views/layout/footer.php'; ?>
