    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-text">
                <p>&copy; <?php echo date('Y'); ?> <strong>SIGUDHANG</strong>. All rights reserved.</p>
            </div>
            <div class="footer-info">
                Version 1.0.0 | Built with <i class="fas fa-heart"></i>
            </div>
        </div>
    </footer>

    <script>
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
