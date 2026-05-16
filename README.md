# Sala Electrónica - Sistema de Reservas

Este es un sistema de gestión de reservas de salas y laboratorios construido con Laravel, Inertia.js y Vue.js. Cuenta con un diseño inspirado en el modo oscuro (dark theme) y permite consultar la disponibilidad de espacios, realizar reservas, visualizarlas en un calendario y administrarlas desde un panel de control.

## Características principales

- **Tecnologías:** Laravel 11, Vue 3, Inertia.js, Tailwind CSS.
- **Autenticación:** Jetstream / Fortify (Login, Registro, Recuperación de contraseña).
- **Notificaciones por Correo:** Configurado con Brevo (SMTP) para enviar notificaciones de creación, cancelación de reservas y recuperación de contraseñas.
- **Gestión de Espacios:** Panel administrativo para gestionar la información y disponibilidad de los espacios.
- **Reservas:** Sistema completo para que los usuarios reserven horarios específicos, con validación para evitar conflictos (overbooking).
- **Calendario Interactivo:** Visualización de las reservas en un calendario.

## Requisitos previos

Asegúrate de tener instalados en tu entorno local:
- PHP >= 8.2
- Composer
- Node.js & npm
- MySQL o MariaDB

## Instrucciones de Instalación

1. **Clonar el repositorio**
   ```bash
   git clone <URL_DEL_REPOSITORIO>
   cd sala-electronica
   ```

2. **Instalar dependencias de PHP**
   ```bash
   composer install
   ```

3. **Instalar dependencias de Node.js**
   ```bash
   npm install
   ```

4. **Configurar el archivo de entorno**
   Copia el archivo `.env.example` y renómbralo a `.env`:
   ```bash
   cp .env.example .env
   ```
   
   Genera la clave de la aplicación:
   ```bash
   php artisan key:generate
   ```

5. **Configuración de la Base de Datos y Correo**
   Abre el archivo `.env` y configura los detalles de tu base de datos y servidor SMTP (por ejemplo, Brevo):
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=nombre_de_tu_base_de_datos
   DB_USERNAME=tu_usuario
   DB_PASSWORD=tu_contraseña

   MAIL_MAILER=smtp
   MAIL_HOST=smtp-relay.brevo.com
   MAIL_PORT=587
   MAIL_USERNAME=tu_usuario_brevo
   MAIL_PASSWORD=tu_contraseña_brevo
   MAIL_ENCRYPTION=tls
   MAIL_FROM_ADDRESS="no-reply@sala-electronica.com"
   MAIL_FROM_NAME="${APP_NAME}"
   ```

6. **Ejecutar migraciones y seeders**
   Este paso creará las tablas en la base de datos y añadirá datos de prueba, incluyendo 3 espacios activos (Laboratorio de Electrónica, Laboratorio de Computación, Sala de Estudio) y usuarios por defecto:
   ```bash
   php artisan migrate:fresh --seed
   ```
   *Usuarios creados por el seeder:*
   - **Admin:** admin@sala.com / password
   - **Usuario:** usuario@sala.com / password

## Ejecución del Proyecto

Para levantar el proyecto en tu entorno local, necesitas ejecutar dos procesos en terminales separadas.

1. **Levantar el servidor de Laravel**
   ```bash
   php artisan serve
   ```
   El servidor estará disponible en `http://localhost:8000`.

2. **Compilar los assets con Vite**
   ```bash
   npm run dev
   ```

3. **Procesar correos en segundo plano (Opcional pero recomendado)**
   Si tienes configurado el sistema de colas (por ejemplo, `QUEUE_CONNECTION=database`), necesitas ejecutar el worker para enviar los correos de notificación:
   ```bash
   php artisan queue:work
   ```

¡Listo! Ya puedes ingresar al sistema de reservas y empezar a utilizarlo.
