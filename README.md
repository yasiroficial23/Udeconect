Documentación del Proyecto: UDE Connect
1. Descripción del sistema
UDE Connect es una plataforma digital integral desarrollada para la Universidad de Cartagena, cuyo propósito principal es visibilizar, gestionar y conectar a la comunidad universitaria con proyectos estudiantiles, certificados, habilidades, perfiles académicos y oportunidades institucionales. La plataforma incluye funcionalidades para el manejo de usuarios (estudiantes, tutores, empresas, visitantes, administradores), gestión de proyectos, certificados, habilidades, publicaciones, y más, proporcionando una experiencia moderna, intuitiva y accesible.
2. Objetivos del sistema
• Fomentar la visibilidad de los proyectos estudiantiles.
• Centralizar la gestión de certificados de bienestar universitario.
• Crear un espacio de interacción para estudiantes, tutores y empresas.
• Facilitar el seguimiento de proyectos de investigación y TCC.
• Automatizar procesos de registro y consulta de habilidades.
3. Tecnologías utilizadas
• PHP (Back-end)
• MySQL (Base de datos)
• Bootstrap (Framework CSS para diseño responsive)
• HTML5 y JavaScript
• Git & GitHub
4. Proceso de instalación
Repositorio: https://github.com/yasiroficial23/Udeconect.git
Pasos:
1. Clonar el repositorio: git clone https://github.com/yasiroficial23/Udeconect.git
2. Importar el archivo SQL `vitrina_talento.sql` en tu servidor local MySQL (por ejemplo con phpMyAdmin).
3. Ubicar el proyecto en la carpeta htdocs de XAMPP.
4. Asegurar que el archivo `db_connect.php` tenga las credenciales correctas.
5. Iniciar Apache y MySQL desde el panel de XAMPP.
6. Acceder desde el navegador: http://localhost/Udeconect/html/index.html
5. Estructura general del sistema
• /html: Contiene las vistas de usuario (feed.php, login, registro, etc).
• /api: Archivos PHP que gestionan lógica del servidor y acceso a la base de datos.
• /img: Recursos gráficos (perfiles, logos, etc).
• /uploads: Documentos, imágenes y certificados subidos por los usuarios.
• /css, /js: Archivos estáticos para diseño y funcionalidad.
6. Estructura de la base de datos
La base de datos `vitrina_talento` contiene múltiples tablas. Algunas importantes:
• usuarios(id, nombre, apellidos, email, password_hash, rol, ...)
• proyectos(id, titulo, descripcion, fecha_inicio, fecha_fin, ...)
• estudiantes_certificados(id, usuario_id, codigo_estudiantil, certificado_generado, ...)
• habilidades(id, nombre, categoria)
• usuario_habilidades(id, usuario_id, habilidad_id, nivel)
• publicaciones(id, usuario_id, proyecto_id, titulo, contenido, ...)
• publicacion_likes(id, publicacion_id, usuario_id)
• publicacion_comentarios(id, publicacion_id, usuario_id, comentario, ...)
7. Metodología de desarrollo
Se utilizó una metodología incremental, donde se fue desarrollando módulo por módulo (usuarios, proyectos, certificados, habilidades, publicaciones), probando cada parte y ajustando según los requerimientos funcionales.
8. Conclusiones
UDE Connect representa una solución moderna y útil para conectar a la comunidad universitaria. Contribuye a la proyección académica y profesional de los estudiantes, y fortalece el vínculo entre universidad, empresa y sociedad.
9. Bibliografía y Webgrafía
• Documentación oficial de PHP: https://www.php.net/manual/es/

• Documentación de Bootstrap: https://getbootstrap.com/

• GitHub del proyecto: https://github.com/yasiroficial23/Udeconect

