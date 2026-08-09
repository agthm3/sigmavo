@extends('errors.layout')

@section('code', '500')
@section('title', 'Terjadi Kesalahan Server')
@section('message', 'Maaf, terjadi kendala pada sistem internal server kami. Tim teknis sedang menangani masalah ini.')
@section('detail_code')
HTTP 500 Internal Server Error.
Exception: {{ isset($exception) ? $exception->getMessage() : 'Internal Database or Code Execution Fault' }}
File: {{ isset($exception) ? $exception->getFile() . ':' . $exception->getLine() : 'Unknown' }}
Timestamp: {{ now()->toIso8601String() }}
@endsection