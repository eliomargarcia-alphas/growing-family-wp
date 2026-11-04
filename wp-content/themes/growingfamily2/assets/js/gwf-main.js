/* ==========================================================================
   GROWING FAMILY - INTERACTIVE ACTIONS JS
   Antes estaba en línea en footer.php. Se carga con defer desde functions.php
   (gwf_enqueue_main_script). Cada módulo solo se activa si sus elementos
   existen en la página.
   ========================================================================== */

(function () {
    'use strict';

    var DESKTOP_MIN = 1024; // > 1024px = escritorio

    function isDesktop() {
        return window.innerWidth > DESKTOP_MIN;
    }

    /* ==========================================================================
       1. MOBILE HAMBURGER MENU DRAWER
       ========================================================================== */
    function initMobileMenu() {
        var hamburgerBtn = document.getElementById('hamburger-btn');
        var mobileDrawer = document.getElementById('mobile-drawer');
        if (!hamburgerBtn || !mobileDrawer) return;

        function setOpen(open) {
            hamburgerBtn.classList.toggle('active', open);
            mobileDrawer.classList.toggle('open', open);
            hamburgerBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
            hamburgerBtn.setAttribute('aria-label', open ? 'Cerrar menú' : 'Abrir menú');
            lockScroll(open); // La página de detrás no se mueve: solo hace scroll el panel
            if (open) {
                var first = mobileDrawer.querySelector('a');
                if (first) first.focus({ preventScroll: true });
            }
        }
        function isOpen() { return mobileDrawer.classList.contains('open'); }

        hamburgerBtn.addEventListener('click', function () { setOpen(!isOpen()); });

        // Grupos (Acerca de, Guías, Blog, Legal): el padre abre y cierra sus hijos; abrir uno cierra los demás
        var groups = mobileDrawer.querySelectorAll('.drawer-list > .menu-item-has-children');
        groups.forEach(function (item, n) {
            var toggle = item.querySelector(':scope > a');
            var sub = item.querySelector(':scope > .sub-menu');
            if (!toggle || !sub) return;
            sub.id = sub.id || 'gwf-drawer-sub-' + n;
            toggle.setAttribute('role', 'button');
            toggle.setAttribute('aria-expanded', 'false');
            toggle.setAttribute('aria-controls', sub.id);
            toggle.addEventListener('click', function (e) {
                e.preventDefault();
                var open = !item.classList.contains('is-open');
                groups.forEach(function (other) {
                    other.classList.remove('is-open');
                    var t = other.querySelector(':scope > a');
                    if (t) t.setAttribute('aria-expanded', 'false');
                });
                item.classList.toggle('is-open', open);
                toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            });
            // El grupo de la página actual empieza abierto
            if (item.classList.contains('current-menu-ancestor') || item.classList.contains('current-menu-parent')) {
                item.classList.add('is-open');
                toggle.setAttribute('aria-expanded', 'true');
            }
        });

        // Cierra el menú al pulsar un enlace de verdad (no los que abren un grupo)
        mobileDrawer.addEventListener('click', function (e) {
            var link = e.target.closest('a');
            if (link && link.getAttribute('role') !== 'button') setOpen(false);
        });

        // Esc cierra y devuelve el foco al botón; Tab no sale del menú mientras está abierto
        document.addEventListener('keydown', function (e) {
            if (!isOpen()) return;
            if (e.key === 'Escape') {
                setOpen(false);
                hamburgerBtn.focus();
            } else if (e.key === 'Tab') {
                var items = [hamburgerBtn].concat(Array.prototype.slice.call(mobileDrawer.querySelectorAll('a')));
                var i = items.indexOf(document.activeElement);
                if (e.shiftKey && i <= 0) { e.preventDefault(); items[items.length - 1].focus(); }
                else if (!e.shiftKey && i === items.length - 1) { e.preventDefault(); items[0].focus(); }
            }
        });

        // Al pasar a escritorio con el menú abierto, se cierra
        window.addEventListener('resize', function () { if (isOpen() && isDesktop()) setOpen(false); });

        window.gwfCloseMenu = function () { if (isOpen()) setOpen(false); };
    }

    /* Bloqueo del scroll de la página (también en iPhone, donde overflow: hidden no basta) */
    var lockedY = 0;
    var lockCount = 0;
    function lockScroll(lock) {
        var body = document.body;
        if (lock) {
            if (lockCount++ > 0) return;
            lockedY = window.scrollY;
            document.documentElement.classList.add('gwf-menu-open');
            body.style.position = 'fixed';
            body.style.top = -lockedY + 'px';
            body.style.left = '0';
            body.style.right = '0';
        } else {
            if (lockCount === 0 || --lockCount > 0) return;
            document.documentElement.classList.remove('gwf-menu-open');
            body.style.position = '';
            body.style.top = '';
            body.style.left = '';
            body.style.right = '';
            window.scrollTo(0, lockedY);
        }
    }

    /* ==========================================================================
       1b. BÚSQUEDA: la lupa (cabecera en móvil, final del menú en escritorio) abre la ventana de búsqueda
       ========================================================================== */
    function initSearch() {
        var dialog = document.getElementById('gwf-search');
        if (!dialog || typeof dialog.showModal !== 'function') return;
        var input = dialog.querySelector('input[type="search"]');
        var opener = null;

        document.querySelectorAll('[data-gwf-search]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                opener = btn;
                if (window.gwfCloseMenu) window.gwfCloseMenu();
                dialog.showModal();
                lockScroll(true);
                input.focus();
            });
        });
        dialog.querySelector('[data-gwf-search-close]').addEventListener('click', function () { dialog.close(); });
        // Pulsar fuera de la ventana la cierra
        dialog.addEventListener('click', function (e) {
            var r = dialog.getBoundingClientRect();
            if (e.target === dialog && (e.clientX < r.left || e.clientX > r.right || e.clientY < r.top || e.clientY > r.bottom)) dialog.close();
        });
        dialog.addEventListener('close', function () {
            lockScroll(false);
            if (opener) opener.focus();
        });
    }

    /* ==========================================================================
       2. SCROLL REVEAL ANIMATIONS (IntersectionObserver)
       ========================================================================== */
    function initReveal() {
        var revealItems = document.querySelectorAll('.reveal-item');
        if (!revealItems.length) return;

        if (!('IntersectionObserver' in window)) {
            revealItems.forEach(function (item) { item.classList.add('reveal-visible'); });
            return;
        }

        var revealObserver = new IntersectionObserver(function (entries, observer) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('reveal-visible');
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.15,
            rootMargin: '0px 0px -50px 0px'
        });

        revealItems.forEach(function (item) { revealObserver.observe(item); });
    }

    /* ==========================================================================
       3. INTERACTIVE STEPS ("¿Cómo funciona en la práctica?")
       ========================================================================== */
    function initSteps() {
        var stepItems = document.querySelectorAll('.step-item');
        if (!stepItems.length) return;

        var desktopCard = document.getElementById('how-desktop-card');
        var desktopNum = document.getElementById('how-desktop-num');
        var desktopTitle = document.getElementById('how-desktop-title');
        var desktopDesc = document.getElementById('how-desktop-desc');
        var desktopMockup = document.getElementById('how-desktop-mockup');

        function text(item, selector) {
            var node = item.querySelector(selector);
            return node ? node.textContent.trim() : '';
        }

        stepItems.forEach(function (item) {
            var header = item.querySelector('.step-header');
            if (!header) return;

            header.addEventListener('click', function () {
                var isActive = item.classList.contains('active');

                // 1. Cerrar todos los acordeones
                stepItems.forEach(function (step) {
                    step.classList.remove('active');
                    var body = step.querySelector('.step-body');
                    if (body) body.style.maxHeight = null;
                });

                // Si ya estaba activo en móvil y vuelve a hacer clic, se colapsa
                if (isActive && !isDesktop()) return;

                // 2. Activar el elemento seleccionado
                item.classList.add('active');

                // Acordeón móvil: altura del texto + imagen
                var activeBody = item.querySelector('.step-body');
                if (activeBody && !isDesktop()) {
                    activeBody.style.maxHeight = (activeBody.scrollHeight + 40) + 'px';
                }

                // 3. Panel de escritorio
                if (desktopCard && isDesktop()) {
                    var num = text(item, '.step-num');
                    var title = text(item, '.step-title');
                    var desc = text(item, '.step-desc');
                    var imgSrc = item.getAttribute('data-img');

                    desktopCard.style.opacity = '0';
                    desktopCard.style.transform = 'translateY(10px)';

                    setTimeout(function () {
                        if (desktopNum) desktopNum.textContent = num;
                        if (desktopTitle) desktopTitle.textContent = title;
                        if (desktopDesc) desktopDesc.textContent = desc;
                        if (desktopMockup && imgSrc) desktopMockup.src = imgSrc;

                        desktopCard.style.opacity = '1';
                        desktopCard.style.transform = 'translateY(0)';
                    }, 200);
                }
            });
        });

        // Ajuste de alturas al cambiar el tamaño de la ventana
        function initAccordions() {
            if (!isDesktop()) {
                var activeStep = document.querySelector('.step-item.active');
                var activeBody = activeStep && activeStep.querySelector('.step-body');
                if (activeBody) activeBody.style.maxHeight = (activeBody.scrollHeight + 40) + 'px';
            } else {
                stepItems.forEach(function (step) {
                    var body = step.querySelector('.step-body');
                    if (body) body.style.maxHeight = null;
                });
            }
        }

        initAccordions();
        window.addEventListener('resize', initAccordions);
    }

    /* ==========================================================================
       4. CHARACTER MOUSE PARALLAX EFFECT (solo escritorio)
       ========================================================================== */
    function initParallax() {
        var parallaxScene = document.getElementById('parallax-scene');
        var chars = document.querySelectorAll('.parallax-char');
        if (!parallaxScene || !chars.length || window.innerWidth <= 768) return;

        // Se escucha la sección padre para limitar el área de activación
        var parentSection = parallaxScene.closest('.section-parallax');
        if (!parentSection) return;

        parentSection.addEventListener('mousemove', function (e) {
            var rect = parentSection.getBoundingClientRect();
            // Posición normalizada respecto al centro de la sección (-1 a 1)
            var normX = (e.clientX - rect.left - rect.width / 2) / (rect.width / 2);
            var normY = (e.clientY - rect.top - rect.height / 2) / (rect.height / 2);

            chars.forEach(function (char) {
                var speed = parseFloat(char.getAttribute('data-speed')) || 1;
                char.style.transform = 'translate(' + (normX * 25 * speed) + 'px, ' + (normY * 15 * speed) + 'px)';
            });
        });

        parentSection.addEventListener('mouseleave', function () {
            chars.forEach(function (char) {
                char.style.transform = 'translate(0px, 0px)';
            });
        });
    }

    /* ==========================================================================
       5. TESTIMONIAL SLIDER CAROUSEL (arrastrable, autoplay)
       ========================================================================== */
    function initTestimonialSlider() {
        var sliderTrack = document.getElementById('slider-track');
        var slides = document.querySelectorAll('.slide-item');
        if (!sliderTrack || !slides.length) return;

        var prevBtn = document.getElementById('prev-slide');
        var nextBtn = document.getElementById('next-slide');
        var dotsContainer = document.getElementById('slider-dots');
        var dots = dotsContainer ? dotsContainer.querySelectorAll('.dot') : [];
        var totalSlides = slides.length;
        var currentSlide = 0;
        var autoplayTimer = null;
        var GAP = 32; // gap de 2rem entre tarjetas en escritorio

        function getMaxIndex() {
            // En escritorio hay 3 visibles: el índice máximo navegable es total - 3
            return isDesktop() ? Math.max(0, totalSlides - 3) : totalSlides - 1;
        }

        function desktopOffset() {
            return (slides[0].getBoundingClientRect().width + GAP) * currentSlide;
        }

        function updateSliderPosition() {
            var maxIndex = getMaxIndex();
            if (currentSlide > maxIndex) currentSlide = maxIndex;

            sliderTrack.style.transform = isDesktop()
                ? 'translateX(-' + desktopOffset() + 'px)'
                : 'translateX(-' + (currentSlide * 100) + '%)';

            dots.forEach(function (dot, index) {
                dot.classList.toggle('active', index === currentSlide);
                // Oculta los dots inalcanzables en escritorio
                dot.style.display = (isDesktop() && index > maxIndex) ? 'none' : 'inline-block';
            });
        }

        function showNextSlide() {
            currentSlide = currentSlide >= getMaxIndex() ? 0 : currentSlide + 1;
            updateSliderPosition();
        }

        function showPrevSlide() {
            currentSlide = currentSlide <= 0 ? getMaxIndex() : currentSlide - 1;
            updateSliderPosition();
        }

        function startAutoplay() {
            if (autoplayTimer) clearInterval(autoplayTimer);
            autoplayTimer = setInterval(showNextSlide, 6000);
        }

        function resetAutoplay() {
            clearInterval(autoplayTimer);
            autoplayTimer = null;
            startAutoplay();
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', function () {
                showNextSlide();
                resetAutoplay();
            });
        }

        if (prevBtn) {
            prevBtn.addEventListener('click', function () {
                showPrevSlide();
                resetAutoplay();
            });
        }

        dots.forEach(function (dot) {
            dot.addEventListener('click', function (e) {
                currentSlide = parseInt(e.target.getAttribute('data-index'), 10) || 0;
                updateSliderPosition();
                resetAutoplay();
            });
        });

        // Arrastre con ratón / táctil
        var startX = 0;
        var diffX = 0;
        var isDragging = false;

        sliderTrack.addEventListener('pointerdown', function (e) {
            isDragging = true;
            startX = e.clientX;
            diffX = 0;
            sliderTrack.style.transition = 'none';
            resetAutoplay();
        });

        sliderTrack.addEventListener('pointermove', function (e) {
            if (!isDragging) return;
            diffX = e.clientX - startX;

            if (Math.abs(diffX) > 5 && e.cancelable) {
                e.preventDefault();
            }

            var currentOffset = isDesktop()
                ? -desktopOffset()
                : -currentSlide * sliderTrack.offsetWidth;

            sliderTrack.style.transform = 'translateX(' + (currentOffset + diffX) + 'px)';
        });

        function endSwipe() {
            if (!isDragging) return;
            isDragging = false;
            sliderTrack.style.transition = 'transform 0.4s cubic-bezier(0.25, 1, 0.5, 1)';

            var threshold = 50; // sensibilidad de arrastre
            if (diffX < -threshold && currentSlide < getMaxIndex()) {
                currentSlide++;
            } else if (diffX > threshold && currentSlide > 0) {
                currentSlide--;
            }

            diffX = 0;
            updateSliderPosition();
        }

        sliderTrack.addEventListener('pointerup', endSwipe);
        sliderTrack.addEventListener('pointerleave', endSwipe);
        sliderTrack.addEventListener('pointercancel', endSwipe);

        var testimonialsSection = document.querySelector('.section-testimonials');
        if (testimonialsSection) {
            testimonialsSection.addEventListener('mouseenter', function () {
                if (autoplayTimer) clearInterval(autoplayTimer);
            });
            testimonialsSection.addEventListener('mouseleave', startAutoplay);
        }

        window.addEventListener('resize', updateSliderPosition);

        startAutoplay();
    }

    /* ==========================================================================
       6. FAQ ACCORDION COLLAPSE/EXPAND
       ========================================================================== */
    function initFaq() {
        var faqItems = document.querySelectorAll('.faq-item');
        if (!faqItems.length) return;

        faqItems.forEach(function (item) {
            var header = item.querySelector('.faq-header');
            var body = item.querySelector('.faq-body');
            if (!header || !body) return;

            header.addEventListener('click', function () {
                var isActive = item.classList.contains('active');

                // Cierra los demás
                faqItems.forEach(function (otherItem) {
                    otherItem.classList.remove('active');
                    var otherBody = otherItem.querySelector('.faq-body');
                    if (otherBody) otherBody.style.maxHeight = null;
                });

                if (!isActive) {
                    item.classList.add('active');
                    body.style.maxHeight = body.scrollHeight + 'px';
                }
            });
        });
    }

    /* ==========================================================================
       7. SCROLL: índice activo (scrollspy) y elementos fijos que suben
       Solo para plantillas de artículo con índice (Easy TOC) o .sidebar-sticky.
       Antes eran dos <script> separados en footer.php; uno lanzaba un error en
       cada scroll (getAttribute('href') en <li> del menú) y otro nunca quitaba
       .up-50 por usar la variable equivocada.
       ========================================================================== */
    function initScrollEffects() {
        var SCROLL_OFFSET = 400;

        // Scrollspy: solo enlaces que apuntan a un ancla (#id)
        var spyLinks = Array.prototype.filter.call(
            document.querySelectorAll('.simple-list-scrollspy .nav-link, .ez-toc-link'),
            function (link) {
                var href = link.getAttribute('href');
                return href && href.indexOf('#') !== -1;
            }
        );
        var spySections = spyLinks.length
            ? Array.prototype.filter.call(
                document.querySelectorAll('section[id], .ez-toc-section[id]'),
                function (section) { return section.id; }
            )
            : [];

        var liftElements = document.querySelectorAll('.navbar-links, #ez-toc-container, .sidebar-sticky');
        var stickySidebars = document.querySelectorAll('.sidebar-sticky');

        if (!spySections.length && !liftElements.length) return;

        function update() {
            var scrollY = window.scrollY;

            if (spySections.length) {
                var current = '';
                spySections.forEach(function (section) {
                    if (scrollY >= section.offsetTop - 200) current = section.id;
                });
                spyLinks.forEach(function (link) {
                    var href = link.getAttribute('href');
                    link.classList.toggle('active', current !== '' && href.slice(href.indexOf('#') + 1) === current);
                });
            }

            var lifted = scrollY >= SCROLL_OFFSET;
            liftElements.forEach(function (el) { el.classList.toggle('up-200', lifted); });
            stickySidebars.forEach(function (el) { el.classList.toggle('up-50', lifted); });
        }

        // Un solo listener pasivo, limitado a un cálculo por frame
        var ticking = false;
        window.addEventListener('scroll', function () {
            if (ticking) return;
            ticking = true;
            window.requestAnimationFrame(function () {
                update();
                ticking = false;
            });
        }, { passive: true });

        update();
    }

    function init() {
        initMobileMenu();
        initSearch();
        initReveal();
        initSteps();
        initParallax();
        initTestimonialSlider();
        initFaq();
        initScrollEffects();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
