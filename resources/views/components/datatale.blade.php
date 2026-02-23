<div class="card stretch stretch-full">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover" id="{{ $tableId }}">
                <thead>
                    {{ $thead }}
                </thead>
                <tbody>
                    {{ $slot }}
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('#{{ $tableId }}').DataTable({
            pageLength: 10
        });
    });
</script>
@endpush