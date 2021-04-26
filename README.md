# esprintf

sprintf mixed with zend escaper.

All placeholder strings must be explicitly labeled as to what type of escaping they will have done to them, so you can see in the source string if the correct escaping is done for the string location. 


## Usage

```
    $templateString = "<span class=':attr_class'>:html_message</span>";
    $params = [
        ':attr_class' => 'warning',
        ':html_message' => 'foo bar'
    ];
     
    echo esprintf($templateString, $params);
```

The template string must either be a literal string embedded in the source code, or an EscapedString which is the type of object returned from esprintf().


## Supported escapers

```
':attr_'
':js_'
':css_' 
':uri_' 
':html_'
```

All go through to the relevant Zend Escaper method.

```
':raw_'
``` 

Allow a raw string to be used, for when the string has already been escaped.