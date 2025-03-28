{{RAX}}
{{$route = route.export()}}
{{$route = data.filter($route, [
'method' => 'CLI'
])}}
{{$route = info_all_add($route)}}
Welcome to raxon.org                  {{terminal.color('blue')}}(c) Remco van der Velde {{terminal.color('green')}}({{config('framework.version')}}){{terminal.color('reset')}}

{{$route = data.sort($route, [
'info' => 'ASC'
])}}
{{foreach($route as $nr => $record)}}
{{if(is.array($record.info))}}
{{$info = implode("\n", $record.info)}}
{{parse.string($info)}}

{{elseif(!is.empty($record.info))}}
{{parse.string($record.info)}}

{{/if}}
{{/foreach}}
