<?php include_once 'views/layout/header.php'; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Task Details</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="/tasks" class="btn btn-sm btn-outline-secondary me-2">
            <span data-feather="arrow-left"></span> Back to Tasks
        </a>
        <a href="/tasks/edit?id=<?php echo $task->id; ?>" class="btn btn-sm btn-outline-primary">
            <span data-feather="edit"></span> Edit Task
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title m-0"><?php echo htmlspecialchars($task->title); ?></h5>
                <span class="badge 
                    <?php 
                    switch($task->status) {
                        case 'pending': echo 'bg-secondary'; break;
                        case 'in_progress': echo 'bg-info'; break;
                        case 'completed': echo 'bg-success'; break;
                        default: echo 'bg-secondary';
                    }
                    ?>">
                    <?php echo ucwords(str_replace('_', ' ', $task->status)); ?>
                </span>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Priority:</strong> 
                            <span class="badge 
                                <?php 
                                switch($task->priority) {
                                    case 'high': echo 'bg-danger'; break;
                                    case 'medium': echo 'bg-warning'; break;
                                    default: echo 'bg-info';
                                }
                                ?>">
                                <?php echo ucfirst($task->priority); ?>
                            </span>
                        </p>
                        <p class="mb-1"><strong>Due Date:</strong> 
                            <?php 
                            $dueDate = new DateTime($task->due_date);
                            $today = new DateTime();
                            $isPast = $dueDate < $today;
                            
                            echo date('F d, Y', strtotime($task->due_date));
                            
                            if ($isPast && $task->status !== 'completed') {
                                echo ' <span class="text-danger">(Overdue)</span>';
                            }
                            ?>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Category:</strong> 
                            <?php if (!empty($task->category_id)): ?>
                            <span class="badge" style="background-color: <?php echo $task->color ?? '#6c757d'; ?>">
                                <?php echo htmlspecialchars($task->category_name); ?>
                            </span>
                            <?php else: ?>
                            <span class="text-muted">Uncategorized</span>
                            <?php endif; ?>
                        </p>
                        <p class="mb-1"><strong>Created:</strong> <?php echo date('F d, Y', strtotime($task->created_at)); ?></p>
                        <p class="mb-1"><strong>Updated:</strong> <?php echo date('F d, Y', strtotime($task->updated_at)); ?></p>
                    </div>
                </div>
                
                <div class="mb-4">
                    <h6>Description:</h6>
                    <div class="p-3 bg-light rounded">
                        <?php if (!empty($task->description)): ?>
                        <p class="mb-0"><?php echo nl2br(htmlspecialchars($task->description)); ?></p>
                        <?php else: ?>
                        <p class="text-muted mb-0">No description provided.</p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="d-flex gap-2">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            Update Status
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item <?php echo $task->status === 'pending' ? 'active' : ''; ?>" href="#" onclick="updateStatus(<?php echo $task->id; ?>, 'pending')">Pending</a></li>
                            <li><a class="dropdown-item <?php echo $task->status === 'in_progress' ? 'active' : ''; ?>" href="#" onclick="updateStatus(<?php echo $task->id; ?>, 'in_progress')">In Progress</a></li>
                            <li><a class="dropdown-item <?php echo $task->status === 'completed' ? 'active' : ''; ?>" href="#" onclick="updateStatus(<?php echo $task->id; ?>, 'completed')">Completed</a></li>
                        </ul>
                    </div>
                    
                    <a href="/tasks/edit?id=<?php echo $task->id; ?>" class="btn btn-primary">
                        <span data-feather="edit"></span> Edit
                    </a>
                    
                    <button type="button" class="btn btn-danger" onclick="confirmDelete(<?php echo $task->id; ?>)">
                        <span data-feather="trash-2"></span> Delete
                    </button>
                </div>
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

function updateStatus(taskId, newStatus) {
    // Using AJAX to update the status
    fetch('/api/tasks.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=update_status&id=' + taskId + '&status=' + newStatus
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload(); // Reload to see the updated status
        } else {
            alert('Failed to update status: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating the status.');
    });
}
</script>

<?php include_once 'views/layout/footer.php'; ?>
