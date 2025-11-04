
# 🚀 Flujo completo de trabajo en Git (sin GitFlow)

## 1️⃣ Iniciar el desarrollo en develop

```shell
git checkout develop                  # Ir a la rama develop
git pull origin develop (git pull)    # Asegurar que este actualizado
git checkout -b feature-x             # Crear y cambiar una rama de feature (opcional si es un cambio grande)
```

### Realizar los cambios en el código y guardar en GIT

```shell
git add .                             # Agregar cambios al area de staging
git commit -m "Mensaje"               # Confirmar el commit
git push origin feature-x (git push)  # Subir la rama feature-x (opcional)
```

### Si trabajaste en una rama feature-x, ahora la fusionamos en develop:

```shell
git checkout develop                  # Volver a develop
git pull origin develop               # Asegurar que está actualizado
git merge --no-ff feature-x           # Fusionar la rama feature en develop
git push origin develop (git push)    # Subir develop actualizado
git branch -d feature-x               # Eliminar la rama local feature-x
git push origin --delete feature-x    # (Opcional) Eliminar la rama remota
```

## 2️⃣ Merge de develop en master y creación del tag

```shell
git checkout master                   # Ir a la rama master
git pull origin master (git pull)     # Asegurar que está actualizada
git merge --no-ff develop             # Fusionar develop en master (dejar mensaje "Merge brange 'develop'")
git push origin master                # Subir los cambios a master

git tag -a v1.0.1 -m "Release v1.0.1" # Crear el tag de la nueva versión
git push origin master --tags         # Subir el tag a GitHub
```

## 3️⃣ Mantener develop igual que master

```shell
git checkout develop                  # Volver a develop
git merge --ff-only master            # Alinear develop con master
git push origin develop               # Subir develop actualizado
```

-------------

# 🚀 Flujo completo cuando desarrollas en master

## 1️⃣ Iniciar y commitear el desarrollo en master

```shell
git checkout master                   # Ir a la rama master
git pull origin master                # Asegurar que está actualizada
```

### Realiza los cambios en el código y luego guárdalos en Git:

```shell
git add .                                             # Agregar los cambios al área de staging
git commit -m "Pequeña mejora directamente en master" # Confirmar el commit
git push origin master                                # Subir los cambios a master
```

## 2️⃣ Crear el tag en master

```shell
git tag -a v1.0.2 -m "Release v1.0.2" # Crear el tag de la nueva versión
git push origin master --tags         # Subir el tag a GitHub
```

## 3️⃣ Mantener develop igual que master

```shell
git checkout develop                  # Cambiar a develop
git merge --ff-only master            # Alinear develop con master
git push origin develop               # Subir develop actualizado
```

-------------

# 🚀 Como igualar develop tras realizar un "git push --force" en master

```shell
git checkout develop                  # Cambiar a la rama develop
git reset --hard master               # Hacer que develop sea igual que master
git push origin develop --force       # Forzar la actualización en remoto
```
