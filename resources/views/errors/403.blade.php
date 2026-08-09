@extends('errors.layout')

@section('title', __('Forbidden'))
@section('code', '403')
@section('heading', __('Forbidden'))
@section('message', $exception->getMessage() ?: __('http_403_message'))
