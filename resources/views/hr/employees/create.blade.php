{{-- resources/views/hr/employees/create.blade.php --}}
@extends('layouts.hr-app')

@section('title', 'Add New Employee')

@section('content')
    <x-hr.breadcrumb title="Add New Employee" parent="Employees" route="hr.employees.index" />
    
    @include('hr.employees.form')
@endsection