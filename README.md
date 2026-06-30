# Nascor Interactive Dishes 🥗

Plugin para WordPress que genera un presentador de platillos altamente inmersivo. Construido con animaciones de rueda semi-circular, formas orgánicas (Splash) y un motor Parallax 3D acelerado por GPU.

## 🚀 Características Principales

*   **Motor Parallax Interactivo:** Rastrea la posición del ratón para crear un efecto de profundidad 3D (levitación y tilt) aplicado al platillo, los textos y las miniaturas.
*   **Splash Orgánico (Blob):** Utiliza una forma generada por CSS puro (`border-radius` dinámico) que se deforma y muta de manera fluida en el fondo del panel de texto.
*   **Rueda Semi-circular:** Transiciones suaves de rotación (entradas y salidas a 45 grados) sincronizadas con cambios de opacidad al cambiar de platillo.
*   **Diseño 100% Responsivo:** En dispositivos móviles (<900px), el motor Parallax se desactiva y el layout muta a un formato vertical optimizado para pantallas táctiles.
*   **Transiciones de Color Dinámicas:** El fondo se adapta suavemente al color predominante de cada platillo activo.

## 🛠️ Instalación

1. Descarga el código fuente de este repositorio en formato `.zip`.
2. En tu panel de administración de WordPress, dirígete a **Plugins > Añadir nuevo > Subir plugin**.
3. Sube el archivo `.zip` y haz clic en **Instalar ahora**.
4. Activa el plugin **Nascor Interactive Dishes**.

## 💻 Uso de Shortcodes (Anidados)

Este plugin utiliza una estructura de shortcodes anidados para registrar la información de cada platillo sin necesidad de bases de datos. Envuelve cada `[nascor_plato_item]` dentro del contenedor principal `[nascor_platos]`[cite: 6].

### Ejemplo de implementación:

```text
[nascor_platos]
    [nascor_plato_item titulo="Poke Bowl Nascor" categoria="Especialidad" imagen="url_del_plato.png" miniatura="url_mini.png" color_fondo="#8bb7e8"]
        Delicioso bowl con salmón fresco, aguacate y aderezo especial.
    [/nascor_plato_item]
    
    [nascor_plato_item titulo="Ramen Volcán" categoria="Plato Caliente" imagen="url_del_plato2.png" miniatura="url_mini2.png" color_fondo="#e86b6b"]
        Ramen picante con fideos artesanales y huevo marinado.
    [/nascor_plato_item]
[/nascor_platos]
