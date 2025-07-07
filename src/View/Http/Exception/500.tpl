{{RAX}}
<html>
<head>
    <title>HTTP/1.0 500 Internal server error: {{$exception.file}}</title>
    <style>
        {{require(config('framework.dir.view') + 'Http/Head/Style.rcss')}}
    </style>
</head>
<body>
<section name="header">
    <h3>HTTP/1.0 500 Internal server error:</h3>
</section><section name="message">
    <h3>{{$exception.message}}</h3>
</section><section name="detail">
    <label>Exception: </label>
    <span>{{$exception.className}}</span><br>
    <label>File: </label>
    <span>{{$exception.file}}</span><br>
    <label>Line: </label>
    <span>{{$exception.line}}</span><br>
    <label>Code: </label>
    <span>{{$exception.code}}</span><br>
</section>
{{if(
config('framework.environment') === 'development' &&
!is.empty($exception.file)
)}}
<section name="source">
    <label>Source: </label><br>
    {{$source = file.read($exception.file)}}
    {{if($source)}}
    {{$read = explode("\n", $source)}}
    {{$read_line = $exception.line - 1}}
    <table class="source">
        {{for($i = ($read_line - 3); $i <= ($read_line + 3); $i++)}}                        
        {{$row = $read[$i]}}        
        {{$row_nr = $i + 1}}
        {{if(
        $i === $read_line &&
        is.set($row)
        )}}
        <tr class="selected"><td class="line"><pre>{{$row_nr}}</pre></td><td class="row"><pre>{{$row}}</pre></td></tr>
        {{elseif(is.set($row))}}
        <tr><td class="line"><pre>{{$row_nr}}</pre></td><td class="row"><pre>{{$row}}</pre></td></tr>
        {{/if}}
        {{/for}}
    </table>
    {{/if}}
</section>
{{/if}}
{{if(
config('framework.environment') === 'development' &&
!is.empty($exception.trace)
)}}
<section name="trace">
    <label>Trace: </label><br>
    <table class="trace">
        {{foreach($exception.trace as $nr => $trace)}}
        <tr class="trace">
            <td class="title"><b>File:</b> {{$trace.file}} (<b>{{$trace.line}}</b>)</td>
        </tr>
        <tr class="trace">
            <td class="class"><b>Class:</b> {{$trace.class}}</td>
        </tr>
        <tr class="trace">
            <td class="function"><b>Function:</b> {{$trace.function}}</td>
        </tr>
        <tr class="trace-source">
            <td colspan="4">
                <label>Source: </label><br>
                {{$source = file.read($trace.file)}}
                {{if($source)}}
                {{$read = explode("\n", $source)}}
                {{$read_line = $trace.line - 1}}
                <table class="source">
                {{for($i = ($read_line - 3); $i <= ($read_line + 3); $i++)}}                        
                    {{$row = $read[$i]}}        
                    {{$row_nr = $i + 1}}
                    {{if(
                    $i === $read_line &&
                    is.set($row)
                    )}}
                    <tr class="selected"><td class="line"><pre>{{$row_nr}}</pre></td><td class="row"><pre>{{$row}}</pre></td></tr>
                    {{elseif(is.set($row))}}
                    <tr><td class="line"><pre>{{$row_nr}}</pre></td><td class="row"><pre>{{$row}}</pre></td></tr>
                    {{/if}}
                {{/for}}
                </table>
                {{/if}}
            </td>
        </tr>
        {{/foreach}}
    </table>
</section>
{{/if}}
</body>
</html>