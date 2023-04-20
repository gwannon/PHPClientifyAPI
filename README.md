# PHPClientifyAPI
Librería de PHP para trabajar contra la API de Clientify


## Instalación
Renombra config.dist.php como config.php y configura tu API Key de Clientify.

## Notas
Los tags, emails, teléfonos y direcciones no se actualizan en la API hasta que no se ejecuta las funciones updateTags(), updateEmails(), ... Es una forma de ahorrar llamadas a la API concentrándolas en una sola función.

## Todo
* ADD|DELETE|UPDATE|HAS direcciones
* ADD|DELETE|UPDATE|HAS teléfonos
* ADD nota
* ADD llamada
* ADD cita

## Tipos de emails, direcciones o télefonos
| id | tipo    |
|----|---------|
| 1  | trabajo |
| 2  | personal|
| 3  | otros   |
| 4  | principal|
