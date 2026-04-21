    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-text">
                <p>&copy; <?php echo date('Y'); ?> <strong>SIGUDHANG</strong>. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Custom Delete Modal -->
    <div class="modal-overlay" id="delete-modal-overlay">
        <div class="delete-modal">
            <div class="modal-header-custom">
                <div class="modal-icon-alert">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h3>Hapus Data</h3>
            </div>
            <div class="modal-body-custom">
                <p>Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.</p>
            </div>
            <div class="modal-footer-custom">
                <button type="button" class="btn-modal-cancel" id="btn-cancel-delete">Batal</button>
                <a href="#" class="btn-modal-confirm" id="btn-confirm-delete">Ya, Hapus</a>
            </div>
        </div>
    </div>

    <script>
    // Delete Modal Logic
    const deleteModal = document.getElementById('delete-modal-overlay');
    const btnCancelDelete = document.getElementById('btn-cancel-delete');
    const btnConfirmDelete = document.getElementById('btn-confirm-delete');

    function openDeleteModal(id) {
        btnConfirmDelete.href = 'hapus.php?id=' + id;
        deleteModal.classList.add('active');
    }

    if (btnCancelDelete) {
        btnCancelDelete.addEventListener('click', () => {
            deleteModal.classList.remove('active');
        });
    }

    // Close modal on click outside
    window.addEventListener('click', (e) => {
        if (e.target === deleteModal) {
            deleteModal.classList.remove('active');
        }
    });

    // Sidebar Mobile Toggle
    const mobileToggle = document.getElementById('mobile-toggle');
    const sidebar = document.querySelector('.sidebar');
    
    if (mobileToggle) {
        mobileToggle.addEventListener('click', () => {
            sidebar.classList.toggle('active');
        });
    }

    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', (e) => {
        if (window.innerWidth <= 1024) {
            if (!sidebar.contains(e.target) && !mobileToggle.contains(e.target) && sidebar.classList.contains('active')) {
                sidebar.classList.remove('active');
            }
        }
    });

    // Update Time
    function updateTime() {
        const now = new Date();
        const timeElement = document.getElementById('current-time');
        if (timeElement) {
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            timeElement.textContent = hours + ':' + minutes;
        }
    }
    
    setInterval(updateTime, 60000);
    updateTime();

    // Auto-hide notifications
    setTimeout(function() {
        const notifications = document.querySelectorAll('.notification'); 
        notifications.forEach(notification => {
            notification.style.transition = "all 0.5s ease";
            notification.style.opacity = '0'; 
            notification.style.transform = 'translateY(-20px)';
            
            setTimeout(() => {
                notification.style.display = 'none';
            }, 500); 
        });
    }, 5000);
    </script>
</div> <!-- Close main-wrapper -->
</div> <!-- Close container -->
</body>
</html>
