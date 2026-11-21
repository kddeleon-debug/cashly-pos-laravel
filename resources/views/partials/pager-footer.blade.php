@if ($paginator->total() > 0)
    <div style="padding:12px 20px 0;color:var(--muted);font-size:13px">
        Showing {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} of {{ $paginator->total() }}
    </div>
@endif
{{ $paginator->links() }}
