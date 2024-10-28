document.getElementById('addVacationForm').addEventListener('submit', function(e) {
    e.preventDefault(); // Prevent the form from submitting the traditional way

    // Gather form data
    let VacationData = {
        employeeId: document.getElementById('employeeId').value,
        numberOfDays: document.getElementById('numberOfDays').value,
        startDate: document.getElementById('startDate').value,
        endDate: document.getElementById('endDate').value,
        vacationType: document.getElementById('vacationType').value
    };

    // Send the data using Fetch API
    fetch('add_vacation.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(VacationData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            location.reload(); // Reload the page to show the new Vacation in the list
        } else {
            // Display error message
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
});
