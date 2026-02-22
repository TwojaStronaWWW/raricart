<footer>
        <div class="footer-content">
            <!-- 1. Brand -->
            <div class="footer-section">
                <h3>Raricart</h3>
                <p>Live Food Station</p>
                <p data-i18n="footer.desc">Mobilne stacje degustacyjne na eventy w całej Polsce.</p>
            </div>

            <!-- 2. Quick Links (Moved 2nd) -->
            <div class="footer-section">
                <h3 data-i18n="footer.quick_links">Szybkie Linki</h3>
                <nav class="footer-links">
                    <a href="#onas" data-i18n="nav.about">O Nas</a>
                    <a href="#oferta" data-i18n="nav.offer">Oferta</a>
                    <a href="#realizacje" data-i18n="nav.gallery">Realizacje</a>
                    <a href="#kontakt" data-i18n="nav.contact">Kontakt</a>
                </nav>
            </div>

            <!-- 3. Contact (Moved 3rd) -->
            <div class="footer-section">
                <h3 data-i18n="nav.contact">Kontakt</h3>
                <div class="contact-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                        class="contact-icon">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"
                            stroke="currentColor" stroke-width="2" stroke-linejoin="round" />
                        <polyline points="22,6 12,13 2,6" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                    <a href="mailto:kontakt@raricart.pl">kontakt@raricart.pl</a>
                </div>
                <div class="contact-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                        class="contact-icon">
                        <path
                            d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.12 2h3a2 2 0 0 1 2 1.72 12.05 12.05 0 0 0 .57 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.05 12.05 0 0 0 2.81.57A2 2 0 0 1 22 16.92z"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <a href="tel:+48883392688" class="phone-link">+48 883 392 688</a>
                </div>
            </div>

            <!-- 4. Media (Labels added) -->
            <div class="footer-section">
                <h3>Media</h3>
                <div class="footer-socials vertical">
                    <a href="https://www.instagram.com/raricart.pl" target="_blank" aria-label="Instagram">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                            class="social-icon">
                            <rect x="2" y="2" width="20" height="20" rx="5" ry="5" stroke="currentColor"
                                stroke-width="2"></rect>
                            <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z" stroke="currentColor"
                                stroke-width="2"></path>
                            <line x1="17.5" y1="6.5" x2="17.51" y2="6.5" stroke="currentColor" stroke-width="2"></line>
                        </svg>
                        <span>Instagram</span>
                    </a>
                    <a href="https://www.facebook.com/people/Raricart/61585951812916/" target="_blank"
                        aria-label="Facebook">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                            class="social-icon">
                            <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            </path>
                        </svg>
                        <span>Facebook</span>
                    </a>
                </div>
            </div>
        </div>



    <!-- Footer Bottom -->
        <div class="footer-bottom" style="text-align: center; padding: 2rem 0; border-top: 1px solid rgba(0,0,0,0.05); font-size: 0.85rem; color: #666; background: var(--color-bg); position: relative; z-index: 25;">
            <div class="container">
                <p>&copy; <?php echo date('Y'); ?> Raricart. Wszystkie prawa zastrzeżone.</p>
                <p><a href="polityka-prywatnosci.php" style="color: inherit; text-decoration: underline;">Polityka Prywatności</a></p>
                <p style="margin-top: 1rem; opacity: 0.8;">Stworzona przez <a href="https://twojastronawww.pl/" target="_blank" style="color: inherit; font-weight: bold;">TwojaStronaWWW</a></p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="/assets/js/script.js?v=<?php echo time(); ?>" defer></script>
</body>
</html>
