@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-7" align="center">
            <form action="{{route('users.update', ['id' => $user->id])}}" method="post">
                @method("PUT")
                @csrf
                <div class="form-row">
                    <label for="name">Name:</label>
                    <input type="text" name="name" class="form-control" value="{{$user->name}}">
                </div>
                <div class="form-row">
                    <label for="email">Email:</label>
                    <input type="email" name="email" class="form-control" value="{{$user->email}}">
                </div>
                <div class="form-row">
                    <input type="submit" name="submit" class="btn btn-primary">
                </div>
            </form>
        </div>
    </div>
@endsection