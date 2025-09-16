# Release Notes

## [Unreleased](https://github.com/kalel1500/kalion/compare/v0.33.1-beta.0...master)

## [v0.33.1-beta.0](https://github.com/kalel1500/kalion/compare/v0.33.0-beta.0...v0.33.1-beta.0) - 2025-09-16

### Added

* Nuevo helper `arr_is_assoc()`

### Changed

* Se ha modificado el método `make()` de la clase `AbstractDataTransferObject` para permitir que el método `fromArray()` reciba un array no asociativo (esto incluye el `fromArray()` de las colecciones de DTOs).
* Tests:
  * Nuevos tests `test_dto_pluck_with_backed_enum()` y `test_dto_pluck_field_only_in_to_array()` para probar que el pluck funciona con campos `enum` y con campos que solo existen en el método `toArray()`
  * (refactor) Ahora se usa el atributo `#[DataProvider('getPosts')]` en el test `test_post_relations()` para recibir los posts en vez de llamar al `useCase` en el test
  * (refactor) Se han sacado las validaciones del test `test_post_pluck()` fuera del `GetPostDataUseCase::getPluckData()`
* Se ha eliminado la reflexion en el método `pluck()` de la clase `AbstractCollectionBase`, ya que es innecesaria porque puede usar la función `property_exists()`. Ahora ya no se comprueba que la propiedad sea public.
* (refactor) Se han mejorado los nombres de métodos y variables de la clase `AbstractEntity`
  * Renombrar variables y métodos privados usados en el método `with()` de la clase `AbstractEntity`
  * Mejorar método `setDeepRelations()` de la clase `AbstractEntity` guardando en la variable `$relationItem` el valor `$this->relations[$relation]`
  * Mejorar método `setDeepRelations()` de la clase `AbstractEntity` renombrando la variable del foreach de `$item` a `$entity`

### Fixed

* (fix) Se han corregido varios errores del método `pluck()` de la clase `AbstractCollectionBase`:
  * Ahora se comprueba si la variable `$collectionItem` es una instancia de `BuildArrayable` antes de llamar al método `toArrayForBuild()`. Si no, devuelve `null`.
  * Ahora se comprueba si la variable `$collectionItem` es una instancia de `Arrayable` y en ese caso se llama al método `toArray()`.
  * Ahora se comprueba si el campo existe dentro del `toArrayForBuild()`. Si no existe se intenta sacar del método `toArray()`.
  * Ahora se comprueba que el valor de `$this->with` no sea null antes de intentar hacer el pluck de la relación.

## [v0.33.0-beta.0](https://github.com/kalel1500/kalion/compare/v0.32.1-beta.1...v0.33.0-beta.0) - 2025-09-11

### Added

* Nueva interfaz `Authentication` para la clase `AuthenticationService`.

### Changed

* (breaking) Se ha añadido el prefijo `thehouseofel.kalion.` a todos los alias de las Facades para evitar conflictos con otros paquetes. Nota: Si accedías a los servicios mediante alguno de los siguientes alias, deberás renombrarlos: 
  * `redirectAfterLogin`  &rarr;  `thehouseofel.kalion.redirectAfterLogin`
  * `redirectDefaultPath` &rarr;  `thehouseofel.kalion.redirectDefaultPath`
  * `processChecker`      &rarr;  `thehouseofel.kalion.processChecker`
* (refactor) Se han ordenado los métodos de la clase `KalionServiceProvider`
* (refactor) Ahora la Facade `Auth` se resuelve directamente contra el nuevo contrato `Authentication::class`. Nota: el alias de contenedor `authManager` ha sido eliminado. Si accedías al servicio mediante app(`authManager`), deberás usar `app(Authentication::class)`.
* Se ha eliminado la propiedad `final` de la clase `CurrentUserService` para hacerla heredable. También se ha modificado el `singleton` para apuntar a la configuración `kalion.auth.services.current_user` y por último se ha creado la variable de entorno `KALION_AUTH_SERVICE_CURRENT_USER` para poder definirla desde el `.env`
* (refactor) Ahora la Facade `LayoutData` se resuelve directamente contra el contrato `Thehouseofel\Kalion\Domain\Services\Contracts\LayoutData::class`. Nota: el alias de contenedor `layoutData` ha sido eliminado. Si accedías al servicio mediante app(`layoutData`), deberás usar `app(LayoutData::class)`.
* (refactor) Se ha mejorado el método `userEntity` de la clase `CurrentUserService` usando variables locales y añadiendo documentación para tipar la variable `$entityClass`
* (refactor) Se ha renombrado el método `entity()` de `CurrentUser` a `userEntity()`
* (breaking) Se han movido y renombrado los Atributos, Interfaces, Traits y otras clases:
  * Atributos:
    * `Domain/Attributes/CollectionOf.php` &rarr; `Domain/Objects/Collections/Attributes/CollectionOf.php`
    * `Domain/Attributes/Computed.php`     &rarr; `Domain/Objects/Entities/Attributes/CollectionOf.php`
    * `Domain/Attributes/RelationOf.php`   &rarr; `Domain/Objects/Entities/Attributes/CollectionOf.php`
  * Traits:
    * `Domain/Traits/HasIds.php`                                &rarr; `Domain/Concerns/Enums/HasIds.php`
    * `Domain/Traits/HasTranslations.php`                       &rarr; `Domain/Concerns/Enums/HasTranslations.php`
    * `Domain/Traits/ParsesRelationFlags.php`                   &rarr; `Domain/Concerns/Relations/ParsesRelationFlags.php`
    * `Domain/Traits/CountMethods.php`                          &rarr; `Domain/Concerns/CountMethods.php`
    * `Domain/Traits/Instantiable.php`                          &rarr; `Domain/Concerns/Instantiable.php`
    * `Domain/Traits/KalionAssertions.php`                      &rarr; `Domain/Concerns/KalionAssertions.php`
    * `Domain/Traits/Singelton.php`                             &rarr; `Domain/Concerns/Singelton.php`
    * `Domain/Traits/KalionExceptionBehavior.php`               &rarr; `Domain/Exceptions/Concerns/KalionExceptionBehavior.php`
    * `Domain/Traits/EntityHasPermissions.php`                  &rarr; `Domain/Objects/Entities/Concerns/EntityHasPermissions.php`
    * `Domain/Traits/HasGuard.php`                              &rarr; `Domain/Objects/Entities/Concerns/HasGuard.php`
    * `Domain/Traits/ModelHasPermissions.php`                   &rarr; `Infrastructure/Models/Concerns/ModelHasPermissions.php`
    * `Infrastructure/Traits/InteractsWithComposerPackages.php` &rarr; `Infrastructure/Console/Commands/Concerns/InteractsWithComposerPackages.php`
  * Interfaces:
    * `Domain/Contracts/IdentifiableEnum.php`                                      &rarr; `Domain/Contracts/Enums/Identifiable.php`
    * `Domain/Contracts/TranslatableEnum.php`                                      &rarr; `Domain/Contracts/Enums/Translatable.php`
    * `Domain/Contracts/Services/CurrentUserContract.php`                          &rarr; `Infrastructure/Services/Auth/Contracts/CurrentUser.php`
    * `Domain/Contracts/Services/LoginContract.php`                                &rarr; `Infrastructure/Services/Auth/Contracts/Login.php`
    * `Domain/Contracts/Services/PasswordResetContract.php`                        &rarr; `Infrastructure/Services/Auth/Contracts/PasswordReset.php`
    * `Domain/Contracts/Services/RegisterContract.php`                             &rarr; `Infrastructure/Services/Auth/Contracts/Register.php`
    * `Domain/Contracts/KalionExceptionInterface.php`                              &rarr; `Domain/Exceptions/Contracts/KalionExceptionInterface.php`
    * `Domain/Contracts/Relatable.php`                                             &rarr; `Domain/Objects/Collections/Contracts/Relatable.php`
    * `Domain/Contracts/BuildArrayable.php`                                        &rarr; `Domain/Objects/DataObjects/Contracts/BuildArrayable.php`
    * `Domain/Contracts/ExportableEntity.php`                                      &rarr; `Domain/Objects/Entities/Contracts/Exportable.php`
    * `Domain/Contracts/Services/LayoutDataContract.php`                           &rarr; `Domain/Services/Contracts/LayoutData.php`
    * `Domain/Contracts/Repositories/JobRepositoryContract.php`                    &rarr; `Domain/Contracts/Repositories/JobRepository.php`
    * `Domain/Contracts/Repositories/PermissionRepositoryContract.php`             &rarr; `Domain/Contracts/Repositories/PermissionRepository.php`
    * `Domain/Contracts/Repositories/RoleRepositoryContract.php`                   &rarr; `Domain/Contracts/Repositories/RoleRepository.php`
    * `Domain/Contracts/Repositories/StatusRepositoryContract.php`                 &rarr; `Domain/Contracts/Repositories/StatusRepository.php`
    * `Domain/Contracts/Repositories/TabulatorRepositoryContract.php`              &rarr; `Domain/Contracts/Repositories/TabulatorRepository.php`
    * (stubs) `Shared/Domain/Contracts/Repositories/CommentRepositoryContract.php` &rarr; `Domain/Contracts/Repositories/CommentRepository.php`
    * (stubs) `Shared/Domain/Contracts/Repositories/PostRepositoryContract.php`    &rarr; `Domain/Contracts/Repositories/PostRepository.php`
    * (stubs) `Shared/Domain/Contracts/Repositories/TagRepositoryContract.php`     &rarr; `Domain/Contracts/Repositories/TagRepository.php`
    * (stubs) `Shared/Domain/Contracts/Repositories/TagTypeRepositoryContract.php` &rarr; `Domain/Contracts/Repositories/TagTypeRepository.php`
  * Otras clases:
    * `Domain/Services/PermissionParser.php`                                      &rarr; `Domain/Services/Auth/PermissionParser.php`
    * `Domain/Services/Repository/UserAccessChecker.php`                          &rarr; `Domain/Services/Auth/UserAccessChecker.php`
    * `Domain/Services/Repository/LayoutData.php`                                 &rarr; `Domain/Services/BaseLayoutData.php`
    * (stubs) `Shared/Domain/Services/Repository/LayoutData.php`                  &rarr; `Shared/Domain/Services/Repository/AppLayoutData.php`
    * `Infrastructure/Repositories/Eloquent/ApiUserRepository.php`                &rarr; `Infrastructure/Repositories/Eloquent/EloquentApiUserRepository.php`
    * `Infrastructure/Repositories/Eloquent/JobRepository.php`                    &rarr; `Infrastructure/Repositories/Eloquent/EloquentJobRepository.php`
    * `Infrastructure/Repositories/Eloquent/PermissionRepository.php`             &rarr; `Infrastructure/Repositories/Eloquent/EloquentPermissionRepository.php`
    * `Infrastructure/Repositories/Eloquent/RoleRepository.php`                   &rarr; `Infrastructure/Repositories/Eloquent/EloquentRoleRepository.php`
    * `Infrastructure/Repositories/Eloquent/StatusRepository.php`                 &rarr; `Infrastructure/Repositories/Eloquent/EloquentStatusRepository.php`
    * `Infrastructure/Repositories/Eloquent/TabulatorRepository.php`              &rarr; `Infrastructure/Repositories/Eloquent/EloquentTabulatorRepository.php`
    * `Infrastructure/Repositories/Eloquent/UserRepository.php`                   &rarr; `Infrastructure/Repositories/Eloquent/EloquentUserRepository.php`
    * (stubs) `Shared/Infrastructure/Repositories/Eloquent/CommentRepository.php` &rarr; `Shared/Infrastructure/Repositories/Eloquent/EloquentCommentRepository.php`
    * (stubs) `Shared/Infrastructure/Repositories/Eloquent/PostRepository.php`    &rarr; `Shared/Infrastructure/Repositories/Eloquent/EloquentPostRepository.php`
    * (stubs) `Shared/Infrastructure/Repositories/Eloquent/TagRepository.php`     &rarr; `Shared/Infrastructure/Repositories/Eloquent/TagRepository.php`
    * (stubs) `Shared/Infrastructure/Repositories/Eloquent/TagTypeRepository.php` &rarr; `Shared/Infrastructure/Repositories/Eloquent/EloquentTagTypeRepository.php`
    * (stubs) `Shared/Infrastructure/Repositories/Eloquent/UserRepository.php`    &rarr; `Shared/Infrastructure/Repositories/Eloquent/EloquentUserRepository.php`
    * `Infrastructure/Services/Auth/CurrentUser.php`                              &rarr; `Infrastructure/Services/Auth/CurrentUserService.php`
    * `Infrastructure/Services/Auth/Login.php`                                    &rarr; `Infrastructure/Services/Auth/LoginService.php`
    * `Infrastructure/Services/Auth/PasswordReset.php`                            &rarr; `Infrastructure/Services/Auth/PasswordResetService.php`
    * `Infrastructure/Services/Auth/Register.php`                                 &rarr; `Infrastructure/Services/Auth/RegisterService.php`
    * `Infrastructure/Services/Auth/AuthManager.php`                              &rarr; `Infrastructure/Services/Auth/AuthenticationService.php`

### Fixed

* (fix) stubs: Se ha movido el `DependencyServiceProvider` de la carpeta `base` a la carpeta `examples`, ya que solo se usa con los ejemplos

## [v0.32.1-beta.1](https://github.com/kalel1500/kalion/compare/v0.32.1-beta.0...v0.32.1-beta.1) - 2025-09-05

### Changed

* Refactor interno sin impacto funcional: métodos de la clase `AbstractEntity` ordenados

## [v0.32.1-beta.0](https://github.com/kalel1500/kalion/compare/v0.32.0-beta.0...v0.32.1-beta.0) - 2025-09-05

### Added

* Nuevo test `test_post_pluck()` para probar el método `pluck()` de las colecciones con relaciones (usando el nuevo `GetPostDataUseCase`).
* Nueva excepción `ReflectionException`.
* Nuevo helper `is_class_model_id()`.

### Changed

* (refactor) Se ha eliminado propiedad `$withFull` de la clase `AbstractEntity` y guardar las relaciones completas en la propiedad `$with`.
* (refactor) Se han movido los métodos `setWith()` y `setIsFull()` y las propiedades `$with` y `$isFull` de las Colecciones `Relatables` al nuevo trait `HasRelatableOptions` para evitar duplicar código.
* Se han definido los métodos `setWith()`, `setIsFull()` y `fromArray()` en la interfaz `Relatable`.
* Se ha modificado la firma del método `fromArray()` de la clase `AbstractCollectionAny` para igualarla a la de la clase `AbstractCollectionEntity`
* (comments) Se han marcado los métodos `toArrayExport()` y `createFake()` de la clase `AbstractCollectionEntity` con `@experimental` para indicar que pueden ser eliminados o modificados.
* Se ha eliminado la constante `ITEM_TYPE` en la clase `AbstractCollectionAny`.
  * Se ha modificado método `resolveItemType()` de la clase `AbstractCollectionBase` para devolver `null` si la clase instanciada extiende de `AbstractCollectionAny`.
  * Ahora el método `resolveItemType()` puede devolver `null`.
  * Ahora la propiedad `$shouldSkipValidation` de la clase `AbstractCollectionBase` solo depende de si `resolveItemType()` devuelve `null` y no de la instancia de la clase (asi la responsabilidad solo depende del método `resolveItemType()`).
* Se ha cacheado la reflexión en el método `resolveItemType()` de la clase `AbstractCollectionBase`.
* Se han modificado los tests:
  * Métodos `computed` ordenados en las entidades.
  * Nuevo test `test_post_pluck()` para probar el método `pluck()` de las colecciones con relaciones.
  * Organizar el código de la carpeta `Support` por contextos para poder añadir más fácilmente. De momento hay `Blogs` y `Shared`.
  * La carpeta `Models` se ha movido dentro de `Infrastructure`.
  * Clase `RelationsTest` renombrada a `BlogRelationsTest` para indicar que es un test del contexto `Blog`. Se ha sacado de la carpeta `Entities`, ya que por ahora no hace falta.
* Se han cambiado las excepciones de la reflexión del método `make()` de la clase `AbstractDataTransferObject` de `AppException` por el nuevo `ReflectionException`.
* Método `props()` eliminado de todas las entidades, ya que ahora está definido en la clase `AbstractEntity`.
* Método `make()` eliminado de todas las entidades, ya que ahora está definido en la clase `AbstractEntity`.
* Se ha añadido la funcionalidad al método `props()` de la clase `AbstractEntity` usando la reflexion para no tener que crearlo en cada entidad.
* Se ha añadido la funcionalidad al método `make()` de la clase `AbstractEntity` usando la reflexion para no tener que crearlo en cada entidad.

### Fixed

* (fix) Se ha eliminado el parámetro `$isFull` del método `getInfoFromRelationWithFlag()` del trait `ParsesRelationFlags`, ya que en caso de tener dos relaciones concatenadas siendo la primera `full` y la segunda normal, la segunda heredaba el `full` de la primera
* (fix) Se ha arreglado el método `toAny()` de la clase `AbstractCollectionBase`. Ahora se pasan los parámetros `$with` y `$isFull` al `CollectionAny::fromArray()` siempre que la colección actual extienda de `Relatable` aunque `$with` sea `null`
* (fix) Se han arreglado los métodos `toArrayExport()` y `createFake()` de la clase `AbstractCollectionEntity`, ya que seguían usando la constante `ITEM_TYPE` que ya no se define siempre. Ahora se usan la propiedad `$resolvedItemType` el método `resolveItemType()` respectivamente.

## [v0.32.0-beta.0](https://github.com/kalel1500/kalion/compare/v0.31.0-beta.0...v0.32.0-beta.0) - 2025-08-31

### Added

* Se han añadido los primeros tests del paquete:
  * Nuevo `TestCase` base para los tests de integración con migraciones y seeders que se ejecutan solo una vez.
  * Nuevo `phpunit.xml` con la info de los tests y la variable de entorno `FRESH_DATABASE`.
  * Nuevo trait `KalionAssertions` con el método `assertArrayStructure` para comprobar la estructura de un array.
  * Nuevo directorio `tests/Support` con todos los archivos necesarios para realizar los tests.
  * Nuevo test `test_post_relations()` para probar las relaciones de las entidades del post (usando el nuevo `GetPostDataUseCase`).
  * Nuevo `server.php` para poder levantar un servidor en local y hacer pruebas.

### Changed

* (refactor) Se ha eliminado la propiedad `final` de varias clases
* Se ha cacheado toda la reflexión en el método `make()` de la clase `AbstractDataTransferObject` (no solo los parámetros)
* (breaking) Gran cambio en las entidades:
  * Métodos de las entidades renombrados `AbstractEntity`:
    * `toArrayProperties()` -> `props()`
    * `createFromArray()` -> `make()` (también en `AbstractDataTransferObject`)
  * Métodos `fromChild` eliminados de entidades heredables y usar los métodos normales en las hijas. De esta forma cualquier entidad es heredable sin tener que definir los métodos:
    * `createFromChildArray()`
    * `toArrayPropertiesFromChild()`
  * Se ha modificado la visibilidad del método `getRelation()` de `public` a `protected`
  * Se ha movido la gestion del cacheo de las propiedades calculadas a la entidad base para no tener que crear propiedades privadas readonly en cada entidad. Ahora se usa el nuevo método `computed()` dentro del método de cada propiedad pasándole un callback que solo se ejecuta la primera vez qeu se llama al método. El método `computed()` guarda los valores en forma de array en la nueva propiedad `$computed`.
  * Se ha eliminado el método `toArrayCalculatedProps()` (y la necesidad de ir creando varios métodos según las necesidades de cada vista). En su lugar ahora se utiliza el nuevo atributo `#[Computed]` en las propiedades calculadas. Se le pueden pasar argumentos con los nombres que antes se hubieran usado para crear nuevos métodos. Ej: `#[Computed('forDashboard', 'forApi')]`
  * Hacer estáticas las propiedades `$incrementing` y `$primaryKey` en la clase `AbstractEntity`
  * Se ha modificado la visibilidad de la propiedad `$databaseFields` de la clase `AbstractEntity` de `public` a `protected`
* (breaking) Se ha simplificado la gestion de las relaciones en el método `toAny()` de la clase `AbstractCollectionBase` 
  * Eliminada clase `SubRelationDataDto`
  * Eliminada clase `Relation`
  * Parámetro `$pluckField` eliminado del método privado `toAny()` ya que dentro ya no se llama al método `pluck()`
  * Toda la lógica del método `Relation::getNextRelation()` se ha movido dentro del método `AbstractCollectionBase::pluck()`
  * El método `getInfoFromRelationWithFlag()` de la clase `Relation` se ha movido al nuevo trait `ParsesRelationFlags` para poder usarlo tanto en la entidad como en la colección sin tener una clase dedicada
