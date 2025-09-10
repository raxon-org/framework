{{RAX}}
Raxon-framework version: {{app.version()}}, Built: {{app.built()}}

Copyright (c) 2018-{{date('Y')}} Remco van der Velde
{{$installation.directory = config('project.dir.root')}}
Installation directory: {{$installation.directory|>default:''}}


PHP {{php.version()}} Copyright (c) The PHP Group

Installed packages: 
Created                Updated               Name
{{if(!is.empty($list))}}
{{foreach($list as $installation)}}
{{date('Y-m-d H:i:s', $installation.ctime)}} {{date('Y-m-d H:i:s', $installation.mtime)}} {{$installation.name}} 

{{/foreach}}
{{/if}}