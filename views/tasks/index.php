<?php include_once 'views/layout/header.php'; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Tasks</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="/tasks/create" class="btn btn-sm btn-outline-primary">
            <span data-feather="plus-circle"></span> New Task
        </a>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Filters</h5>
            </div>
            <div class="card-body">
                <form action="/tasks" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select">
                            <option value="">All Statuses</option>
                            <option value="pending" <?php echo isset($_GET['status']) && $_GET['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="in_progress" <?php echo isset($_GET['status']) && $_GET['status'] === 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                            <option value="completed" <?php echo isset($_GET['status']) && $_GET['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="priority" class="form-label">Priority</label>
                        <select name="priority" id="priority" class="form-select">
                            <option value="">All Priorities</option>
                            <option value="low" <?php echo isset($_GET['priority']) && $_GET['priority'] === 'low' ? 'selected' : ''; ?>>Low</option>
                            <option value="medium" <?php echo isset($_GET['priority']) && $_GET['priority'] === 'medium' ? 'selected' : ''; ?>>Medium</option>
                            <option value="high" <?php echo isset($_GET['priority']) && $_GET['priority'] === 'high' ? 'selected' : ''; ?>>High</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="category_id" class="form-label">Category</label>
                        <select name="category_id" id="category_id" class="form-select">
                            <option value="">All Categories</option>
                            <?php while ($category = $categories->fetch()): ?>
                            <option value="<?php echo $category['id']; ?>" <?php echo isset($_GET['category_id']) && $_GET['category_id'] == $category['id'] ? 'selected' : ''; ?>>
                                <?php echo $category['name']; ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="due_date" class="form-label">Due Date</label>
                        <select name="due_date" id="due_date" class="form-select">
                            <option value="">All Dates</option>
                            <option value="today" <?php echo isset($_GET['due_date']) && $_GET['due_date'] === 'today' ? 'selected' : ''; ?>>Today</option>
                            <option value="week" <?php echo isset($_GET['due_date']) && $_GET['due_date'] === 'week' ? 'selected' : ''; ?>>This Week</option>
                        </select>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="search" class="form-label">Search</label>
                        <input type="text" name="search" id="search" class="form-control" placeholder="Search by title or description" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                    </div>
                    
                    <div class="col-md-3">
                        <label for="sort" class="form-label">Sort By</label>
                        <select name="sort" id="sort" class="form-select">
                            <option value="due_date" <?php echo (!isset($_GET['sort']) || $_GET['sort'] === 'due_date') ? 'selected' : ''; ?>>Due Date</option>
                            <option value="priority" <?php echo isset($_GET['sort']) && $_GET['sort'] === 'priority' ? 'selected' : ''; ?>>Priority</option>
                            <option value="status" <?php echo isset($_GET['sort']) && $_GET['sort'] === 'status' ? 'selected' : ''; ?>>Status</option>
                            <option value="category" <?php echo isset($_GET['sort']) && $_GET['sort'] === 'category' ? 'selected' : ''; ?>>Category</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="direction" class="form-label">Order</label>
                        <select name="direction" id="direction" class="form-select">
                            <option value="asc" <?php echo (!isset($_GET['direction']) || $_GET['direction'] === 'asc') ? 'selected' : ''; ?>>Ascending</option>
                            <option value="desc" <?php echo isset($_GET['direction']) && $_GET['direction'] === 'desc' ? 'selected' : ''; ?>>Descending</option>
                        </select>
                    </div>
                    
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Apply Filters</button>
                        <a href="/tasks" class="btn btn-secondary">Reset</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Task List</h5>
            </div>
            <div class="card-body">
                <?php if ($tasks && $tasks->rowCount() > 0): ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Status</th>
                                <th>Priority</th>
                                <th>Category</th>
                                <th>Due Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($task = $tasks->fetch()): ?>
                            <tr>
                                <td>
                                    <a href="/tasks/view?id=<?php echo $task['id']; ?>">
                                        <?php echo $task['title']; ?>
                                    </a>
                                </td>
                                <td>
                                    <span class="badge 
                                        <?php 
                                        switch($task['status']) {
                                            case 'pending': echo 'bg-secondary'; break;
                                            case 'in_progress': echo 'bg-info'; break;
                                            case 'completed': echo 'bg-success'; break;
                                            default: echo 'bg-secondary';
                                        }
                                        ?>">
                                        <?php echo ucwords(str_replace('_', ' ', $task['status'])); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge 
                                        <?php 
                                        switch($task['priority']) {
                                            case 'high': echo 'bg-danger'; break;
                                            case 'medium': echo 'bg-warning'; break;
                                            default: echo 'bg-info';
                                        }
                                        ?>">
                                        <?php echo ucfirst($task['priority']); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if (!empty($task['category_name'])): ?>
                                    <span class="badge" style="background-color: <?php echo $task['color'] ?? '#6c757d'; ?>">
                                        <?php echo $task['category_name']; ?>
                                    </span>
                                    <?php else: ?>
                                    <span class="text-muted">Uncategorized</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php 
                                    $dueDate = new DateTime($task['due_date']);
                                    $today = new DateTime();
                                    $interval = $today->diff($dueDate);
                                    $isPast = $dueDate < $today;
                                    
                                    echo date('M d, Y', strtotime($task['due_date']));
                                    
                                    if ($isPast && $task['status'] !== 'completed') {
                                        echo ' <span class="text-danger">(Overdue)</span>';
                                    } elseif ($interval->days === 0 && $task['status'] !== 'completed') {
                                        echo ' <span class="text-warning">(Today)</span>';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="/tasks/view?id=<?php echo $task['id']; ?>" class="btn btn-sm btn-info" title="View">
                                            <span data-feather="eye"></span>
                                        </a>
                                        <a href="/tasks/edit?id=<?php echo $task['id']; ?>" class="btn btn-sm btn-primary" title="Edit">
                                            <span data-feather="edit"></span>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger" title="Delete" onclick="confirmDelete(<?php echo $task['id']; ?>)">
                                            <span data-feather="trash-2"></span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <p class="text-muted text-center">No tasks found. Try adjusting your filters or add a new task.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this task? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="/tasks/delete" method="POST" id="deleteForm">
                    <input type="hidden" name="id" id="deleteTaskId">
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(taskId) {
    document.getElementById('deleteTaskId').value = taskId;
    var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}
</script>

<?php include_once 'views/layout/footer.php'; ?>
