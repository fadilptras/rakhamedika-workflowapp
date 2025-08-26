<script>
    document.addEventListener('DOMContentLoaded', function () {
        const addModal = document.getElementById('add-modal');
        const editModal = document.getElementById('edit-modal');
        
        // Logika untuk membuka modal
        document.getElementById('open-add-modal-btn').addEventListener('click', () => {
            addModal.classList.remove('hidden');
        });

        document.querySelectorAll('.open-edit-modal-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const user = JSON.parse(btn.dataset.user);
                const form = document.getElementById('edit-form');
                
                form.action = `{{ url('admin/users') }}/${user.id}`;
                
                form.querySelector('#edit-name').value = user.name;
                form.querySelector('#edit-email').value = user.email;
                form.querySelector('#edit-role').value = user.role;
                form.querySelector('#edit-jabatan').value = user.jabatan || '';
                
                if (user.tanggal_bergabung) {
                    form.querySelector('#edit-tanggal_bergabung').value = user.tanggal_bergabung.split('T')[0];
                } else {
                    form.querySelector('#edit-tanggal_bergabung').value = '';
                }
                
                const imagePreview = form.querySelector('#edit-image-preview');
                if (user.profile_picture) {
                    imagePreview.src = `{{ asset('storage') }}/${user.profile_picture}`;
                    imagePreview.classList.remove('hidden');
                } else {
                    imagePreview.src = '';
                    imagePreview.classList.add('hidden');
                }
                
                editModal.classList.remove('hidden');
            });
        });

        // Logika untuk menutup semua modal
        function closeModal() {
            document.querySelector('#add-modal form').reset();
            document.getElementById('add-image-preview').classList.add('hidden');
            document.querySelector('#edit-modal form').reset();
            document.getElementById('edit-image-preview').classList.add('hidden');
            
            addModal.classList.add('hidden');
            editModal.classList.add('hidden');
        }

        document.querySelectorAll('.close-modal-btn').forEach(btn => {
            btn.addEventListener('click', closeModal);
        });

        window.addEventListener('click', (event) => {
            if (event.target == addModal || event.target == editModal) {
                closeModal();
            }
        });
        
        // Logika untuk pratinjau gambar saat di-upload
        function setupImagePreview(inputId, previewId) {
            const input = document.getElementById(inputId);
            const preview = document.getElementById(previewId);

            input.addEventListener('change', function(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.src = e.target.result;
                        preview.classList.remove('hidden');
                    }
                    reader.readAsDataURL(file);
                }
            });
        }

        setupImagePreview('add-profile_picture', 'add-image-preview');
        setupImagePreview('edit-profile_picture', 'edit-image-preview');
    });
</script>