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
{{$output = []}}
{{foreach($route as $nr => $record)}}
{{if(is.array($record.info))}}
{{$info = implode("\n", $record.info)}}
{{$output[] = parse.string($info)}}
{{elseif(!is.empty($record.info))}}
{{$output[] = parse.string($record.info)}}
{{/if}}
{{/foreach}}
{{$output = explode("\n", implode('', $output))}}
{{dd($output)}}