document.getElementById('addBranchForm').addEventListener('submit', function(e) {
    e.preventDefault(); // Prevent the form from submitting the traditional way

    // Gather form data
    let BranchData = {
        BranchId: document.getElementById('BranchId').value,
        BranchName: document.getElementById('BranchName').value
    };

    // Send the data using Fetch API
    fetch('add_branch.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(BranchData)
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

// Populate the Update Form with the selected Branch's data
function populateUpdateForm(BranchId, BranchName) {
    document.getElementById('updateBranchId').value = BranchId;
    document.getElementById('updateBranchName').value = BranchName;
}

// Update Branch using Fetch API
function updateBranch() {
    let BranchData = {
        BranchId: document.getElementById('updateBranchId').value,
        BranchName: document.getElementById('updateBranchName').value
    };

    fetch('update_branch.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(BranchData)
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

// Delete Branch using Fetch API
function deleteBranch(BranchId) {
    if (confirm("Are you sure you want to delete this Branch?")) {
        fetch('delete_branch.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ BranchId })
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