* (refactor) Mejorada la documentación del método `AbstractModelId::from()`
* (refactor) Usar parámetros nombrados al instanciar la clase `PaginationDataDto`
* (breaking) Firma del método `AbstractCollectionEntity::fromArray()` modificada. Se ha eliminado el tipado del parámetro `$data` y el tipado de retorno del método. Se ha añadido la documentación del método con un retorno condicional (con `@template`).
* (refactor) Se ha ampliado la documentación del método `AbstractEntity::fromArray()`

### Removed

* (breaking) Se ha simplificado la gestion de las relaciones
  * Se ha eliminado la clase `SubRelationDataDto`
  * Se ha eliminado la clase `Relation`
* (refactor) Se ha eliminado el método privado `fromData()` y movido el código al método `fromArray()` en la clase `AbstractCollectionEntity`
* (breaking) Se ha eliminado a la funcionalidad `fromEloquent` de las entidades y colecciones por lo que ahora en los repositorios es obligatorio usar el método `::fromArray()`. Se han eliminado los métodos `AbstractCollectionEntity::fromEloquent()` y `AbstractEntity::fromObject()`
* (breaking) Se ha eliminado a la funcionalidad `setRelation` de las entidades por lo que ahora es obligatorio usar el atributo `RelationOf`. Se ha eliminado el método `setRelation()`

### Fixed

* (fix) Prevenir errores en el método `make()` (antiguo `createFromArray`) de la clase `AbstractDataTransferObject` cuando el constructor del DTO usa unión o intersección de tipos
* (fix) Prevenir error si algún método de relación no tiene definido el atributo `#[RelationOf(...)]`

## [v0.31.0-beta.0](https://github.com/kalel1500/kalion/compare/v0.30.0-beta.0...v0.31.0-beta.0) - 2025-08-17

### Added

* (docs) Nuevo archivo `regex.md` para guardar los regex que puedan ser útiles. Pueden servir para hacer los refactors en los proyectos de los cambios de esta versión del paquete.

### Changed

* (refactor) Propiedad `$type` del atributo `CollectionOf` renombrada a `$class`
* (breaking) Se ha cambiado el sufijo `Do` de todas las clases por `Dto`
* (breaking) Clase `AbstractDataObject` renombrada a `AbstractDataTransferObject`
* (breaking) Se han movido todas las clases abstractas de las capetas `Contracts` a las carpetas `Abstracts`
* (breaking) Se ha modificado el prefijo `Contract` de todas las clases por `Abstract`

### Fixed

* (fix) Añadir valor inicial a la propiedad `$relations` de la clase `AbstractEntity` para prevenir un error cuando se intenta acceder a una relación de una entidad sin haber seteado ninguna otra anteriormente

## [v0.30.0-beta.0](https://github.com/kalel1500/kalion/compare/v0.29.2-beta.1...v0.30.0-beta.0) - 2025-08-14

### Added

* Nuevas clases añadidas al rehacer el sistema de comprobación de los servicios de Laravel:
  * Nuevos casos de uso `CheckProcessQueueUseCase` y `CheckProcessReverbUseCase` para obtener el estado de un servicio
  * Nuevo enum `CheckableProcessVo`
  * Nueva clase `ProcessChecker` para calcular si un proceso está activo en el servidor
  * Nueva fachada `ProcessChecker` para poder usar fácilmente los métodos de la clase `ProcessChecker`
* Nueva interfaz `TranslatableEnum` y nuevo trait `HasTranslations` para añadir fácilmente traducciones a los enums
* Nuevos métodos `keys()` y `whereNotIn()` añadidos en la clase `ContractCollectionBase`
* Nueva excepción `EntityRelationException` para gestionar todas las excepciones relacionadas con las relaciones de las entidades
* Nuevo Atributo `RelationOf` para poner en los métodos de las relaciones y asi evitar tener que poner un método `set` para cada relación
* Nueva excepción `KalionException` que extiende de Exception
* Nuevo método `doesntContain()` añadido a la clase `ContractCollectionBase`
* Nuevo método `toArrayForBuild()` añadido a la clase `ContractCollectionDo`

### Changed

* (breaking) Clase `ResponseBasicDo` renombrada a `ResponseCommonDto`
* (breaking) Se ha eliminado la clase abstracta `ContractResponseDefaultDo` y se ha trasladado el código a la clase `ResponseBasicDo`, ya que ahora se puede extender de ella directamente
* Se ha eliminado el modificador `final` de la clase `ResponseBasicDo` para que se pueda extender
* (breaking) Se han cambiado los métodos `emitEvent()` y `emitEventSimple()` de la clase `Broadcast` por `tryBroadcast()` y `annotateResponse()` y ahora cada uno tiene una sola responsabilidad
* (breaking) Se ha rehecho todo el sistema de comprobación de los servicios de Laravel:
  * Traducciones modificadas:
      * `k::service.*` -> `k::process.*`
      * `k::service.websockets.*` -> `k::process.reverb.*`
  * Rutas modificadas:
      * `kalion.ajax.queues.checkService` -> `kalion.ajax.process.broadcastQueueStatus`
      * `kalion.ajax.websockets.checkService` -> `kalion.ajax.process.broadcastReverbStatus`
  * Rutas añadidas:
      * `kalion.ajax.process.checkQueue`
      * `kalion.ajax.process.checkReverb`
  * Controladores `AjaxQueuesController` y `AjaxWebsocketsController` unificados en el `AjaxCheckProcessController` (lógica modificada, ya que ahora se usan los nuevos casos de uso `CheckProcessQueueUseCase` y `CheckProcessReverbUseCase`)
  * Excepción `ServiceException` renombrada a `ProcessException` y ahora extiende de `KalionException` en vez de `KalionRuntimeException`
  * Comando `ServiceCheck` renombrado a `ProcessCheck`. Firma renombrada de `kalion:service-check` a `kalion:process-check`. Lógica rehecha por completo usando el nuevo `ProcessChecker`
  * Eventos `EventCheckQueuesStatus` y `EventCheckWebsocketsStatus` unificados en el `EventCheckQueuesStatus` (nombre del canal cambiado a `process-status`)
  * Servicio `Queue` con el método `check()` eliminado. Ahora se puede llamar usando la Facade `ProcessChecker::assertQueue` en su lugar. Además, ya no guarda el estado en la caché.
* (breaking) Se han renombrado las siguientes clases:
  * la interfaz `EnumWIthIdsContract` -> `IdentifiableEnum`
  * el trait `WithIdsAndToArray` -> `HasIds`
  * el método `values` (de interfaz `EnumWIthIdsContract`) -> `ids`
* Añadido parámetro `$path` al helper `src_path()` para poder pasarle un path para concatenar al src
* Se han realizado cambios en las Colecciones y Entidades:
  * (internal) Se ha modificado el método `setFirstRelation()` para que lea el nuevo atributo `RelationOf` en vez del método `set` al asignar las relaciones
  * (break-command) Se han adaptado todas las entidades para usar el atributo `RelationOf` en vez de usar los métodos set en cada relación (incluyendo los stubs del `kalion:start`)
  * Se ha añadido el comentario `@deprecated` al método `setRelation()` para indicar que se eliminara en un futuro y que se debe usar el atributo `#[RelationOf()]`
  * (refactor) Se han eliminado los métodos `fromRelationData()` de las clases `ContractEntity` y `ContractCollectionEntity` y reemplazarlos por un `match` en el método `setRelation()` de la clase `ContractEntity` para simplificar la lógica
  * Se ha añadido el comentario `@deprecated` en los métodos `fromEloquent()`, `fromObject()` y `createFromObject()` para indicar que se eliminara en un futuro
  * Se ha modificado método `getRelation()` de la clase `ContractEntity` para dejar de recibir el parámetro `$name` y obtener el nombre de la relación del propio nombre del método de la relación. De esta forma ya no es necesario que cada relación de las entidades le pase un parámetro con el mismo valor que el nombre del método.
* (breaking) Se han realizado cambios en las Excepciones:
  * Interfaz `KalionException` renombrada a `KalionExceptionInterface`
  * Hacer que la Interfaz `KalionExceptionInterface` extienda de `Throwable`
  * Hacer que la Excepción `UnexpectedApiResponseException` extienda de la nueva `KalionException` en vez de la `KalionRuntimeException`
  * (breaking) Se ha substituido los usos excepciones eliminadas (`KalionException`, `KalionHttpException`, `KalionLogicException` y `KalionRuntimeException`) por la nueva excepción `EntityRelationException` usando los métodos estáticos para simplificar la gestion de las excepciones de las relaciones. Los nuevos métodos son:
    * `cannotDeleteDueToRelation()` (devuelve un 409)
    * `relationDataNotFound()`
    * `relationNotLoadedInEloquentResult()`
    * `relationNotSetInEntitySetup()`
  * (refactor) Usar parámetros nombrados al llamar al método `initKalionException()` en las excepciones base
  * Se ha añadido el nuevo parámetro `$statusCode` al final de los constructores de las clases `KalionException.php`, `KalionLogicException.php` y `KalionRuntimeException.php` para que una misma excepción pueda tener viarios métodos estáticos con diferentes códigos http.
  * Se han eliminado los modificadores `final` de las excepciones concretas para permitir su extensión desde las aplicaciones
* (breaking) Eliminado método `getWith()` de la clase `ContractEntity` (ya que seguramente el comportamiento del `with` cambie en el futuro)
* (breaking) Se han modificado los métodos de las colecciones para asimilarlos lo más posible a los métodos de eloquent (para evitar posibles futuros errores de compatibilidad):
  * Parámetro `$field` renombrado a `$value` en el método `pluck()` de la clase `ContractCollectionBase`
  * Se ha eliminado el tipado de retorno de los métodos de la clase `ContractCollectionBase`
  * Se ha eliminado el tipado de los parámetros en la clase `ContractCollectionBase`
  * Se han adaptado los métodos `first()`, `implode()` y `last()` de las colecciones para igualar las firmas y el comportamiento con los de Laravel
  * Eliminar tipado (`array`) de la propiedad `$items` de la clase `ContractCollectionBase`

### Removed

* (breaking) La clase `ResponseBasicDo` ha dejado de existir (renombrada)
* (breaking) Se ha eliminado la clase `ContractResponseDefaultDo`
* (breaking) Al rehacer el sistema de comprobación de los servicios de Laravel se han eliminado las siguientes clases:
  * `ServiceException`
  * `ServiceCheck`
  * `EventCheckWebsocketsStatus`
  * `EventCheckQueuesStatus`
  * `AjaxQueuesController`
  * `AjaxWebsocketsController`
  * `Queue`
* (breaking) Se ha eliminado las 4 excepciones de las relaciones de las entidades:
  * `HasRelationException`
  * `NotFoundRelationDataException`
  * `NotFoundRelationDefinitionException`
  * `UnsetRelationException`

### Fixed

* (fix) Usar el helper `object_to_array()` en el método `fromArray()` de la clase `ContractCollectionEntity` para convertir los datos a arrays si el primer item es un objeto
* (fix) Modificar la publicación de las clases de los componentes.
  * Ahora solo se publican las blades, ya que al tener el namespace las clases no se pueden sobreescribir. 
  * Lo que se publica ahora son unas nuevas clases de los componentes (guardadas en los stubs) que extienden de las originales en la ruta `Shared/Infrastructure/View/Vendor/Kal/Components`. 
  * También se ha creado un nuevo `componentNamespace` llamado `kal2` para acceder a estas clases y asi poder sobreescribir las originales.
* (fix) Modificar path de la publicación de las blades de `resources/views/vendor/kalion` a `resources/views/vendor/kal` ya que la carpeta se debe llamar igual que el prefijo
* (fix) Usar el helper `object_to_array()` para convertir el resultado del `->toArray()` de Eloquent a un array profundo cuando `$saveBuilderObject === true` en el método `fromEloquent()` de la clase `ContractCollectionEntity` (de esta forma se puede pasar el resultado de un QueryBuilder en vez de solo los resultados de modelos)

## [v0.29.2-beta.1](https://github.com/kalel1500/kalion/compare/v0.29.2-beta.0...v0.29.2-beta.1) - 2025-07-28

### Changed

* Refactors internos sin impacto funcional: 
  * Métodos `toOriginal()` y `toBase()` renombrados `toStatic()` y `toAny()` en la clase `ContractCollectionBase`
  * Eliminar método `getItemToArray()` (y mover la lógica dentro `toArray()`) en la clase `ContractCollectionBase`
  * Añadido el tipo de retorno `static` en el método `toStatic()`
  * Añadir documentación `PhpDoc` en los métodos de la clase `ContractCollectionBase`

## [v0.29.2-beta.0](https://github.com/kalel1500/kalion/compare/v0.29.1-beta.0...v0.29.2-beta.0) - 2025-07-28

### Added

* Nuevos métodos `toModel()` y `toNotModel()` añadidos a la clase `ContractValueObject` para poder convertir fácilmente las instancias de modelo a base y viceversa
* Se ha añadido el nuevo método `every()` en las colecciones (`ContractCollectionBase`)

### Changed

* stubs: Variables de los archivos `.env` ordenadas
* Se han reemplazado todos los tipos `ContractModelId` de los parámetros `$ids` por el tipo mixto `ModelId|ModelIdNull` en las entidades

## [v0.29.1-beta.0](https://github.com/kalel1500/kalion/compare/v0.29.0-beta.0...v0.29.1-beta.0) - 2025-07-22

### Changed

* (refactor) Se ha reemplazado la constante `ITEM_TYPE` por el atributo `CollectionOf` en todas las colecciones
* (refactor) Métodos ordenados en la clase `ContractCollectionBase`

## [v0.29.0-beta.0](https://github.com/kalel1500/kalion/compare/v0.28.0-beta.0...v0.29.0-beta.0) - 2025-07-22

### Added

* Nuevo atributo `CollectionOf` para añadir a las Colecciones y que no sea necesario definir el constructor y la constante `ITEM_TYPE` en cada colección.

### Changed

* (breaking) Se ha eliminado el `tryCatch` en el método `fromArray()` de la clase `ContractCollectionDo` por lo que ahora si recibe un tipo inesperado devuelve un `TypeError` en vez del `InvalidValueException`
* Se ha eliminado el constructor de todas las colecciones
* Se han modificado los métodos `fromArray()` de las colecciones (`ContractCollectionDo`, `ContractCollectionEntity` y `ContractCollectionVo`):
  * Ahora usan el nuevo método estático `resolveItemType()` para obtener la clase del atributo o de la constante (y de paso no tener que hacer la validación en cada una)
  * Se han añadido las keys recibidas al crear la colección (ya que ahora esta los puede mantener)
  * (breaking) Al crear las clases ahora se les pasa el array completo (sin desempaquetar) por lo que el constructor de las clases finales ya no puede recibir el parámetro asi `__construct(TypeClass ...$items)`
* (breaking) Modificar las colecciones (`ContractCollectionBase`) para poder mantener las keys tras usar cualquier método después de haber usado el `->put($key, $item)`
  * Añadido nuevo constructor en la clase `ContractCollectionBase` para permitir que las colecciones puedan recibir el array de items sin desempaquetar (asociativo o no)
  * (info) El parámetro `$items` del constructor ya no está tipado, pero dentro se valída el tipo definido en el atributo `#[CollectionOf(...)]` de la clase (si no existe el atributo usa la constante `ITEM_TYPE`)
  * El constructor usa los nuevos métodos `validateItems()` y `validateItem()` para validar que el tipo de los elementos (el constructor ahora guarda cálculos como `shouldSkipValidation` y `resolvedItemType` por lo que si se sobreescribe el constructor hay que llamar al padre)
  * Eliminado método `ensureIsValid()` y reemplazado el uso por `validateItem()` en el método `push()`
  * Añadida validación en el método `put()`
  * (todo) Eliminar el constructor de todas las colecciones (basta con definir la constante `ITEM_TYPE` o el atributo `#[CollectionOf(...)]`) o llamar al `parent::__construct`
* (breaking) Se ha simplificado la gestion de los tipos en las colecciones:
  * Eliminadas constantes `VALUE_CLASS_REQ` y `VALUE_CLASS_NULL` (de las colecciones VO), porque ahora las colecciones de VO no pueden ser generics (o son nullable o no)
  * Se ha modificado el método `fromArray` de la clase `ContractCollectionVo` para dejar de recibir el segundo parámetro `$nullable` ya que ahora estas colecciones solo pueden ser de un tipo
  * La clase `CollectionModelId` ahora solo puede contener items de tipo `ModelId` (antes también podían ser de `ModelIdNull`)
  * La clase `CollectionInts` ahora solo puede contener items de tipo `IntVo` (antes también podían ser de `IntNullVo`)
  * La clase `CollectionStrings` ahora solo puede contener items de tipo `StringVo` (antes también podían ser de `StringNullVo`)
  * Se han renombrado las constantes `ENTITY` y `VALUE_CLASS` de las colecciones que extienden de `ContractCollectionEntity` y `ContractCollectionVo` respectivamente a `ITEM_TYPE` para que todas las colecciones puedan ser definidas con una sola constante
  * Eliminada constante `IS_ENTITY`, ya que ahora todas las colecciones tienen la misma constante para guardar la clase

## [v0.28.0-beta.0](https://github.com/kalel1500/kalion/compare/v0.27.4-beta.1...v0.28.0-beta.0) - 2025-07-18

### Added

* Nuevo método `getWith()` en la clase `ContractEntity` para poder obtener el valor de las relaciones cargadas en la entidad

### Changed

* (refactor) Cambiar la forma de guardar las relaciones de las entidades en la clase `ContractEntity`. Guardarlas en el array `$this->relations[]` en vez de crear propiedades dinámicas
* (breaking) Cambiar los constructores de las excepciones a métodos estáticos que hagan el new para que siempre se pueda hacer el new desde fuera si no se quiere usar el mensaje por defecto

## [v0.27.4-beta.1](https://github.com/kalel1500/kalion/compare/v0.27.4-beta.0...v0.27.4-beta.1) - 2025-07-14

### Fixed

* (fix) Arreglar el acceso a la traducción de `k::text.input.matricula` en la configuración `kalion.auth.available_fields.matricula.label`

## [v0.27.4-beta.0](https://github.com/kalel1500/kalion/compare/v0.27.3-beta.0...v0.27.4-beta.0) - 2025-07-11

### Added

* Nuevos mensajes de error en las traducciones
* Nuevo helper `weighted_random_numbers`
* Nuevos value objects para añadir el tipo `Float`

### Fixed

* (fix) Arreglado error en el orden de las migraciones del comando `kalion:start` (usar un $timestamp global, ya que sino el método `addSecond()` las desordenaba)
* (fix) Adaptar el `StartCommandService` para que los métodos `modifyFile_BootstrapApp_toAddMiddlewareRedirect` y `modifyFile_BootstrapApp_toAddExceptionHandler` funcionen en las nuevas versiones de Laravel con los retornos `:void` en los callbacks
* (fix) Añadir tipo `string` al parámetro `$code` de la clase `ExceptionContextDo` (ya que hay errores de Laravel en los que el `$code` es de tipo string)

## [v0.27.3-beta.0](https://github.com/kalel1500/kalion/compare/v0.27.2-beta.0...v0.27.3-beta.0) - 2025-06-26

### Added

* Nueva excepción `UnexpectedApiResponseException` para cuando una api devuelve una estructura inesperada

### Changed

* (refactor) Mejorar lógica en el `ExceptionHandler.php` al renderizar las excepciones del dominio (kalion) para facilitar la lectura

### Fixed

* (fix) corregir los métodos `first()` de las colecciones usando el `parent::first()`, ya que fallaba cuando la primera key no era 0
* (fix) Prevenir error en el método `ensureIsValid()` de la clase `ContractCollectionBase` cuando recibe un array

## [v0.27.2-beta.0](https://github.com/kalel1500/kalion/compare/v0.27.1-beta.0...v0.27.2-beta.0) - 2025-06-20

### Changed

* ExceptionHandler: Mejorada clase `ExceptionHandler` para que al renderizar las excepciones `ModelNotFoundException` también se encargue cuando la respuesta sea JSON y el debug este activo para que muestre el origen del error (igual que se hace el método `get_html_laravel_debug_stack_trace()` en html pero en el Json)

## [v0.27.1-beta.0](https://github.com/kalel1500/kalion/compare/v0.27.0-beta.0...v0.27.1-beta.0) - 2025-06-19

### Added

* Nuevo método `get` añadido a las colecciones (`ContractCollectionBase`)
* Nuevo método `put` añadido a las colecciones (`ContractCollectionBase`)

### Changed

* Mejorado el método `push` de la clase `ContractCollectionBase` para que acepte multiples parámetros (igualado al comportamiento de Laravel)

### Fixed

* (fix) Arreglar método `push` de la clase `ContractCollectionBase` para que devuelva la propia colección `$this`

## [v0.27.0-beta.0](https://github.com/kalel1500/kalion/compare/v0.26.0-beta.0...v0.27.0-beta.0) - 2025-05-29

### Added

