document.getElementById('addDepartmentForm').addEventListener('submit', function(e) {
    e.preventDefault(); // Prevent the form from submitting the traditional way

    // Gather form data
    let departmentData = {
        departmentId: document.getElementById('departmentId').value,
        departmentName: document.getElementById('departmentName').value
    };

    // Send the data using Fetch API
    fetch('add_department.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(departmentData)
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
// Populate the Update Form with the selected department's data
function populateUpdateForm(departmentId, departmentName) {
    document.getElementById('updateDepartmentId').value = departmentId;
    document.getElementById('updateDepartmentName').value = departmentName;
}

// Update Department using Fetch API
function updateDepartment() {
    let departmentData = {
        departmentId: document.getElementById('updateDepartmentId').value,
        departmentName: document.getElementById('updateDepartmentName').value
    };

    fetch('update_department.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(departmentData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            location.reload(); // Reload the page to show updated data
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

// Delete Department using Fetch API
function deleteDepartment(departmentId) {
    if (confirm("Are you sure you want to delete this department?")) {
        fetch('delete_department.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ departmentId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                location.reload(); // Reload the page to reflect deletion
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
}

