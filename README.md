# PHPClientifyAPI
Librería de PHP para trabajar contra la API de Clientify. Por ahora permite crear, editar y borrar contactos. 
Además de añadir/quitar emails, teléfonos y etiquetas. Por último permite activar una automatización en 
el contacto sin necesidad de cumplir con ningún lanzador.

## Instalación
En config.dist.php tienes el código para establecer la conexión con tu API Key de Clientify para que metas en tu proyecto.

## Notas
Los tags, emails, teléfonos y direcciones no se actualizan en la API hasta que no se ejecuta la función update() (que ejecuta a su vez updateTags(), updateEmails(), ...) Es una forma de ahorrar llamadas a la API concentrándolas en una sola función. Lo último que se debería hacer es actualizar el cotnacto. El contacto sabe que se ha modificado y que actualziaciones debe ejecutar.

## ToDo Contactos
* EDIT campos personalizados
* ADD llamada
* ADD cita
* ADD usuario

## ToDo Otros
* Clase para gestionar compañias

## Tipos de télefonos
| id | tipo    |
|----|---------|
| 1  | trabajo |
| 2  | personal|
| 3  | otros   |
| 4  | principal|
