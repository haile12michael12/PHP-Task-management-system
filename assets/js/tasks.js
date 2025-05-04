/**
 * Tasks Manager - JavaScript functionality
 * Handles AJAX requests, form validation, and UI interactions
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize task status updates
    initTaskStatusUpdates();
    
    // Initialize date pickers
    initDatePickers();
    
    // Initialize task filters
    initTaskFilters();
    
    // Initialize category color pickers
    initColorPickers();
    
    // Task form validation
    initFormValidation();
});

/**
 * Initialize task status updates via AJAX
 */
function initTaskStatusUpdates() {
    // Get all status update buttons
    const statusDropdowns = document.querySelectorAll('.dropdown-item[onclick*="updateStatus"]');
    
    if (statusDropdowns.length === 0) return;
    
    // Add click event listeners
    statusDropdowns.forEach(item => {
        const originalOnClick = item.getAttribute('onclick');
        
        // Remove the original onclick attribute
        item.removeAttribute('onclick');
        
        // Extract task ID and status from the original onclick
        const match = originalOnClick.match(/updateStatus\((\d+),\s*['"](\w+)['"]\)/);
        
        if (match) {
            const taskId = match[1];
            const status = match[2];
            
            // Add new event listener
            item.addEventListener('click', function(e) {
                e.preventDefault();
                updateTaskStatus(taskId, status);
            });
        }
    });
}

/**
 * Update task status via AJAX
 */
function updateTaskStatus(taskId, newStatus) {
    // Show loading indicator
    const statusBadge = document.querySelector(`.task-status-${taskId}`);
    if (statusBadge) {
        statusBadge.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
    }
    
    // Create form data
    const formData = new FormData();
    formData.append('action', 'update_status');
    formData.append('id', taskId);
    formData.append('status', newStatus);
    
    // Send AJAX request
    fetch('/api/tasks.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Reload the page to show updated status
            window.location.reload();
        } else {
            // Show error message
            alert('Failed to update task status: ' + (data.message || 'Unknown error'));
            if (statusBadge) {
                statusBadge.textContent = newStatus; // Reset to previous text
            }
        }
    })
    .catch(error => {
        console.error('Error updating task status:', error);
        alert('An error occurred while updating the task status. Please try again.');
        if (statusBadge) {
            statusBadge.textContent = newStatus; // Reset to previous text
        }
    });
}

/**
 * Initialize date pickers for due dates
 */
function initDatePickers() {
    // If using native HTML5 date inputs, add min date constraint
    const dateInputs = document.querySelectorAll('input[type="date"]');
    
    dateInputs.forEach(input => {
        // Only set min date if it's for creating a new task
        if (input.id === 'due_date' && !input.value) {
            const today = new Date().toISOString().split('T')[0];
            input.value = today;
        }
    });
}

/**
 * Initialize task filters
 */
function initTaskFilters() {
    const filterForm = document.querySelector('form[action="/tasks"]');
    
    if (!filterForm) return;
    
    // Auto-submit when select fields change
    const selectInputs = filterForm.querySelectorAll('select');
    selectInputs.forEach(select => {
        select.addEventListener('change', function() {
            filterForm.submit();
        });
    });
    
    // Add debounce for search input
    const searchInput = filterForm.querySelector('input[name="search"]');
    if (searchInput) {
        let debounceTimer;
        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(function() {
                filterForm.submit();
            }, 500);
        });
    }
}

/**
 * Initialize category color pickers
 */
function initColorPickers() {
    const colorPicker = document.getElementById('colorPicker');
    const colorInput = document.getElementById('color');
    const previewColor = document.getElementById('previewColor');
    
    if (!colorPicker || !colorInput || !previewColor) return;
    
    // Update color input when color picker changes
    colorPicker.addEventListener('input', function() {
        colorInput.value = this.value;
        previewColor.style.backgroundColor = this.value;
    });
    
    // Update color picker when color input changes
    colorInput.addEventListener('input', function() {
        const colorValue = this.value;
        if (/^#([A-Fa-f0-9]{6})$/.test(colorValue)) {
            colorPicker.value = colorValue;
            previewColor.style.backgroundColor = colorValue;
        }
    });
}

/**
 * Initialize form validation
 */
function initFormValidation() {
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
                
                // Add validation classes
                form.classList.add('was-validated');
                
                // Focus first invalid field
                const invalidField = form.querySelector(':invalid');
                if (invalidField) {
                    invalidField.focus();
                }
            }
        });
    });
}

/**
 * Confirm task deletion
 */
function confirmDelete(taskId) {
    document.getElementById('deleteTaskId').value = taskId;
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}

/**
 * Confirm category deletion
 */
function confirmCategoryDelete(categoryId, categoryName) {
    document.getElementById('deleteCategoryId').value = categoryId;
    document.getElementById('categoryName').textContent = categoryName;
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}
