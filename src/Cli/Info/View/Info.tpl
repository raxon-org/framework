{{RAX}}
{{if(config('silence') === true)}}
raxon.org silence mode... {{terminal.color('green')}}{{config('framework.version')}}{{terminal.color('reset')}}

{{else}}
Welcome to raxon.org {{terminal.color('green')}}{{config('framework.version')}}{{terminal.color('reset')}}


{{binary()}} bin                            | Creates binary
{{binary()}} cache clear                    | Clears the app cache
{{binary()}} info                           | This info
{{binary()}} info all                       | All info
{{binary()}} license                        | raxon_org/framework license
{{binary()}} password                       | Password hash generation
{{binary()}} uuid                           | Uuid generation
{{binary()}} version                        | Version information
{{/if}}