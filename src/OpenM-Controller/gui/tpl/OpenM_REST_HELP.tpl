<!DOCTYPE html>
<html>
    <head>
        <title>{$help.title}</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="{$resources_dir}css/bootstrap.min.css" rel="stylesheet" media="screen">
        <LINK href="{$resources_dir}OpenM-ID/api/Impl/view/user-form.css" rel="stylesheet" type="text/css">        
        <link href="{$resources_dir}css/bootstrap-esponsive.min.css" rel="stylesheet">
        <!--[if lt IE 9]>
                <script src="{$resources_dir}dist/html5shiv.js"></script>
        <![endif]-->
    </head>
    <body class="body">
        <h1>
            {$help.title}
        </h1>
        <div class="hero-unit">
            <h2 id="TOC">
                Table Of Content
            </h2>
            <div class="accordion" id="accordion2">
                {foreach from=$help.apis item=api}
                    <div class="accordion-group">
                        <div class="accordion-heading">
                            <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapse{$api.name}">
                                {$api.name}
                            </a>
                        </div>
                        <div id="collapse{$api.name}" class="accordion-body collapse">
                            <div class="accordion-inner">
                                <a href="#{$api.name}" id="TOC.{$api.name}">Begin</a>
                                <ol>
                                    <li>Methods:
                                        <ul>
                                            {foreach from=$api.methods item=method}
                                                <li>
                                                    <a href="#{$api.name}.{$method.name}" id="TOC.{$api.name}.{$method.name}">{$method.name}</a>
                                                </li>
                                            {/foreach}
                                        </ul>
                                    </li>
                                    <li>Constants:
                                        <ul>
                                            <li>
                                                <a href="#{$api.name}.{$method.name}.parameters" id="TOC.{$api.name}.{$method.name}.parameters">Parameters</a>
                                            </li>
                                            <li>
                                                <a href="#{$api.name}.{$method.name}.return_defined_parameters" id="TOC.{$api.name}.{$method.name}.return_defined_parameters">RETURN defined Parameters (direct HTTP return in JSON)</a>
                                            </li>
                                            <li>
                                                <a href="#{$api.name}.{$method.name}.return_defined_values" id="TOC.{$api.name}.{$method.name}.return_defined_values">RETURN defined values (direct HTTP return in JSON)</a>
                                            </li>
                                            <li>
                                                <a href="#{$api.name}.{$method.name}.additional_services" id="TOC.{$api.name}.{$method.name}.additional_services">Additional Services</a>
                                            </li>
                                            <li>
                                                <a href="#{$api.name}.{$method.name}.others" id="TOC.{$api.name}.{$method.name}.others">Other constants</a>
                                            </li>
                                        </ul>
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                {/foreach}
            </div>
        </div>
        {foreach from=$help.apis item=api}
            <div class="hero-unit">
                <h2><a href="#TOC.{$api.name}"><i class="icon-arrow-up"></i></a> '<b id="{$api.name}">{$api.name}</b>' <i>(?api={$api.name})</i>{if $api.version!=""} VERSION: {$api.version}{/if}</h2>
                <h3>Contains the following methods:</h3>
                {foreach from=$api.methods item=method}
                    <p>
                        <a href="#TOC.{$api.name}.{$method.name}"><i class="icon-arrow-up"></i></a> <b id="{$api.name}.{$method.name}">{$method.name}</b>
                        <i>(&method={$method.name})</i>, waiting following parameters:
                    <dl class="dl-horizontal">
                        {foreach from=$method.parameters item=parameter name=parameter}
                            <dt>{if $parameter.isOptional}[{/if}{$parameter.name}{if $parameter.isOptional}{if $parameter.defaultValue != ""}={$parameter.defaultValue}{/if}]{/if}</dt>
                            <dd><i>(&{$parameter.parameterName}={$parameter.name|lower}_example, with '{$parameter.name|lower}_example' url encoded)</i></dd>
                        {/foreach}
                    </dl>
                    <i class="icon-flag"></i>Example: 
                    <a href="{$help.url}?api={$api.name}&method={$method.name}{foreach from=$method.parameters item=parameter}&{$parameter.parameterName}={$parameter.name}_Example{/foreach}" target="_blank">
                        {$help.url}
                        ?api={$api.name}
                        &method={$method.name}{foreach from=$method.parameters item=parameter}
                        &{$parameter.parameterName}={$parameter.name}_Example{/foreach}
                    </a>
                </p>
            {/foreach}

            <h3>And the following constants:</h3>
            <h4 id="{$api.name}.{$method.name}.parameters"><a href="#TOC.{$api.name}.{$method.name}.parameters"><i class="icon-arrow-up"></i></a> Parameters</h4>
            <div class="well">
                {foreach from=$api.constants.parameters item=parameter name=parameter}
                    <ul>
                        <p>
                        <dl>
                            <dt>{$parameter.name}:</dt>
                            <dd>"{$parameter.value}"
                                <i>(Example: &{$parameter.value}={$parameter.value|lower}_example)</i></dd>
                        </dl>
                        </p>
                    </ul>
                {/foreach}
            </div>
            <br>
            <h4 id="{$api.name}.{$method.name}.return_defined_parameters"><a href="#TOC.{$api.name}.{$method.name}.return_defined_parameters"><i class="icon-arrow-up"></i></a> RETURN defined Parameters (direct HTTP return in JSON)</h4>
            <div class="well">
                {foreach from=$api.constants.returns item=return name=return}
                    <ul>
                        <p>
                        <dl>
                            <dt>{$return.name}:</dt>
                            <dd>{$return.value}
                                <i>(Example: {literal}{{/literal}"{$return.value}":"{$return.value|lower}_example{literal}"}{/literal})</i></dd>
                        </dl>
                        </p>
                    </ul>
                {/foreach}
            </div>
            <br>
            <h4 id="{$api.name}.{$method.name}.return_defined_values"><a href="#TOC.{$api.name}.{$method.name}.return_defined_values"><i class="icon-arrow-up"></i></a>  RETURN defined values (direct HTTP return in JSON)</h4>
            <div class="well">
                {foreach from=$api.constants.returnValues item=returnValue name=returnValue}
                    <ul>
                        <p>
                        <dl >
                            <dt>{$returnValue.name}:</dt>
                            <dd>"{$returnValue.value}" 
                                <i>(Example: {literal}{{/literal}"{if $returnValue.return!=""}{$returnValue.return}{else}RETURN_PARAMETER_example{/if}":"{$returnValue.value}"{literal}}{/literal})</i></dd>
                        </dl>
                        </p>
                    </ul>
                {/foreach}    
            </div>  
            <br>
            <h4 id="{$api.name}.{$method.name}.additional_services"><a href="#TOC.{$api.name}.{$method.name}.additional_services"><i class="icon-arrow-up"></i></a> Additional Services</h4>
            <div class="well">
                {foreach from=$api.constants.otherAPI item=otherAPI name=otherAPI}
                    <ul>
                        <p>
                        <dl class="dl-horizontal">
                            <dt>{$otherAPI.name}</dt>
                            <dd><a href="{$help.url}?{$otherAPI.value}">?{$otherAPI.value}</a></dd>
                        </dl>
                        </p>
                    </ul>
                {/foreach}
            </div>
            <br>
            <h4 id="{$api.name}.{$method.name}.others"><a href="#TOC.{$api.name}.{$method.name}.others"><i class="icon-arrow-up"></i></a> Other constants</h4>
            <div class="well">
                {foreach from=$api.constants.others item=other name=other}
                    <ul>
                        <p>
                        <dl >
                            <dt>{$other.name}:</dt>
                            <dd>{$other.value}</dd>
                        </dl>
                        </p>
                    </ul>
                {/foreach}
            </div>
        </div>
        <br>
    {/foreach}
    <script src="{$resources_dir}jquery.min.js"></script>
    <script src="{$resources_dir}js/bootstrap.min.js"></script>
</body>
</html>