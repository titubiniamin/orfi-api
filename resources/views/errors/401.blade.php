@extends('errors.layout')

@php
  $error_number = 401;
@endphp

@section('title')
  Unauthorized action.
@endsection

@section('description')
    <p>Please contact admin</p>
  @php
    $default_error_message = "Please <a href='javascript:history.back()''>go back</a> or return to <a href='".url('')."'>our homepage</a>.";
  @endphp
  {!! isset($exception)? ($exception->getMessage()?$exception->getMessage():$default_error_message): $default_error_message !!}
@endsection
