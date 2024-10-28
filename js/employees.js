document.getElementById('addEmployeeForm').addEventListener('submit', function(e) {
    e.preventDefault(); // Prevent the form from submitting the traditional way

    // Gather form data
    let employeeData = {
        employeeId: document.getElementById('employeeId').value,
        fullName: document.getElementById('fullName').value,
        gender: document.getElementById('gender').value,
        phone: document.getElementById('phone').value,
        nationality: document.getElementById('nationality').value,
        jobTitle: document.getElementById('jobTitle').value,
        department: document.getElementById('department').value,
        branch: document.getElementById('branch').value
    };

    // Send the data using Fetch API
    fetch('add_employee.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(employeeData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            location.reload(); // Reload the page to show the new employee in the list
        } else {
            // Display error message
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
});
