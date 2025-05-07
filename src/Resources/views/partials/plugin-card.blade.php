// Resources/views/partials/plugin-card.blade.php

<div class="col-md-4 mb-4">
	<div class="card plugin-card">
		<div class="card-header d-flex justify-content-between align-items-center">
			<h5 class="mb-0">{{ $plugin->getTitle() }}</h5>
			<span class="badge badge-{{ $plugin->isEnabled() ? 'success' : 'secondary' }}">
                {{ $plugin->isEnabled() ? 'Enabled' : 'Disabled' }}
            </span>
		</div>
		<div class="card-body">
			<p class="card-text">{{ $plugin->getDescription() }}</p>
			<p class="card-text">
				<small class="text-muted">Version: {{ $plugin->getVersion() }}</small>
			</p>
		</div>
		<div class="card-footer bg-transparent">
			<div class="d-flex justify-content-between">
				@if($plugin->isEnabled())
					<form action="{{ route('plugins.uninstall', $plugin->getName()) }}" method="POST" class="d-inline">
						@csrf
						<button type="submit" class="btn btn-sm btn-warning">Disable</button>
					</form>
				@else
					<form action="{{ route('plugins.install', $plugin->getName()) }}" method="POST" class="d-inline">
						@csrf
						<button type="submit" class="btn btn-sm btn-success">Enable</button>
					</form>
				@endif
				
				<a href="{{ route('plugins.settings.edit', $plugin->getName()) }}"
				   class="btn btn-sm btn-info">Settings</a>
				
				<form action="{{ route('plugins.delete', $plugin->getName()) }}" method="POST" class="d-inline">
					@csrf
					@method('DELETE')
					<button type="submit" class="btn btn-sm btn-danger"
							onclick="return confirm('Are you sure you want to delete this plugin?')">
						Delete
					</button>
				</form>
			</div>
		</div>
	</div>
</div>