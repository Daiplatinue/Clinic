document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('updateModal');
    const modalClose = modal.querySelector('.modal-close');
    const updateForm = document.getElementById('updateForm');

    window.showUpdateModal = function(checkupId) {
        document.getElementById('checkup_id').value = checkupId;
        modal.classList.remove('hidden');
    };

    modalClose.addEventListener('click', function() {
        modal.classList.add('hidden');
    });

    updateForm.addEventListener('submit', async function(e) {
        e.preventDefault();

        const formData = new FormData(updateForm);

        try {
            const response = await fetch('../doctor/process-checkup.php', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                alert('Checkup updated successfully!');
                modal.classList.add('hidden');
                location.reload(); 
            } else {
                alert('Error: ' + data.message);
            }
        } catch (error) {
            alert('An error occurred while updating the checkup');
            console.error('Error:', error);
        }
    });
});