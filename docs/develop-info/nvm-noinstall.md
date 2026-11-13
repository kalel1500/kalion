# 📚 Instalación Manual de NVM-Windows (Sin Permisos de Administrador)

Esta guía documenta la instalación de **Node Version Manager para Windows (NVM-Windows)** sin usar el instalador, ideal para entornos corporativos donde los permisos de administrador están restringidos. Se utiliza un método alternativo de enlace de carpetas para evitar el requerimiento de permisos al cambiar de versión.

## 🎯 Requisitos Previos

* Permisos de escritura en la carpeta de destino.
* Credenciales temporales de administrador (para configurar las Variables de Entorno del Sistema).
* Permiso para usar el comando `mklink /J` (Directorio de Unión).

---

## 1. Descarga, Preparación y `settings.txt`

### 1.1. Descarga y Extracción

1.  Descarga el archivo **`nvm-noinstall.zip`** desde la página de GitHub.
2.  Extrae el contenido en una ruta de trabajo simple, **sin espacios** si es posible, por ejemplo:
    * **Ruta Base NVM:** `C:\dev\nvm-noinstall`

### 1.2. Configuración del Archivo `settings.txt`

1.  En la **Ruta Base NVM** (`C:\dev\nvm-noinstall`), crea un archivo llamado **`settings.txt`**.
2.  El contenido debe usar **dos puntos (`:`)** y reflejar tu ruta real:

```text
root: C:\dev\nvm-noinstall
path: C:\dev\nvm-noinstall\nodejs
arch: 64
proxy: none
```
> ⚠️ **NOTA:** No crees la carpeta nodejs manualmente. NVM la creará como un enlace.

---

## 2. Configuración de Variables de Entorno (Sistema)

Usarás la interfaz gráfica con las credenciales temporales para configurar las variables a nivel de **Sistema**.

1. Abre la configuración de **Variables de Entorno**.
2. Introduce las credenciales temporales cuando se te soliciten.
3. Busca la sección "**Variables del sistema**".

### 2.1. Crear Variables del Sistema

* Crea la variable `NVM_HOME`:
  * **Nombre**: `NVM_HOME`
  * **Valor**: `C:\dev\nvm-noinstall`
* Crea la variable `NVM_SYMLINK`:
  * **Nombre**: `NVM_SYMLINK`
  * **Valor**: `C:\dev\nvm-noinstall\nodejs`

### 2.2. Editar la Variable Path del Sistema

* Edita la variable `Path` y añade dos nuevas entradas:
  *` %NVM_HOME%`
  *` %NVM_SYMLINK%`
* Acepta todos los cambios y **cierra/vuelve a abrir la terminal** para que las variables se carguen.

---

## 3. Instalación de Node.js y Automatización del Uso

### 3.1. Instalación Inicial

Desde tu terminal normal (CMD o PowerShell), instala la versión deseada:
```shell
nvm install lts
```

### 3.2. Automatización del Cambio de Versión (`nvm-use.bat`)

Para evitar el error de permisos con `nvm use`, crearemos un archivo `.bat` que usa `mklink /J` automáticamente.

1. Crea una carpeta para scripts, por ejemplo: `C:\dev\Scripts`.
2. Crea un archivo llamado `nvm-use.bat` dentro de esa carpeta y pega el siguiente contenido, **ajustando la ruta NVM_HOME si es diferente**:
```shell
@echo off
set VERSION_TO_USE=%1
set NVM_HOME="C:\dev\nvm-noinstall"
set NVM_SYMLINK="C:\dev\nvm-noinstall\nodejs"
set TARGET_DIR=%NVM_HOME%\v%VERSION_TO_USE%

if "%VERSION_TO_USE%"=="" (
echo.
echo Uso: nvm-use <version> (Ej. nvm-use 20.12.0)
nvm ls
goto :EOF
)

echo Cambiando a Node.js version %VERSION_TO_USE%...

REM Elimina el enlace existente
rmdir %NVM_SYMLINK%

REM Crea el nuevo enlace de union (Junction)
mklink /J %NVM_SYMLINK% %TARGET_DIR%

echo.
node -v
```

### 3.3. Configurar Path para el Script

Para que el script funcione desde cualquier carpeta, añade la carpeta `Scripts` a tu `Path` **de Usuario** (no de Sistema, ya que no requieres permisos para editar el Path de tu usuario):

1. Abre la terminal.
2. Ejecuta:
   ```shell
   setx PATH "%PATH%;C:\dev\Scripts"
   ```
   > **Nota**: Reemplaza `C:\dev\Scripts` con la ruta real de tu carpeta de scripts.
3. Cierra y vuelve a abrir la terminal.

---

## 4. Uso y Verificación

### 4.1. Cambiar de Versión

Para activar una versión (ej. 20.12.0), usa el script recién creado:
```shell
nvm-use 20.12.0
```

### 4.2. Verificación Final

```shell
node -v
npm -v
```
> **Solución IDE:** Si la versión no se actualiza en PhpStorm o VS Code, **cierra y vuelve a abrir la aplicación** para que cargue el entorno de variables actualizado.
