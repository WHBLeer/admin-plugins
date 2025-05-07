<div class="col-md-6 col-lg-4 col-xxl-3">
	<div class="card blog-card overflow-hidden">
		<a href="{{$plugin->getCover()}}" class="glightbox img-hover-zoom" data-glightbox="type: image; zoomable: true;">
			<img src="{{$plugin->getCover()}}" class="card-img-top" alt="...">
		</a>
		<div class="tag-container">
			<span class="badge text-light-{{ $plugin->isEnabled() ? 'success' : 'secondary' }}">{{ $plugin->isEnabled() ? 'Enabled' : 'Disabled' }}</span>
		</div>
		<div class="card-body">
			<p class="text-body-secondary"><i class="ti ti-calendar-due"></i> Version: {{ $plugin->getVersion() }}</p>
			<a href="{{route('admin.blog_details')}}" class="bloglink">
				<h5 class="title-text mb-2">{{ $plugin->getTitle() }}</h5>
			</a>
			<p class="card-text text-secondary">
				{{ $plugin->getDescription() }}
			</p>
			<div class="app-divider-v dashed py-3"></div>
			<div class="d-flex justify-content-between align-items-center gap-2 position-relative">
				<div class="h-40 w-40 d-flex-center b-r-10 overflow-hidden bg-primary position-absolute">
					<img src="{{plugin_cover($plugin->getAuthor())}}" alt="avatar" class="img-fluid">
				</div>
				<div class="ps-5">
					<h6 class="text-dark f-w-500 mb-0"> {{ $plugin->getAuthor() }}</h6>
					<p class="text-secondary f-s-12 mb-0">{{ $plugin->getEmail() }}</p>
				</div>
				<div>
					<div class="btn-group dropdown-icon-none">
						<button class="btn border-0 icon-btn b-r-4 dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-auto-close="true" aria-expanded="false">
							<i class="ti ti-dots-vertical f-s-18 text-dark"></i>
						</button>
						<ul class="dropdown-menu">
							<li class="settings-btn">
								<a class="dropdown-item text-success" href="{{ route('admin.plugins.settings.edit', $plugin->getName()) }}">
									<i class="ti ti-archive"></i> Settings
								</a>
							</li>
							@if($plugin->isEnabled())
								<li class="uninstall-btn">
									<a class="dropdown-item text-warning" href="javascript:;" data-action="{{ route('admin.plugins.uninstall', $plugin->getName()) }}" onclick="uninstallPlugin(this)">
										<i class="ti ti-trash"></i> Disable
									</a>
								</li>
							@else
								<li class="install-btn">
									<a class="dropdown-item text-success" href="javascript:;" data-action="{{ route('admin.plugins.install', $plugin->getName()) }}" onclick="installPlugin(this)">
										<i class="ti ti-trash"></i> Enable
									</a>
								</li>
							@endif
							<li class="delete-btn">
								<a class="dropdown-item text-danger" href="javascript:;" data-action="{{ route('admin.plugins.delete', $plugin->getName()) }}" onclick="deletePlugin(this)">
									<i class="ti ti-trash"></i> Delete
								</a>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>