* Nuevo método `tryFromId()` en el trait `WithIdsAndToArray` para poder crear el enum desde un `id` de forma segura (y que si no existe devuelva null)
* Nuevo helper `vite_asset()` para usar en lugar de la directiva `@viteAsset()` ya que los ids no la reconocen y puede ser más confuso
* Nuevo parámetro `--skip-examples` añadido al comando `kalion:start` para no generar los archivos relacionados con los ejemplos de `Home`, `Posts`, `Tags` y `Comments`
* Nuevo sistema de excepciones basado en un trait base para poder dividir las excepciones del paquete en `LogicException` y `RuntimeException`:
  * Nuevo trait `KalionExceptionBehavior` con la lógica que había en `KalionException`
  * Nueva interfaz `KalionException`
  * Nuevas excepciones creadas que usan el trait `KalionExceptionBehavior` y extienden de la interfaz `KalionException`
    * `KalionHttpException`
    * `KalionLogicException`
    * `KalionRuntimeException`
* Nuevo middleware `ForceArraySessionInCloud` añadido al grupo de rutas `web` para evitar que se guarde una sessión cada vez que el cloud hace una petición a la ruta `/health` y asi evitar que se llene la tabla `sessions`. Se han añadido las siguientes variables de entorno:
  * `KALION_WEB_MIDDLEWARE_FORCE_ARRAY_SESSION_IN_CLOUD_ACTIVE`
  * `KALION_WEB_MIDDLEWARE_FORCE_ARRAY_SESSION_IN_CLOUD_CLOUD_USER_AGENT_VALUE`
* Nuevas variables de entorno para configurar las rutas de las imágenes `logo.svg` y `favicon.ico`:
  * `KALION_LAYOUT_ASSET_PATH_LOGO`
  * `KALION_LAYOUT_ASSET_PATH_FAVICON`

### Changed

* (refactor) Añadir return `static` en el trait `WithIdsAndToArray` en vez de usar los tipos genéricos de PHPDoc
* Añadir la ruta `welcome` en el método `defaultRedirectUri()` de la clase `Redirector` por si no existieran las rutas `dashboard` y `home`
* (refactor) Mejorar la importación de la directiva `@vite()` en el componente `layout/app.blade.php` usando un if ternario para obtener la extension del JS
* Cambios en el comando `kalion:start`:
  * (internal) Cambios internos en los métodos del `StartCommandService` para mejorar el control del flujo:
  * Desacoplar el comando `kalion:start` de NPM para no depender del entorno de Nodejs:
    * Añadir las dependencias de NPM manualmente al `package.json` en vez de hacer el install (hacer una petición al registro de `npmjs` para obtener la última versión de cada dependencia)
    * Añadir todos los archivos que generaban en con el comando `npx kalion-js` en los `stubs`
  * Añadir el comando `composer dump-autoload` a la cadena de ejecuciones
  * Hacer que en `developMode` se añadan las dependencias al archivo `composer.json` manualmente sin ejecutar el comando `composer require`
  * (breaking) Eliminar el parámetro `$simple` el comando `kalion:start` ya que las configuraciones del js son necesitaras (eliminado método que añadía el import de flowbite al bootstrap.js)
  * Añadir mensaje en `developMode` para dar feedback aunque no se ejecute el método
  * Saltar las acciones largas (instalaciones y llamadas a la api) en `developMode`
  * Mover los mensajes al inicio de cada método (adaptar contenido indicando que está iniciando) y añadir uno o varios mensajes durante y al final (con más sangria y de color verde) para dar feedback de como está yendo el proceso
* <u>**!!! (breaking) !!!**</u> Reestructurar las excepciones base para poder dividir las excepciones del paquete entre las `LogicException` y las `RuntimeException`:
    * Excepciones eliminadas:
      * `BasicException`
      * `BasicHttpException`
      * `KalionException`
  * Cambiar las referencias de la antigua excepción `KalionException` a la nueva interfaz `KalionException`
  * Cambiar las referencias de la antigua excepción `BasicHttpException` a la nueva `KalionHttpException`
* (breaking) Excepciones modificadas:
  * Hacer `public` y `readonly` la propiedad $title del `ExceptionContextDo` y eliminar el método `getTitle()`
  * Eliminar el método `getStatusCode()` del `ExceptionContextDo` y usar la propiedad publica `statusCode`
  * Mover el parámetro `$code` del constructor de la clase `BasicHttpException` detrás del `$previous` para igualar el orden con la clase `BasicException`
* (breaking) Renombrar tabla `states` a `statuses` y el modelo de `State` a `Status` (renombradas las clases entidad colección y repository)
* Renombrar configuraciones y variables de entorno:
  * (breaking) `kalion.enable_preferences_cookie` => `kalion.web_middlewares.add_preferences_cookies.active`
  * (breaking) `KALION_ENABLE_PREFERENCES_COOKIE` => `KALION_WEB_MIDDLEWARE_ADD_PREFERENCES_COOKIES_ACTIVE`
  * `kalion.force_array_session_in_cloud` => `kalion.web_middlewares.force_array_session_in_cloud.active`
  * `KALION_FORCE_ARRAY_SESSION_IN_CLOUD` => `KALION_WEB_MIDDLEWARE_FORCE_ARRAY_SESSION_IN_CLOUD_ACTIVE`
* Hacer configurable las rutas de las imágenes `logo.svg` y `favicon.ico` en los componentes con las nuevas variables de entorno `KALION_LAYOUT_ASSET_PATH_LOGO` y `KALION_LAYOUT_ASSET_PATH_FAVICON`
* Mejoras en los componentes de la layout:
  * Mejorar estilos botón `logout` para dar un feedback al usuario cuando se ha clicado
  * Mejorar estilos `sidebar` cuando está colapsado (centrar texto cuando hay saltos de línea):
    * Botón del dropdown: Permitir saltos de línea y centrar texto [eliminar: `whitespace-nowrap`, añadir: `sc:text-center`]
    * Enlace: Centrar texto de primer nivel cuando no tiene counter [añadir: `'text-center' => (!isset($counter) && $level === '0')`]

### Fixed

* (fix) startCommand: Prevenir error en la ejecución del `composer require` y en ese caso hacer que se añadan las dependencias al archivo `composer.json` manualmente
* (fix) startCommand: Guardar todos los archivos `stubs` al generar el `kalion.lock` en el método `saveLock()` incluidos los que empiezan por `.`
* (fix) startCommand: Prevenir errores al ejecutar el método `execute_Process()` de la clase `StartCommandService`

## [v0.26.0-beta.0](https://github.com/kalel1500/kalion/compare/v0.25.1-beta.0...v0.26.0-beta.0) - 2025-05-01

### Added

* Nueva ruta `/` llamada `index` que redirige a la ruta por defecto (`default_url()`)
* Nuevas Facades `RedirectDefaultPath` y `RedirectAfterLogin` para acceder de forma estática al método `::redirectTo()` de las clases que extienden de `Redirector`
* Nuevo método `redirectDefaultPathTo()` en la clase `Kalion` para poder configurar la ruta por defecto de la aplicación en el `ServiceProvider`
* Nueva clase `RedirectDefaultPath` (extiende de `Redirector`) para centralizar la lógica de la url por defecto
* Nueva clase abstracta `Redirector` con la lógica genérica de la clase `RedirectAfterLogin` para mejorar reutilización y mantener consistencia en redirecciones
* Nueva configuración `kalion.default_path` para configurar la ruta por defecto de la aplicación

### Changed

* (stubs) Número del método `getMessageCounter()` de la clase `LayoutData` modificado para diferenciar fácilmente si se aplica esta clase
* (breaking) Clases `Layout` renombradas a `LayoutData` (interfaz, clase, fachada y clase en los stubs)
* Archivo `README.md` actualizado
* Se previno error si no existe la ruta con el nombre `index` en las blades usando el helper `safe_route()` (en caso de que se añada la ruta `/` en la aplicación con otro nombre)
* (breaking) Nuevo parámetro `$default` añadido al helper `safe_route()` para poder devolver una url por defecto. Comportamiento modificado: Si no recibe este parámetro ahora devuelve `null`, para seguir devolviendo `#` tiene que recibirlo como parámetro
* (breaking) Parámetro `$route` del helper `safe_route()` renombrado a `$name` y tipado con `string|null`
* (breaking) Helper `get_url_from_route()` renombrado a `safe_route()`
* (breaking) Clase `KalionController` renombrada a `TestController`
* (breaking) El método `redirectTo()` de la clase `Redirector` ahora siempre devuelve la url completa
* (breaking) El método `redirectTo()` de las clases de redirección (`extends Redirector`) deja de ser estático. Ahora se usan las fachadas para acceder al método (`::redirectTo()`).
* (breaking) Las siguientes clases ahora pasan a ser internas del paquete (`@internal`) por lo que, a partir de esta versión, pueden no mantener compatibilidad:
    <details>
    <summary>Mostrar</summary>

    * `src\Application\GetIconsUseCase`
    * `src\Domain\Contracts\Services\CurrentUserContract`
    * `src\Domain\Contracts\Services\LoginContract`
    * `src\Domain\Contracts\Services\PasswordResetContract`
    * `src\Domain\Contracts\Services\RegisterContract`
    * `src\Domain\Objects\DataObjects\ExceptionContextDo`
    * `src\Domain\Objects\DataObjects\LoginFieldDto`
    * `src\Domain\Objects\DataObjects\SubRelationDataDo`
    * `src\Domain\Services\Repository\UserAccessChecker`
    * `src\Domain\Services\PermissionParser`
    * `src\Domain\Services\Relation`
    * `src\Domain\Services\TailwindClassFilter`
    * `src\Domain\Traits\Singelton`
    * `src\Infrastructure\Http\Controllers\Web\TestController`
    * `src\Infrastructure\Services\Auth\CurrentUser`
    * `src\Infrastructure\Services\Auth\Login`
    * `src\Infrastructure\Services\Auth\PasswordReset`
    * `src\Infrastructure\Services\Auth\Register`
    * `src\Infrastructure\Services\Commands\PublishAuthCommandService`
    * `src\Infrastructure\Services\Commands\StartCommandService`
    * `src\Infrastructure\Services\Config\Redirect\DefaultPath`
    * `src\Infrastructure\Services\Config\Redirect\RedirectAfterLogin`
    * `src\Infrastructure\Services\Config\Redirect\Redirector`
    * `src\Infrastructure\Traits\InteractsWithComposerPackages`

    </details>
* Mejorada la lógica helper `default_url()`:
  * Usar la nueva clase `RedirectDefaultPath::redirectTo()` en vez de concatenar los valores de las configuraciones `app.url` y `kalion.default_route`
  * Prevenir redirección masiva lanzando una excepción si no se ha encontrado una url por defecto en el helper `default_url()`
* (refactor) Clase `RedirectAfterLogin` refactorizada extrayendo lógica común a la clase abstracta `Redirector` para mejorar reutilización y mantener consistencia en redirecciones
* Helper `app_url()` mejorado: Se reemplaza `config('app.url')` por `url('/')` para mayor consistencia con la URL generada por Laravel

### Fixed

* (fix) Se corrigió el nombre del paquete en el `README.md`

### Removed

* (breaking) Eliminada ruta de test `/kalion/sessions`
* (breaking) Eliminado método `root` del Controller `Kalion`
* (breaking) Eliminada ruta `kalion.root` (modificados los enlaces que la usaban para usar a la nueva ruta `index`)
* (breaking) (stubs) Eliminada ruta `/` de las rutas de los `stubs` (ya que se ha movido al paquete)
* (breaking) Eliminado helper `default_route()`
* (breaking) Eliminada configuración `kalion.default_route_name`
* (breaking) Eliminada configuración `kalion.default_route`

## [v0.25.1-beta.0](https://github.com/kalel1500/kalion/compare/v0.25.0-beta.0...v0.25.1-beta.0) - 2025-04-30

### Added

* Nueva clase `RedirectAfterLogin` para centralizar la lógica de redirección tras el `login` (si no está configurado, busca si existen las rutas `dashboard` y `home` y si no existen redirige a la `/`)
* Nuevas opciones de configuración de la ruta a la que redirigir tras el login:
  * En el archivo de configuración `kalion.auth.redirect_after_login` (con la variable de entorno `KALION_AUTH_REDIRECT_AFTER_LOGIN`)
  * En el método `register()` del `AppServiceProvider` usando la clase `Kalion` (`Kalion::redirectAfterLoginTo('home')`) 

### Changed

* Modificar la propiedad `engines` añadida al `package.json` en el comando `kalion:start`:
  * Ya no se añade la propiedad `npm` (en el método `modifyFile_PackageJson_toAddEngines()`)
  * Modificar el valor por defecto de la configuración `kalion.version_node` de `^20.11.1` a `>=20.11.1` para restringir solo la version `minima` y permitir instalar versiones de Node superiores a la 20
* (stubs) Actualizar archivos de `stubs/original` para coincidir con la última version de Laravel 12 (`stubs/original/resources/css/app.css`)

### Fixed

* (fix) Añadir la opción `reset` en el método `saveLock()` para borrar el archivo `kalion.lock` al hacer el `reset` si existe
* (fix) Arreglar redirección errónea después del login cuando no existe la ruta `dashboard` incorporando lógica en la nueva clase `RedirectAfterLogin`

### Docs

* Añadir una pequeña documentación sobre el comando `kalion:start` en el `README.md`
* (fix) Arreglar el comando de instalación del `README.md` añadiendo el `@beta` (`composer require kalel1500/kalion:@beta`)

## [v0.25.0-beta.0](https://github.com/kalel1500/kalion/compare/v0.24.0-beta.0...v0.25.0-beta.0) - 2025-04-25

### Changed

* Modificar comando `kalion:start` (StartCommandService) 
  * Guardar un archivo `kalion.lock` durante el comando `kalion:start` con la version del paquete y los `stubs` generados por si en futuras versiones del paquete se realizan cambios en el comando (que rompen compatibilidad) y se quisieran terminar de borrar archivos que el `--reset` ha dejado de contemplar
  * Nuevo método `deleteLastVersionFiles()` en el comando `kalion:start` por si en el futuro queremos borrar archivos que el `--reset` ha dejado de contemplar cuando se actualiza el paquete (por ahora el método no se usa)
* Actualizar dependencia `@kalel1500/kalion-js` a la versión `^0.9.0-beta.0` y adaptar el `app.css` para importar `flowbite`
* Refactorizar archivos de los ejemplos (stubs):
  * Modificar todas las referencias a `Admin` por `Tags`
  * Modificar todas las referencias a `Dashboard` por `Posts`
  * Modificar todas las referencias a `Default` por `Home`
  * Hacerlo con las carpetas, controllers, casos de uso, data objects, rutas, vistas, links y también en el JS
  * Carpeta `app` del Js renombrada a `config`

## [v0.24.0-beta.0](https://github.com/kalel1500/kalion/compare/v0.23.1-beta.0...v0.24.0-beta.0) - 2025-04-22

### Added

* (stubs) Variables de entorno añadidas al `.env.save.local`:
  * `KALION_AUTH_BLADE_FAKE=kal::pages.auth.landing`
  * `KALION_PACKAGES_TO_SCAN_FOR_JOBS=`
* Nuevos archivos de traducciones `lang/en/text.php` y `lang/es/text.php`
* Nuevo valor `text_align` añadido a la propiedad `$specials` de la clase `TailwindClassFilter`
* Nuevos componentes:
  * Nuevo componente `input.label`
  * Nuevo componente `input` (con borde rojo si el campo que recibe contiene algún error)
  * Nuevo componente `input.full.checkbox`
  * Nuevo componente `form.button`
  * Nuevo componente `form.question-link`
  * Nuevo componente `input.error`
  * Nuevo componente `form.checkbox-terms`
* Añadidas traducciones al inglés
* Nuevo sistema de `Auth` con login y registro reales con estilos de Flowbite y el código backend del paquete `laravel/breeze` 
  * Nuevo componente `layout/guest` (con estilos tailwind de Flowbite)
  * Nueva vista `login`
  * Nuevo `register` (vista, ruta y controlador)
  * Nuevo `password reset` (vista, ruta y controlador)
  * Nuevas configuraciones `auth.disable_register` y `auth.disable_password_reset` para poder ocultar los enlaces del `login`
  * Nuevo servicio `Login` con el código de Laravel para iniciar y cerrar sessión (extraído del paquete `laravel/breeze`)
  * Nuevo servicio `Register` con el código de Laravel para hacer el registro 
  * Nuevo servicio `PasswordReset` con el código de Laravel para resetear la contraseña 
  * Hacer que los controller `Auth` solo llamen a los servicios anteriores mediante la fachada `Auth`
  * Hacer configurables todos los servicios de `Auth` utilizando el `Service Container`, `Facades`, `Interfaces` y la configuración para que se puedan sobreescribir desde la aplicación

### Changed

* Mejorar valores de las configuraciónes `kalion.auth.available_fields.id`
* Mejorar estilos mensaje de error en la blade `pages.auth.landing`
* (refactor) Cambiar la forma en que se carga el `singleton` `layoutService`. Usar el método `alias` para enlazar con la interfaz y definir otro `singleton` para esa interfaz
* Hacer que el `LayoutService` (ahora `Layout`) de la aplicación extienda del `LayoutService` del paquete y mover la lógica del método `getUserInfo` al `LayoutService` del paquete
* Hacer que el `LayoutService` (ahora `Layout`) se pueda configurar desde la configuración de Laravel para no tener que definirlo en el `DependencyServiceProvider` de la aplicación
* Gran refactor de los nombres y ubicaciones de varias clases y métodos
  * <u>**!!! (breaking) !!!**</u> Renombrar clase `WebsocketsService` a `Broadcast`
  * <u>**!!! (breaking) !!!**</u> Renombrar clase `QueueService` a `Queue`
  * Renombrar clase `CookieService` a `Cookie`
  * Renombrar clase `AuthService` a `CurrentUser` y moverla dentro de la carpeta `Auth`
  * Renombrar `AuthService::userEntity()` a `Auth::user()` y hacer que la fachada apunte al nuevo servicio `AuthManager` (de esta forma se podrán ir añadiendo más servicios de `auth` que serán publicados por el `AuthManager`)
  * Renombrar método `userEntity()` de la clase `CurrentUser` a `entity()`
  * <u>**!!! (breaking) !!!**</u> Renombrar helper `userEntity()` a `user()`
  * Renombrar `LayoutService` a `Layout` y mover de la carpeta `RepositoryServices` a la carpeta `Repository`
  * Mover `TagTypeService` de la carpeta `RepositoryServices` a la carpeta `Repository`
  * Renombrar `AuthorizationService` a `UserAccessChecker` y mover a la carpeta `Repository`
* Añadir el `@see` en la documentación de las `facades` para indicar la clase que implementa los métodos de la fachada
* Archivos de `lang` (traducciones) actualizados: 
  * Pasar literales de los componentes a traducciones
  * Usar traducciones al definir el `label` de los `fields` en la configuración `kalion.auth.available_fields`
  * Mejorar traducción `auth.user_not_found`
* Componentes modificados:
  * (breaking) componentes `select` y `textarea` movidos de `form` a `input`
  * (breaking) Simplificar componente `input.select`
  * (breaking) Simplificar componente `input.textarea`
  * Añadir clase `font-medium` al componente `link`
  * Añadida la propiedad `value` al componente `link` para poder pasar el texto como propiedad
  * Añadida la propiedad `type` al componente `button`
  * Añadido el valor `blue-form` a la propiedad `color` del componente `button`
  * Componente `form` rehecho con nuevas clases y nuevos parámetros `method` y `action`
  * Comentar línea innecesaria en el componente `messages`, ya que la variable `$errors` ya está disponible por defecto
* Modificar condición al sobreescribir la `config('auth.providers.users.model')` en el `Kalion::setAuthApiGuards()` para que se sobreescriba solo si tiene el valor por defecto (asi no es obligatorio declarar la variable de entorno `AUTH_MODEL`)
* (refactor) Eliminar parámetro mensaje del `NotFoundHttpException` en el método `KalionController::sessions()`
* Paquete `@kalel1500/kalion-js` actualizado a la version `^0.7.0-beta.2`
* Componentes modificados:
  * (refactor) Componente `layout/auth/landing.blade.php` movida fuera de la carpeta `auth`
  * (refactor) Blade `pages/auth/fake.blade.php` renombrada a `pages/auth/landing.blade.php`
  * Mover el JS para cargar el `darkMode` a un nuevo componente `js/dark-mode.blade.php`
  * Ordenar y comentar el `head` del componente `layout/app.blade.php`
  * Mejora componente `dark-mode.blade.php` para que busque el `theme` en el `localStorage` si no lo encuentra en el `html`
  * (stubs) Usar el componente `js/dark-mode` en la blade `welcome.blade.php` de los stubs
  * Añadir atributo `type` en el `<link rel='icon'>` del componente `layout/app`
* Repositorios modificados:
  * Hacer heredables todos los Repositorios (quitar la palabra reservada `final` de las clases y hacer `protected` las propiedades)
  * (refactor) Renombrar las propiedades `$model` de los repositorios
  * (refactor) Establecer el valor de las propiedades `$model` de los Repositorios directamente en la propiedad en vez de en el constructor
