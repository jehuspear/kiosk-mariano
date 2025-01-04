document.addEventListener('DOMContentLoaded', function() {
    // Load staff data when page loads
    loadStaffData();

    // Add event listeners
    document.getElementById('searchStaff').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        filterStaffTable(searchTerm);
    });

    document.getElementById('saveStaffBtn').addEventListener('click', addStaff);
    document.getElementById('updateStaffBtn').addEventListener('click', updateStaff);
    document.getElementById('confirmDeleteBtn').addEventListener('click', deleteStaff);
});

function loadStaffData() {
    fetch('get_staff_details.php')
        .then(response => response.json())
        .then(data => {
            const tableBody = document.getElementById('staffTableBody');
            tableBody.innerHTML = '';

            data.forEach(staff => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${staff.Staff_Email}</td>
                    <td>${staff.Staff_FirstName} ${staff.Staff_MiddleName} ${staff.Staff_LastName}</td>
                    <td>${staff.Staff_Role}</td>
                    <td><span class="badge ${staff.Staff_Status === 'Active' ? 'bg-success' : 'bg-danger'}">${staff.Staff_Status}</span></td>
                    <td>
                        <button class="btn btn-sm btn-primary" onclick="editStaff(${staff.Staff_ID})">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="showDeleteModal(${staff.Staff_ID})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                `;
                tableBody.appendChild(row);
            });
        })
        .catch(error => console.error('Error loading staff data:', error));
}

function filterStaffTable(searchTerm) {
    const rows = document.getElementById('staffTableBody').getElementsByTagName('tr');
    
    for (let row of rows) {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    }
}

function addStaff() {
    const form = document.getElementById('addStaffForm');
    const formData = new FormData(form);

    fetch('add_staff.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadStaffData();
            bootstrap.Modal.getInstance(document.getElementById('addStaffModal')).hide();
            form.reset();
        } else {
            alert(data.message || 'Error adding staff');
        }
    })
    .catch(error => console.error('Error:', error));
}

function editStaff(staffId) {
    fetch(`get_staff_details.php?id=${staffId}`)
        .then(response => response.json())
        .then(staff => {
            const form = document.getElementById('editStaffForm');
            form.staffId.value = staff.Staff_ID;
            form.firstName.value = staff.Staff_FirstName;
            form.middleName.value = staff.Staff_MiddleName;
            form.lastName.value = staff.Staff_LastName;
            form.email.value = staff.Staff_Email;
            form.contactNumber.value = staff.Staff_ContactNumber;
            form.address.value = staff.Staff_Address;
            form.birthDate.value = staff.Staff_BirthDate;
            form.role.value = staff.Staff_Role;
            form.status.value = staff.Staff_Status;

            new bootstrap.Modal(document.getElementById('editStaffModal')).show();
        })
        .catch(error => console.error('Error:', error));
}

function updateStaff() {
    const form = document.getElementById('editStaffForm');
    const formData = new FormData(form);

    fetch('update_staff.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadStaffData();
            bootstrap.Modal.getInstance(document.getElementById('editStaffModal')).hide();
        } else {
            alert(data.message || 'Error updating staff');
        }
    })
    .catch(error => console.error('Error:', error));
}

function showDeleteModal(staffId) {
    document.getElementById('deleteStaffId').value = staffId;
    new bootstrap.Modal(document.getElementById('deleteStaffModal')).show();
}

function deleteStaff() {
    const staffId = document.getElementById('deleteStaffId').value;
    
    fetch('delete_staff.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `id=${staffId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadStaffData();
            bootstrap.Modal.getInstance(document.getElementById('deleteStaffModal')).hide();
        } else {
            alert(data.message || 'Error deleting staff');
        }
    })
    .catch(error => console.error('Error:', error));
}
