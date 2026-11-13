# Documentación interna

## Start command

|                                                                  |              |             |
|------------------------------------------------------------------|--------------|-------------|
| ->new()->saveLock()                                              |              | developMode |
| ->publishKalionConfig()                                          |              | developMode |
| ->stubsCopyFile_DependencyServiceProvider()                      |              |             |
| ->stubsCopyFiles_Config()                                        |              |             |
| ->stubsCopyFiles_Migrations()                                    | skipExamples |             |
| ->stubsCopyFiles_Js()                                            |              |             |
| ->stubsCopyFolder_Factories()                                    | skipExamples |             |
| ->stubsCopyFolder_Seeders()                                      | skipExamples |             |
| ->stubsCopyFolder_Lang()                                         |              |             |
| ->stubsCopyFolder_Resources()                                    | skipExamples |             |
| ->stubsCopyFolder_Src()                                          | skipExamples |             |
| ->stubsCopyFile_RoutesWeb()                                      | skipExamples |             |
| ->createEnvFiles()                                               | skipExamples | developMode |
| ->deleteDirectory_Http()                                         | skipExamples |             |
| ->deleteDirectory_Models()                                       | skipExamples |             |
| ->deleteFile_Changelog()                                         |              |             |
| ->modifyFile_BootstrapProviders_toAddDependencyServiceProvider() | skipExamples |             |
| ->modifyFile_BootstrapApp_toAddMiddlewareRedirect()              |              |             |
| ->modifyFile_BootstrapApp_toAddExceptionHandler()                |              |             |
| ->modifyFile_ConfigApp_toUpdateTimezone()                        |              |             |
| ->modifyFile_JsBootstrap_toAddImportFlowbite()                   |              |             |
| ->modifyFile_Gitignore_toDeleteLockFileLines()                   |              | developMode |
| ->modifyFile_PackageJson_toAddNpmDependencies()                  |              |             |
| ->modifyFile_PackageJson_toAddScriptTsBuild()                    |              |             |
| ->modifyFile_PackageJson_toAddEngines()                          |              |             |
| ->modifyFile_ComposerJson_toAddSrcNamespace()                    | skipExamples |             |
| ->modifyFile_ComposerJson_toAddHelperFilePath()                  | skipExamples |             |
| ->execute_ComposerRequire_toInstallComposerDependencies()        |              | developMode |
| ->execute_ComposerDumpAutoload()                                 |              |             |
| ->execute_gitAdd()                                               |              |             |
| ->execute_NpmInstall()                                           |              | developMode |
| ->execute_NpmRunBuild()                                          |              | developMode |
