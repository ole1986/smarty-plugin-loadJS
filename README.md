# Smarty 3.X Template plugin 'loadJS'

This function template for Smarty v3.x is used to load javascript file dynamically
It also supports combining all defined document into one merged file starting with `merged-[...].js` 

Example (template):
 
```html
<head>
     {* load all javascript files from a directory using php glob *}
     {loadJS location='scripts/*.js' output='scripts/'}
     {* load a list of js files and merge them into a single JS file 'merged-[...].js'*}
     {loadJS location=['scripts/init.js', 'scripts/second.js'] output='scripts/' concatenate=true}
</head>
 ```
  
Example (php):
 
```php
// check if a merged js file exists in an output folder
LoadJS::IsMerged('scripts/')
// get the merged file using static property 'mergedFile'
echo LoadJS::$mergedFile
```
 