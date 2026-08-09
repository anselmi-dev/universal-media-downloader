@extends('errors.layout')

@section('title', __('Too Many Requests'))
@section('code', '429')
@section('heading', __('Too Many Requests'))
@section('message', __('http_429_message'))
