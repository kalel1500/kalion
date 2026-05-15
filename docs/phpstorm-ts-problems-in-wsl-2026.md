Aquí tienes el resumen del problema y la solución en un formato limpio y estructurado para guardarlo o consultarlo en el futuro:
## 📝 Origen del Problema
Al actualizar a PhpStorm v2026, JetBrains introdujo un nuevo motor de comunicación remota llamado EEL (Environment Execution Layer).
Si el plugin interno encargado de gestionar las conexiones de Linux está desactivado o corrupto, PhpStorm intenta arrancar los servicios de desarrollo (TypeScript, Tailwind, Prettier) usando la API local de Windows (LocalEelApi) en lugar de la API de Linux (WSL EEL API). Esto hace que los servidores de lenguaje colapsen en silencio y dejen de indexar los tipos de node_modules y de detectar las configuraciones.
------------------------------
## 🛠️ Pasos de Solución (Guía de Referencia)
Sigue estos pasos si el error Expected WSL EEL API... but got LocalEelApi vuelve a bloquear los servicios:

1. Abrir Preferencias:
* Usa el atajo Ctrl + Alt + S (o Cmd + , en macOS).
2. Acceder a Plugins:
* Dirígete a la sección Plugins en el menú izquierdo.
    * Cambia a la pestaña Installed (Instalados).
3. Activar el Agente de Ejecución:
* En la barra de búsqueda escribe: Remote Execution Agent.
    * Marca la casilla para activarlo de nuevo.
    * (Nota: Si ya aparece marcado, desmárcalo, haz clic en Apply, vuélvelo a marcar y vuelve a pulsar Apply para forzar su reinicio).
4. Reiniciar el IDE:
* Haz clic en OK y reinicia por completo PhpStorm.

------------------------------
## 💡 Buenas prácticas adicionales para proyectos Laravel + TS en subcarpetas
Si el plugin está activo pero experimentas problemas de indexación debido a que tu código frontend está dentro de la subcarpeta /src:

* Intérprete correcto: Asegúrate siempre en Languages & Frameworks > Node.js de que la ruta apunte al ejecutable de tu distribución de WSL (wsl://Ubuntu...), nunca al Node nativo de Windows.
* Puente de tipados: Coloca un archivo tsconfig.json básico en la raíz de Laravel que herede del de tu subcarpeta para guiar al indexador:

{
"extends": "./src/tsconfig.json"
}


¿Te gustaría guardar también los pasos para limpiar de forma segura las cachés de indexación por si el editor se queda congelado en el futuro?

