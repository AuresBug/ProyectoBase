@extends('adminlte::master')


@section('adminlte_css')
  @stack('css')
  @yield('css')
@stop


@section('classes_body', 'text-center')

{{-- @section('body_data', $layoutHelper->makeBodyData()) --}}


@section('body')

@stop

@section('adminlte_js')
  @stack('js')
  @yield('js')
@stop
