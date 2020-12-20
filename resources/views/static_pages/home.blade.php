@extends('layouts.default')
@section('title', '主页')
@section('content')
    <div class="jumbotron">
        <h1>Hello Laravel</h1>
        <p class="lead">
            <a href="{{route('help')}}">需要帮助？</a>
        </p>
        <p>
            <a  class="btn btn-success btn-lg">登录</a>
        </p>
        <p>
            <a  class="btn btn-success btn-lg" href="{{route('singup')}}">现在注册</a>
        </p>
    </div>
@stop
