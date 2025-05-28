# Documentación interna

## Sobre el prefijo "kal"

"kal" es una abreviatura de Kernel Abstraction Layer, un concepto clave tanto en la arquitectura hexagonal como en DDD. La idea es la siguiente:

Kernel: Se refiere al núcleo de la aplicación, donde reside la lógica de negocio pura. En DDD, se prioriza la centralidad del dominio, el "corazón" del sistema, libre de detalles de infraestructura.

Abstraction Layer: Representa la capa que separa el núcleo del dominio de las dependencias externas, permitiendo que la lógica central permanezca limpia y desacoplada de tecnologías o frameworks específicos.

Al usar el prefijo "kal", indicas que los elementos asociados (ya sean clases, componentes CSS u otros) forman parte de ese esfuerzo de mantener una separación clara entre el dominio y otros aspectos de la aplicación. Esto refuerza la intención de seguir buenas prácticas de diseño, tal como lo promueve la arquitectura hexagonal y DDD, al mantener el núcleo de la aplicación protegido y aislado de cambios externos.

Además, esta nomenclatura ayuda a comunicar de manera inmediata que esos componentes están pensados para interactuar con el dominio de forma segura y controlada, siguiendo principios de diseño modular y escalable, algo que también se alinea con la filosofía de Laravel y el desarrollo moderno.

## Start command

|                                                                  |        |             |
|------------------------------------------------------------------|--------|-------------|
| ->new()->saveLock()                                              |        | developMode |
| ->publishKalionConfig()                                          |        | developMode |
| ->stubsCopyFile_DependencyServiceProvider()                      |        |             |
| ->stubsCopyFiles_Config()                                        |        |             |
| ->stubsCopyFiles_Migrations()                                    |        |             |
| ->stubsCopyFiles_Js()                                            |        |             |
| ->stubsCopyFolder_Factories()                                    |        |             |
| ->stubsCopyFolder_Seeders()                                      |        |             |
| ->stubsCopyFolder_Lang()                                         |        |             |
| ->stubsCopyFolder_Resources()                                    |        |             |
| ->stubsCopyFolder_Src()                                          |        |             |
| ->stubsCopyFile_RoutesWeb()                                      |        |             |
| ->createEnvFiles()                                               |        | developMode |
| ->deleteDirectory_Http()                                         |        |             |
| ->deleteDirectory_Models()                                       |        |             |
| ->deleteFile_Changelog()                                         |        |             |
| ->modifyFile_BootstrapProviders_toAddDependencyServiceProvider() |        |             |
| ->modifyFile_BootstrapApp_toAddMiddlewareRedirect()              |        |             |
| ->modifyFile_BootstrapApp_toAddExceptionHandler()                |        |             |
| ->modifyFile_ConfigApp_toUpdateTimezone()                        |        |             |
| ->modifyFile_JsBootstrap_toAddImportFlowbite()                   |        |             |
| ->modifyFile_Gitignore_toDeleteLockFileLines()                   |        | developMode |
| ->modifyFile_PackageJson_toAddNpmDependencies()                  |        |             |
| ->modifyFile_PackageJson_toAddScriptTsBuild()                    |        |             |
| ->modifyFile_PackageJson_toAddEngines()                          |        |             |
| ->modifyFile_ComposerJson_toAddSrcNamespace()                    |        |             |
| ->modifyFile_ComposerJson_toAddHelperFilePath()                  |        |             |
| ->execute_ComposerRequire_toInstallComposerDependencies()        |        | developMode |
| ->execute_ComposerDumpAutoload()                                 |        |             |
| ->execute_gitAdd()                                               |        |             |
| ->execute_NpmInstall()                                           |        | developMode |
| ->execute_NpmRunBuild()                                          |        | developMode |
