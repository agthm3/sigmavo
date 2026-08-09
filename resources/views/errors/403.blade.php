@extends('errors.layout')

@section('code', '403')
@section('title', 'Akses Ditolak (Forbidden)')
@section('message', 'Maaf, Anda tidak memiliki hak akses atau izin yang cukup untuk membuka halaman ini.')
@section('detail_code')
HTTP 403 Forbidden Exception.
Role User Anda saat ini tidak diizinkan mengakses route ini.
Timestamp: {{ now()->toIso8601String() }}
@endsection