
# Comandos de Git

## Publicar un tag específico:

```git
git push origin <nombre_del_tag>
```

## Eliminar un tag:

```git
git tag -d <nombre_del_tag>
git push origin --delete <nombre_del_tag>
```

## Configurar variables de entorno durante el desarrollo

Durante el desarrollo, en la aplicación se pueden configurar las siguientes variables para que el comando ""

```dotenv
KALION_PACKAGE_IN_DEVELOP=true
KALION_KEEP_MIGRATIONS_DATE=true
```

## Gestión de ramas de GIT

### Inicio cuando solo hay una rama "master"

```shell
git checkout -b develop master # Crear rama develop de master
git push -u origin develop     # Subir la nueva rama develop
```

### Eliminar una rama

```shell
git branch -d branch_name             # Eliminar la rama local branch_name
git push origin --delete branch_name  # (Opcional) Eliminar la rama remota
```

### Eliminar una tag

```shell
git tag -d nombre_tag         # Eliminar un tag en local
git push -d origin nombre_tag # Eliminar el tag del servidor
```

### Establecer la hora de un commit y abrir el editor para poder poner comillas dobles

```shell
export GIT_AUTHOR_DATE="2025-09-13 01:57:00"
export GIT_COMMITTER_DATE="2025-09-13 01:57:00"
git add .
git commit
```
