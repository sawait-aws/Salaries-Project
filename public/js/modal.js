document.addEventListener('DOMContentLoaded', function() {
    // Show the modal if there is an unauthorized access error
    if (document.getElementById('unauthorizedModal')) {
        showModal();
    }
});

function showModal() {
    document.getElementById('unauthorizedModal').style.display = 'flex';
}
function closeModal() {
    document.getElementById('unauthorizedModal').style.display = 'none';
}


