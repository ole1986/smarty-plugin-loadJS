# Smarty 3.X Template plugin 'loadJS'

This Smarty template function is used to dynamically load javascript file into HTML head lines
It also supports combining all defined document into one single JS file starting with `merged-[...].js` using the parameter `concatenate=true`

### Installation

Copy the function.loadJS.php into the Smarty Template plugin directory.

You can also use a custom plugin folder and define it as plugin folder using Smarty method `setPluginsDir`

```php
// example using parent plugins folder to load additional plugins into Smarty Template Class
$smarty->setPluginsDir('./plugins');
```

### Example (template):
 
```html
<head>
     {* load all javascript files from a directory using php glob *}
     {loadJS location='scripts/*.js' output='scripts/'}
     {* load a list of js files and merge them into a single JS file 'merged-[...].js'*}
     {loadJS location=['scripts/init.js', 'scripts/second.js'] output='scripts/' concatenate=true}
</head>
 ```
  
### Example (php):
 
```php
// check if a merged js file exists in an output folder
LoadJS::IsMerged('scripts/')
// get the merged file using static property 'mergedFile'
echo LoadJS::$mergedFile
```
 
