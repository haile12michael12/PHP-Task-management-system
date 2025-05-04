<?php include_once 'views/layout/header.php'; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Create New Task</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="/tasks" class="btn btn-sm btn-outline-secondary">
            <span data-feather="arrow-left"></span> Back to Tasks
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Task Details</h5>
            </div>
            <div class="card-body">
                <form action="/tasks/store" method="POST">
                    <div class="mb-3">
                        <label for="title" class="form-label">Title *</label>
                        <input type="text" class="form-control" id="title" name="title" required 
                               value="<?php echo isset($_SESSION['flash_old']['title']) ? htmlspecialchars($_SESSION['flash_old']['title']) : ''; ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4"><?php echo isset($_SESSION['flash_old']['description']) ? htmlspecialchars($_SESSION['flash_old']['description']) : ''; ?></textarea>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="status" class="form-label">Status *</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="">Select Status</option>
                                <option value="pending" <?php echo (isset($_SESSION['flash_old']['status']) && $_SESSION['flash_old']['status'] === 'pending') ? 'selected' : ''; ?>>Pending</option>
                                <option value="in_progress" <?php echo (isset($_SESSION['flash_old']['status']) && $_SESSION['flash_old']['status'] === 'in_progress') ? 'selected' : ''; ?>>In Progress</option>
                                <option value="completed" <?php echo (isset($_SESSION['flash_old']['status']) && $_SESSION['flash_old']['status'] === 'completed') ? 'selected' : ''; ?>>Completed</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="priority" class="form-label">Priority *</label>
                            <select class="form-select" id="priority" name="priority" required>
                                <option value="">Select Priority</option>
                                <option value="low" <?php echo (isset($_SESSION['flash_old']['priority']) && $_SESSION['flash_old']['priority'] === 'low') ? 'selected' : ''; ?>>Low</option>
                                <option value="medium" <?php echo (isset($_SESSION['flash_old']['priority']) && $_SESSION['flash_old']['priority'] === 'medium') ? 'selected' : ''; ?>>Medium</option>
                                <option value="high" <?php echo (isset($_SESSION['flash_old']['priority']) && $_SESSION['flash_old']['priority'] === 'high') ? 'selected' : ''; ?>>High</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="due_date" class="form-label">Due Date *</label>
                            <input type="date" class="form-control" id="due_date" name="due_date" required 
                                   value="<?php echo isset($_SESSION['flash_old']['due_date']) ? htmlspecialchars($_SESSION['flash_old']['due_date']) : date('Y-m-d'); ?>">
                        </div>
                        
                        <div class="col-md-6">
                            <label for="category_id" class="form-label">Category</label>
                            <select class="form-select" id="category_id" name="category_id">
                                <option value="">Uncategorized</option>
                                <?php while ($category = $categories->fetch()): ?>
                                <option value="<?php echo $category['id']; ?>" 
                                        <?php echo (isset($_SESSION['flash_old']['category_id']) && $_SESSION['flash_old']['category_id'] == $category['id']) ? 'selected' : ''; ?> 
                                        style="color: <?php echo $category['color']; ?>">
                                    <?php echo $category['name']; ?>
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="/tasks" class="btn btn-secondary me-md-2">Cancel</a>
                        <button type="submit" class="btn btn-primary">Create Task</button>
                    </div>
                </form>
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
