<?php
/**
 * The template for displaying the footer
 *
 * Contains the opening of the #site-footer div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package WordPress
 * @subpackage Twenty_Twenty
 * @since Twenty Twenty 1.0
 */

?>
			
				<footer class="footer">
			        <div class="container footer-grid reveal-item">
			            <div class="footer-brand">
			                <img src="https://growing.family/wp-content/uploads/2026/08/Logo-blanco.png" alt="Growing Family Logo" class="footer-logo">
			                <p class="footer-tagline">¡Tu tiempo hoy guiará su camino!</p>
			                <p class="footer-desc">
			                    Acompañamos el crecimiento de tu familia con ciencia y humanidad. Convertimos la intención en momentos que se quedan para siempre.
			                </p>
			            </div>

			            <div class="footer-links">
			                <h3 class="footer-title">Enlaces</h3>
			                <ul class="footer-list">
			                    <li><a href="https://growing.family/acerca-de/" class="footer-link">Acerca de</a></li>
			                    <li><a href="https://growing.family/blog/" class="footer-link">Blog</a></li>
			                    <li><a href="https://growing.family/lista-de-espera/" class="footer-link">Lista de espera</a></li>
			                    <li><a href="https://growing.family/contacto-y-soporte/" class="footer-link">Contacto</a></li>
			                </ul>
			            </div>

			            <div class="footer-social">
			                <h3 class="footer-title">Síguenos en:</h3>
			                <div class="social-icons">
			                    <a href="https://www.instagram.com/growingfamily.app/" class="social-link" aria-label="Instagram" target="_blank">
			                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
			                            <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
			                            <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
			                            <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
			                        </svg>
			                    </a>
			                    <a href="https://www.facebook.com/GrowingFamilyAPP" class="social-link" aria-label="Facebook" target="_blank">
			                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
			                            <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path>
			                        </svg>
			                    </a>
			                    <a href="https://www.youtube.com/@GrowingFamilyApp" class="social-link" aria-label="YouTube" target="_blank">
			                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
			                            <path d="M22.54 6.42a2.78 2.78 0 0 0-1.95-1.96C18.88 4 12 4 12 4s-6.88 0-8.59.46a2.78 2.78 0 0 0-1.95 1.96A29 29 0 0 0 1 12a29 29 0 0 0 .46 5.58 2.78 2.78 0 0 0 1.95 1.96C5.12 20 12 20 12 20s6.88 0 8.59-.46a2.78 2.78 0 0 0 1.95-1.96A29 29 0 0 0 23 12a29 29 0 0 0-.46-5.58z"></path>
			                            <polygon points="9.75 15.02 15.5 12 9.75 8.98 9.75 15.02"></polygon>
			                        </svg>
			                    </a>
			                    <a href="https://www.tiktok.com/@growingfamilyapp" class="social-link" aria-label="Tiktok" target="_blank">
                                    <img src="https://growing.family/wp-content/uploads/2026/08/tiktok.svg" alt="tiktok" width="24" height="24">
			                    </a>
			                </div>
			            </div>
			        </div>

			        <div class="container footer-bottom reveal-item">
			            <p class="copyright">Copyright © 2026 Growing Family. Todos los derechos reservados.</p>
			            <p class="medical-disclaimer">
			                Growing Family es un ecosistema informativo diseñado para orientar a las familias en la crianza. El contenido de este sitio, la app y sus recursos no sustituye el consejo, diagnóstico o tratamiento de profesionales de la salud o especialistas médicos. El uso de esta plataforma implica la aceptación de nuestros <a href="https://growing.family/terminos-y-condiciones/">Términos y condiciones</a> y <a href="https://growing.family/poltica-de-privacidad/">Política de privacidad</a>.
			            </p>
			        </div>
			    </footer>
				

		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
    	<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Cal+Sans&display=swap" rel="stylesheet">
		<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g==" crossorigin="anonymous" referrerpolicy="no-referrer" rel="stylesheet" />

		<script>
			/* ==========================================================================
   GROWING FAMILY - INTERACTIVE ACTIONS JS
   ========================================================================== */

