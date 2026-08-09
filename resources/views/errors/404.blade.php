@extends('errors.layout')

@section('code', '404')
@section('title', 'Halaman Tidak Ditemukan')
@section('message', 'Maaf, halaman atau tautan yang Anda cari tidak tersedia, telah dihapus, atau alamat URL salah.')
@section('detail_code')
HTTP 404 Not Found Exception.
Target URL: {{ request()->fullUrl() }}
Route Name: {{ request()->route() ? request()->route()->getName() : 'Unknown' }}
Timestamp: {{ now()->toIso8601String() }}
@endsection