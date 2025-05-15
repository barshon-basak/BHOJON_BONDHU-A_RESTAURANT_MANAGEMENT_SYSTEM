</main>
<footer class="bg-gradient-to-r from-primary to-primary-dark mt-auto text-white">
    <div class="max-w-7xl mx-auto px-6 py-8 grid grid-cols-1 md:grid-cols-4 gap-8">
        <div>
            <h3 class="text-xl font-bold mb-4">About BHOJON Bondhu</h3>
            <p class="text-gray-200 max-w-sm">
                BHOJON Bondhu is your trusted food ordering platform, delivering delicious meals from your favorite restaurants right to your doorstep.
            </p>
        </div>
        <div>
            <h3 class="text-xl font-bold mb-4">Contact Us</h3>
            <ul class="text-gray-200 space-y-2">
                <li>Email: barshon.basak@northsouth.edu</li>
                <li>Phone: +8801712341234</li>
                <li>Address: North South University.</li>
            </ul>
        </div>
        <div>
            <h3 class="text-xl font-bold mb-4">Follow Us</h3>
            <div class="flex space-x-6 text-2xl">
                <a href="https://www.facebook.com/barshonbasak123" class="hover:text-yellow-300" aria-label="Facebook">
                    <i class="fab fa-facebook"></i>
                </a>
                <a href="https://www.facebook.com/faizurzunayed" class="hover:text-yellow-300" aria-label="Facebook">
                    <i class="fab fa-facebook"></i>
                </a>
                <a href="https://www.instagram.com/barshon_18?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw==" class="hover:text-yellow-300" aria-label="Instagram">
                    <i class="fab fa-instagram"></i>
                </a>
                <a href="https://www.instagram.com/z_zunayed/?utm_source=ig_web_button_share_sheet" class="hover:text-yellow-300" aria-label="Instagram">
                    <i class="fab fa-instagram"></i>
                </a>
            </div>
        </div>
        
    </div>
    <div class="border-t border-yellow-400 mt-8 pt-4 text-center text-yellow-300">
        &copy; <?php echo date('Y'); ?> BHOJON BONDHU. All rights reserved to Barshon & Zunayed.
    </div>
</footer>

<script>
    // Auto-hide flash messages after 5 seconds
    const flashMessage = document.querySelector('.flash-message');
    if (flashMessage) {
        setTimeout(() => {
            flashMessage.style.opacity = '0';
            setTimeout(() => {
                flashMessage.remove();
            }, 300);
        }, 5000);
    }
</script>
</body>
</html>
