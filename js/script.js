document.getElementById('loginForm').addEventListener('submit', function (e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('../api/login_api.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        const errorMessageDiv = document.getElementById('error-message');
        if (data.status === 'success') {
            window.location.href = 'dashboard.php'; // Redirect to dashboard
        } else {
            errorMessageDiv.textContent = data.message; // Display error message
            errorMessageDiv.style.display = 'block'; // Show the error message
        }
    })
    .catch(error => console.error('Error:', error));
});
