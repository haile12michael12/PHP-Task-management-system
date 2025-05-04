<?php include_once 'views/layout/header.php'; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Dashboard</h1>
</div>

<div class="row">
    <!-- Task Status Overview -->
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title">Task Status Overview</h5>
            </div>
            <div class="card-body">
                <?php 
                $statuses = ['pending' => 0, 'in_progress' => 0, 'completed' => 0];
                $total = 0;
                
                if ($taskCountByStatus) {
                    foreach ($taskCountByStatus as $statusCount) {
                        $statuses[$statusCount['status']] = (int)$statusCount['count'];
                        $total += (int)$statusCount['count'];
                    }
                }
                ?>
                
                <div class="status-overview">
                    <div class="row text-center mb-3">
                        <div class="col-md-4">
                            <div class="card bg-light mb-2">
                                <div class="card-body py-3">
                                    <h2 class="card-title"><?php echo $statuses['pending']; ?></h2>
                                    <p class="card-text">Pending</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-info mb-2">
                                <div class="card-body py-3 text-white">
                                    <h2 class="card-title"><?php echo $statuses['in_progress']; ?></h2>
                                    <p class="card-text">In Progress</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-success mb-2">
                                <div class="card-body py-3 text-white">
                                    <h2 class="card-title"><?php echo $statuses['completed']; ?></h2>
                                    <p class="card-text">Completed</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <?php if ($total > 0): ?>
                    <div class="progress" style="height: 25px;">
                        <div class="progress-bar bg-light" role="progressbar" style="width: <?php echo ($statuses['pending'] / $total) * 100; ?>%" aria-valuenow="<?php echo $statuses['pending']; ?>" aria-valuemin="0" aria-valuemax="<?php echo $total; ?>">
                            <?php if ($statuses['pending'] > 0): ?>
                            <?php echo round(($statuses['pending'] / $total) * 100); ?>%
                            <?php endif; ?>
                        </div>
                        <div class="progress-bar bg-info" role="progressbar" style="width: <?php echo ($statuses['in_progress'] / $total) * 100; ?>%" aria-valuenow="<?php echo $statuses['in_progress']; ?>" aria-valuemin="0" aria-valuemax="<?php echo $total; ?>">
                            <?php if ($statuses['in_progress'] > 0): ?>
                            <?php echo round(($statuses['in_progress'] / $total) * 100); ?>%
                            <?php endif; ?>
                        </div>
                        <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo ($statuses['completed'] / $total) * 100; ?>%" aria-valuenow="<?php echo $statuses['completed']; ?>" aria-valuemin="0" aria-valuemax="<?php echo $total; ?>">
                            <?php if ($statuses['completed'] > 0): ?>
                            <?php echo round(($statuses['completed'] / $total) * 100); ?>%
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php else: ?>
                    <p class="text-muted text-center">No tasks yet. Start by adding a task!</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Categories Overview -->
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title">Tasks by Category</h5>
            </div>
            <div class="card-body">
                <?php if ($categoryStats && count($categoryStats) > 0): ?>
                <div class="category-overview">
                    <?php foreach ($categoryStats as $category): ?>
                    <div class="d-flex align-items-center mb-2">
                        <div class="category-color" style="background-color: <?php echo $category['color']; ?>; width: 16px; height: 16px; border-radius: 50%; margin-right: 8px;"></div>
                        <div class="category-name flex-grow-1"><?php echo $category['name']; ?></div>
                        <div class="category-count badge bg-secondary"><?php echo $category['task_count']; ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <p class="text-muted text-center">No categories yet. Start by adding a category!</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Upcoming Tasks -->
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title">Upcoming Tasks</h5>
            </div>
            <div class="card-body">
                <?php if ($upcomingTasks && $upcomingTasks->rowCount() > 0): ?>
                <ul class="list-group">
                    <?php while ($task = $upcomingTasks->fetch()): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <a href="/tasks/view?id=<?php echo $task['id']; ?>" class="task-title"><?php echo $task['title']; ?></a>
                            <small class="d-block text-muted">
                                Due: <?php echo date('M d, Y', strtotime($task['due_date'])); ?>
                                <?php if (!empty($task['category_name'])): ?>
                                <span class="badge" style="background-color: <?php echo $task['color'] ?? '#6c757d'; ?>">
                                    <?php echo $task['category_name']; ?>
                                </span>
                                <?php endif; ?>
                            </small>
                        </div>
                        <span class="badge rounded-pill 
                            <?php 
                            switch($task['priority']) {
                                case 'high': echo 'bg-danger'; break;
                                case 'medium': echo 'bg-warning'; break;
                                default: echo 'bg-info';
                            }
                            ?>">
                            <?php echo ucfirst($task['priority']); ?>
                        </span>
                    </li>
                    <?php endwhile; ?>
                </ul>
                <?php else: ?>
                <p class="text-muted text-center">No upcoming tasks!</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Overdue Tasks -->
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title text-danger">Overdue Tasks</h5>
            </div>
            <div class="card-body">
                <?php if ($overdueTasks && $overdueTasks->rowCount() > 0): ?>
                <ul class="list-group">
                    <?php while ($task = $overdueTasks->fetch()): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <a href="/tasks/view?id=<?php echo $task['id']; ?>" class="task-title"><?php echo $task['title']; ?></a>
                            <small class="d-block text-danger">
                                Due: <?php echo date('M d, Y', strtotime($task['due_date'])); ?>
                                <?php if (!empty($task['category_name'])): ?>
                                <span class="badge" style="background-color: <?php echo $task['color'] ?? '#6c757d'; ?>">
                                    <?php echo $task['category_name']; ?>
                                </span>
                                <?php endif; ?>
                            </small>
                        </div>
                        <span class="badge rounded-pill 
                            <?php 
                            switch($task['priority']) {
                                case 'high': echo 'bg-danger'; break;
                                case 'medium': echo 'bg-warning'; break;
                                default: echo 'bg-info';
                            }
                            ?>">
                            <?php echo ucfirst($task['priority']); ?>
                        </span>
                    </li>
                    <?php endwhile; ?>
                </ul>
                <?php else: ?>
                <p class="text-muted text-center">No overdue tasks. Good job!</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
            <a href="/tasks/create" class="btn btn-primary">
                <span data-feather="plus-circle"></span> Add New Task
            </a>
        </div>
    </div>
</div>

<?php include_once 'views/layout/footer.php'; ?>
