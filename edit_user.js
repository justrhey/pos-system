// Confirm before submitting Edit User form
document.querySelector('.add-user-form').addEventListener('submit', function(e) {
  if (!confirm('Are you sure you want to update the user?')) {
    e.preventDefault();
  }
});

// Confirm before deleting user (example for delete buttons if any)



