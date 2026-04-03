<div class="table-card" id="mainCard">

    <div class="toolbar">
        {{ $filters }}
    </div>

    <div class="table-responsive">
        {{ $slot }}
    </div>

    <div class="pagination-container">
        {{ $pagination }}
    </div>

</div>