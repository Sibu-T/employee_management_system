document.getElementById('addUserForm').addEventListener('submit', function(e) {
    e.preventDefault(); // Prevent the form from submitting the traditional way

    // Gather form data
    let userData = {
        employeeId: document.getElementById('employeeId').value,
        username: document.getElementById('username').value,
        password: document.getElementById('password').value,
        role: document.getElementById('role').value
    };

    // Send the data using Fetch API
    fetch('add_user.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(userData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            location.reload(); // Reload the page to show the new user in the list
        } else {
            // Display error message
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
});

