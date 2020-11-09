<h1>Available Permissions</h1>

@forelse ($permissions as $permission)
    <li>{{ $permission->name }}</li>
@empty
    <p>No permissions configured yet.</p>
@endforelse
