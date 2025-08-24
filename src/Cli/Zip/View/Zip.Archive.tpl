{{$options = options()}}
{{$source = $options.source}}
{{$target = $options.target}}
{{zip.archive($source, $target)}}
