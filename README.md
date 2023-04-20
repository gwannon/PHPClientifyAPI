# PHPClientifyAPI
Librería de PHP para trabajar contra la API de Clientify


## Instalación
Renombra config.dist.php como config.php y configura tu API Key de Clientify.

## Notas
Los tags, emails, teléfonos y direcciones no se actualizan en la API hasta que no se ejecuta las funcione updateTags(), updateEmails(), ... Es una forma de ahorrar llamadas a la API concentrandolas en una sola función.

## Todo
* add/delete/update/has direcciones
* add/delete/update/has teléfonos

## Tipos de emails, direcciones o télefonos
| id | tipo    |
|----|---------|
| 1  | trabajo |
| 2  | personal|
| 3  | otros   |
| 4  | principal|
