<?php
/**
 * Plugin Name:       Nascor Interactive Dishes
 * Plugin URI:        https://nascor.ar
 * Description:       Presentador de platillos con animación semi-circular, Splash orgánico y motor Parallax interactivo.
 * Version:           1.1.0
 * Author:            Vivy (Nascor AI Architect)
 * Text Domain:       nascor-dishes
 */

if (!defined('ABSPATH')) {
    exit; // Seguridad
}

class Nascor_Interactive_Dishes {

    private static $dishes_data = [];

    public function __construct() {
        add_shortcode('nascor_platos', [$this, 'render_wrapper']);
        add_shortcode('nascor_plato_item', [$this, 'render_item']);
        add_action('wp_footer', [$this, 'inject_assets']);
    }

    public function render_wrapper($atts, $content = null) {
        self::$dishes_data = [];
        do_shortcode($content);

        if (empty(self::$dishes_data)) return '';

        $json_data = htmlspecialchars(json_encode(self::$dishes_data), ENT_QUOTES, 'UTF-8');

        ob_start();
        ?>
        <div class="nsc-dishes-breakout">
            <div class="nsc-dishes-container" id="nsc-dishes-app" data-dishes="<?php echo $json_data; ?>">
                
                <div class="nsc-dishes-bg" id="nsc-dishes-bg"></div>

                <div class="nsc-dishes-ui">
                    
                    <div class="nsc-dishes-left-panel">
                        <div class="nsc-dishes-blob" id="nsc-ui-blob"></div>

                        <div class="nsc-dishes-text-content" id="nsc-dish-texts">
                            <span class="nsc-dish-category" id="nsc-dish-cat"></span>
                            <h2 class="nsc-dish-title" id="nsc-dish-title"></h2>
                            <p class="nsc-dish-desc" id="nsc-dish-desc"></p>
                        </div>

                        <div class="nsc-dishes-thumbnails" id="nsc-dish-thumbnails">
                            </div>
                    </div>

                </div>

                <div class="nsc-dishes-stage" id="nsc-dishes-stage">
                    <img src="" alt="Plato Activo" id="nsc-active-dish" class="nsc-dish-img">
                    <img src="" alt="Plato Fantasma" id="nsc-ghost-dish" class="nsc-dish-img nsc-hidden">
                </div>

            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    public function render_item($atts, $content = null) {
        $a = shortcode_atts([
            'titulo' => 'Poke Bowl Nascor',
            'categoria' => 'Especialidad',
            'imagen' => '', 
            'miniatura' => '', 
            'color_fondo' => '#8bb7e8' 
        ], $atts);

        self::$dishes_data[] = [
            'title' => $a['titulo'],
            'category' => $a['categoria'],
            'image' => $a['imagen'],
            'thumbnail' => !empty($a['miniatura']) ? $a['miniatura'] : $a['imagen'],
            'bgColor' => $a['color_fondo'],
            'description' => wp_strip_all_tags($content)
        ];

        return '';
    }

    public function inject_assets() {
        if (empty(self::$dishes_data)) return;
        ?>
        <style>
            .nsc-dishes-breakout {
                width: 100vw;
                height: 100vh;
                margin-left: calc(-50vw + 50%);
                position: relative;
                overflow: hidden;
            }
            .nsc-dishes-container {
                position: absolute;
                top: 0; left: 0; width: 100%; height: 100%;
                display: flex;
                align-items: center;
                perspective: 1000px;
            }
            .nsc-dishes-bg {
                position: absolute;
                top: 0; left: 0; width: 100%; height: 100%;
                transition: background-color 0.8s cubic-bezier(0.25, 1, 0.5, 1);
                z-index: 1;
            }
            
            .nsc-dishes-ui {
                position: relative;
                z-index: 3;
                width: 100%;
                max-width: 1400px;
                margin: 0 auto;
                padding: 0 5%;
                pointer-events: none; 
            }

            /* --- PANEL IZQUIERDO y SPLASH (Blob) --- */
            .nsc-dishes-left-panel {
                width: 45%;
                pointer-events: auto;
                position: relative; /* Crucial para encapsular el blob */
                padding: 40px; /* Espacio para que el blob respire */
                z-index: 2;
            }

            /* El Splash Transparente (Pure CSS Morphing Blob) */
            .nsc-dishes-blob {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.08); /* Sombreado elegante */
                backdrop-filter: blur(5px);
                z-index: -1;
                border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%;
                animation: nsc-morph 8s ease-in-out infinite both alternate;
                will-change: transform, border-radius;
            }

            @keyframes nsc-morph {
                0% { border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%; }
                100% { border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%; }
            }

            .nsc-dishes-text-content {
                color: #fff;
                transition: opacity 0.4s ease, transform 0.4s ease;
                will-change: transform, opacity;
            }
            .nsc-dishes-text-content.is-switching {
                opacity: 0;
                transform: translateY(-20px);
            }
            .nsc-dish-category { font-size: 16px; font-weight: 600; text-transform: uppercase; letter-spacing: 2px; opacity: 0.9; margin-bottom: 10px; display: block;}
            .nsc-dish-title { font-size: 4.5vw; font-weight: 800; margin: 0 0 20px 0; line-height: 1.1; font-family: 'Fraunces', serif; color: #fff;}
            .nsc-dish-desc { font-size: 17px; opacity: 0.85; margin-bottom: 40px; line-height: 1.6; max-width: 95%;}

            /* --- MINIATURAS (Botones) --- */
            .nsc-dishes-thumbnails {
                display: flex;
                gap: 15px;
                flex-wrap: wrap;
                will-change: transform;
            }
            .nsc-thumb-btn {
                background: transparent;
                border: 3px solid transparent;
                padding: 0;
                width: 75px;
                height: 75px;
                border-radius: 50%;
                cursor: pointer;
                overflow: hidden;
                transition: all 0.4s cubic-bezier(0.25, 1, 0.5, 1);
                filter: grayscale(30%) drop-shadow(0 10px 10px rgba(0,0,0,0.15));
            }
            .nsc-thumb-btn img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                transition: transform 0.5s ease;
            }
            .nsc-thumb-btn:hover {
                transform: scale(1.1) translateY(-5px);
                filter: grayscale(0%) drop-shadow(0 15px 15px rgba(0,0,0,0.25));
                border-color: rgba(255,255,255,0.5);
            }
            .nsc-thumb-btn.active {
                transform: scale(1.2) translateY(-5px);
                filter: grayscale(0%) drop-shadow(0 20px 20px rgba(0,0,0,0.35));
                border-color: #fff;
            }
            .nsc-thumb-btn.active img {
                transform: scale(1.1);
            }

            /* --- ESCENARIO DEL PLATO --- */
            .nsc-dishes-stage {
                position: absolute;
                right: -5%;
                top: 50%;
                /* El Y central se maneja ahora en JS para sumar el Parallax */
                transform: translateY(-50%); 
                width: 55vw;
                height: 55vw;
                max-width: 900px;
                max-height: 900px;
                z-index: 2;
                display: flex;
                align-items: center;
                justify-content: center;
                pointer-events: none;
                will-change: transform;
            }
            
            .nsc-dish-img {
                position: absolute;
                width: 100%;
                height: auto;
                object-fit: contain;
                filter: drop-shadow(0 50px 40px rgba(0,0,0,0.4));
                /* Eje de rotación para la rueda */
                transform-origin: 50% 250%; 
                transition: transform 1s cubic-bezier(0.34, 1.56, 0.64, 1), opacity 0.8s ease;
                will-change: transform, opacity;
            }

            .state-center { transform: rotate(0deg); opacity: 1; }
            .state-enter-right { transform: rotate(45deg); opacity: 0; }
            .state-exit-left { transform: rotate(-45deg); opacity: 0; }
            .state-enter-left { transform: rotate(-45deg); opacity: 0; }
            .state-exit-right { transform: rotate(45deg); opacity: 0; }
            .nsc-hidden { display: none; }

            /* Responsive Móvil */
            @media (max-width: 900px) {
                .nsc-dishes-container { flex-direction: column; justify-content: flex-start; padding-top: 5vh; }
                .nsc-dishes-left-panel { width: 100%; text-align: center; padding: 20px; }
                .nsc-dishes-blob { border-radius: 20px; animation: none; height: 110%; top: -5%; }
                .nsc-dish-desc { max-width: 100%; font-size: 15px; margin-bottom: 20px;}
                .nsc-dish-title { font-size: 9vw; }
                .nsc-dishes-thumbnails { justify-content: center; gap: 10px; }
                .nsc-thumb-btn { width: 60px; height: 60px; }
                
                .nsc-dishes-stage {
                    top: auto;
                    bottom: -10%;
                    right: 50%;
                    transform: translateX(50%) !important;
                    width: 90vw;
                    height: 90vw;
                }
            }

        </style>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const container = document.getElementById('nsc-dishes-app');
                if (!container) return;

                const dishes = JSON.parse(container.getAttribute('data-dishes'));
                let currentIndex = 0;
                let isAnimating = false;

                const bg = document.getElementById('nsc-dishes-bg');
                const catEl = document.getElementById('nsc-dish-cat');
                const titleEl = document.getElementById('nsc-dish-title');
                const descEl = document.getElementById('nsc-dish-desc');
                const textContent = document.getElementById('nsc-dish-texts');
                const activeImg = document.getElementById('nsc-active-dish');
                const ghostImg = document.getElementById('nsc-ghost-dish');
                const thumbContainer = document.getElementById('nsc-dish-thumbnails');
                
                // Elementos Parallax
                const stage = document.getElementById('nsc-dishes-stage');
                const blob = document.getElementById('nsc-ui-blob');

                // 1. Generar Miniaturas
                dishes.forEach((dish, index) => {
                    const btn = document.createElement('button');
                    btn.className = `nsc-thumb-btn ${index === 0 ? 'active' : ''}`;
                    btn.setAttribute('aria-label', `Ver ${dish.title}`);
                    btn.innerHTML = `<img src="${dish.thumbnail}" alt="Miniatura ${dish.title}">`;
                    
                    btn.addEventListener('click', () => {
                        if (index !== currentIndex) changeDish(index);
                    });
                    
                    thumbContainer.appendChild(btn);
                });

                const thumbBtns = document.querySelectorAll('.nsc-thumb-btn');

                function initDish(index) {
                    const dish = dishes[index];
                    bg.style.backgroundColor = dish.bgColor;
                    catEl.textContent = dish.category;
                    titleEl.textContent = dish.title;
                    descEl.innerHTML = dish.description;
                    activeImg.src = dish.image;
                    activeImg.className = 'nsc-dish-img state-center';
                }

                initDish(currentIndex);

                // Variables Motor Parallax
                let mouseX = 0, mouseY = 0;
                let targetX = 0, targetY = 0;
                const windowWidth = window.innerWidth;
                const windowHeight = window.innerHeight;
                const isMobile = windowWidth <= 900;

                // Motor Parallax 3D & Levitación (Acelerado GPU)
                function renderLoop() {
                    if (!isMobile) {
                        // Suavizado Lerp
                        mouseX += (targetX - mouseX) * 0.1;
                        mouseY += (targetY - mouseY) * 0.1;

                        // Respiración (Levitación Constante)
                        const floatY = Math.sin(Date.now() * 0.002) * 12;

                        // Efectos de profundidad multicapa
                        // Plato (Rotación 3D Tilt sutil + Movimiento inverso)
                        stage.style.transform = `translateY(calc(-50% + ${floatY + mouseY * 20}px)) translateX(${mouseX * -20}px) rotateX(${mouseY * -10}deg) rotateY(${mouseX * 10}deg)`;
                        
                        // Textos (Movimiento en dirección del ratón)
                        textContent.style.transform = `translate(${mouseX * 15}px, ${mouseY * 15}px)`;
                        
                        // Miniaturas (Movimiento rápido, mayor profundidad frontal)
                        thumbContainer.style.transform = `translate(${mouseX * 25}px, ${mouseY * 25}px)`;

                        // Splash/Blob Orgánico (Movimiento lento e inverso al texto para dar volumen 3D)
                        blob.style.transform = `translate(${mouseX * -15}px, ${mouseY * -15}px)`;
                    }
                    requestAnimationFrame(renderLoop);
                }
                renderLoop();

                // Tracking de Mouse
                container.addEventListener('mousemove', (e) => {
                    targetX = (e.clientX / windowWidth) * 2 - 1;
                    targetY = (e.clientY / windowHeight) * 2 - 1;
                });
                container.addEventListener('mouseleave', () => {
                    targetX = 0; targetY = 0; 
                });

                // Función Principal de Cambio (Rueda Semi-circular)
                function changeDish(newIndex) {
                    if (isAnimating || newIndex < 0 || newIndex >= dishes.length) return;
                    isAnimating = true;

                    const direction = newIndex > currentIndex ? 1 : -1;
                    const nextDish = dishes[newIndex];

                    thumbBtns.forEach(b => b.classList.remove('active'));
                    thumbBtns[newIndex].classList.add('active');

                    textContent.classList.add('is-switching');
                    
                    ghostImg.src = nextDish.image;
                    ghostImg.className = `nsc-dish-img ${direction > 0 ? 'state-enter-right' : 'state-enter-left'}`;
                    ghostImg.classList.remove('nsc-hidden');

                    void ghostImg.offsetWidth; 

                    bg.style.backgroundColor = nextDish.bgColor;
                    activeImg.className = `nsc-dish-img ${direction > 0 ? 'state-exit-left' : 'state-exit-right'}`;
                    ghostImg.className = 'nsc-dish-img state-center';

                    setTimeout(() => {
                        catEl.textContent = nextDish.category;
                        titleEl.textContent = nextDish.title;
                        descEl.innerHTML = nextDish.description;
                        textContent.classList.remove('is-switching');
                    }, 400);

                    setTimeout(() => {
                        activeImg.style.transition = 'none'; 
                        activeImg.src = ghostImg.src;
                        activeImg.className = 'nsc-dish-img state-center';
                        
                        void activeImg.offsetWidth; 
                        
                        activeImg.style.transition = ''; 
                        ghostImg.classList.add('nsc-hidden');
                        
                        currentIndex = newIndex;
                        isAnimating = false;
                    }, 1000); 
                }
            });
        </script>
        <?php
    }
}

new Nascor_Interactive_Dishes();