{{-- resources/views/hr/employees/edit.blade.php --}}
@extends('layouts.hr-app')

@section('title', 'Edit Employee - ' . $employee->user->name)

@section('content')
    <x-hr.breadcrumb title="Edit Employee" parent="Employees" route="hr.employees.index" />
    
    @include('hr.employees.form', ['employee' => $employee])
@endsection