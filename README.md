# Workive

Portal de Empleo para Alumnos y Empresas

## Descripción

Workive es una plataforma web que conecta alumnos y empresas para potenciar oportunidades laborales, prácticas y gestión de ofertas de empleo. El sistema permite la creación y administración de perfiles tanto para estudiantes como para empresas, la publicación de ofertas, la gestión de candidaturas y la interacción a través de un entorno sencillo y moderno.

## Tecnologías y lenguajes utilizados

- **Backend:** PHP (POO, MVC)
- **Frontend:** HTML5, CSS3, Javascript (mínimo, enfoque tradicional con plantillas)
- **Plantillas:** League Plates Template Engine
- **ORM / Acceso a Datos:** PDO (acceso a MySQL)
- **Contenedores:** Docker, Docker Compose
- **Base de datos:** MySQL

## Despliegue

La aplicación está preparada para desplegarse mediante contenedores Docker, facilitando la gestión y el desarrollo en cualquier entorno. El servicio de base de datos se ejecuta como contenedor MySQL y la app PHP se despliega en otro contenedor. Recomendada la ejecución mediante:

docker-compose up --build


Esto crea los contenedores y levanta la base de datos y el backend automáticamente.

## Estructura principal

- `/app`: Código fuente del backend PHP
- `/db`: Scripts y estructura inicial para la base de datos MySQL
- `/public`: DocumentRoot del servidor (archivos .php públicos, CSS, imágenes)

## Características principales

- Registro y login para alumnos y empresas
- Publicación y gestión de ofertas de empleo
- Inscripción de alumnos en ofertas
- Panel de administración
- Gestión de usuarios y empresas por separado
- Notificaciones y mensajes internos
- Gestión segura de sesiones y contraseñas

## Configuración

Configura el archivo `.env` (o su equivalente en PHP) para los parámetros de conexión de la base de datos si no usas Docker.

## Estado del proyecto

Actualmente en desarrollo. Cualquier ayuda o sugerencia es bienvenida.

## Autoría

Desarrollado por Izan Martínez Castro, Izanmc18, 2º Desarrollo de Aplicaciones Webs (IES "Las Fuentezuelas").

---

**Licencia:** MIT o la que tú definas.