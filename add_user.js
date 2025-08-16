
  // Confirm before submitting Add User form
  document.querySelector('.add-user-form').addEventListener('submit', function(e) {
    if (!confirm('Are you sure you want to add this user?')) {
      e.preventDefault();
    }
  });

  // Confirm before deleting user - example for delete buttons
  document.querySelectorAll('.delete-user-btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
      if (!confirm('Are you sure you want to delete this user?')) {
        e.preventDefault();
      }
    });
  });
