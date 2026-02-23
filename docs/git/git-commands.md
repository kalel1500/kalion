
# Comandos de Git

## Publicar un tag específico:

```shell
git push origin <nombre_del_tag>
```

## Eliminar un tag:

```shell
git tag -d <nombre_del_tag>
git push --delete origin <nombre_del_tag>
```

## Crear una rama

```shell
git checkout -b <nombre_rama> <nombre_rama_origen> # (opcional)
git push -u origin <nombre_rama>
```

### Eliminar una rama

```shell
git branch -d branch_name             # Eliminar la rama local branch_name
git push origin --delete branch_name  # (Opcional) Eliminar la rama remota
```

### Establecer la hora de un commit y abrir el editor para poder poner comillas dobles

```shell
export GIT_AUTHOR_DATE="2025-09-13 01:57:00"
export GIT_COMMITTER_DATE="2025-09-13 01:57:00"
git add .
git commit
```
