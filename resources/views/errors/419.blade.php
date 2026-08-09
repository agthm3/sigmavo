@extends('errors.layout')

@section('code', '419')
@section('title', 'Sesi Halaman Kadaluarsa')
@section('message', 'Sesi login atau formulir Anda telah kadaluarsa karena terlalu lama tidak aktif. Silakan muat ulang halaman.')
@section('detail_code')
HTTP 419 Page Expired (CSRF Token Mismatch).
Timestamp: {{ now()->toIso8601String() }}
@endsection