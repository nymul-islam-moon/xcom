<div class="text-center">
    <div class="dropdown">
        <button class="btn btn-sm btn-secondary dropdown-toggle" 
                type="button" 
                id="actionsDropdown{{ $id }}" 
                data-bs-toggle="dropdown" 
                aria-expanded="false">
            Actions
        </button>

        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="actionsDropdown{{ $id }}">
            @foreach($actions as $action)
                @if($action['type'] === 'divider')
                    <li><hr class="dropdown-divider"></li>
                @elseif($action['type'] === 'link')
                    <li>
                        <a class="dropdown-item" href="{{ $action['url'] }}">
                            <i class="bi {{ $action['icon'] ?? '' }} me-2"></i> {{ $action['label'] }}
                        </a>
                    </li>
                @elseif($action['type'] === 'delete')
                    <li>
                        <form action="{{ $action['url'] }}" method="POST" 
                              onsubmit="return confirm('{{ $action['confirm'] ?? 'Are you sure?' }}');"
                              style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="bi {{ $action['icon'] ?? 'bi-trash' }} me-2"></i> {{ $action['label'] }}
                            </button>
                        </form>
                    </li>
                @endif
            @endforeach
        </ul>
    </div>
</div>