* Mejoras en las clases de `jobs`: 
  * Inyectar `JobRepositoryContract` en el `AjaxJobsController` (añadir `singleton` en el `KalionServiceProvider`)
  * Inyectar los `UseCases` en el `AjaxJobsController` en vez de instanciarlos y pasarle el repository
  * Devolver directamente los `$jobs` en el parámetro `$data` del `response_json()` en el `AjaxJobsController`
* <u>**!!! (breaking) !!!**</u> (refactor) Mover todos los Repositorios dentro de la carpeta `Eloquent` y renombrarlos para quitar el sufijo `Eloquent` del nombre
* Comando `kalion:publish-auth` modificado
  * (refactor) Método `publishConfigKalionUser()` renombrado a `publishConfigKalionAndUpdateClasses()` en la clase `PublishAuthCommandService`
  * Añadir nuevo parámetro `--onlyUpdate` al comando `kalion:publish-auth` para no publicar la configuración `config/kalion.php`
  * Si se reciben los dos parámetros `--reset` y `--onlyUpdate` hacer que se restaure el contenido en vez de borrar el archivo `config/kalion.php`
* Sistema de `Auth` mejorado:
  * (refactor) Usar el `route('dashboard', absolute: false)` en vez de `'/dashboard'` al redirigir tras hacer el login
  * Regenerar el id de sessión tras hacer login
  * Renombrar el `AuthController` a `LoginController`
  * Mover el `LoginController` a la carpeta `Auth`
  * (breaking) Archivo `config/kalion_user.php` eliminado y configuraciones movidas al archivo `config/kalion.php`
    * Configuraciones de entorno renombradas: `kalion_user.` -> `kalion.auth`
    * Variables de entorno renombradas:
      * `KALION_USER_ENTITY_WEB` -> `KALION_AUTH_ENTITY_WEB`
      * `KALION_USER_REPOSITORY_WEB` -> `KALION_AUTH_REPOSITORY_WEB`
* Mover las clases `UserFactory` y `UserSeeder` de la aplicación (`stubs`) al paquete e indicar la `UserFactory` en el modelo `User` del paquete
* Modificar migraciones de roles y permisos
  * Añadir campo `description` en las tablas `roles` y `permissions`
  * Añadir índice `unique` en los campos `name` de las tablas `roles` y `permissions`
* (refactor) Usar el método `disableFor()` en vez del `except()` de la clase `EncryptCookies` (con el `afterResolving`) para evitar el encriptado de las cookies de las preferencias del usuario
* Archivos de stubs modificados: 
  * Pasar campo `$other_field` de la clase `UserEntity` a `promoted property`
  * Eliminar código genérico modelo `User` de la aplicación (`stubs`) y extender del modelo del paquete
  * Mover el enlace a la vista `welcome` debajo de un separador (en la configuración `config/kalion_links.php`)

### Fixed

