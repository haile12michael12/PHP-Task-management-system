<?php include_once 'views/layout/header.php'; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Categories</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="/categories/create" class="btn btn-sm btn-outline-primary">
            <span data-feather="plus-circle"></span> New Category
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Category List</h5>
            </div>
            <div class="card-body">
                <?php if ($categories && $categories->rowCount() > 0): ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Color</th>
                                <th>Created Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($category = $categories->fetch()): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="category-color me-2" style="width: 16px; height: 16px; border-radius: 50%; background-color: <?php echo $category['color']; ?>"></div>
                                        <?php echo $category['name']; ?>
                                    </div>
                                </td>
                                <td>
                                    <code><?php echo $category['color']; ?></code>
                                </td>
                                <td>
                                    <?php echo date('M d, Y', strtotime($category['created_at'])); ?>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="/categories/edit?id=<?php echo $category['id']; ?>" class="btn btn-sm btn-primary" title="Edit">
                                            <span data-feather="edit"></span>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger" title="Delete" onclick="confirmDelete(<?php echo $category['id']; ?>, '<?php echo htmlspecialchars($category['name']); ?>')">
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
                <p class="text-muted text-center">No categories found. Add your first category!</p>
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
                Are you sure you want to delete the category "<span id="categoryName"></span>"? This action cannot be undone.
                <div class="alert alert-warning mt-3">
                    <small>Note: You cannot delete a category that is assigned to any tasks.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="/categories/delete" method="POST" id="deleteForm">
                    <input type="hidden" name="id" id="deleteCategoryId">
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(categoryId, categoryName) {
    document.getElementById('deleteCategoryId').value = categoryId;
    document.getElementById('categoryName').textContent = categoryName;
    var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}
</script>

<?php include_once 'views/layout/footer.php'; ?>
