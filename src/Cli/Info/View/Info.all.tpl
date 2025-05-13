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
{{$command = []}}
{{$description = []}}
{{$output = explode("\n", implode("\n", $output))}}
{{foreach($output as $nr => $line)}}
{{$explode = explode('|', $line)}}
{{if(array.key.exist($explode, 0))}}
{{$command[$nr] = string.trim($explode[0])}}
{{/if}}
{{if(array.key.exist($explode, 1))}}
{{$description[$nr] = string.trim($explode[1])}}
{{else}}
{{$description[$nr] = ''}}
{{/if}}
{{/foreach}}
{{foreach($command as $nr => $cmd)}}
{{$counter = $nr + 1}}
[ {{$counter}} ] {{$cmd}}
{/foreach}
{{dd($description)}}