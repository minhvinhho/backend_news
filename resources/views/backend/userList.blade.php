@extends('layouts.admin')
@section('content')
    <div class="panel panel-default no-margin-bottom">
        <div class="panel-heading">
            <strong>All Users</strong>&nbsp;
            <a href="{{route('create-user')}}"><span class="fa fa-plus"></span></a>
        </div>
        <div class="panel-body">
            <table class="table table-hover">
                <tr class="text-center">
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th class="text-center">Operations</th>
                </tr>
                @foreach($users as $user)
                    <tr>
                        <td>{{$user->id}}</td>
                        <td><a href="{{route("get-user", ["userId"=>$user->id])}}">{{$user->name}}</a></td>                    
                        <td>{{$user->email}}</td>
                        <td class="text-center">
                            {{implode(",",$user->roles->pluck('name')->toArray())}}
                        </td>
                        <td class="text-center">
                            @if($user->is_active != 'fa-toggle-on')
                                    <label class="label label-success">Active</label>
                                @else
                                    <label class="label label-danger">Lock</label>
                                @endif
                        </td>
                        <td>{{$user->createdAtHuman}}</td>
                        <td class="text-center">
                            <a href="{{route('edit-user', ['userId' => $user->id])}}">
                                <span class="fa fa-edit text-primary"></span>&nbsp;
                            </a>
                            <a href="{{route('toggle-user-active', $user->id)}}">
                                <span class="fa fa-lg {{$user->is_active ? 'fa-toggle-on text-success' : 'fa-toggle-off text-grey'}}"></span>
                            </a>
                            <a href="{{route('delete-user', $user->id)}}" onclick="return confirm('Are you sure to delete');">
                                <span class="fa fa-trash text-danger"></span>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </table>
            {{$users->links()}}
        </div>
    </div>
@endsection