* (fix) Corregido error al instanciar el repositorio del usuario en los métodos `userHasPermission()` y `userHasRole()` de la clase `AuthorizationService` (faltaba el `new`)
* (fix) Corregido error al comprobar los roles del usuario en el middleware `UserHasRole` (se usaba el método `can` que es para permisos en vez del `is` ()
* (fix) Añadir parámetro `$guard` en la definición del método `userEntity` de la interfaz `CurrentUserContract` (antes llamada `AuthServiceContract`)
* (fix) Corregido error en la clase `TailwindClassFilter` al filtrar las clases de la propiedad `$specials` (se filtraban solo cuando las especiales estaban en origen y ahora también cuando son las custom) + Nuevos tests añadidos para comprobar que funciona
* (fix) Reinstalar `phpunit/phpunit` para poder pasar los tests (se desinstalo al eliminar el `orchestral/testbench`)
* (fix) Devolver `null` en el método `getUserInfo()` de la clase `LayoutService` si `userEntity()` es `null` para evitar errores al entrar a una blade con el layout `layout/app` que este fuera del middleware `auth`
* (fix) Auth: Añadir `->withInput()` en el `redirect()` del `LoginController` para mostrar el valor antiguo en el formulario
* (fix) Arreglar orden migraciones en el comando `kalion:start` (`StartCommandService`)
* (fix) Limitar la query de usuarios a 10 en los seeders de `Post` y `Comment` para asegurar que no se insertan miles de registros
* (fix) Arreglar métodos `down()` de las migraciones para poder ejecutar el `rollback` sin errores
* (fix) stubs: Usar dos `\` en las clases definidas en el `.env.save.local`

### Removed

* Eliminar el `@template` del `PHPDoc` para indicar el tipo `UserEntity`

## [v0.23.1-beta.0](https://github.com/kalel1500/kalion/compare/v0.23.0-beta.0...v0.23.1-beta.0) - 2025-04-10

### Added

* Nuevo trait `HasGuard` para guardar la guard en las entidades de usuario
* Api: Nueva migración `api` con las tablas:
  * api_users
  * api_role_user
  * api_logs

### Changed

* Modificar la clase `PublishAuthCommandService` del comando `PublishAuth`:
  * (refactor) Eliminar `\` inicial en las clases del archivo `config/kalion_user.php`
  * Modificar método `publishConfigKalionUser()` para que tras publicar la configuración `kalion_user.php` la modifique para añadir las clases por defecto de la aplicación
  * (fix) Corregir método `modifyFile_ConfigAuth_toUpdateModelAndAddApi()` para que solo añada el `guard` y el `provider` si no existen
* Obtener la clase del `UserRepository` de la configuración `Kalion::getClassUserRepository($guard)` en vez de instanciar el `UserRepositoryContract` en la clase `AuthorizationService` para poder pasar el `$guard` y que se instancie el repository que toque
* Pasar el parámetro `$guard` en el helper `userEntity()` para pasarlo al `AuthService` para guardarlo al instanciar la entidad

### Fixed

* (fix) Corregir nombre clase usuario de la variable de entorno `AUTH_MODEL` en el `.env.save.local`
* (fix) corregir nombre campo `name` en el método `toArrayProperties()` de la clase `ApiUserEntity`

### Removed

* Eliminar la interfaz `UserRepositoryContract`, que ya no se usa (eliminarlo también de los `stubs`)

## [v0.23.0-beta.0](https://github.com/kalel1500/kalion/compare/v0.22.0-beta.0...v0.23.0-beta.0) - 2025-03-30

### Changed

* Eliminar las comprobaciones `Schema::hasTable` en las migraciones
* Mover la migración `create_permission_tables.php` de los `stubs` al paquete (`database/migrations`), ya que no es una migración de ejemplo, sino que pertenece al paquete (comando `start` modificado para copiar también las migraciones que hay en la carpeta `database/migrations`)
* (refactor) `StartCommandService.php` movido a la carpeta `commands`
* (refactor) stubs: eliminado archivo `web_php_old.php` (comando `start` modificado)
* stubs: Nuevo archivo de helpers `helpers_domain.php` (comando `start` modificado para añadirlo al `composer.json`)
* (refactor) Helpers renombrados `DomainHelpers.php` a `helpers_domain.php` y `InfrastructureHelpers.php` a `helpers_Infrastructure.php`
* (refactor) Renombrar método `configure()` a `mergeConfig()` en el `KalionServiceProvider`
* (refactor) Mover el seteo de la configuración al método `boot()` para asegurar de que ya esté todo cargado
* Nueva funcionalidad `Api Auth` para poder hacer login con Laravel desde la API: 
  * Nuevos modelos `User` y `ApiUser`
  * Sobreescribir la configuración de `auth` en el `KalionServiceProvider` para añadir la nueva guard `guards.api` y el nuevo provider `providers.api_users` (nuevo método `setAuthApiGuards()`)
  * (breaking) Modificar configuraciones para permitir multiples `guards`
  * Nuevas clases `ApiUserEntity` y `ApiUserRepository`
  * Añadir parámetro `$guard` al método `userEntity()` del `AuthService`
  * (breaking) (stubs) Actualizar el archivo de configuración `kalion_user.php` de los stubs
  * Quitar el return type del helper `userEntity()` y ponerlo como PHPDoc para no forzar el tipo
  * (refactor) Eliminar el `returnType` del método `AuthService::userEntity()`
  * Añadir nuevas variables de entorno para poder configurar las clases de usuario más fácilmente:
    * `KALION_USER_ENTITY_WEB`
    * `KALION_USER_ENTITY_API`
    * `KALION_USER_REPOSITORY_WEB`
    * `KALION_USER_REPOSITORY_API`
  * Quitar la publicación del `config/kalion_user.php`, ya que ahora se configura en el `.env`
  * Mover el helper `userEntity()` de los `stubs` de la aplicación al `heplers_domain.php` del paquete
  * Mover los métodos `publishConfigKalionUser()` y `modifyFile_ConfigAuth_toUpdateModel()` del comando `KalionStart` al nuevo comando `PublishAuth`
  * Modificar el método `modifyFile_ConfigAuth_toUpdateModel()` del comando `PublishAuth` para que también añada los arrays `api` en `guards` y `providers`

### Fixed

* (fix) corregir nombre campo `name` en los métodos `toArray` del `UserEntity`
* (fix) Permitir el tipo `Illuminate\Support\Collection` en el parámetro `$data` del método `fromData()` de la clase `ContractCollectionEntity`

## [v0.22.0-beta.0](https://github.com/kalel1500/kalion/compare/v0.21.0-beta.0...v0.22.0-beta.0) - 2025-03-25

### Added

* Nuevo método `flatMap()` en la clase `ContractCollectionBase`

### Changed

* (refactor) Modificar método `collapse()` de la clase `ContractCollectionBase` para usar el `collect()->collapse()` de Laravel
* (breaking) Igualar comportamiento del método `collapse()` de la clase `ContractCollectionBase` al `collapse()` de Laravel:
  * No eliminar valores `null`
  * Eliminar valores que no sean arrays
* (breaking) Eliminar funcionalidad del `EnumDynamic`, ya que no termina de funcionar

### Fixed

* (fix) Arreglar el método collapse del `ContractCollectionBase`, ya que devolvía todos los items dentro de arrays

## [v0.21.0-beta.0](https://github.com/kalel1500/kalion/compare/v0.20.0-beta.0...v0.21.0-beta.0) - 2025-03-25

### Changed

* (refactor) Usar la nueva sintaxis de `arrow function [fn()]` en los callbacks de la clase `ContractCollectionBase`
* (breaking) Varios helpers no tan genéricos movidos a métodos estáticos de clases del paquete:
  * |                                      |        |                                           |
    |--------------------------------------|--------|-------------------------------------------|
    | `get_shadow_classes()`               | &rarr; | `Kalion::getShadowClasses()`              |
    | `get_login_field_data()`             | &rarr; | `Kalion::getLoginFieldData()`             |
    | `get_sub_with()`                     | &rarr; | `Relation::getNextRelation()`             |
    | `get_info_from_relation_with_flag()` | &rarr; | `Relation::getInfoFromRelationWithFlag()` |
    | `broadcasting_is_active()`           | &rarr; | `Kalion::broadcastingEnabled()`           |
    | `get_class_user_model()`             | &rarr; | `Kalion::getClassUserModel()`             |
    | `get_class_user_entity()`            | &rarr; | `Kalion::getClassUserEntity()`            |
    | `get_class_user_repository()`        | &rarr; | `Kalion::getClassUserRepository()`        |
* (breaking) Mover las configuraciones del `KalionServiceProvider` de los métodos estáticos de la clase `Kalion` a las nuevas configuraciones:
  * |                                                                          |        |                                              |
    |--------------------------------------------------------------------------|--------|----------------------------------------------|
    | `Kalion::runMigrations()`/`Kalion::shouldRunMigrations()`                | &rarr; | `config('kalion.run_migrations')`            |
    | `Kalion::publishMigrations()`/`Kalion::shouldPublishMigrations()`        | &rarr; | `config('kalion.publish_migrations')`        |
    | `Kalion::ignoreRoutes()`/`Kalion::shouldRegistersRoutes()`               | &rarr; | `config('kalion.register_routes')`           |
    | `Kalion::enablePreferencesCookie()`/`Kalion::enabledPreferencesCookie()` | &rarr; | `config('kalion.enable_preferences_cookie')` |

### Removed

* Eliminar dependencia de desarrollo `orchestra/testbench`
* Eliminar todos los helpers relacionados con las colecciones (`coll_`) y lógica movida a la propia clase `ContractCollectionBase`:
  * `coll_first`
  * `coll_last`
  * `coll_where`
  * `coll_where_in`
  * `coll_contains`
  * `coll_unique`
  * `coll_filter`
  * `coll_sort_by`
  * `coll_sort`
  * `coll_sort_desc`
  * `coll_group_by`
  * `coll_select`
  * `coll_flatten`
  * `coll_take`
* Eliminar método `stubsCopyFile_AppServiceProvider()` del comando `KalionStart`, ya que ahora no hace falta modificarlo (archivo `AppServiceProvider` eliminado de los `stubs`)

## [v0.20.0-beta.0](https://github.com/kalel1500/kalion/compare/v0.19.0-beta.1...v0.20.0-beta.0) - 2025-03-21

### Changed

* (breaking) Mover el segundo parámetro `$logChannel` del helper log_if_fail() al final como tercer parámetro después del `$callback`
* (breaking) Renombrar helper `save_execute` a `log_if_fail()`
* Permitir que el parámetro `$logChannel` del helper `save_execute()` pueda ser `null`
* (refactor) Extraer lógica del `shouldRenderJsonWhen()` en el método estático `shouldRenderJson()` del `ExceptionHandler.php` para evitar tener que escribirla varias veces

### Fixed

* (fix) Añadir comprobación `self::shouldRenderJson()` al renderizar los `ModelNotFoundException` en el `ExceptionHandler` para evitar que todas las excepciones `ModelNotFoundException` devuelvan siempre una blade si se debe devolver un `json`

## [v0.19.0-beta.1](https://github.com/kalel1500/kalion/compare/v0.19.0-beta.0...v0.19.0-beta.1) - 2025-03-19

### Changed

* Optimizar comando `JobDispatch` al escanear las para que se salte las carpetas ocultas y si en los paquetes encuentra la carpeta `src` busque directamente dentro y se salte las demás

### Fixed

* (fix) Definir las rutas que se deben escanear en un array `$pathsToScan` y llamar al `findJobDirsOnPath()` con un `array_map()`. Asi se arregla el error con la llamada `$this->findJobDirsOnPath(...$packages)` ya que no funcionaba y además evitamos llamar al `findJobDirsOnPath()` multiples veces
* (fix) Usar `DIRECTORY_SEPARATOR` y `normalize_path()` en las rutas que se le pasan al `findJobDirsOnPath()` en el comando `JobDispatch`, ya que cuando se ejecuta en Linux falla el `scandir()` si tiene `\`

## [v0.19.0-beta.0](https://github.com/kalel1500/kalion/compare/v0.18.1-beta.0...v0.19.0-beta.0) - 2025-03-18

### Added

* Añadir nuevos comandos de Git en el `docs/git/git-commands.md`
* Ampliar funcionalidad del Auth:
    * (refactor) Nuevo Helper `get_class_user_repository()` obtener la clase `UserRepository` de la configuración
    * Nuevo componente layout `components.layout.auth.landing`
    * Nuevas configuraciones para poder configurar las blades del `login` y `register` desde la aplicación
      * `kalion.auth.blades.fake`
      * `kalion.auth.blades.login`
      * `kalion.auth.blades.register`

### Changed

* Comandos renombrados: Prefijo `kalion` añadido a los comandos del paquete:
  * `clear:all`&rarr;`kalion:clear-all`
  * `job:dispatch`&rarr;`kalion:job-dispatch`
  * `logs:clear`&rarr;`kalion:logs-clear`
  * `service:check`&rarr;`kalion:service-check`
* Rehacer por completo el comando `LogsClear`
  * Comprobar si existe la carpeta `Logs`
  * Vaciar todos los logs de la carpeta en vez de solo el `laravel.log`
  * Usar el Filesystem (`File::put()`) en vez de ejecutar el `echo '' > logs/laravel.log`
  * Ajustar permisos solo en Linux, ya que en Windows no hace falta y asi evitar el fallo que daba en windows
  * Usar el `shell_exec` en vez del `exec` para ejecutar el comando `chmod`
  * Asignar permisos `775` en vez de `777`
  * Dejar de guardar Logs con el resultado tanto `try` como en el `catch`
* (refactor) Actualizar Comandos a la última version de Laravel
* Líneas `$this->info()` eliminadas del comando `ClearAll`, ya que basta con la última
* Ampliar funcionalidad del Auth:
  * (refactor) Usar el nuevo Helper `get_class_user_repository()` en el `KalionServiceProvider` para obtener la clase `UserRepository`
  * (refactor) Mover blade de `pages.login.fake.index` a `pages.auth.fake` 
  * Simplificar la blade `pages.auth.fake` con el `components.layout.auth.landing`
  * Hacer que el campo del `Fake Login` sea configurable:
    * Nuevas configuraciones `kalion_auth.login.field` y `kalion_auth.login.fields` (con varias configuraciones por defecto y una custom que se configura en el `.env`)
    * Nueva clase `LoginFieldDto`
    * Nuevo helper `get_login_field_data()`
    * Hacer que tanto la blade `pages.login.fake` como el método `AuthController::store()` obtengan el `field` dinámicamente con el helper `get_login_field_data()`
  * Modificar propiedad `lang` del html de la layout `layout.login.landing` para que la obtenga dinámicamente
  * Mover el mensaje que aparece cuando no se encuentra el usuario en el `AuthController` a la traducción `k::auth.user_not_found`
  * Archivo de configuraciones `kalion_auth.php` renombrado a `kalion_user.php`
  * Configuraciones movidas de `kalion_layout.php` a `kalion.php`
  * (breaking) Configuraciones renombradas
    * `kalion.fake_login_active`&rarr;`kalion.auth.fake` (`KALION_FAKE_LOGIN_ACTIVE`&rarr;`KALION_AUTH_FAKE`)
    * `kalion_auth.entity_class`&rarr;`kalion_user.entity`
    * `kalion_auth.user_repository_class`&rarr;`kalion_user.repository`
    * `kalion_auth.load_roles`&rarr;`kalion.auth.load_roles`
    * `kalion_auth.display_role_in_exception`&rarr;`kalion.auth.display_role_in_exception`
    * `kalion_auth.display_permission_in_exception`&rarr;`kalion.auth.display_permission_in_exception`
    * `kalion_layout.theme`&rarr;`kalion.layout.theme`
    * `kalion_layout.active_shadows`&rarr;`kalion.layout.active_shadows`
    * `kalion_layout.sidebar_collapsed`&rarr;`kalion.layout.sidebar_collapsed`
    * `kalion_layout.sidebar_state_per_page`&rarr;`kalion.layout.sidebar_state_per_page`
    * `kalion_layout.blade_show_main_border`&rarr;`kalion.layout.blade_show_main_border`
  * Usar la nueva configuración `kalion.auth.blades.fake` en el método `AuthController::create()`
* Mejoras en el comando JobDispatch: 
  * Usar `app()->makeWith($class, $options)` al ejecutar el `dispatch_sync` para permitir la inyección de dependencias
  * Modificar parámetros del comando `job:dispatch` para permitir pasar un array `{--p=*}` en vez de tener 3 parámetros fijos `{--param1=} {--param2=} {--param3=}` y pasar todo el array al Job recibido (de esta forma se pueden pasar tantos parámetros como requiera el job)
  * Añadir descripciones a los argumentos del `job:dispatch`
* (breaking) renamePackage: renombrar prefijo traducciones de `h` a `k`
* Modificar version de laravel en los literales de las blades
* Nuevos Value Objects para los Enums `nullables`: 
  * Renombrar clase `ContractEnumVo` a `ContractBaseEnumVo`
  * Crear las nuevas clases `ContractEnumVo` y `ContractEnumNullVo` que extienden de esta base (para poder indicar si los Enums son nullables o no sin tener que ponerlo en cada Enum)
  * Modificar la clase `EnumDynamicVo` para establecerla como `$nullable = false` y crear la nueva clase `EnumDynamicNullVo` para cuando pueda ser nullable
* (breaking) Clase `EnvVo` renombrada a `Env`
* (breaking) Añadir parámetro `active` al componente `x-kal::sidebar.item` en vez de usar el `isRouteActive()`
* <u>**!!! (breaking) !!!**</u> Eliminar los helpers del `env` del archivo `InfrastructureHeplers.php` y mover toda la lógica a la clase `EnvVo`
* <u>**!!! (breaking) !!!**</u> Clase `MyCarbon` renombrada a `Date` y movida de `Infrastructure\Helpers` a `Infrastructure\Services`
* <u>**!!! (breaking) !!!**</u> Helpers renombrados en `DomainHeplers.php`:
    <details>
    <summary>Mostrar</summary>
    
    * `strToCamelCase`&rarr;`str_camel`
    * `strTurncate`&rarr;`str_truncate`
    * `verifyEmail`&rarr;`validate_email`
    * `splitAtUpperCase`&rarr;`explode_by_uppercase`
    * `abortC_if`&rarr;`abort_d_if`
    * `isValidBoolean`&rarr;`is_valid_bool`
    * `isDomainException`&rarr;`is_kalion_exception`
    * `collectAny`&rarr;`collect_any`
    * `objectToArray`&rarr;`object_to_array`
    * `arrayToObject`&rarr;`array_to_object`
    * `cloneObject`&rarr;`obj_clone`
    * `arrayKeepKeys`&rarr;`array_keep`
    * `arrayDeleteKeys`&rarr;`array_delete`
    * `array_diff_assoc_recursive`&rarr;`array_diff_assoc_deep`
    * `getSubWith`&rarr;`get_sub_with`
    * `getInfoFromRelationWithFlag`&rarr;`get_info_from_relation_with_flag`
    * `soIsWindows`&rarr;`so_is_windows`
    * `strContainsHtml`&rarr;`str_contains_html`
    
    </details>
* <u>**!!! (breaking) !!!**</u> Helpers renombrados en `InfrastructureHeplers.php`:
    <details>
    <summary>Mostrar</summary>

    * `defaultUrl`&rarr;`default_url`
    * `defaultRoute`&rarr;`default_route`
    * `appUrl`&rarr;`app_url`
    * `getHtmlLaravelDebugStackTrace`&rarr;`get_html_laravel_debug_stack_trace`
    * `getClassUserEntity`&rarr;`get_class_user_entity`
    * `getClassUserModel`&rarr;`get_class_user_model`
    * `getUrlFromRoute`&rarr;`get_url_from_route`
    * `broadcastingIsActive`&rarr;`broadcasting_is_active`
    * `arrAllValuesAreArray`&rarr;`array_has_only_arrays`
    * `collTake`&rarr;`coll_take`
    * `collFlatten`&rarr;`coll_flatten`
    * `collSelect`&rarr;`coll_select`
    * `collGroupBy`&rarr;`coll_group_by`
    * `collSortDesc`&rarr;`coll_sort_desc`
    * `collSort`&rarr;`coll_sort`
    * `collSortBy`&rarr;`coll_sortby`
    * `collFilter`&rarr;`coll_filter`
    * `collUnique`&rarr;`coll_unique`
    * `collContains`&rarr;`coll_contains`
    * `collWhereIn`&rarr;`coll_where_in`
    * `collWhere`&rarr;`coll_where`
    * `collLast`&rarr;`coll_last`
    * `collFirst`&rarr;`coll_first`
    * `strToSnake`&rarr;`str_snake`
    * `urlContainsAjax`&rarr;`url_contains_ajax`
    * `getGoodEmailsFromArray`&rarr;`filter_valid_emails`
    * `debugIsActive`&rarr;`debug_is_active`
    * `appIsInDebugMode`&rarr;`debug_is_active`
    * `dropdownIsOpen`&rarr;`dropdown_is_open`
    * `responseJson`&rarr;`response_json`
    * `responseJsonWith`&rarr;`response_json_with`
    * `responseJsonError`&rarr;`response_json_error`

    </details>
* (refactor) Quitar tryCatch al `firstOrFail()` del `StateEloquentRepository` ya que ahora se encarga el ExceptionHandler
* <u>**!!! (breaking) !!!**</u> Migrar todo el código para usar las características de PHP 8.2 (promoted properties, static return type, type multiple, ...)
* <u>**!!! (breaking) !!!**</u> Dejar de soportar las versiones de PHP `^7.4|^8.0|^8.1` y las versiones de laravel `^7.0|^8.0`

### Removed

* Archivo de configuración `kalion_layout.php` eliminado (se han movido al `kalion.php`)
* (breaking) Eliminar clase `MyLog` y mover el código de los métodos estáticos a los nuevos helpers `log_error()`, `log_error_on()`, `log_error_on_queues()`, `log_error_on_loads()`
* (breaking) Eliminar clase `MyJob` y mover código `MyJob::launchSimple()` al nuevo helper `save_execute()`
* (breaking) Eliminar clase `MyDebug`
* <u>**!!! (breaking) !!!**</u> Helpers eliminados en `DomainHeplers.php`:
    <details>
    <summary>Mostrar</summary>

    * `dashesToCamelCase`
    * `strToSlug`
    * `remove_accents`
    * `getFirstMessageIfIsArray`
    * `stringHtmlOfArrayMessages`
    * `strContains`
    * `arrayContains`
    * `arrayFirstWhere`
    * `clearArrayToIntegers`
    * `arrayToString`
    * `anyToBoolean`
    * `strStartsWith`
    * `strEndsWith`
    * `arrayHasDupes`
    * `buildForeignKeyName`
    * `addMessagesSeparator`
    * `getSrcNamespace`
    * `getRelationCollection`
    * `stringToArray`
    * `stringToObject`
    * `isBlank`
    * `clearWith`
    * `mapToLabelStructure`
    * `arrFormatIsEntity`
    * `arrFormatIsCollection`

    </details>
* <u>**!!! (breaking) !!!**</u> Helpers eliminados en `InfrastructureHeplers.php`:
    <details>
    <summary>Mostrar</summary>

    * `getIconFullAttributes`
    * `getOtherAttributes`
    * `getIconClasses`
    * `myCollect`
    * `arrSort`
    * `dbTransaction`
    * `myOptional`
    * `isValidationException`
    * `formatArrayOfEmailsToSendMail`
    * `myCarbon`
    * `collectionContains`
    * `collectE`
    * `compareDates`
    * `formatStringDatetimeTo`
    * `createRandomString`
    * `routeContains`
    * `getRouteInput`
    * `currentRouteNamed`
    * `isRouteActive`
    * `showActiveClass`
    * `envIsTest`
    * `envIsNotLocal`
    * `envIsNotPre`
    * `envIsNotPorduction`
    * `envIsLocal`
    * `envIsPre`
    * `envIsPorduction`
    * `getEnvironmentReal`
    * `getEnvironment`

    </details>

### Fixed

* (fix) Solucionar error en el comando `JobDispatch` cuando la configuración `kalion.packages_to_scan_for_jobs` es `null`

## [v0.18.1-beta.0](https://github.com/kalel1500/kalion/compare/v0.18.0-beta.0...v0.18.1-beta.0) - 2025-03-06

### Added

* Nuevo Helper `Instantiable` con una función estática `new` para instanciar clases de forma estática
* Nuevo helper `get_class_from_file($filePath)` que a partir de la ruta de un archivo devuelve la clase (namespace + name)

### Changed

* Añadir el Trait `Instantiable` en la clase `TailwindClassFilter` para poder llamar al filter mas fácilmente y eliminar el helper `filterTailwindClasses()`
* Comando JobDispatch (`job:dispatch`) modificado:
  * Código refactorizado para mejorar la legibilidad
  * Hacer el método `scanJobDirsProject()` más flexible y que busque la carpeta `Jobs` en cualquier sitio y no solo dentro de `Infrastructure`
  * Al buscar la carpeta Jobs en la aplicación, hacer que busque también en la carpeta `app` además de en `src`
  * Método `scanJobDirsProject()` renombrado a `findJobDirsOnPath()`
  * Dejar de calcular el `namespace` transformando el `path` y usar el helper `get_class_from_file()`
  * Variable de configuración `job_paths_from_other_packages` renombrada a `packages_to_scan_for_jobs`
  * Nueva variable de entorno `KALION_PACKAGES_TO_SCAN_FOR_JOBS` para poder pasarle los paquetes en un string desde el `.env`
  * Cambiar el contenido de la configuración `packages_to_scan_for_jobs` para guardar el nombre de los paquetes en vez de guardar el `namespace` (y adaptar el comando `JobDispatch`)
  * Comprobar si la carpeta Jobs ya existe en directamente en la ruta que se está escaneando (hasta ahora solo se buscaba la carpeta Jobs dentro de cada carpeta que hay en la ruta que se escanea)
  * (refactor) Modificar flujo para obtener todas las rutas donde buscar Jobs (kalion, paquetes configurados y app) y después escanearlas todas y llamar a la ejecución
  * !!! Cambiar lógica para que en vez de ejecutar el primer job que encuentre `(según el orden de búsqueda: kalion, otros paquetes, app.scr y app.app)`, devuelva un mensaje con la lista de jobs que se han encontrado con el nombre recibido y que se pueda seleccionar el que se quiera ejecutar

### Fixed

* (fix) Nuevo helper `str_contains()` para que funcione en versiones anteriores de PHP8
* (fix) Varios errores arreglados en el comando JobDispatch (`job:dispatch`): 
  * Arreglar la recursividad del método `scanJobDirsProject()`, ya que no estaba guardando el resultado cuando se llamaba a sí mismo
  * Cambiar el `dispatch` por el `dispatch_sync`, ya que a partir de Laravel 11 la conexión por defecto es `database`

### Removed

* Eliminar método `tryDispatchJobFromPath()` (el que antes se llamaba `tryExecJobInNamespace()`) y mover la lógica dentro del `handle()`

## [v0.18.0-beta.0](https://github.com/kalel1500/kalion/compare/v0.17.1-beta.0...v0.18.0-beta.0) - 2025-03-06

### Changed

* <u>**!!! (breaking) !!!**</u> Paquete de Js renombrado de `@kalel1500/laravel-ts-utils` a `@kalel1500/kalion-js`
* <u>**!!! (breaking) !!!**</u> Repositorio renombrado de `laravel-hexagonal-and-ddd-architecture-utilities` a `kalion`

### Fixed

* (fix) Solucionado error con el `dark_theme` en el `ExamplesController.ts` (se cambió por `theme` y se convirtió en string y aquí se quedó igual)

## [v0.17.1-beta.0](https://github.com/kalel1500/kalion/compare/v0.17.0-beta.0...v0.17.1-beta.0) - 2025-03-06

### Changed

* Mejoras en el comando de inicio `kalion:start` para evitar tener que mantener las versiones de los paquetes de NPM manualmente en la configuración
  * Añadir parámetro `$show_number` en el método `line` del `StartCommandService` (para poder evitar mostrar el número en ocasiones)
  * No mostrar el número del paso en el mensaje de inicio de un proceso (método `execute_Process()` del `StartCommandService`)
  * Añadir parámetro `$show_number` en el método `execute_Process` del `StartCommandService` (para poder evitar mostrar el número en ocasiones)
  * Nuevo método `execute_NpmInstallDependencies()` para instalar las dependencias de NPM
  * Cambiar los métodos `modifyFile_PackageJson_toAddNpmDevDependencies()` y `modifyFile_PackageJson_toAddNpmDependencies()` por el nuevo `execute_NpmInstallDependencies()` para no tener que mantener las versiones de los paquetes manualmente en el archivo de configuración
  * Mover el método `modifyFile_PackageJson_toAddEngines()` debajo del `execute_NpmInstallDependencies()` para que los `engines` siempre se añadan después de las `dependencies`
  * Nuevas variables de entorno `KALION_VERSION_NODE` y `KALION_VERSION_NPM` para que las versiones de `node` y `npm` sean configurables sin tener que publicar la configuración del paquete (`config/kalion.php`)
* Actualizar el archivo `git-flow-commands.md` con más comandos

### Removed

* config: Eliminar las configuraciones de las versiones de las dependencias de NPM que ya no se usan (`config/kalion.php`)

## [v0.17.0-beta.0](https://github.com/kalel1500/kalion/compare/v0.16.0-beta.0...v0.17.0-beta.0) - 2025-03-06

### Added

* docs: Nuevos archivos `git-commands.md` y `git-flow-commands.md` con todos los comandos de GIT necesarios para la gestión de las ramas
* docs: Nuevo archivo `package-documentation.md` con información sobre el prefijo `kal`
* Nueva funcionalidad del DarkTheme para obtener la configuración del sistema:
  * Nueva clase `ThemeVo` para guardar los valores del Tema (`dark`, `light` y `system`)
  * Modificar el componente `theme-toggle.blade.php` para pintar varios botones en vez de varios iconos y facilitar la gestión en el JS
  * Nuevo componente `x-hexagonal::icon.indeterminate` para establecer el tema del sistema
  * Asignar cada icono a su color y cambiar los textos (y las acciones del Js) (igual que Laravel)
  * Añadir atributos `data-theme` y `color-theme` en el html para poder leerlos mas adelante
  * `<script>` en el HTML para cargar el DarkMode rápidamente (sirve para evitar ver saltos de color cuando la conexión es lenta y el `theme` es `system`, ya que el código compilado tarda más en cargar)
  * `<script>` en el HTML para cargar el DarkMode en la `welcome.blade.php`
* Añadir el `.editorconfig` de Laravel
* Nuevos helpers `appUrl()`, `defaultRoute()` y `defaultUrl()`
* Nuevas variables de configuración `default_route` y `default_route_name`

### Changed

* stubs: Dejar en el archivo de configuración de los stubs `kalion_auth.php` solo los valores que no se configuran con variables de entorno
* Eliminar las variables de entorno `KALION_AUTH_ENTITY_CLASS` y `KALION_AUTH_ENTITY_CLASS`, ya que es mejor que se configuren en el propio archivo de configuración `kalion_auth.php`
* Actualizar paquete `@kalel1500/laravel-ts-utils` a la version `^0.6.0-beta.0` (composer y config start command)
* <u>**!!! (breaking) !!! Renombrar nombre corto del paquete de `Hexagonal` a `Kalion` (provider, service, command, constants, prefixes, paths, cookie, roues, config, exceptions, controller, env and namespaces)**</u>
* (breaking) layout: 
  * (refactor) Renombrar brakepoint `vsm` a `xs` (en el paquete de JS)
  * (refactor) Shadows personalizadas renombradas en el paquete de JS
  * Deprecar helpers `getIconClasses()`, `getOtherAttributes()` y `getIconFullAttributes()`
  * (refactor) Dejar de usar los helpers `getIconClasses()` y `getOtherAttributes()` para usar `{{ $attributes->mergeTailwind('size-6') }}` en los componentes de los iconos
  * <u>**!!! (breaking) !!!**</u> Renombrar prefijo blades de `hexagonal` a `kal`
* layout: Nuevos parámetros `tag` y `underline` en el componente `hexagonal::link` para hacer que por defecto este subrayado y poder cambiar el tag `a`
* (breaking) darkTheme: Renombrar las variables `dark_theme` a `theme` y convertir de `null` a `string` (`cookie` y `config`)
* Migrar proyecto a Laravel 12
* stubs: Renombrar archivo `.env.local` a `.env.save.local` para que Vite no sobreescriba las variables del `.env`
* stubs: Usar el nuevo Helper `defaultUrl()` en el `redirect` del la ruta base (`/`)
* Migrar proyecto y Tailwindcss 4
  * Actualizar dependencias `flowbite` y `tailwindcss`, instalar `@tailwindcss/postcss` y eliminar `autoprefixer`
  * Ejecutar `npx @tailwindcss/upgrade`
  * Utilizar las nuevas configuraciones de tailwind (imports del paquete `laravel-ts-utils`)
  * Archivo `postcss.config.js` eliminado y cambiado por el plugin `tailwindcss()` en vite.config.js
  * (stubs) Modificar los archivos de stubs para la migración a Tailwind 4
  * hexagonalStart: eliminar `copy` y `delete` del archivo `tailwind.config.ts` en el comando `hexagonal:start`

### Fixed

* (fix) stubs: arreglar `app.css` original
* (fix) layout: Mover la clase `hidden xs:flex` del `left-side` al `brand` para que siempre muestre el icono hamburguesa y lo que se esconda es el logo
* (fix) fixMergeTailwind: Corregir la Macro `mergeTailwind`, ya que no combinaba bien las clases con las variantes
  * Nuevo helper `filterTailwindClasses()`
  * Nueva clase `tests/Unit/ComponentsTests.php` para testear el helper `filterTailwindClasses()`
  * Mejorar el helper `filterTailwindClasses()` ya que no contemplaba las clases que no tienen prefijo (aún hace falta definirlas todas)
  * (refactor) Extraer código del helper `filterTailwindClasses` a la nueva clase `TailwindClassFilter` para mejorar la legibilidad
  * Prevenir casuística de que una `$specialClass` esté en una variante en las `$custom_class`
  * Nueva propiedad `$groups` para poder forzar a qué grupo pertenece una clase independientemente de los guiones que tenga (por ejemplo para que `bg-white` pueda reemplazar a `bg-blue-500`)
* (fix) Comprobar `is_null` antes del `strtolower` en el método `checkPermittedValues()` de la clase `ContractEnumVo` para evitar error cuando la propiedad `$caseSensitive` es `false`
* (fix) Añadir el `{!! Renderer::css() !!}` en la vista `pages/login/fake/index.blade.php` para que renderice el css del propio paquete en lugar de usar la url hacia `tailwindcss@2.2.19`
* (fix) corregido el helper `get_shadow_classes()` (ruta errónea de la variable `active_shadows`)
* (fix) Cambiar los `redirect('/')` del `AuthController.php y HexagonalController.php` por `redirect(appUrl())` para que se pueda redirigir la petición correctamente a la base de la aplicación incluso cuando esta esté dentro de un `path`

## [v0.16.0-beta.0](https://github.com/kalel1500/kalion/compare/v0.15.0-beta.0...v0.16.0-beta.0) - 2025-02-27

### Added

* permission: !!!Nueva funcionalidad para poder pasarle parámetros a los métodos `is()` y `can()` del `UserEntity` para los roles que son querys y requieran recibir parámetros
* (refactor) Exceptions: Mover el renderizado de la vista de errores de Laravel al nuevo helper `getHtmlLaravelDebugStackTrace()`
* permission: Nuevos Middlewares para poder comprobar los Permisos y Roels en las rutas con los alias `userCan:` y `userIs:` (nueva excepción `UnauthorizedException`)
* permission: Nuevo método `is()` en el trait `EntityHasPermissions.php` para poder comprobar si un usuario tiene un rol
* permission: Nueva funcionalidad de Roles y Permisos (primera version -> migraciones, modelos, entidades, colecciones, repositorios, traits)

### Changed

* (breaking) Exceptions: !!!Modificar `$exceptions->render(HexagonalException)` para que con el `DEBUG=true`, en las `AbortException` renderice siempre la `getHtmlLaravelDebugStackTrace()` y solo deje a Laravel encargarse de renderizar cuando sea diferente de `BasicHttpException`. De esta forma nuestras BasicHttpException se renderizan con nuestra vista `hexagonal::pages.exceptions.error`
* Exceptions: Mejorar estilos de la layout de errores `minimal.blade.php`
* Exceptions: Añadir el texto del `StatusCode` como `$title` y mover el mensaje debajo del código como un subtítulo (se renderizan los estilos propios)
* Exceptions: Igualar la blade de las Excepciones con la que tiene Laravel internamente (con su misma layout)
* (breaking) (refactor) Exceptions: Mover y renombrar `resources/views/pages/custom-error.blade.php` a `resources/views/pages/exceptions/error.blade.php`
* Exceptions: Hacer que el `->render(HexagonalException)` solo deje renderizar a Laravel si es Debug o si la Excepción no es `BasicHttpException` (para que las `BasicHttpException` siempre usen nuestra vista `hexagonal::pages.custom-error`)
* (refactor) Exceptions: Ordenar métodos del `ExceptionHandler` en el orden en que Laravel los ejecuta para que se lea más claramente
* (refactor) Exceptions: Mover la lógica de del manejo de una excepción Http de la clase `AbortException` a la nueva clase `BasicHttpException` para que otras puedan extender de ella y devolver excepciones Http que extiendan de nuestra clase base `HexagonalException`
* (refactor) ServiceProvider: cambiar el `$kernel->appendMiddlewareToGroup()` por el `$router->pushMiddlewareToGroup()`
* (refactor) Cambiar el `returnType` fijo del método `AuthService::userEntity()` por un tipo genérico para facilitar la detección de tipos
* (refactor) Usar nombre completo de las clases en el `$singletons` del `HexagonalServiceProvider.php` en vez de usar los imports para una mejor lectura
* Auth: Cargar la relación de `roles` en el método `AuthService::userEntity()` (configurable con la nueva variable `hexagonal_auth.load_roles`)
* Auth: Tipar las clases `AuthService` (incluyendo la Interfaz y la Fachada) para indicar que puede devolver `null`
* Auth: Hacer que el método `AuthService::userEntity()` devuelva `null` si `auth()->user()` devuelve `null`
* Auth: Modificar clase `UserEntity.php` para que sea fácilmente heredable
* (breaking) Modificar el método `->contains()` de las colecciones (`ContractCollectionBase.php`) si recibe un callback le pase la instancia del `$item` original y no un `object` de PHP

### Fixed

* (fix) stubs: Cambiar el color del texto cuando es `dark` en la `home.blade.php`
* (fix) Layout: eliminado el `Content-Security-Policy` del `head` (lo que hace es poner HTTPS automáticamente en las rutas)
* (fix) Quitar la referencia al modelo `Src\Shared\Infrastructure\Models\User` en el `AuthController` (ahora se usa el helper `getUserClass()` que obtiene la clase de la configuración)

## [v0.15.0-beta.0](https://github.com/kalel1500/kalion/compare/v0.14.1-beta.1...v0.15.0-beta.0) - 2025-02-17

### Added

* hexagonalStart: Nuevo método `modifyFile_PackageJson_toAddEngines()` para añadir la sección `engines` en el `package.json` y asi limitar las versiones necesarias
* !!! Añadir nuevo `$exceptions->render()` en el `ExceptionHandler.php` para renderizar manualmente los `ModelNotFoundException` para que todos los `findOrFail()` en local muestren la vista `trace` y en PRO muestren nuestra vita `custom-error` sin tener que envolverlos en un `tryCatch`
* Nuevo helper `concat_fields_with()`
* Nuevas traducciones añadidas
* layout: Nuevo parámetro `size` en el componente `hexagonal::button`
* layout: Nuevo componente `hexagonal::tabulator.buttons`
* layout: Nuevo método `get_shadow_classes()` que hace la comprobación de la configuración `hexagonal.active_shadows` (si están desactivadas en el componente `hexagonal::section` poner un borde)
* layout: Añadir configuración `hexagonal.active_shadows` para activar o no las sombras grandes en la `layout`
* layout: Añadir el color `gray` en el componente `hexagonal::button`
* layout: Nuevos componentes de iconos (`icon.pencil-square, icon.plus-circle, icon.x-circle`)
* Nuevo helper `getIconFullAttributes()` que concatena el `getIconClasses()` y `getOtherAttributes()` para no repetir tanto código en los componentes
* !!! Nueva funcionalidad en el método `createFromArray()` de la clase `ContractDataObject.php` para poder pasarle al `::fromArray()` un array con valores primitivos y que los tipos de los parámetros se instáncien automáticamente usando el `ReflectionClass` (por ahora se debe activar manualmente en cada DTO con la constante `REFLECTION_ACTIVE`)
* Nueva excepción genérica `AppException`
* layout: Nuevos componentes `hexagonal::tab` y `hexagonal::tab.item`
* layout: Nuevos iconos
* hexagonalStart: (example) Nuevo método `stubsCopyFolder_ResourcesFront()` para generar los archivos TS del front (después del `execute_NpxLaravelTsUtils()`)
* hexagonalStart: indicar en amarillo cuando es DEVELOP
* layout: Nuevos componentes `select, badge, heading, link y text`
* hexagonalStart: (example) Nuevo método `stubsCopyFiles_Config()` para generar las configuraciones iniciales
* Nuevos `ValueObjects` para guardar las fechas con formato `Timestamp` en las Entidades
* hexagonalStart: (example) Añadir el código para hacer la inversa del `modifyFile_ComposerJson_toAddHelperFilePath()` cuando el `$this->isReset()` es `true`
* hexagonalStart: (example) Nuevo método `modifyFile_ComposerJson_toAddHelperFilePath()` para añadir el helper
* hexagonalStart: Nuevo parámetro `$resourcesFolderRestored` para controlar que no se ejecute dos veces el mismo código.
* hexagonalStart: (example) Nuevo método `stubsCopyFolder_Seeders()` para generar los Seeders
* hexagonalStart: (example) Nuevo método `stubsCopyFolder_Factories()` para generar las Factorias
* hexagonalStart: (example) Nuevo parámetro `$keepMigrationsDate` en el `stubsCopyFiles_Migrations()` para poder mantener los nombres de las migraciones en la plantilla (se añade nueva variable en la config `hexagonal.keep_migrations_date`)
* hexagonalStart: (example) Añadida la opción `$reset` en el método `stubsCopyFiles_Migrations()`
* hexagonalStart: (example) Nuevo método `stubsCopyFiles_Migrations()` para generar las migraciones sin ejecutar el `vendor:publish`
* Stubs example: Crear unos ejemplos básicos para mostrar como funciona la arquitectura hexagonal (lista de Posts con Tags y administración de Tags)
  * Migraciones
  * Modelos
  * Entidades
  * Repositorios
  * Controllers
  * Vistas
  * Todo el código del front en TS (filtro + Tabulator editable)
* hexagonalStart: nuevo método `modifyFile_ConfigAuth_toUpdateModel()` para modificar el modelo de usuario para la autenticación
* hexagonalStart: Nuevo método `modifyFile_BootstrapApp_toAddMiddlewareRedirect()` para añadir `$middleware->redirectUsersTo('home');` en el `->withMiddleware`
* hexagonalStart: Actualizar el `DependencyServiceProvider.php` con los nuevos repositorios de los ejemplos
* hexagonalStart: Método `deleteDirectory_Models()` descomentado para borrar y restaurar la carpeta `app/Models` (añadido modelo `User` a los `stubs`)
* hexagonalStart: Nuevo método `stubsCopyFolder_Lang()` al comando `hexagonal:start` 
* hexagonalStart: Hacer que el comando `hexagonalStart` publique las nuevas configuraciones
* Auth: Nueva `Facade` `AuthService` para obtener el `userEntity` dinámicamente de la configuración
* Auth: Nueva config `config/hexagonal_auth.php` para configurar la entidad del usuario
* Auth: Nueva clase `UserEntity`
* Auth: Nueva vista `Landing Page` para hacer el login
* Auth: Nueva funcionalidad `fake login` para poder hacer Login con el `email`

### Changed

* public: new build
* Actualizar paquete `@kalel1500/laravel-ts-utils` a la version `^0.5.0-beta.0`
* (refactor) `package.json`: script `build-check` renombrado a `ts-build`
* dependencias de NPM actualizadas
* tsUtilsDevelop: actualizar archivos iniciales del front (`@kalel1500/laravel-ts-utils`)
* (breaking) Añadir validación para que todas las excepciones del dominio deban recibir un mensaje obligatoriamente
* (breaking) Mover las traducciones que se definían con `keys` a sus propios archivos de arrays (`art.php`, `database.php`, `error.php`, `field.php`, `service.php`) y dejar en el json las traducciones que si son frases
  * <details>
    <summary>Traducciones modificadas</summary>

      * `serverError`&rarr;`Server Error`
      * `database_notFoundRecords`&rarr;`deleted`
      * `database_notFoundRecordOfModel`&rarr;`deleted`
      * `database_notFoundRecordsOfModel`&rarr;`deleted`
      * `websockets_serviceInactive`&rarr;`k::service.websockets.inactive`
      * `websockets_failedActionMessage`&rarr;`k::service.websockets.failed_action_message`
      * `websockets_failedActionBladeMessage`&rarr;`k::service.websockets.failed_action_blade_message`
      * `queues_ServiceActive`&rarr;`k::service.queues.active`
      * `queues_ServiceInactive`&rarr;`k::service.queues.inactive`
      * `error_featureUnavailable`&rarr;`k::error.feature_unavailable`

    </details>
* config: Mover las configuraciones de los enlaces del `Navbar` y el `Sidebar` al nuevo archivo `hexagonal_links.php` para poder publicarlo de forma independiente
* config: Añadir el prefijo `HEXAGONAL_LAYOUT_` a las variables de entorno del archivo de configuración `hexagonal_layout.php`
* Añadir constructor a la excepción `HasRelationException` para recibir parámetros y setear un mensaje
* (refactor) layout: poner en minúscula las keys de los colores del componente `hexagonal::button`
* (breaking) !!!Cambio de clase base en `HexagonalException`: ahora extiende `Exception` en lugar de `DomainException`
* layout: Añadir sombras al `Navbar`, al `Sidebar` y al `Footer`
* layout: cambiar las sombras manuales `shadow-[0_0_5px_2px_rgba(0,0,0,0.3)]` por alias `shadow-h-2xl` (se configura en la nueva versión del paquete `laravel-ts-utils`
* hexagonalStart: (example) No ejecutar el comando `vendor:publish --tag 'hexagonal-config-auth'` si `$developMode===true` (se ha movido a los stubs el nuevo archivo de configuración `hexagonal_auth.php`)
* (refactor) hexagonalStart: mover todos los stubs a las carpetas `generate/simple` y `generate/front`
* layout: Intensificar sombra del componente `section`
* (refactor) Cambiar el método deprecado `appIsInDebugMode()` por el nuevo `debugIsActive()`
* stubs: `.env.local` actualizado
* stubs: Cambiar la ruta `/` de un `get` con callback al método `Route::redirect`
* stubs: Proteger rutas con el middleware `auth`
* (refactor) hexagonalStart: Extraer la lógica de restaurar los resources en el nuevo método `restoreResources()` y llamarlo también en el `stubsCopyFolder_Resources()`
* (refactor) hexagonalStart: mover validación de Laravel 11 del método estático `configure` al constructor para que se lance siempre
* (breaking) (refactor) serviceProvider: renombrar métodos de la clase `Version` para mejorar la legibilidad
* (refactor) serviceProvider: hacer ternario el `if` de las traducciones
* (refactor) serviceProvider: hacer las migraciones clases anónimas (ya que solo se ejecutan en versiones de laravel 9 o superiores)
* (refactor) serviceProvider: renombrar migración `states` a la fecha en la que se creó, ya que no es una original del framework
* hexagonalStart: Modificar el `publishHexagonalConfig()` para que se publique el config/hexagonal_auth.php` incluso en modo develop
* (breaking) hexagonalStart: Traducciones movidas del paquete a los `stubs` para facilitar el acceso a los mensajes de errores
  * <details>
    <summary>Traducciones que se han de definir en la aplicación</summary>

      * `E-Mail Address`
      * `Password`
      * `Remember Me`
      * `Login`
      * `Forgot Your Password?`
      * `Register`
      * `Name`
      * `Confirm Password`
      * `Reset Password`
      * `Reset Password Notification`
      * `You are receiving this email because we received a password reset request for your account.`
      * `This password reset link will expire in :count minutes.`
      * `If you did not request a password reset, no further action is required.`
      * `Please confirm your password before continuing.`
      * `Regards`
      * `Whoops!`
      * `Hello!`
      * `If you’re having trouble clicking the \":actionText\" button, copy and paste the URL below\ninto your web browser: [:actionURL](:actionURL)`
      * `If you’re having trouble clicking the \":actionText\" button, copy and paste the URL below\ninto your web browser: [:displayableActionUrl](:actionURL)`
      * `If you’re having trouble clicking the \":actionText\" button, copy and paste the URL below\ninto your web browser:`
      * `Send Password Reset Link`
      * `Logout`
      * `Verify Email Address`
      * `Please click the button below to verify your email address.`
      * `If you did not create an account, no further action is required.`
      * `Verify Your Email Address`
      * `A fresh verification link has been sent to your email address.`
      * `Before proceeding, please check your email for a verification link.`
      * `If you did not receive the email`
      * `click here to request another`
      * `All rights reserved.`

    </details>
* (breaking) config: Mover las configuraciones relacionadas con los enlaces del `Navbar` y el `Sidebar` al nuevo archivo `hexagonal_links.php`
* (breaking) config: Mover las configuraciones relacionadas con la `Layout` al nuevo archivo `hexagonal_layout.php`
* config: Añadir enlace al `Log Out` en el navbar
* layout: Pasar nueva variable `:is_post` en el componente `x-hexagonal::navbar.dropdown.link` para indicar que el item es un formulario y no un enlace
* (breaking) web: Meter las rutas en un grupo con el middleware `auth`
* (refactor) ServiceProvider: Mover los prefijos `hexagonal` del `registerRoutes()` de `HexagonalServiceProvider` a cada ruta para poderlo configurar a nivel de ruta

### Removed

* hexagonalStart: Eliminar método `modifyFile_DatabaseSeeder_toCommentUserFactory()`, ya que el archivo `DatabaseSeeder.php` ahora se genera desde los stubs
* Comentar la funcionalidad en el `HexagonalServiceProvider` que actualiza la fecha de las migraciones publicadas en versiones anteriores a Laravel 11

### Fixed

* (fix) añadir carpeta `stubs` en el `exclude` del tsconfig.json para que no compruebe esa carpeta durante el build
* (fix) layout: arreglar estilos componente `hexagonal::icon.user-profile` para que tenga un `with` fijo de 6, pero que se pueda sobreescribir (antes se definía solo en el link del dropdown y en otros sitios no se mostraba bien)
* (fix) compatibilityLaravel7: sol. error en el método `from()` de la clase `ContractModelId.php`, ya que en PHP 7.4 no se puede concatenar el acceso a una variable estática con la llamada a un método estático (asi: `static::MY_CONST::anyMethod()`)
* (fix) serviceProvider: solucionar error al renombrar las migraciones + renombrar solo las nuestras
* (fix) compatibilityLaravel7: cambiar el `$command->fail()` por el `$command->error()` en el `__construct` del `StartCommandService` ya que el método `fail()` solo existe en Laravel 11
* (fix) compatibilityLaravel7: no publicar migraciones en las versiones anteriores a Laravel 9 (ya que son clases anónimas)
* (fix) compatibilityLaravel7: Crear nuestro propio trait `InteractsWithComposerPackages` para no utilizar el de `Illuminate\Foundation` que en versiones antiguas no existe + adaptarlo para que acepte el parámetro `$isRemove`
* (fix) compatibilityLaravel7: no llamar al método `Blade::componentNamespace()` antes de la version de Laravel9
* (fix) solucionar error con los enlaces simples del navbar (no se seteaba el link aunque se configurara el nombre de la ruta)

### Deprecated

* (deprecate) Deprecar helper `appIsInDebugMode()` y crear el nuevo `debugIsActive()`

## [v0.14.1-beta.1](https://github.com/kalel1500/kalion/compare/v0.14.1-beta.0...v0.14.1-beta.1) - 2025-01-29

### Added

* Nuevos métodos `publishMigrations()` y `shouldRunMigrations()` en la clase `Hexagonal` para separar la lógica en el `HexagonalServiceProvider` y asi poder configurar las dos acciones por separado.

### Changed

* Renombrar migración de `CreateStatesTable` (se ha cambiado la fecha)
* Hexagonal:
  * (refactor) Tipar las propiedades de la clase `Hexagonal` y cambiar los `static::` por `self::`, ya que es `final`
  * (refactor) renombrar propiedades de la clase `Hexagonal` y formatear código
  * (refactor) ordenar métodos de la clase `Hexagonal`
  * (refactor) eliminar comentarios para simplificar la clase
* Renombrar migración de `Sessions` a `Users` y añadir la creación de `users` y `password_reset_tokens`
* Renombrar las migraciones de `Cache` y `Jobs` como vienen en `Laravel 11`

## [v0.14.1-beta.0](https://github.com/kalel1500/kalion/compare/v0.14.0-beta.0...v0.14.1-beta.0) - 2025-01-29

### Added

* Nuevo helper `normalize_path()`
* Nuevo método `updateNameOfMigrationsIfExist()` en el `HexagonalServiceProvider` para actualizar la fecha de las migraciones publicadas en versiones anteriores a `Laravel 11`

### Changed

* (literal) Nombre del paquete `@kalel1500/laravel-ts-utils` corregido en el string del comando de inicio
* README.md: Títulos mejorados + espacios eliminados + textos traducidos
* README.md: Añadida la información para publicar los archivos del paquete
* (refactor) Método `removeProviderFromBootstrapFile()` movido al principio de la clase `HexagonalServiceProvider`

## [v0.14.0-beta.0](https://github.com/kalel1500/kalion/compare/v0.13.0-beta.3...v0.14.0-beta.0) - 2025-01-28

### Added

* Nuevo trait `CountMethods` para contar los métodos que tiene una clase
* Nueva clase `Version` para centralizar todas las comparaciones de versiones (tanto de laravel como PHP)

### Changed

* hexagonalStart:
  * actualizar dependencia `@kalel1500/laravel-ts-utils` a la versión `^0.4.0-beta.10`
  * configurar las versiones de las dependencias de NPM en la configuración (`config/hexagonal`)
* Dependencias de NPM actualizadas
* layout:
  * Establecer un ancho y alto fijos a la imagen del logo de la App para prevenir salto de imagen (por si tarda en cargar)
  * (refactor) simplificar lógica extension del Javascript en el `welcome.blade.php`
  * cambiar origen logo Flowbite a local (con la directiva `@viteAsset()`) en vez de apuntar a internet
* stubs:
  * actualizar archivos a la version 11.6 de `laravel/laravel`
  * guardar las imágenes en el paquete `hexagonal` en lugar de en el `@kalel1500/laravel-ts-utils`
* hexagonalStart:
  * Nuevo método `modifyFile_PackageJson_toAddNpmDependencies()` para instalar el paquete `@kalel1500/laravel-ts-utils` en `dependencies` + añadir condición para eliminar propiedad si queda un array vacío
  * Nuevo parámetro `$simple` para poder limitar la instalación a solo lo necesario para un backend o una api
  * Separar método `execute_NpmInstallAndNpmRunBuild()` en dos (`execute_NpmInstall()` y `execute_NpmRunBuild()`)
  * Extraer el código repetido de `execute_NpmInstall()` y `execute_NpmRunBuild()` al método `execute_Process()`
  * Automatizar lógica de los números para no tener que pasarlo por parámetro en la ejecución de cada método
  * Propiedad `$packageInDevelop` renombrada a `$developMode`
  * Nuevo método `execute_NpxLaravelTsUtils()`
  * Quitar condición `&& !$this->reset` para cortar la ejecución en `developMode` del método `stubsCopyFile_AppServiceProvider()`
  * Añadir condición `$developMode` en el método `execute_ComposerRequire_toInstallComposerDependencies()` (para que no se instale cada vez)
  * Documentar y tipar métodos privados
  * Pintar mensaje inicial del método `execute_Process()` solo si no es `null`
  * Nuevo método `execute_gitAdd()`
  * Mejorar método `createEnvFiles()` para que el `reset` solo elimine el `.env.local` y regenere el `.env` y el `.env.example` en base al archivo que ahora se guarda en `stubs/original/.env.example` y después genere la key
  * Usar el nuevo trait `CountMethods` para contar los pasos del `StartCommandService` en vez de pasarlo manualmente por el constructor
  * Añadir las condiciones del parámetro `$this->simple` para no ejecutar los métodos relacionados con el paquete `@kalel1500/laravel-ts-utils` cuando se pasa el parámetro `--simple`
  * Obtener la configuración `hexagonal.package_in_develop` de la nueva variable de entorno `env('HEXAGONAL_PACKAGE_IN_DEVELOP', false)`
  * No sobreescribir el archivo `.env` si el parámetro `$this->developMode` es `true` para poder configurarlo en la variable `HEXAGONAL_PACKAGE_IN_DEVELOP` del `.env`
  * Nuevo método `stubsCopyFolder_Images()`
  * Unificar métodos `stubsCopyFolder_Views()` y `stubsCopyFolder_Images()` en `stubsCopyFolder_Resources()`
  * Añadir condición ` || $this->simple` al deshacer los métodos del front para que se pueda lanzar el `--simple` después del full y que siga funcionando:
    * -`modifyFile_PackageJson_toAddNpmDevDependencies`
    * -`modifyFile_PackageJson_toAddNpmDependencies`
    * -`modifyFile_PackageJson_toAddScriptTsBuild`
    * -`execute_NpxLaravelTsUtils`
  * Nuevo método privado `isReset($isFront)` para poder definir que métodos pertenecen al front y mover la lógica del `$this->simple` dentro de este nuevo método
  * Añadir el archivo `resources/js/app.js` a los stubs con la compilación de las imágenes
* development-tips: añadida información del `composer.json` para instalar la versión `dev-master` del paquete con un enlace durante el desarrollo
* tsUtilsDevelop: instalado el nuevo paquete `@kalel1500/laravel-ts-utils` y ejecutado el comando `npx laravel-ts-utils` (se han actualizado los imports de los archivos `.ts`)
* (breaking) Dejar de soportar la version `7.2.5` de PHP (ahora como mínimo la `7.4`)
* hexagonalStartReset:
  * (refactor) mejorar método `createEnvFiles()` (para facilitar el borrado)
  * (refactor) mejorar método `modifyFile_DatabaseSeeder_toCommentUserFactory()` (hacerlo mas genérico)
  * Añadir código para revertir cada método (cuando recibamos el parámetro `--reset`)
  * (refactor) mejorar método `modifyFile_JsBootstrap_toAddImportFlowbite()` (hacerlo mas genérico)
  * Métodos reordenados
  * (refactor) mejorar método `modifyFile_ComposerJson_toAddSrcNamespace()` (para facilitar el borrado y eliminar orden)
  * Identar los mensajes y añadir prefijo con el número de la tarea y el total de tareas
  * Comentar los métodos que no se utilizan en vez de llamarlos y comentar el contenido
  * Renombrar propiedad `$skipHarmlessMethods` por `$packageInDevelop` y añadir la configuración en el `config/hexagonal.php`
  * Desactivar los siguientes métodos cuando se ha configurado a true la variable $packageInDevelop en la config` ->
    * -`publishHexagonalConfig()`
    * -`stubsCopyFile_AppServiceProvider()`
    * -`modifyFile_Gitignore_toDeleteLockFileLines()`
    * -`execute_NpminstallAndNpmRunBuild()`
* hexagonalStartReset:
  * Añadir parámetro `--reset` al comando `hexagonal:start`
  * (refactor) Mejorar estructura rutas archivos para evitar repeticiones
* hexagonalStartRelaunch:
  * `publishHexagonalConfig()` -> forzar republicación de la configuración eliminando la actual
  * Cambiar `addCommentIgnoreMigrationsInAppServiceProvider()` por `stubsCopyAppServiceProvider()` ya que es más fácil tener el archivo creado en `stubs` que hacer el regex
  * (fix) arreglar método `addHexagonalExceptionHandlerInBootstrapApp()` (no funcionaba siempre)
  * Añadir validación de la versión de Laravel 11 en los métodos `addDependencyServiceProviderToBootstrapFile()` y `addHexagonalExceptionHandlerInBootstrapApp()`
  * No permitir lanzar el comando en versiones de Laravel inferiores a Laravel 11
  * (fix) prevenir error al relanzar el método `commentUserFactoryInDatabaseSeeder()`
  * Métodos renombrados con `_` para separar los conceptos
  * (fix) Prevenir error al relanzar el método `modifyFile_JsBootstrap_toAddImportFlowbite()`
  * (fix) Sol. error al ejecutar el método `$this->command->requireComposerPackages()` desde el servicio (se ha movido al comando, ya que el trait no se pasa con el $this)
  * Prevenir `execute_ComposerRequire_toInstallComposerDependencies()` para que no se ejecute si ya está instalado
  * Añadir propiedad `$skipHarmlessMethods` para poder saltar los comandos de instalación durante el desarrollo
  * Nuevo método `restoreFilesModifiedByPackageLaravelTsUtils()` en el `HexagonalStart.php` para deshacer el comando `npx laravel-ts-utils` por si se relanza el `hexagonal:start` después del npx del paquete de JS
  * (refactor) Usar las rutas con los métodos `stubsPath()` y `originalStubsPath()` en vez de concatenarlas
* hexagonalStartReset:
  * Mover los archivos de `stubs` a la carpeta `stubs/generate`
  * Añadir los archivos originales en la carpeta `stubs/original`
* hexagonalStartRoutes: Comprobar la versión de php para las rutas
* Tags de las versiones renombrados

### Fixed

* (fix) añadir validaciones `!$this->app->runningInConsole() && !empty(config('app.key'))` al registrar el middleware `AddPreferencesCookies` en el `HexagonalServiceProvider` (para solucionar error cuando la variable `APP_KEY=` está vacía y en el `AppServiceProvider` se llama a `Hexagonal::configure()->enablePreferencesCookie()`)
* (fix) sol. error en la macro `@mergeTailwind()` cuando las clases tienen espacios (se ha añadido `array_filter` después del `explode`)
* (fix) eliminar tipado del parámetro $code de la clase `ExceptionContextDo.php`, ya que puede ser string (en las excepciones de eloquent)
* (fix) sol. error en el método `translatedValue()` de la clase `ContractEnumVo` cuando el valor del `enum` es `null`

## [v0.13.0-beta.3](https://github.com/kalel1500/kalion/compare/v0.13.0-beta.2...v0.13.0-beta.3) - 2025-01-13

### Changed

* Nuevo método `getResponse()` en la `HexagonalException` para poder definir la respuesta completa en cada `Exception` (nuevo parámetro `$custom_response` en el `ExceptionContextDo` para usarlo en el `toArray()`)
* Refactor: renombrar método `toArrayForDebug()` de `ExceptionContextDo` a `arrayDebugInfo()` y hacer que solo devuelva la info del debug para mergearlo después y ahorrar código
* Refactor: Mover todo el código de la clase `HexagonalStart` al nuevo servicio `StartCommandService` y llamar a los métodos en cadena en el método `handle()`
* Nueva Macro `mergeTailwind` en la clase `ComponentAttributeBag` para poder usar el `$attributes->mergeTailwind(...)` en los componentes

## [v0.13.0-beta.2](https://github.com/kalel1500/kalion/compare/v0.13.0-beta.1...v0.13.0-beta.2) - 2024-11-26

### Changed

* Paquete `laravel-ts-utilities` actualizado a la version `1.3.0-beta.7` 
  * (se ha añadido la funcionalidad de rotar las flechas de los dropdowns del sidebar)
  * (se ha corregido error cuando no hay sidebar)
* enlaces: ajustar estilos del sidebar collapsed para visualizar mejor los dropdowns
* enlaces: rotar flecha de los dropdowns cuando están abiertos
* enlaces: reorganizar enlaces iniciales
* enlaces: permitir varios niveles de dropdowns
* Añadir el `$attributes->merge(['class' => ...])` en el componente `<section>` para poder añadirle clases al componente desde cada vista
* Refactor: `NavigationItem` movida a `...Objects\DataObjects\Layout\Contracts`
* (breaking) Refactor: Colecciones movidas de `Src\Domain\Objects\Collections` a sus respectivas carpetas:
  * `Src\Domain\Objects\Entities\Collections` (`Entities`)
  * `Src\Domain\Objects\ValueObjects\EntityFields\Collections` (`ModelId`)
  * `Src\Domain\Objects\ValueObjects\Primitives\Collections` (`Vo`)
  * `Src\Domain\Objects\DataObjects\Layout\Collections` (`Items layout`)
* Hacer clase `NavigationItem` abstracta
* Paquete `laravel-ts-utilities` actualizado a la versión `1.3.0-beta.5` (se ha solucionado el warning de Vite de la compilación)

### Fixed

* (fix) Cambiar los `new self()` por `new static()` en los métodos `createFromArray()` de las entidades para evitar errores en las relaciones al extender las entidades
* (fix) Quitar el `final` de todos los Modelos, Entidades, Colecciones y Repositorios para poder extenderlos desde la aplicación
* (fix) arreglar error en la directiva `@viteAsset`
* (fix) comprobar si el objeto es de tipo `Enum` en el `fromArray()` de la clase `ContractCollectionDo`

## [v0.13.0-beta.1](https://github.com/kalel1500/kalion/compare/v0.12.0-beta.1...v0.13.0-beta.1) - 2024-11-26

### Added

* docs: Nuevo archivo `todo-list.md` con las siguientes tareas del paquete
* stubs:
  * añadir nuevo `web_php_old.php` adaptado al `PHP < 8` (en el futuro se añadirá una condición en el comando `hexagonal:start`)
  * añadir blade `welcome.blade.php`, ya que tras las instalaciones hay que comprobar la extension del archivo JS al usar la directiva `@vite()`
* cookies:
  * Nuevo Middleware `AddPreferencesCookies.php` que genera las cookies (si no existen) con las preferencias del usuario por defecto 
  * Añadir código para registrar el middleware en el `HexagonalServiceProvider.php`
  * Nuevas variables de configuración para las cookies y las preferencias del usuario
  * Nuevo servicio `CookieService` con la lógica de la creación de la cookie para poder reutilizarla desde la aplicación
  * Nueva ruta `/cookie/update` (controller `AjaxCookiesController`) para actualizar la cookie por ajax
  * Nueva clase `CookiePreferencesDo` para simplificar el código y el flujo de la clase `CookieService`
  * Hacer que por defecto el `sidebarCollapsed` del `Layout/App` se configure globalmente (`config('hexagonal.sidebar_collapsed_default')`) y solo usar los items si `config('hexagonal.sidebar_state_per_page') === true`
  * Nueva variable $darkMode en `Layout/App` (`hexagonal.dark_mode_default`) para configurar por defecto el modo oscuro
  * Añadir nueva lógica en `Layout/App` para establecer las variables `$darkMode` y `$sidebarCollapsed` según las cookies recibidas (si están habilitadas)
  * Leer variables de configuración de las cookies del archivo `.env`
  * (fix) comprobar la config del dark-mode al pintar los iconos `theme-toggle`, ya que por defecto estaban ocultos a la vez
  * Nueva vista de ejemplo `example/modify-cookie` con botones para modificar la cookie desde el front (código TS)

### Changed

* Paquete `laravel-ts-utilities` actualizado a la version `1.3.0-beta.4`
* docs: archivo `development-tips.md` actualizado con el regex para excluir carpetas al comparar dos proyectos
* Layout:
  * (refactor) ordenar head del componente `layout/app`
  * Modificar font-weight de los enlaces del sidebar cuando está colapsado
  * Componente `icon.user` renombrado a `icon.user-profile`
  * icons: 
    * Creados nuevos componentes para los iconos
    * Nueva ruta `example/icons` con la vista de todos los iconos disponibles
    * Modificar los iconos para que reciban los `$attributes`, las propiedades `strokeWidth`, `flowbite` y `outline` y estructurarlos para poder añadir los tres tipos de iconos
    * Cambiar todos los iconos de SVG a los nuevos componentes (nuevo componente `<x-render-icon>` para poder renderizar por el componente, el nombre, o el nombre con la clase separados por `;`
  * Nuevos enlaces añadidos al Sidebar con todas las rutas definidas hasta ahora
  * (fix) Corregir títulos de las páginas
  * stubs: Ponerle nombre a la ruta `welcome` (para poder acceder a ella desde el sidebar)
* stubs: Cambios ruta `/home` 
  * renombrar y mover controller de `Src\Home\Infrastructure\HomeController` a `Src\Shared\Infrastructure\Http\Controllers\DefaultController` 
  * renombrar método de `index` a `home` 
  * renombrar y mover vista de `pages.home.index` a `pages.default.home` 
  * renombrar nombre de ruta de `home.index` a `home` 
  * Añadir texto `Hola mundo` en la vista `home.blade.php`
* Añadir validación en la migración `create_states_table` para comprobar que no exista la tabla `states` antes de crearla
* Nuevos métodos `fromJson()` `toJson()` y `__toString()` en la clase `ContractDataObject.php` + hacer que implemente la interfaz `Jsonable`
* Cambios servicio `Hexagonal.php`:
    * (breaking) Modificar clase `Hexagonal.php` para hacer que sea configurable en cadena
    * (breaking) Establecer valor `$runsMigrations` por defecto a `false` para que por defecto no se ejecuten las migraciones del paquete y haya que activarlas manualmente desde la aplicación
    * Añadir configuración en la clase `Hexagonal` para activar las Cookies de las preferencias que por defecto están desactivadas
* (breaking) Mover la carpeta `Controllers` dentro de `Http`
* Cambios en el `HexagonalServiceProvider`:
  * Añadir nueva publicación en el `registerPublishing()` del `HexagonalServiceProvider.php` para permitir publicar el componente `layout/app` de forma independiente con el tag `hexagonal-view-layout`, ya que es el componente que más se puede querer editar
  * (fix) Cambiar validación `shouldRegistersRoutes()` por `shouldRunMigrations()` al publicar las migraciones
* Comando `hexagonal:start`
  * Modificar el comando `hexagonal:start` para que no elimine la carpeta `app/Models`
  * (fix) Añadir la ruta completa a la clase `Hexagonal` al añadir la línea `Hexagonal::ignoreMigrations()` al `AppServiceProvider` en el comando `hexagonal:start` para no tener que importar la clase
  * Descomentar la línea `Hexagonal::ignoreMigrations()` en el comando `hexagonal:start` para que por defecto se ignoren las migraciones del paquete
  * (fix) Añadir las clases de los componentes al publicar las vistas en el comando `hexagonal:start`
  * adaptar escritura del `AppServiceProvider` a la nueva forma de configuración del paquete (y hacer que por defecto esté comentada)

### Fixed

* (fix) Arreglar directiva blade `@viteAsset`, ya que debe ejecutar el código en la vista y no al declarar la directiva (funcionaba solo porque le pasaba un parámetro estático)
* (fix) Definir manualmente los archivos en los que tailwind tiene que buscar las clases al compilar el css
* stubs: (fix) sol. error en la ruta del import `DefaultController`
* (fix) solucionar error vite poniendo el `publicDir` a `false` (ya que coincide con el `outDir`)
* stubs: (fix) corregir nombre ruta /home (`home.index`) para que sea coherente con el paquete del front

## [v0.12.0-beta.1](https://github.com/kalel1500/kalion/compare/v0.11.0-beta.2...v0.12.0-beta.1) - 2024-11-11

### Added

* Añadidas las traducciones en español (del paquete `Laraveles/spanish`)
* Publicadas las traducciones de laravel en el paquete

### Changed

* (breaking) Renombrar propiedad `$allowNull` por `$nullable` y método `checkAllowNull()` por `checkNullable()` (en todas las clases que los usan)
* (breaking) Eliminar helpers innecesarios `HTTP_...()` ya que son constantes que están definidas en la clase `Symfony\Component\HttpFoundation\Response`
* (breaking) Eliminar propiedades `$reasonNullNotAllowed`, `$mustBeNull` y `$reasonMustBeNull` y simplificar lógica `checkAllowNull()` de la clase `ContractValueObject` (se ha movido la lógica a la aplicación que la usa, ya que es un caso concreto de esa aplicación)
* (phpdoc) Añadir el tipo de retorno `null` en el PhpDoc del método `CollectionEntity::fromArray()`
* (breaking) Modificar parámetro `$isFull` de las entidades para que se pueda pasar un `string` con el nombre del método que queramos usar para obtener las propiedades calculadas de la entidad al hacer el `toArray()`
* Añadir la propiedad `$datetime_eloquent_timestamps = 'Y-m-d\TH:i:s.u\Z'` en el helper `MyCarbon`
* Añadir el `->setTimezone()` en el método `carbon()` de la clase `ContractDateVo` (por si es una fecha UTC) y guardar en la propiedad `$valueCarbon` para evitar hacer el cálculo varias veces
* Nuevo método `from()` en la clase `ContractDateVo` para poder crear las fechas con formato `timestamp` (de Eloquent) que se formatean con el timezone UTC en el `toArray()`
* (phpdoc) simplificar return types en los PhpDoc (cambiar varios `@return T` por `@return static` o `@return $this`)
* Añadir `@stack('css-variables')` y `@stack('styles')` en el componente `layout/app.blade.php` para poder añadir CSS adicional en cada página

### Fixed

* (fix) Prevenir error si el método `CollectionEntity::fromArray()` recibe un `null`
* (fix) Sobreescribir método `new()` en la clase `ContractDateVo` para pasar el parámetro `$formats` al constructor

## [v0.11.0-beta.2](https://github.com/kalel1500/kalion/compare/v0.11.0-beta.1...v0.11.0-beta.2) - 2024-11-11

### Added

* docs: `code-of-interest` -> código interesante para añadir limitaciones a las peticiones por API (`limit-api-action.md`)
* stubs: Nueva carpeta `stubs` con todos los archivos necesarios en el comando `HexagonalStart`
* Nuevo comando (`HexagonalStart`) creado para crear los archivos iniciales en la aplicación
  <details>
  
    - creados: 
      - crear provider `app/Providers/DependencyServiceProvider.php`
      - crear vista `resources/views/pages/home/index.blade.php`
      - crear controlador `src/Home/Infrastructure/HomeController.php`
      - crear servicio `src/Shared/Domain/Services/RepositoryServices/LayoutService.php`
      - crear envs `.env`, `.env.local` y `APP_KEY` generada
      - publicar configuración `config/hexagonal.php` generada
    - eliminados:
      - eliminar los archivos `.lock` del `.gitignore`
      - eliminar carpeta `app/Http`
      - eliminar carpeta `app/Models`
      - eliminar archivo `CHANGELOG.md`
    - modificados: 
      - añadir `DependencyServiceProvider` en `/bootstrap/providers.php`
      - añadir `ExceptionHandler` en `/bootstrap/app.php`
      - añadir dependencias de NPM en el `package.json`
      - añadir script `ts-build` en el `package.json`
      - instalar `tightenco/ziggy`
      - añadir namespace `Src` en el `composer.json`
      - añadir rutas iniciales en `routes/web.php`
      - añadir configuración inicial en `tailwind.config.js`
      - comentar User factory en `database/seeders/DatabaseSeeder`
      - importar `flowbite` in `resources/js/bootstrap.js`
      - añadir comentario `HexagonalService::ignoreMigrations()` en el `app/Providers/AppServiceProvider.php`
    - otros:
      - añadir comandos `composer dump-autoload`, `npm install` y `npm run build`

  </details>

### Changed

* docs: `development-tips.md` -> añadir comandos para eliminar un tag
* Nuevo método estático `fromId()` en el trait `WithIdsAndToArray` para poder instanciar un `BackedEnum` a partir del `Id`
* Rutas: Añadir la ruta `hexagonal.root` en el componente `navbar.brand`
* Rutas: Añadir las rutas `hexagonal.queues.queuedJobs` y `hexagonal.queues.queuedJobs` en la configuración del `sidebar`
* Rutas: Cambiar `route('default')` de la vista `jobs.blade.php` por `route('hexagonal.root')` para no depender de que la aplicación tenga creada la ruta `default`
* Rutas: Crear nueva ruta `/root` que hace una redirección hacia `/`
* Rutas: Clase `TestController` renombrada a `HexagonalController`
* Añadir middleware `web` en las rutas del paquete

### Fixed

* (fix) añadir el icono al breadcrumb del example2.blade.php (que se perdió en algún momento)
* (fix) Sol. error al obtener los `$links` para comprobar `$this->sidebarCollapsed` -> cambiar `hexagonal.sidebar_links` por `hexagonal.sidebar.items`
* (fix) Prevenir errores cuando en la configuración no hay `navbar.items`, `sidebar.items` o `sidebar.footer`

### Removed

* renderer: eliminar ruta y vista `testVitePackage`, ya que ahora se hace de otra forma y ya está funcionando en el Layout

## [v0.11.0-beta.1](https://github.com/kalel1500/kalion/compare/v0.10.0-beta.2...v0.11.0-beta.1) - 2024-11-06

### Changed

* Añadir parámetro $formats en el constructor de la clase `DateVo`
* Adaptar el método `checkFormat()` de la clase `MyCarbon` para validar zeros y Crear nuevo método `checkFormats()` para validar un array de formatos
* !!! Añadir propiedad `$allowZeros` para poder pasarle fechas con zeros.
* !!! (breaking) Modificar propiedad `$formats` de `ContractDateVo` de `String` a `Array` para que acepte varios formatos
* (breaking) Eliminar formateo de fecha en el constructor de la clase `ContractDateVo` para mantener la integridad de los datos
* Añadir nuevo formato `$datetime_startYear_withoutSeconds` en la clase `MyCarbon`
* Poner un valor por defecto a la propiedad `$format`
* !!! (breaking) Modificar segundo parámetro constructor de `ContractDateVo` para recibir el formato

## [v0.10.0-beta.2](https://github.com/kalel1500/kalion/compare/v0.10.0-beta.1...v0.10.0-beta.2) - 2024-11-06

### Changed

* Renombrar migraciones y validar que no existan antes de crearlas para evitar conflictos con las migraciones del proyecto
* Mover la directiva @routes encima de los scripts
* !!!Renderer: Insertar CSS y JS compilado en el `layout` en lugar de usar directive `@vitePackage` para no tener que generar una ruta laravel que sirva los archivos

### Fixed

* (fix) Prevenir error al llamar al `favicon.ico` con `Vite::asset` en el `layout` usando la nueva directiva `@viteAsset` que contiene un tryCatch
* (fix) Prevenir error al llamar al JS con `@vite()` en el `layout` comprobando que exista un archivo `.ts` en el proyecto (usar extension `.js` si no existe)

## [v0.10.0-beta.1](https://github.com/kalel1500/kalion/compare/v0.9.0-beta.3...v0.10.0-beta.1) - 2024-11-05

### Changed

* <u>**!!! (breaking) !!!**</u> Permitir que los ValueObjects que no son NULL, estén vacíos (`empty()`) para mantener la integridad de los datos
* <u>**!!! (breaking) !!!**</u> Dejar de limpiar el value en la clase `ContractStringVo` para mantener la integridad de los datos
* comentarios añadidos en el método `checkAllowNull()` de la clase `ContractValueObject`
* (phpdoc) añadir tipos dinámicos en PhpDoc con `@template` en las clases de las colecciones
* (refactor) eliminar condición innecesaria en `ContractCollectionEntity::fromData()`
* (refactor) ordenar código validaciones del método `ContractCollectionEntity::fromData()`

### Removed

* eliminar código comentado
* eliminar código duplicado en la clase `ContractModelId`

### Fixed

* (fix) prevenir errores al añadir validaciones en los métodos `fromData()` de las colecciones para validar que las constantes siempre tengan un valor definido
* (fix) Prevenir error cuando se crea un StringVo con el valor `''` (añadida propiedad `protected $allowNull = false` en los ValueObjects que no deban permitir null)

## [v0.9.0-beta.3](https://github.com/kalel1500/kalion/compare/v0.9.0-beta.2...v0.9.0-beta.3) - 2024-11-05

### Added

* Nuevas clases `UnsignedInt` (tanto primitivas como de Entidad) para tener un ValueObject que solo acepte números positivos

### Changed

* Actualizar dependencia de npm `laravel-ts-utilities` a la versión `1.3.0-beta.1` + actualizar identación archivos
* Permitir que la clase `ContractIntVo` tenga números negativos (quitar de la validación el `$value < 0`)

### Fixed

* (fix) incluir la validación `checkAllowNull()` en el método `ensureIsValidValue` de la clase `ContractModelId`
* (fix) adaptar método `ensureIsValidValue()` de `ContractModelId` a la clase padre haciendolo `protected` y renombrando la variable `$id` por `$value`

## [v0.9.0-beta.2](https://github.com/kalel1500/kalion/compare/v0.9.0-beta.1...v0.9.0-beta.2) - 2024-11-04

### Added

* Nuevos Value Objects `ModelIdZero` y `ModelIdZeroNull` para poder crear ids permitiendo que el valor sea igual a `0`

### Changed

* Actualizar PhpDoc del método `ContractModelId::from()` con un `@return T` (de la template definida en la clase -> `@template T of ContractModelId`)
* Adaptar código de `PHP 8` a `PHP 7.2.5` (cambiar `match` en los componentes y arrow function en trait `WithIdsAndToArray`)
* !!!Rollback versiones mínimas de `PHP` y `Laravel`. Volver a añadir las versiones (`^7.2.5|^8.0|^8.1`) de php y las versiones (`^7.0|^8.0`) de laravel
* Añadir variable `protected $minimumValueForModelId` en la clase `ContractModelId` para poder sobreescribirla desde fuera creando otras clases que extiendan de ella. Por defecto se mantiene el valor de la configuración `config('hexagonal.minimum_value_for_model_id')`
* Usar las variables estáticas para obtener la clase al hacer el new `ModelId...()` en el método `ContractModelId::from()` para poder crear otras clases que extiendan de `ContractModelId`
* Añadir configuración `hexagonal.minimum_value_for_model_id` para establecer el valor mínimo permitido en el value object `ModelId`
* config: Comentario Layout terminado en `config/hexagonal.php`
* docs: Añadido código interesante para formatear los logs como JSON

### Fixed

* (fix) corregir gramática comentario
* (fix) corregir error al pasar el antiguo parámetro HTTP_CODE en el constructor de la clase `UnsetRelationException`

## [v0.9.0-beta.1](https://github.com/kalel1500/kalion/compare/v0.8.0-beta.1...v0.9.0-beta.1) - 2024-10-31

### Added

* Nuevo Enum `EnumWIthIdsContract` y Nuevo Trait `WithIdsAndToArray` para los ValueObject de tipo Enum
* public: new build
* Nueva vista con código js para comparar dos bloques HTML
* Nuevas vistas (blades) con ejemplos de Tailwind
* Crear y compilar todo el JS y CSS necesario para las vistas internas del paquete (con nueva directiva `@vitePackage()`)
* Instalar paquete `laravel-ts-utilities` para poder compilar js y css propios del paquete
* Docs: Nuevos archivos con código interesante
* Nueva interfaz `LayoutServiceContract` (para que al crear el servicio en la aplicación, tenga todos los métodos)
* Nuevos componentes para crear una Layout inicial en tailwind:
  * Componente Layout
  * Componente Navbar
  * Componente Sidebar
  * Componente Footer
  * Enlaces Navbar y Sidebar definidos en la configuración `config/hexagonal.php`
* Nuevos componentes blade reutilizables en Tailwind

### Changed

* Establecer la variable de entorno `HEXAGONAL_BROADCASTING_ENABLED` por defecto a `false`
* <u>**!!! (breaking) !!! Subir versiones mínimas de `PHP` y `Laravel` a `^8.2` y `^11.0` respectivamente**</u>
* (refactor) Se utiliza el método `toArrayDynamic()` en los métodos `toArrayDb()` y `toArrayWith()` de la clase `ContractCollectionEntity`
* Se ha añadido el método `toArrayDynamic()` en la clase `ContractCollectionBase` para facilitar la creación de otros métodos `toArray...()` en otras entidades y colecciones
* Se han añadido las propiedades `$primaryKey` e `$incrementing` en la clase `ContractEntity` para controlar el `id` en el método `toArrayDb`
* (breaking) Hacer que `HexagonalException` extienda de `DomainException` en vez de `RuntimeException`
* (refactor) Renombrar `DomainException` a `HexagonalException`
* Añadir método `toArrayVo()` en la clase `ContractDataObject`
  <hr/>
* (refactor) Ordenar Rutas en el `routes/web.php`
* (refactor) importar los controllers en las Rutas en el `routes/web.php`
* (refactor) Mover vistas a la carpeta `pages` (para separarlas de los componentes)
* Cambiar los imports por rutas absolutas en el `web.php`
* Separar los Controllers en las carpetas `Ajax` y `Web`
* Hacer que la clase `AbortException` extienda de la interfaz `HttpExceptionInterface` para que Laravel la trate como una excepción Http
* (breaking) Renombrar el helper `abortC` a `abort_d` ya que es el abort del dominio
* (breaking) Renombrar la clase `GeneralException` a `AbortException` ya que se entiende mejor su propósito
* Añadir la constante `MESSAGE` en la clase `BasicException` para poder definir un mensaje por defecto en cada excepción que herede de esta clase

### Fixed

* (fix) Quitar prefijo `hexagonal` de la ruta de test ya que el paquete ya lo añade automáticamente
* (fix) Prevenir error del helper `getUrlFromRoute()` cuando la ruta no existe
* (fix) Añadir modo estricto en la interfaz `Relatable`

## [v0.8.0-beta.1](https://github.com/kalel1500/kalion/compare/v0.7.0-beta.1...v0.8.0-beta.1) - 2024-10-25

### Added

* Nueva ruta `LayoutController@public` para servir los assets del paquete (y asi poder compilarlo internamente)
* Nueva ruta (y vista) `test` para probar como compila el @vite desde el paquete
* Nuevo método `each` en la Colección Base
* nuevos helpers: `getUrlFromRoute()`, `strToSlug()`
* nuevos helpers: `isRouteActive()`, `dropdownIsOpen()`, `currentRouteNamed()`

### Changed

* (breaking) modificar comportamiento del método `Collection::fromArray()` para que si recibe null devuelva null, en lugar de una colección vacía (en todas las colecciones)
* varios helpers marcados como deprecados + PhpDoc helper actualizado
* componentes: nueva variable (config) para el componente <x-layouts.app>
* componentes: nueva traducción para el componente <x-messages>

### Removed

* eliminar helpers antiguos

## [v0.7.0-beta.1](https://github.com/kalel1500/kalion/compare/v0.6.0-beta.1...v0.7.0-beta.1) - 2024-09-10

### Added

* Nuevos métodos `toNoSpaces()` y `toCleanString()` en `ContractValueObject`.
* Nuevos métodos `formatToSpainDatetime()` y `carbon()` en `ContractDateVo`
* Nuevos métodos `toNull()` y `toNotNull()` en `ContractValueObject` (y nuevas constantes para guardar las clases y hacer el cálculo).
* Nuevo método `toCamelCase()` en `ContractValueObject`.
* Nuevo método `toArrayCalculatedProps` en `ContractEntity` para poder sobreescribirlo y definir las propiedades calculadas.
* Nuevo método `clearString` en la clase `ContractStringVo` para hacer que si se recibe un string vacío, se asigne el valor `null`.
* Registrar en el `ServiceProvider` la relación entre la interfaz del StateRepository y su implementación (en el array de `$singletons`) para no tener que hacerlo en la aplicación.
* Nuevo método `from()` en la clase `ContractModelId` + utilizarlo en lugar del `new ModelIdNull` en las entidades.

### Changed

* Sobreescribir método `new()` en el ArrayTabulatorFiltersVo para poder pasarle todos los parámetros que tiene el constructor.
* Permitir que al definir las relaciones, si son asociativas, la key pueda tener varias con punto. Ej: `[relOne.SubRel1 => [SubRel2, SubRel3]`.
* Ordenar y documentar las variables de entorno del archivo de configuración del paquete.
* Rediseño completo de la gestión de errores:
    * Ordenar y documentar código del `ExceptionHandler`.
    * Mover renderizado de las excepciones del dominio al `ExceptionHandler`.
    * Añadir información previous al `toArrayForDebug`.
    * Permitir que el $message y el `$code` sean opcionales en el `DomainBaseException`.
    * No hacer que el `previous` sobreescriba la información de la excepción actual.
    * Modificar mensajes de error de las excepciones.
    * Implementar bien la Hexagonal.
    * Excepciones ordenadas y renombradas.
    * Parámetros ordenados y simplificados.
    * Nuevo parámetro `$statusCode` en las excepciones para no usar el `$code` que no es para eso.
    * Los códigos HTTP se definen en las excepciones en lugar de pasarlo cada vez (asi por cada ex se controla su code).
    * Lógica `getExceptionData()` y `getExceptionMessage()` movida al DTO `ExceptionContextDo`.
    * Clase `CustomException` renombrada a `GeneralException`.
    * `ExceptionHandler` mejorado con el `getStatusCode()` del contexto.
    * Nuevo código comentado en el `ExceptionHandler` para en un futuro poder sobreescribir otras excepciones (database).
    * Nuevo código comentado en el `ExceptionHandler` para en un futuro poder sobreescribir el renderizado de la vista de errores (por si se quiere pasar una excepción previa).
    * Ahora el `responseJsonError()` ya no hace falta para las excepciones de dominio (y para las otras casi tampoco).
* Mejorar método `MyCarbon::parse()` para que no devuelva `null`.
* Hacer que el método `createFromObject()` de la clase `ContractEntity` no sea obligatorio.
* Modificar métodos `toUppercase()` y `toLowercase()` de `ContractValueObject`.
* No permitir ni devolver null en el `fromArray()` y `fromObject()`.
* Rediseño completo del funcionamiento de las Entidades y sus relaciones:
  * Una entidad solo tiene que tener sus propiedades en el constructor (ni relaciones ni propiedades calculadas en eloquent).
  * En lugar de recibir los cálculos de eloquent, se definirán en la entidad utilizando las relaciones definidas también en la entidad.
  * Se ha creado el nuevo método `toArrayCalculatedProps()` para separar los campos de las propiedades calculadas y poder decidir si traerlas o no al crear las entidades y relaciones.
  * Nuevos métodos `getRelation()` y `setRelation()` `ContractEntity` para poder definir mejor las relaciones en las entidades y no tener que definir una propiedad para cada relación.
  * Al crear las entidades y colecciones se podrá pasar el parámetro `$isFull` para indicar si se tiene que traer las propiedades calculadas.
  * Al crear las entidades y colecciones, en las relaciones se podrá añadir un flag para indicar si son full o no. Ej.: `OneEntityCollection::fromArray($data, ['relOne:f', 'relTwo:s', 'relThree:f.subRelOne:s'])`.
  * Se ha definido la variable de entorno `HEXAGONAL_ENTITY_CALCULATED_PROPS_MODE` para definir si como se comportan las relaciones por defecto cuando no se indica el flag.
* Mejorar la lógica del método `pluck()` de la clase `ContractCollectionBase`.
* Utilizar las nuevas interfaces en el método `getItemToArray()` y hacer el código más legible.
* Renombrar método `toArrayWithAll` por `toArrayForBuild` en la clase `ContractDataObject` (nueva interfaz `BuildArrayable` para indicar que la clase debe contener el método `toArrayForBuild()`).
* Renombrar interfaces: `MyArrayableContract` a `Arrayable` y `ExportableEntityC` a `ExportableEntity`.
* Modificar firma métodos de `ContractCollectionEntity` y `ContractEntity`, para permitir que se pueda recibir un string en lugar de un array en el parámetro `$with`.
* Dejar que se cree la relación vacía si no hay datos en el método `with()` de la clase `ContractEntity`.
* Cambiar el `new ModelIdNull(...)` de las entidades por el `ModelId::from(...)` para que solo se cree la instancia `ModelIdNull` si el valor recibido es null y de lo contrario se cree la instancia de `ModelId`.

### Removed

* Eliminar método `toArrayForJs()` de la clase `ContractDataObject`.
* Eliminar `FindStateByCodeUseCase` de infraestructura y mover lógica a `StateDataService` en el dominio.
* Eliminar el método `toModelId` de la clase `ModelIdNull`.

### Fixed

* Solucionado error en el método `fromArray()` cuando recibimos una paginación (no se estaba setenado bien el `$data` tras guardar los datos de la paginación).
* Solucionar error de tipo en el método `flatten()` de `ContractCollectionBase`.
* Quitar lógica duplicada:
  * Quitar parámetro `$last` método `setFirstRelation()` de la clase `ContractEntity` y no pasarlo al método `$setRelation()` de cada entidad (ya que de esto se encarga el método `setLastRelation()`).
  * Quitar parámetro `$with` método `fromRelationData()` de la clase `ContractCollectionEntity`, ya que, los métodos `set...()` de las entidades que llaman a este método ya no reciben en `$with`.

## [v0.6.0-beta.1](https://github.com/kalel1500/kalion/compare/v0.5.0-beta.3...v0.6.0-beta.1) - 2024-08-16

### Added

* Nuevo archivo `development-tips.md` para guardar los comandos de git recurrentes

### Changed

* Renombrar método `items()` a `all()`
* Mover propiedad `$item` encima de `$allowNull`
* Permitir que sea `null` el parámetro `$relationName` del helper `getSubWith()`
* Método `toBase()` simplificado y hecho privado
* Sacar transformaciones de la función `$getItemValue` y crear una llamada `$clearItemValue` en el método `pluck()` para poder añadir más adelante el `setPreviousClass`
* Mejora método `collapse()`: Unir el `$item->toArray()` en el mismo `if()` mirando la instancia `MyArrayableContract`
* Método `->values()` de la clase `ContractCollectionBase.php` modificado para que sea como el de Laravel, ya que antes no hacía nada util
* Mejoras `@PHPDoc`

## [v0.5.0-beta.3](https://github.com/kalel1500/kalion/compare/v0.5.0-beta.2...v0.5.0-beta.3) - 2024-08-16

### Added

* Nuevo método `pluckTo()` en la clase `ContractCollectionBase.php` (para que tras hacer el `pluck`, haga directamente el `toCollection`)
* Nueva versión de la imagen del título del `README.md`
* Indicar con `@phpdoc` que el método `toCollection()` devuelve una instancia de la clase que recibe como argumento

### Changed

* Pasar el `$pluckField` al `toBase()` en lugar del `$with` y calcular el `getWithValue()` dentro
* Método `getWithValue()` simplificado

### Removed

* Eliminados svgs del `README.md` que no se utilizan
* Eliminar método `getWithValue()` y mover lógica al `toBase()`
* Quitar lógica `isInstanceOfRelatable()` y `isClassRelatable()` de `DomainHeplers.php` y hacer que las clases con relaciones implementen la interfaz `Relatable`

## [v0.5.0-beta.2](https://github.com/kalel1500/kalion/compare/v0.5.0-beta.1...v0.5.0-beta.2) - 2024-08-12

### Added

* Añadir CHANGELOG.md con todos los cambios de cada version (todos los tags renombrados por nuevos tags beta)
* composer.json: Añadir `minimum-stability` y `prefer-stable`

## [v0.5.0-beta.1](https://github.com/kalel1500/kalion/compare/v0.4.0-beta.2...v0.5.0-beta.1) - 2024-07-19

### Changed

!!!Gran refactor de la gestión de errores (mejorada y simplificada):
* `BasicException` -> parámetros null y valores por defecto
* `DomainHelpers` -> parámetros abort
* `DomainHelpers::getExceptionData()` -> trasladar estructuras al objeto `DataExceptionDo` y simplificar método
* `ContractDataObject` -> `toArray()` de los métodos cambiados por el `toArrayVisible()` para que no afecta cuando se cambie uno
* `DomainBaseException` -> simplificar estructura con el getExceptionData()
* `ExceptionHandler` -> cambiar orden del array
* `responseJsonError()` -> simplificar código con el `getExceptionData()`

## [v0.4.0-beta.2](https://github.com/kalel1500/kalion/compare/v0.4.0-beta.1...v0.4.0-beta.2) - 2024-07-19

### Fixed

* Mejora método `pluck` de la `CollectionBase` para que funcione con las propiedades readonly en PHP 8.2

## [v0.4.0-beta.1](https://github.com/kalel1500/kalion/compare/v0.3.0-beta.1...v0.4.0-beta.1) - 2024-07-17

### Changed

* `HexagonalServiceProvider`: mover `mergeConfigFrom()` del `register()` a su método especifico `configure()`
* `HexagonalServiceProvider`: meter prefijo `__DIR__.'/../../` de las rutas a la variable `HEXAGONAL_PATH`
* `HexagonalServiceProvider`: Eliminar método `addNewConfigLogChannels()` y meter código en `HexagonalService::setLogChannels()` para dejar el provider más limpio
* `HexagonalServiceProvider`: Mover método `HexagonalService::setLogChannels()` del `boot()` al `register()` (configure)
* Clase `HexagonalService` movida de `rc/Domain/Services` a `src/Infrastructure/Services`, ya que ahora utiliza el método `config()` de laravel
* Clase `HexagonalService` renombrada a `Hexagonal`
* `HexagonalServiceProvider`: registrar y publicar vistas
* Reestructurar vistas: Mover vista jobs.blade.php de `/views/queues/` a `/views/`

### Removed

* Quitar referencia vista externa `pages.errors.custom-error` en `DomainBaseException` trayendo el html a la vista `hexagonal::custom-error`

## [v0.3.0-beta.1](https://github.com/kalel1500/kalion/compare/v0.2.0-beta.4...v0.3.0-beta.1) - 2024-06-28

### Added

* Clase `MyJob`: Añadir parámetro `$logChannel` en los métodos para indicar donde guardar el Log
* Configurar los canales `queues` y `loads` para los Logs
* Clase `MyLog`: nuevos métodos `errorOnLoads` y `errorOn`

### Changed

* Clase `MyLog`: método `onQueuesError` renombrado a `errorOnQueues`

### Removed

* Clase `MyJob`: Quitar `echo` del mensaje de error
* Clase `MyJob`: Quitar fecha del mensaje, ya que el log ya pone la fecha

### Fixed

* Sol. error en la forma de mergear la configuracion de los nuevos canales de Logs

## [v0.2.0-beta.4](https://github.com/kalel1500/kalion/compare/v0.2.0-beta.3...v0.2.0-beta.4) - 2024-06-26

### Fixed

* Sol. error: columna `class` renombrada a `code` en la migración de la tabla `states` y eliminar restricción `class_type_unique`

### Removed

* Quitar dependencia del paquete `laravel-ts-utilities` del `composer.json` y el `README.md`

## [v0.2.0-beta.3](https://github.com/kalel1500/kalion/compare/v0.2.0-beta.2...v0.2.0-beta.3) - 2024-06-19

### Added

* Añadir las rutas `queues.checkService` y `websockets.checkService` al paquete
* Crear nuevas rutas ajax para obtener los Jobs y los Jobs Fallidos (`getJobs` y `getFailedJobs`)
* Nuevas rutas `queues.queuedJobs` y `queues.failedJobs` que solo devuelven una vista html con un id para tabulator
* Navbar añadido en la vista de Jobs
* `composer.json`: Añadir scripts `post-install` y `post-update` para que se instale el paquete de NPM `laravel-ts-utilities` (ya que es necesario para las vistas de los Jobs)
* `README`: Añadir información paquete laravel-ts-utilities
* `README`: Cambiar enlace del paquete laravel-ts-utilities del de NPM al de Github

### Removed

* Quitar el prefijo de las rutas `hexagonal` en la configuración

## [v0.2.0-beta.2](https://github.com/kalel1500/kalion/compare/v0.2.0-beta.1...v0.2.0-beta.2) - 2024-06-13

### Removed

* Quitar condición `runningInConsole()` al registrar los comandos para poder usarlos desde el código con `Artisan:call()`

## [v0.2.0-beta.1](https://github.com/kalel1500/kalion/compare/v0.1.0-beta.2...v0.2.0-beta.1) - 2024-06-13

### Changed

* Hacer finales todas las clases que no se van a extender
* Cambio de la Licencia del proyecto por `GNU General Public License v3.0`

### Removed

* Quitar el throws del PhpDoc del método `emitEvent()` ya que tiene un `tryCatch`

## [v0.1.0-beta.2](https://github.com/kalel1500/kalion/compare/v0.1.0-beta.1...v0.1.0-beta.2) - 2024-05-23

### Added

* Nuevo `ExceptionHandler.php` con el método `getUsingCallback()` para pasar como callback en el método `withExceptions()` al crear la aplicación en `/bootstrap/app.php -> Application::configure()->withExceptions(callback())`. Es para que todas las excepciones que devuelvan un Json tengan la estructura `['success' => ..., 'message' => '...', 'data' => []]`

### Removed

* Eliminar puntos y coma innecesarios

## v0.1.0-beta.1 - 2024-05-23

Primera versión funcional del paquete
