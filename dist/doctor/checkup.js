document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('updateModal');
    const modalClose = modal.querySelector('.modal-close');
    const updateForm = document.getElementById('updateForm');

    // Function to start checkup
    window.startCheckup = async function(checkupId, button) {
        try {
            const formData = new FormData();
            formData.append('checkup_id', checkupId);

            const response = await fetch('start-checkup.php', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                // Show success message
                const successAlert = document.createElement('div');
                successAlert.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-fade-in';
                successAlert.innerHTML = '<i class="fas fa-check-circle mr-2"></i>Check-up started successfully!';
                document.body.appendChild(successAlert);

                // Remove success message after 3 seconds
                setTimeout(() => {
                    successAlert.remove();
                }, 3000);

                // Change button to "Update Status"
                button.innerHTML = '<i class="fas fa-edit mr-2"></i>Update Status';
                button.onclick = () => showUpdateModal(checkupId);
                button.className = 'bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200';
            } else {
                throw new Error(data.message);
            }
        } catch (error) {
            // Show error message
            const errorAlert = document.createElement('div');
            errorAlert.className = 'fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-fade-in';
            errorAlert.innerHTML = `<i class="fas fa-exclamation-circle mr-2"></i>${error.message || 'An error occurred'}`;
            document.body.appendChild(errorAlert);

            // Remove error message after 3 seconds
            setTimeout(() => {
                errorAlert.remove();
            }, 3000);
        }
    };

    // Show modal function
    window.showUpdateModal = function(checkupId) {
        document.getElementById('checkup_id').value = checkupId;
        modal.classList.remove('hidden');
    };

    // Close modal when clicking the close button
    modalClose.addEventListener('click', function() {
        modal.classList.add('hidden');
    });

    // Close modal when clicking outside
    window.addEventListener('click', function(event) {
        if (event.target === modal) {
            modal.classList.add('hidden');
        }
    });

    // Handle form submission
    updateForm.addEventListener('submit', async function(e) {
        e.preventDefault();

        const submitButton = updateForm.querySelector('button[type="submit"]');
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Updating...';

        try {
            const formData = new FormData(updateForm);
            const response = await fetch('process-checkup.php', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                // Show success message
                const successAlert = document.createElement('div');
                successAlert.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-fade-in';
                successAlert.innerHTML = '<i class="fas fa-check-circle mr-2"></i>Check-up updated successfully!';
                document.body.appendChild(successAlert);

                // Remove success message after 3 seconds
                setTimeout(() => {
                    successAlert.remove();
                }, 3000);

                // Close modal and reload page
                modal.classList.add('hidden');
                location.reload();
            } else {
                throw new Error(data.message);
            }
        } catch (error) {
            // Show error message
            const errorAlert = document.createElement('div');
            errorAlert.className = 'fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-fade-in';
            errorAlert.innerHTML = `<i class="fas fa-exclamation-circle mr-2"></i>${error.message || 'An error occurred'}`;
            document.body.appendChild(errorAlert);

            // Remove error message after 3 seconds
            setTimeout(() => {
                errorAlert.remove();
            }, 3000);
        } finally {
            submitButton.disabled = false;
            submitButton.innerHTML = 'Update Status';
        }
    });
}); 