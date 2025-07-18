{{RAX}}
{{$route = route.export()}}
{{$route = data.filter($route, [
'method' => 'CLI'
])}}
{{$route = info.all.add($route)}}
Welcome to raxon.org                  {{terminal.color('blue')}}(c) Remco van der Velde {{terminal.color('green')}}({{config('framework.version')}}){{terminal.color('reset')}}

{{$route = data.sort($route, [
'info' => 'ASC'
])}}
{{$output = info.output($route)}}
{{implode('',$output)}}
{{dd($literal)}}