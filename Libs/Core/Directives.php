<?php
namespace Taro\PageMaker\Core;

class Directives
{
    const VAR_DIRECTIVE = [
        'begin'=>'[[', 'end'=>']]'
    ];
    const CONTENT_DIRECTIVE = [
        'symbol'=>'@content'
    ];
    const IF_DIRECTIVE = [
        'begin'=>'@if', 'end'=>'@endif','condition_begin'=>'(','condition_end'=>')'
    ];
    const FOR_DIRECTIVE = [
        'begin'=>'@for', 'end'=>'@endfor','condition_begin'=>'(','condition_end'=>')'
    ];
    const FOREACH_DIRECTIVE = [
        'begin'=>'@foreach', 'end'=>'@endforeach','condition_begin'=>'(','condition_end'=>')'
    ];
    const WHILE_DIRECTIVE =  [
        'begin'=>'@while', 'end'=>'@endwhile','condition_begin'=>'(','condition_end'=>')'
    ];
    const INCLUDES_DIRECTIVE = [
        'symbol'=>'@includes','file_begin'=>'(','file_end'=>')','ext'=>'.php'
    ];
    const EXTENDS_DIRECTIVE = [
        'symbol'=>'@extends','file_begin'=>'(','file_end'=>')','ext'=>'.php'
    ];
    const FUNC_DIRECTIVE = [
        'symbol'=>'|'
    ];
}
