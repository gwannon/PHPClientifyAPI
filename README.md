# PHPClientifyAPI
Librería de PHP para trabajar contra la API de Clientify. Por ahora permite crear, editar y borrar contactos. 
Además de añadir/quitar emails, teléfonos y etiquetas. Por último permite activar una automatización en 
el contacto sin necesidad de cumplir con ningún lanzador.

## Instalación
Renombra config.dist.php como config.php y configura tu API Key de Clientify.

## Notas
Los tags, emails, teléfonos y direcciones no se actualizan en la API hasta que no se ejecuta las funciones updateTags(), updateEmails(), ... Es una forma de ahorrar llamadas a la API concentrándolas en una sola función.

## ToDo Contactos
* ADD llamada
* ADD cita
* ADD usuario

## ToDo Otros
* Clase para gestionar compañias

## Tipos de emails, direcciones o télefonos
| id | tipo    |
|----|---------|
| 1  | trabajo |
| 2  | personal|
| 3  | otros   |
| 4  | principal|
