{{$options = options()}}
{{$source = $options.source}}
{{$target = $options.target}}
{{unset($options.source)}}
{{unset($options.target)}}
{{zip.extract($source, $target, $options)}}