document.addEventListener('DOMContentLoaded', () => {

    /* ==========================================================================
       1. MOBILE HAMBURGER MENU DRAWER
       ========================================================================== */
    const hamburgerBtn = document.getElementById('hamburger-btn');
    const mobileDrawer = document.getElementById('mobile-drawer');

    if (hamburgerBtn && mobileDrawer) {
        hamburgerBtn.addEventListener('click', () => {
            const isActive = hamburgerBtn.classList.toggle('active');
            mobileDrawer.classList.toggle('open', isActive);
            
            // Prevent scrolling on body when menu is open
            document.body.style.overflow = isActive ? 'hidden' : '';
        });

        // Close drawer when clicking links
        const drawerLinks = mobileDrawer.querySelectorAll('.drawer-link');
        drawerLinks.forEach(link => {
            link.addEventListener('click', () => {
                hamburgerBtn.classList.remove('active');
                mobileDrawer.classList.remove('open');
                document.body.style.overflow = '';
            });
        });
    }

    /* ==========================================================================
       2. SCROLL REVEAL ANIMATIONS (IntersectionObserver)
       ========================================================================== */
    const revealItems = document.querySelectorAll('.reveal-item');
    
    if ('IntersectionObserver' in window) {
        const revealObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('reveal-visible');
                    // Unobserve after showing to avoid recalculations
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.15, // trigger when 15% of element is in viewport
            rootMargin: '0px 0px -50px 0px' // offset slightly from bottom viewport edge
        });

        revealItems.forEach(item => revealObserver.observe(item));
    } else {
        // Fallback for older browsers
        revealItems.forEach(item => item.classList.add('reveal-visible'));
    }

    /* ==========================================================================
       3. INTERACTIVE STEPS ("¿Cómo funciona en la práctica?")
       ========================================================================== */
    const stepItems = document.querySelectorAll('.step-item');
    const desktopCard = document.getElementById('how-desktop-card');
    const desktopNum = document.getElementById('how-desktop-num');
    const desktopTitle = document.getElementById('how-desktop-title');
    const desktopDesc = document.getElementById('how-desktop-desc');
    const desktopMockup = document.getElementById('how-desktop-mockup');

    stepItems.forEach((item) => {
        const header = item.querySelector('.step-header');
        
        header.addEventListener('click', () => {
            const isActive = item.classList.contains('active');

            // 1. Cerrar todos los acordeones
            stepItems.forEach(step => {
                step.classList.remove('active');
                const body = step.querySelector('.step-body');
                if (body) body.style.maxHeight = null;
            });

            // Si ya estaba activo en móvil y vuelve a hacer clic, se colapsa
            if (isActive && window.innerWidth <= 1024) return;

            // 2. Activar el elemento seleccionado
            item.classList.add('active');
            
            // Acordeón Móvil: Calcular nueva altura (incluyendo el texto + imagen)
            const activeBody = item.querySelector('.step-body');
            if (activeBody && window.innerWidth <= 1024) {
                // Le damos un pequeño margen extra para scrollHeight
                activeBody.style.maxHeight = (activeBody.scrollHeight + 40) + "px";
            }

            // 3. Panel Desktop (Solo si estamos en pantallas grandes)
            if (desktopCard && window.innerWidth > 1024) {
                const num = item.querySelector('.step-num').textContent.trim();
                const title = item.querySelector('.step-title').textContent.trim();
                const desc = item.querySelector('.step-desc').textContent.trim();
                const imgSrc = item.getAttribute('data-img') || 'phone-step-1.jpg';

                desktopCard.style.opacity = '0';
                desktopCard.style.transform = 'translateY(10px)';

                setTimeout(() => {
                    desktopNum.textContent = num;
                    desktopTitle.textContent = title;
                    desktopDesc.textContent = desc;
                    desktopMockup.src = imgSrc;

                    desktopCard.style.opacity = '1';
                    desktopCard.style.transform = 'translateY(0)';
                }, 200);
            }
        });
    });

    // Ajuste dinámico de alturas en caso de cambio de tamaño de ventana
    function initAccordions() {
        if (window.innerWidth <= 1024) {
            const activeStep = document.querySelector('.step-item.active');
            if (activeStep) {
                const activeBody = activeStep.querySelector('.step-body');
                if (activeBody) activeBody.style.maxHeight = (activeBody.scrollHeight + 40) + "px";
            }
        } else {
            stepItems.forEach(step => {
                const body = step.querySelector('.step-body');
                if (body) body.style.maxHeight = null;
            });
        }
    }

    initAccordions();
    window.addEventListener('resize', initAccordions);

    /* ==========================================================================
       4. CHARACTER MOUSE PARALLAX EFFECT
       ========================================================================== */
    const parallaxScene = document.getElementById('parallax-scene');
    const chars = document.querySelectorAll('.parallax-char');

    if (parallaxScene && chars.length > 0 && window.innerWidth > 768) {
        // Track parent section instead of full screen to restrict trigger area
        const parentSection = parallaxScene.closest('.section-parallax');
        
        parentSection.addEventListener('mousemove', (e) => {
            const rect = parentSection.getBoundingClientRect();
            // Mouse coordinates relative to the center of the section bounding box
            const mouseX = e.clientX - rect.left - (rect.width / 2);
            const mouseY = e.clientY - rect.top - (rect.height / 2);
            
            // Normalize values (-0.5 to 0.5 range)
            const normX = mouseX / (rect.width / 2);
            const normY = mouseY / (rect.height / 2);

            chars.forEach(char => {
                const speed = parseFloat(char.getAttribute('data-speed')) || 1;
                // Move character slightly based on normalized cursor offset and its unique speed weight
                const moveX = normX * 25 * speed;
                const moveY = normY * 15 * speed;
                
                char.style.transform = `translate(${moveX}px, ${moveY}px)`;
            });
        });

        // Reset positions when mouse leaves the section
        parentSection.addEventListener('mouseleave', () => {
            chars.forEach(char => {
                char.style.transform = 'translate(0px, 0px)';
            });
        });
    }

    /* ==========================================================================
       5. TESTIMONIAL SLIDER CAROUSEL (Touch Swipable - Mobile/Tablet only)
       ========================================================================== */
    const sliderTrack = document.getElementById('slider-track');
    const prevBtn = document.getElementById('prev-slide');
    const nextBtn = document.getElementById('next-slide');
    const dotsContainer = document.getElementById('slider-dots');
    const dots = dotsContainer ? dotsContainer.querySelectorAll('.dot') : [];
    const slides = document.querySelectorAll('.slide-item');
    
    let currentSlide = 0;
    const totalSlides = slides.length;
    let autoplayTimer = null;

    if (sliderTrack && totalSlides > 0) {

        function getMaxIndex() {
            // En desktop (> 1024px) hay 3 visibles, por lo que el máximo índice navegable es total - 3
            return window.innerWidth > 1024 ? Math.max(0, totalSlides - 3) : totalSlides - 1;
        }

        function updateSliderPosition() {
            const maxIndex = getMaxIndex();
            
            // Garantiza que no nos pasemos del límite al cambiar de pantalla
            if (currentSlide > maxIndex) {
                currentSlide = maxIndex;
            }

            if (window.innerWidth > 1024) {
                // Ancho de 1 ítem + gap de 2rem (32px)
                const slideWidth = slides[0].getBoundingClientRect().width;
                const gap = 32; 
                const moveAmount = (slideWidth + gap) * currentSlide;
                sliderTrack.style.transform = `translateX(-${moveAmount}px)`;
            } else {
                sliderTrack.style.transform = `translateX(-${currentSlide * 100}%)`;
            }
            
            // Actualizar estado de los dots
            dots.forEach((dot, index) => {
                dot.classList.toggle('active', index === currentSlide);
                // Oculta los dots sobrantes en desktop si no son alcanzables
                if (window.innerWidth > 1024 && index > maxIndex) {
                    dot.style.display = 'none';
                } else {
                    dot.style.display = 'inline-block';
                }
            });
        }

        function showNextSlide() {
            const maxIndex = getMaxIndex();
            currentSlide = currentSlide >= maxIndex ? 0 : currentSlide + 1;
            updateSliderPosition();
        }

        function showPrevSlide() {
            const maxIndex = getMaxIndex();
            currentSlide = currentSlide <= 0 ? maxIndex : currentSlide - 1;
            updateSliderPosition();
        }

        // Eventos para botones
        if (nextBtn) {
            nextBtn.addEventListener('click', () => {
                showNextSlide();
                resetAutoplay();
            });
        }

        if (prevBtn) {
            prevBtn.addEventListener('click', () => {
                showPrevSlide();
                resetAutoplay();
            });
        }

        // Eventos para los indicadores (dots)
        dots.forEach(dot => {
            dot.addEventListener('click', (e) => {
                currentSlide = parseInt(e.target.getAttribute('data-index')) || 0;
                updateSliderPosition();
                resetAutoplay();
            });
        });

        // Arrastre con Mouse / Touch
        let startX = 0;
        let diffX = 0;
        let isDragging = false;

        sliderTrack.addEventListener('pointerdown', (e) => {
            isDragging = true;
            startX = e.clientX;
            diffX = 0;
            sliderTrack.style.transition = 'none';
            resetAutoplay();
        });

        sliderTrack.addEventListener('pointermove', (e) => {
            if (!isDragging) return;
            diffX = e.clientX - startX;

            if (Math.abs(diffX) > 5 && e.cancelable) {
                e.preventDefault();
            }

            const maxIndex = getMaxIndex();
            let currentOffset = 0;
            
            if (window.innerWidth > 1024) {
                const slideWidth = slides[0].getBoundingClientRect().width;
                const gap = 32;
                currentOffset = -(slideWidth + gap) * currentSlide;
            } else {
                currentOffset = -currentSlide * sliderTrack.offsetWidth;
            }

            sliderTrack.style.transform = `translateX(${currentOffset + diffX}px)`;
        });

        const endSwipe = () => {
            if (!isDragging) return;
            isDragging = false;
            sliderTrack.style.transition = 'transform 0.4s cubic-bezier(0.25, 1, 0.5, 1)'; 
            
            const maxIndex = getMaxIndex();
            const threshold = 50; // Sensibilidad de arrastre
            
            if (diffX < -threshold && currentSlide < maxIndex) {
                currentSlide++;
            } else if (diffX > threshold && currentSlide > 0) {
                currentSlide--;
            }
            
            diffX = 0;
            updateSliderPosition();
        };

        sliderTrack.addEventListener('pointerup', endSwipe);
        sliderTrack.addEventListener('pointerleave', endSwipe);
        sliderTrack.addEventListener('pointercancel', endSwipe);

        // Autoplay activo en Desktop y Móvil
        function startAutoplay() {
            if (autoplayTimer) clearInterval(autoplayTimer);
            autoplayTimer = setInterval(showNextSlide, 6000);
        }

        function resetAutoplay() {
            clearInterval(autoplayTimer);
            autoplayTimer = null;
            startAutoplay();
        }

        const testimonialsSection = document.querySelector('.section-testimonials');
        if (testimonialsSection) {
            testimonialsSection.addEventListener('mouseenter', () => {
                if (autoplayTimer) clearInterval(autoplayTimer);
            });
            testimonialsSection.addEventListener('mouseleave', () => {
                startAutoplay();
            });
        }

        window.addEventListener('resize', () => {
            updateSliderPosition();
        });

        startAutoplay();
    }

    /* ==========================================================================
       6. FAQ ACCORDION COLLAPSE/EXPAND
       ========================================================================== */
    const faqItems = document.querySelectorAll('.faq-item');

    faqItems.forEach(item => {
        const header = item.querySelector('.faq-header');
        const body = item.querySelector('.faq-body');

        header.addEventListener('click', () => {
            const isActive = item.classList.contains('active');
            
            // Close other FAQ items
            faqItems.forEach(otherItem => {
                otherItem.classList.remove('active');
                const otherBody = otherItem.querySelector('.faq-body');
                if (otherBody) otherBody.style.maxHeight = null;
            });

            if (!isActive) {
                item.classList.add('active');
                body.style.maxHeight = body.scrollHeight + 'px';
            } else {
                item.classList.remove('active');
                body.style.maxHeight = null;
            }
        });
    });

});

		</script>

		<script>
			const sections = document.querySelectorAll("section, .ez-toc-section");
			const navLinks = document.querySelectorAll(".nav-link, .ez-toc-link");

			window.addEventListener("scroll", () => {
			    let current = "";

			    sections.forEach((section) => {
			        const sectionTop = section.offsetTop;
			        // Verifica si el scroll está en la sección y si la sección tiene un ID
			        if (scrollY >= sectionTop - 200 && section.hasAttribute("id")) {
			            current = section.getAttribute("id");
			        }
			    });

			    navLinks.forEach((link) => {
			        link.classList.remove("active");
			        if (link.getAttribute("href").includes(current)) {
			            link.classList.add("active");
			        }
			    });
			});


			const miElementos = document.querySelectorAll('.navbar-links, #ez-toc-container, .sidebar-sticky');
			const pixelesDeDesplazamiento = 400;

			window.addEventListener('scroll', () => {
			    if (window.scrollY >= pixelesDeDesplazamiento) {
			        miElementos.forEach(elemento => {
			            elemento.classList.add('up-200');
			        });
			    } else {
			        miElementos.forEach(elemento => {
			            elemento.classList.remove('up-200');
			        });
			    }
			});

		</script>

		<script>

			const miElementos2 = document.querySelectorAll('.sidebar-sticky');
			const pixelesDeDesplazamiento2 = 400;

			window.addEventListener('scroll', () => {
			    if (window.scrollY >= pixelesDeDesplazamiento2) {
			        miElementos2.forEach(elemento => {
			            elemento.classList.add('up-50');
			        });
			    } else {
			        miElementos.forEach(elemento => {
			            elemento.classList.remove('up-50');
			        });
			    }
			});

		</script>

		<script>
		    document.addEventListener('DOMContentLoaded', function() {
		        const emailInput = document.getElementById('email');
		        const emailError = document.getElementById('emailError');
		        const emailOk = document.getElementById('emailOk');
		        const form = document.getElementById('miFormulario');
		        
		        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

		        function validarEmail() {
		            const emailValue = emailInput.value.trim();
		            
		            // Función para ocultar ambos mensajes (añadir la clase 'hidden')
		            function hideAllMessages() {
		                emailError.classList.add('hidden');
		                emailOk.classList.add('hidden');
		                // Quita el atributo aria-invalid del input
		                emailInput.removeAttribute('aria-invalid');
		            }

		            if (emailValue === '') {
		                // 1. Si está vacío: oculta ambos mensajes.
		                hideAllMessages();
		                return false;
		            } else if (emailRegex.test(emailValue)) {
		                // 2. Formato correcto: oculta error y MUESTRA OK
		                emailError.classList.add('hidden');
		                emailOk.classList.remove('hidden'); // Muestra el mensaje OK
		                emailInput.removeAttribute('aria-invalid'); // Marca el input como válido (implícito)
		                return true;
		            } else {
		                // 3. Formato incorrecto: MUESTRA error y oculta OK
		                emailError.classList.remove('hidden'); // Muestra el mensaje de Error
		                emailOk.classList.add('hidden');
		                emailInput.setAttribute('aria-invalid', 'true'); // Marca el input con error
		                return false;
		            }
		        }

		        // 1. Validación en vivo (al escribir o desenfocar el campo)
		        emailInput.addEventListener('input', validarEmail);
		        emailInput.addEventListener('blur', validarEmail);

		        // 2. Validación final antes del envío
		        form.addEventListener('submit', function(event) {
		            if (!validarEmail()) {
		                event.preventDefault();
		                // Asegura que el mensaje de error se muestre al intentar enviar.
		                emailError.classList.remove('hidden'); 
		                alert('Por favor, ingresa un correo electrónico válido antes de suscribirte.');
		            }
		        });
		    });
		</script>
		
		<?php wp_footer(); ?>

	</body>
</html>
