
Buscar clases con el prefijo
```regexp
\bContract(?!s)(\w*)
```

Buscar rutas que contengan "Contracts" excepto carpetas que contengan interfaces
```regexp
Thehouseofel\\Kalion\\(.*)\\Contracts\\((?!Repositories|Services|Arrayable|BuildArrayable|ExportableEntity|IdentifiableEnum|KalionExceptionInterface|Relatable|TranslatableEnum).*)
```

Buscar todas las clases que terminen con el sufijo Do
```regexp
\b(\w+)Do\b
```

