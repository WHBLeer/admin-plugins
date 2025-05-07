// Resources/views/admin/index.blade.php

@extends('layouts.admin')

@section('title', 'Plugins Management')

@section('content')
	<div class="container-fluid">
		<div class="row mb-4">
			<div class="col-md-6">
				<h2>Plugins Management</h2>
			</div>
			<div class="col-md-6 text-right">
				<button class="btn btn-primary" data-toggle="modal" data-target="#uploadPluginModal">
					<i class="fas fa-upload"></i> Upload Plugin
				</button>
			</div>
		</div>
		
		<div class="card">
			<div class="card-body">
				@if(session('success'))
					<div class="alert alert-success">{{ session('success') }}</div>
				@endif
				
				@if(session('error'))
					<div class="alert alert-danger">{{ session('error') }}</div>
				@endif
				
				<div class="row">
					@foreach($plugins as $plugin)
						@include('plugins::partials.plugin-card', ['plugin' => $plugin])
					@endforeach
				</div>
			</div>
		</div>
	</div>
	
	<!-- Upload Plugin Modal -->
	<div class="modal fade" id="uploadPluginModal" tabindex="-1" role="dialog" aria-labelledby="uploadPluginModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="uploadPluginModalLabel">Upload Plugin</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<form action="{{ route('plugins.upload') }}" method="POST" enctype="multipart/form-data">
					@csrf
					<div class="modal-body">
						<div class="form-group">
							<label for="pluginFile">Plugin ZIP File</label>
							<input type="file" class="form-control-file" id="pluginFile" name="plugin" required>
							<small class="form-text text-muted">Upload a valid plugin ZIP package</small>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
						<button type="submit" class="btn btn-primary">Upload & Install</button>
					</div>
				</form>
			</div>
		</div>
	</div>
@endsection