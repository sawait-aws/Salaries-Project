document.addEventListener('DOMContentLoaded', function () {


    // Close unauthorized modal event listener
    document.querySelectorAll('.close-modal').forEach(button => {
        button.addEventListener('click', closeModal);
    });

    // Load salary details for employee
window.loadSalaryDetails = function (salaryId) {
    fetch(`/employee/load-salary/${salaryId}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert('Unauthorized');
            } else {
                const salaryCardHTML = `
                    <h3 class="salary-title">Salary Details (${data.year}-${data.month})</h3>
                    <div class="salary-card">
                        <div class="salary-item">
                            <h4>Gross Salary</h4>
                            <p>${data.gross_salary}</p>
                        </div>
                        <div class="salary-item">
                            <h4>Commission</h4>
                            <p>${data.commission}</p>
                        </div>
                        <div class="salary-item">
                            <h4>Salaf</h4>
                            <p>${data.salaf}</p>
                        </div>
                        <div class="salary-item">
                            <h4>Salaf Deducted</h4>
                            <p>${data.salaf_deducted}</p>
                        </div>
                        <div class="salary-item">
                            <h4>Working Days</h4>
                            <p>${data.working_days }</p>
                        </div>
                        <div class="salary-item">
                            <h4>Unpaid Days</h4>
                            <p>${data.unpaid_days }</p>
                        </div>
                        <div class="salary-item">
                            <h4>Sick Leave</h4>
                            <p>${data.sick_leave }</p>
                        </div>
                        <div class="salary-item">
                            <h4>Deduction</h4>
                            <p>${data.deduction }</p>
                        </div>
                        <div class="salary-item">
                            <h4>Bonus</h4>
                            <p>${data.bonus }</p>
                        </div>
                        <div class="salary-item">
                            <h4>Salary to Be Paid</h4>
                            <p>${data.salary_to_be_paid}</p>
                        </div>
                    </div>
                `;
                document.querySelector('.salary-section').innerHTML = salaryCardHTML;
            }
        })
        .catch(error => console.error('Error loading salary details:', error));
}


    // Employee salary box click event listener
    document.querySelectorAll('.list-box').forEach(box => {
        box.addEventListener('click', function () {
            const salaryId = this.getAttribute('data-salary-id');
            loadSalaryDetails(salaryId);
        });
    });
});
