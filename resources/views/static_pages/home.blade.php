@extends('layouts.default')
@section('title', '主页')
@section('content')
    <div class="jumbotron">
        <h1>Hello Laravel</h1>
        <p class="lead">
            <a href="{{route('help')}}">需要帮助？</a>
        </p>
        @if(!Auth::check())
        <p>
            <a  href="{{route('login')}}" class="btn btn-success btn-lg">登录</a>
        </p>
        <p>
            <a  class="btn btn-success btn-lg" href="{{route('signup')}}">现在注册</a>
        </p>
            @endif
    </div>
@stop
